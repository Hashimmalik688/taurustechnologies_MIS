<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImportEMSDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = storage_path('ems_import.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at {$csvPath}");
            return;
        }

        $file = fopen($csvPath, 'r');
        $header = null;
        $imported = 0;
        $updated = 0;
        $skipped = 0;

        while (($data = fgetcsv($file)) !== false) {
            // Skip header row
            if ($header === null) {
                $header = array_map(function($h) {
                    return strtolower(trim(str_replace(' ', '_', $h)));
                }, $data);
                continue;
            }

            // Build associative array
            $record = array_combine($header, $data);

            $email = strtolower(trim($record['email'] ?? ''));
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                continue;
            }

            // Prepare data - only include non-empty values
            $employeeData = [
                'email' => $email,
            ];

            // Add other fields if not empty
            if (!empty(trim($record['position'] ?? ''))) {
                $employeeData['position'] = trim($record['position']);
            }
            if (!empty(trim($record['contact_info'] ?? ''))) {
                $employeeData['contact_info'] = trim($record['contact_info']);
            }
            if (!empty(trim($record['emergency_contact'] ?? ''))) {
                $employeeData['emergency_contact'] = trim($record['emergency_contact']);
            }
            if (!empty(trim($record['cnic'] ?? ''))) {
                $employeeData['cnic'] = trim($record['cnic']);
            }
            if (!empty(trim($record['area_of_residence'] ?? ''))) {
                $employeeData['area_of_residence'] = trim($record['area_of_residence']);
            }
            if (!empty(trim($record['status'] ?? ''))) {
                $employeeData['status'] = trim($record['status']);
            }
            if (!empty(trim($record['mis'] ?? ''))) {
                $employeeData['mis'] = trim($record['mis']);
            }

            // Check if employee exists
            $employee = Employee::where('email', $email)->first();

            if ($employee) {
                // Update only empty fields
                $dataToUpdate = [];
                foreach ($employeeData as $key => $value) {
                    // Skip email as it's the key
                    if ($key !== 'email') {
                        // Only update if employee field is empty
                        if (empty($employee->{$key})) {
                            $dataToUpdate[$key] = $value;
                        }
                    }
                }

                if (!empty($dataToUpdate)) {
                    $employee->update($dataToUpdate);
                    $updated++;
                } else {
                    $skipped++;
                }
            } else {
                // Create new employee
                // Set defaults for required fields
                if (!isset($employeeData['name'])) {
                    $employeeData['name'] = explode('@', $email)[0];
                }
                if (!isset($employeeData['status'])) {
                    $employeeData['status'] = 'Active';
                }
                if (!isset($employeeData['mis'])) {
                    $employeeData['mis'] = 'No';
                }

                Employee::create($employeeData);
                $imported++;
            }
        }

        fclose($file);

        $this->command->info("✓ EMS Data Import Complete!");
        $this->command->info("  Imported: {$imported} new employees");
        $this->command->info("  Updated: {$updated} existing employees");
        $this->command->info("  Skipped: {$skipped} records");
    }
}
