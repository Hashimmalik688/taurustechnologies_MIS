<?php
namespace App\Imports;

use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class EmployeesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $email = $row['email'] ?? null;
            $cnic = $row['cnic'] ?? null;
            if (!$email && !$cnic) {
                Log::warning('Skipping row: missing both email and cnic', $row->toArray());
                continue;
            }
            $employee = Employee::where('email', $email)
                ->orWhere('cnic', $cnic)
                ->first();
            if ($employee) {
                $employee->update([
                    'name' => $row['name'] ?? $employee->name,
                    'contact_info' => $row['contact_info'] ?? $employee->contact_info,
                    'emergency_contact' => $row['emergency_contact'] ?? $employee->emergency_contact,
                    'cnic' => $row['cnic'] ?? $employee->cnic,
                    'position' => $row['position'] ?? $employee->position,
                    'area_of_residence' => $row['area_of_residence'] ?? $employee->area_of_residence,
                    'status' => $row['status'] ?? $employee->status,
                    'mis' => $row['mis'] ?? $employee->mis,
                    // 'passport_image' => handle image if needed
                ]);
            } else {
                Employee::create([
                    'name' => $row['name'] ?? '',
                    'email' => $row['email'] ?? '',
                    'contact_info' => $row['contact_info'] ?? '',
                    'emergency_contact' => $row['emergency_contact'] ?? '',
                    'cnic' => $row['cnic'] ?? '',
                    'position' => $row['position'] ?? '',
                    'area_of_residence' => $row['area_of_residence'] ?? '',
                    'status' => $row['status'] ?? '',
                    'mis' => $row['mis'] ?? '',
                    // 'passport_image' => handle image if needed
                ]);
            }
        }
    }
}
