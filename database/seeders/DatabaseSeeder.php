<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seeding default Admin / Biro Akademik
        User::factory()->create([
            'name' => 'Biro Akademik',
            'username' => 'admin',
            'email' => 'admin@royal.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Seeding Muchtarinal Choiri (NIM: 23220459)
        $userMuchtarinal = User::factory()->create([
            'name' => 'Muchtarinal Choiri',
            'username' => '23220459',
            'email' => 'muchtarinal.choiri@royal.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);

        Student::create([
            'user_id' => $userMuchtarinal->id,
            'nim' => '23220459',
            'major' => 'Sistem Informasi',
            'faculty' => 'Fakultas Ilmu Komputer',
            'admission_year' => 2023,
            'gpa' => 3.80,
            'status' => 'Lulus',
            'certificate_status' => 'Belum Cetak',
            'certificate_number' => '102/UNROY/SI/S1/2027',
            'graduation_date' => '2027-08-25',
            'degree' => 'S.Kom.',
        ]);

        // 3. Seeding Ahmad Wijaya (NIM: 23220460)
        $userAhmad = User::factory()->create([
            'name' => 'Ahmad Wijaya',
            'username' => '23220460',
            'email' => 'ahmad.wijaya@royal.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);

        Student::create([
            'user_id' => $userAhmad->id,
            'nim' => '23220460',
            'major' => 'Sistem Informasi',
            'faculty' => 'Fakultas Ilmu Komputer',
            'admission_year' => 2023,
            'gpa' => 3.65,
            'status' => 'Lulus',
            'certificate_status' => 'Antrean Cetak',
            'certificate_number' => '103/UNROY/SI/S1/2027',
            'graduation_date' => '2027-08-25',
            'degree' => 'S.Kom.',
        ]);

        // 4. Seeding Cia Kartika (NIM: 23220461)
        $userCia = User::factory()->create([
            'name' => 'Cia Kartika',
            'username' => '23220461',
            'email' => 'rina.kartika@royal.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);

        Student::create([
            'user_id' => $userCia->id,
            'nim' => '23220461',
            'major' => 'Sistem Informasi',
            'faculty' => 'Fakultas Ilmu Komputer',
            'admission_year' => 2023,
            'gpa' => 3.92,
            'status' => 'Lulus',
            'certificate_status' => 'Sudah Cetak',
            'certificate_number' => '104/UNROY/SI/S1/2027',
            'graduation_date' => '2027-08-25',
            'degree' => 'S.Kom.',
        ]);

        // 5. Seeding Putri Manurung (NIM: 23220462)
        $userPutri = User::factory()->create([
            'name' => 'Putri Manurung',
            'username' => '23220462',
            'email' => 'putri.manurung@royal.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);

        Student::create([
            'user_id' => $userPutri->id,
            'nim' => '23220462',
            'major' => 'Sistem Informasi',
            'faculty' => 'Fakultas Ilmu Komputer',
            'admission_year' => 2023,
            'gpa' => 3.95,
            'status' => 'Aktif',
            'certificate_status' => 'Belum Cetak',
            'certificate_number' => '110/UNROY/SI/S1/2028',
            'graduation_date' => '2028-08-21',
            'degree' => 'S.Kom.',
        ]);

        // 6. Seeding Aura Maulana Lubis (NIM: 23220569)
        $userAura = User::factory()->create([
            'name' => 'Aura Maulana Lubis',
            'username' => '23220569',
            'email' => 'auramaulana@royal.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);

        Student::create([
            'user_id' => $userAura->id,
            'nim' => '23220569',
            'major' => 'Sistem Informasi',
            'faculty' => 'Fakultas Ilmu Komputer',
            'admission_year' => 2023,
            'gpa' => 4.00,
            'status' => 'Aktif',
            'certificate_status' => 'Antrean Cetak',
            'certificate_number' => '107/UNROY/SI/S1/2028',
            'graduation_date' => '2028-09-14',
            'degree' => 'S.Kom.',
        ]);
    }
}
