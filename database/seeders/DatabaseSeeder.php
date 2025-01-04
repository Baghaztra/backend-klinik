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
        // update
        $n = 0;
        foreach ($doctors as $doctor) {
            // profile
            $pfp = [
                'profiles/doctors/01JGQQGDVNBJWDD8G119CHFX8J.jpg',
                'profiles/doctors/01JGQQRRR5EZEKMZK565FH6N0F.jpg',
                'profiles/doctors/01JGQQSSQF2K30FFM14ZEP6BYR.jpg',
                'profiles/doctors/01JGQQV4VD4PV229T10J7QFE2C.jpg',
                'profiles/doctors/01JGQQVTT4S36NX7KXCH0F36R5.jpg',
            ];
            $doctor->update(['profile' => $pfp[$n % count($pfp)]]);
            $n++;
            // jadwal
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
