<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('1'),
            'role' => 'admin'
        ]);

        Patient::factory(5)->create();
        $doctors = Doctor::factory(5)->create();
        // jadwal
        foreach ($doctors as $doctor) {
            $days = [
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat',
                'Sabtu',
                'Minggu'
            ];
            foreach ($days as $day) {
                $make = (bool)mt_rand(0, 1);
                if ($make) {
                    DoctorSchedule::factory()->create([
                        'doctor_id' => $doctor->id,
                        'day' => $day,
                    ]);
                }
            }
        }
        Appointment::factory(10)->create();
        MedicalRecord::factory(10)->create();
    }
}
