<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeEMSDataSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Ateeq ur Rehman',
                'email' => 'ateqrahmaan77@gmail.com',
                'contact_info' => '3165574484',
                'emergency_contact' => '3035116526',
                'cnic' => '37405-5116244-7',
                'position' => 'Admin Manager',
                'area_of_residence' => 'Shamasabad, Rawalpindi',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Marhaba Nadeem',
                'email' => 'marhabanadeem49@gmail.com',
                'contact_info' => '3063409229',
                'emergency_contact' => '3144507142',
                'cnic' => '37405-8736421-4',
                'position' => 'HR Manager',
                'area_of_residence' => 'Kartarpura, Rawalpindi',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Khizra Nadeem',
                'email' => 'khizra0357@gmail.com',
                'contact_info' => '3358005734',
                'emergency_contact' => '3144507142',
                'cnic' => '37405-7560565-4',
                'position' => 'Media Manager',
                'area_of_residence' => 'Kartarpura, Rawalpindi',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Bareera Nadeem',
                'email' => 'rajputbareera295@gmail.com',
                'contact_info' => '3369479169',
                'emergency_contact' => '3144507142',
                'cnic' => '37405-6955675-6',
                'position' => 'QA Manager',
                'area_of_residence' => 'Kartarpura, Rawalpindi',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Haris Waqar',
                'email' => 'davidhariss37@gmail.com',
                'contact_info' => '3465145406',
                'emergency_contact' => '3458540850',
                'cnic' => '37405-4841630-1',
                'position' => 'Closer',
                'area_of_residence' => 'Committee Chowk, Hostel',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Adeel Bashir Malik',
                'email' => 'adeelm100088@gmail.com',
                'contact_info' => '3184537179',
                'emergency_contact' => '3355047581',
                'cnic' => '37405-3886487-9',
                'position' => 'Closer',
                'area_of_residence' => 'Faisal Colony Tench Bhatta',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Farzand Ali',
                'email' => 'farzandalimirza2@gmail.com',
                'contact_info' => '3145250100',
                'emergency_contact' => '3065044504',
                'cnic' => '61101-7761708-1',
                'position' => 'Closer',
                'area_of_residence' => 'Mughal Sihala, Islamabad',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Abdullah Ayub',
                'email' => 'ayubabdullah536@gmail.com',
                'contact_info' => '3155153826',
                'emergency_contact' => '3335153826',
                'cnic' => '61101-1946188-9',
                'position' => 'Closer',
                'area_of_residence' => 'Pims Colony G-8/3 Islamabad',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Syeda Sidra Batool',
                'email' => 'sarahgarcea9@gmail.com',
                'contact_info' => '3035929753',
                'emergency_contact' => '3035929753',
                'cnic' => '37405-9992889-6',
                'position' => 'Closer',
                'area_of_residence' => 'Satellite Town, Rawalpindi',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            [
                'name' => 'Chaudhary Waseem',
                'email' => 'waseem@taurus.com',
                'contact_info' => '3225016367',
                'emergency_contact' => '3402803375',
                'cnic' => '61101-3559538-5',
                'position' => 'Office Boy',
                'area_of_residence' => 'Zia Masjid, Islamabad',
                'status' => 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
            ],
            // ... (Add all other rows in the same format) ...
        ];
        foreach ($data as $row) {
            Employee::updateOrCreate(
                ['email' => $row['email']],
                $row
            );
        }
    }
}