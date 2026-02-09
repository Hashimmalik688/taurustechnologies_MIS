<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Str;

class ImportEMSData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ems:import {file : Path to the CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import employee data from EMS CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1;
        }

        $file = fopen($filePath, 'r');
        $header = null;
        $row = 0;
        $imported = 0;
        $linked = 0;
        $updated = 0;

        while (($data = fgetcsv($file)) !== false) {
            $row++;

            // Skip header row
            if ($row === 1) {
                $header = $data;
                continue;
            }

            // Build associative array from row data
            $record = array_combine($header, $data);

            // Normalize and clean data
            $email = trim($record['Email'] ?? '');
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->warn("Row $row: Invalid email, skipping");
                continue;
            }

            $email = strtolower($email);

            // Prepare employee data
            $employeeData = [
                'name' => trim($record['Name'] ?? $record['name'] ?? ''),
                'email' => $email,
                'contact_info' => trim($record['Contact info'] ?? $record['contact_info'] ?? $record['Contact'] ?? ''),
                'emergency_contact' => trim($record['Emergency Contact'] ?? $record['emergency_contact'] ?? ''),
                'cnic' => trim($record['CNIC'] ?? $record['cnic'] ?? ''),
                'position' => trim($record['Position'] ?? $record['position'] ?? ''),
                'area_of_residence' => trim($record['Area of Residence'] ?? $record['area_of_residence'] ?? $record['Residence'] ?? ''),
                'status' => trim($record['Status'] ?? 'Active'),
                'mis' => trim($record['MIS'] ?? 'No'),
            ];

            // Check if employee exists
            $employee = Employee::where('email', $email)->first();

            if ($employee) {
                // Check if user exists with this email
                $user = User::where('email', $email)->first();
                if ($user && !in_array($employee->mis, ['Yes', 'yes'])) {
                    // Link employee to user
                    $employeeData['mis'] = 'Yes';
                    $employee->update($employeeData);
                    $linked++;
                } else {
                    // Update existing employee
                    $employee->update($employeeData);
                    $updated++;
                }
            } else {
                // Create new employee
                Employee::create($employeeData);
                $imported++;
            }
        }

        fclose($file);

        $this->info("Import completed!");
        $this->info("Imported: $imported new employees");
        $this->info("Linked: $linked employees to users");
        $this->info("Updated: $updated existing employees");

        return 0;
    }
}
