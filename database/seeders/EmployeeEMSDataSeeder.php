<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

/**
 * Seeds sample employee records for fresh installs / demos.
 *
 * IMPORTANT: Never commit real employee PII (names, CNIC, phone numbers,
 * addresses) into this file. Use placeholder data only. Real employees
 * should be added through the EMS management UI.
 */
class EmployeeEMSDataSeeder extends Seeder
{
    public function run(): void
    {
        // Only seed sample employees on a fresh install (empty table).
        // Real employees are managed via the EMS UI / User Management.
        if (Employee::count() > 0) {
            $this->command->info('Employees table already has data — skipping EMS sample seed.');
            return;
        }

        $data = [
            [
                'name' => 'Sample Admin Manager',
                'email' => 'admin.manager@example.com',
                'contact_info' => '3000000001',
                'emergency_contact' => '3000000002',
                'cnic' => '00000-0000000-0',
                'position' => 'Admin Manager',
                'area_of_residence' => 'City A',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Sample HR Manager',
                'email' => 'hr.manager@example.com',
                'contact_info' => '3000000003',
                'emergency_contact' => '3000000004',
                'cnic' => '00000-0000000-1',
                'position' => 'HR Manager',
                'area_of_residence' => 'City B',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Sample Media Manager',
                'email' => 'media.manager@example.com',
                'contact_info' => '3000000005',
                'emergency_contact' => '3000000006',
                'cnic' => '00000-0000000-2',
                'position' => 'Media Manager',
                'area_of_residence' => 'City C',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Sample QA Manager',
                'email' => 'qa.manager@example.com',
                'contact_info' => '3000000007',
                'emergency_contact' => '3000000008',
                'cnic' => '00000-0000000-3',
                'position' => 'QA Manager',
                'area_of_residence' => 'City D',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Sample Closer 1',
                'email' => 'closer1@example.com',
                'contact_info' => '3000000009',
                'emergency_contact' => '3000000010',
                'cnic' => '00000-0000000-4',
                'position' => 'Closer',
                'area_of_residence' => 'City E',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Sample Closer 2',
                'email' => 'closer2@example.com',
                'contact_info' => '3000000011',
                'emergency_contact' => '3000000012',
                'cnic' => '00000-0000000-5',
                'position' => 'Closer',
                'area_of_residence' => 'City F',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Sample Closer 3',
                'email' => 'closer3@example.com',
                'contact_info' => '3000000013',
                'emergency_contact' => '3000000014',
                'cnic' => '00000-0000000-6',
                'position' => 'Closer',
                'area_of_residence' => 'City G',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Sample Closer 4',
                'email' => 'closer4@example.com',
                'contact_info' => '3000000015',
                'emergency_contact' => '3000000016',
                'cnic' => '00000-0000000-7',
                'position' => 'Closer',
                'area_of_residence' => 'City H',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Sample Closer 5',
                'email' => 'closer5@example.com',
                'contact_info' => '3000000017',
                'emergency_contact' => '3000000018',
                'cnic' => '00000-0000000-8',
                'position' => 'Closer',
                'area_of_residence' => 'City I',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Sample Office Support',
                'email' => 'support@example.com',
                'contact_info' => '3000000019',
                'emergency_contact' => '3000000020',
                'cnic' => '00000-0000000-9',
                'position' => 'Office Boy',
                'area_of_residence' => 'City J',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
        ];
        foreach ($data as $row) {
            Employee::updateOrCreate(
                ['email' => $row['email']],
                $row
            );
        }
    }
}