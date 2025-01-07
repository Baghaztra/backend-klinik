<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a history of the appointments by user id.
     */
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $search = $request->query('search');

        // Ambil semua janji temu dengan relasi yang diperlukan
        $appointments = Appointment::with(['patient', 'doctor.schedule'])
            ->when($user->role === 'patient', function ($query) use ($user) {
                return $query->where('patient_id', $user->patient->id);
            })
            ->when($user->role === 'doctor', function ($query) use ($user) {
                return $query->where('doctor_id', $user->doctor->id);
            })
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('doctor.user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })->orWhereHas('doctor', function ($query) use ($search) {
                    $query->where('specialization', 'like', "%{$search}%");
                })->orWhere('appointment_date', 'like', "%{$search}%"
                )->orWhere('status', 'like', "%{$search}%");
            })
            ->get()
            ->map(function ($appointment) {
                $day = Carbon::parse($appointment->appointment_date)->translatedFormat('l');
                $time = optional($appointment->doctor->schedule->firstWhere('day', $day))->jam;

                return [
                    'id' => $appointment->id,
                    'doctor' => $appointment->doctor->user->name,
                    'specialization' => $appointment->doctor->specialization,
                    'patient' => $appointment->patient->user->name,
                    'gender' => $appointment->patient->gender == "male" ? "Pria" : "Wanita",
                    'age' => Carbon::parse($appointment->patient->birth_date)->age,
                    'phone_number' => $appointment->patient->phone_number,
                    'complaints' => $appointment->complaints,
                    'appointment_date' => $appointment->appointment_date,
                    'time' => $time,
                    'status' => $appointment->status,
                ];
            });

        return response()->json($appointments, 200);
    }

    /**
     * Make a newly appointment in storage.
     */
    public function store(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if ($user->role == 'doctor') {
            return response()->json(['message' => 'Appointments must be create by patient'], 401);
        }

        $validatedData = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'complaints' => 'string',
            'appointment_date' => 'required|date',
            'status' => 'required|string',
        ]);
        $validatedData['patient_id'] = $user->patient->id;

        $appointment = Appointment::create($validatedData);
        return response()->json(['message' => 'Appointment created successfully', 'data' => $appointment], 200);
    }

    /**
     * Display the appointment that the status is .
     */
    public function appointmentLatest()
    {
        $user = auth('sanctum')->user();

        $appointments = Appointment::with(['patient', 'doctor'])
            ->when($user->role === 'patient', function ($query) use ($user) {
                return $query->where('patient_id', $user->patient->id);
            })
            ->when($user->role === 'doctor', function ($query) use ($user) {
                return $query->where('doctor_id', $user->doctor->id);
            })
            ->where('status', '!=', 'canceled')
            ->where('appointment_date', '>', Carbon::yesterday())
            ->orderBy('appointment_date', 'asc')
            ->get()
            ->map(function ($appointment) {
                $day = Carbon::parse($appointment->appointment_date)->translatedFormat('l');
                $time = optional($appointment->doctor->schedule->firstWhere('day', $day))->jam;

                return [
                    'id' => $appointment->id,
                    'doctor' => $appointment->doctor->user->name,
                    'specialization' => $appointment->doctor->specialization,
                    'patient' => $appointment->patient->user->name,
                    'gender' => $appointment->patient->gender == "male" ? "Pria" : "Wanita",
                    'age' => Carbon::parse($appointment->patient->birth_date)->age,
                    'phone_number' => $appointment->patient->phone_number,
                    'complaints' => $appointment->complaints,
                    'appointment_date' => $appointment->appointment_date,
                    'time' => $time,
                    'status' => $appointment->status,
                ];
            });

        return response()->json($appointments, 200);
    }
    
    /**
     * Display the specified appointment.
     */
    public function show($id)
    {
        $appointment = Appointment::with(['patient', 'doctor'])->find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }
        
        $day = Carbon::parse($appointment->appointment_date)->translatedFormat('l');
        $time = optional($appointment->doctor->schedule->firstWhere('day', $day))->jam;

        return response()->json([
            'id' => $appointment->id,
            'doctor' => $appointment->doctor->user->name,
            'doctor_id' => $appointment->doctor->id,
            'patient' => $appointment->patient->user->name,
            'patient_id' => $appointment->patient->id,
            'gender' => $appointment->patient->gender == "male" ? "Pria" : "Wanita",
            'age' => Carbon::parse($appointment->patient->birth_date)->age,
            'phone_number' => $appointment->patient->phone_number,
            'complaints' => $appointment->complaints,
            'appointment_date' => $appointment->appointment_date,
            'time' => $time,
            'status' => $appointment->status,
        ], 200);
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        $user = auth('sanctum')->user();

        if ($user->role == 'patient') {
            return response()->json(['message' => 'Appointments can\'t be edited by patient'], 401);
        }
        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }
        if ($appointment->doctor_id != $user->doctor->id) {
            return response()->json(['message' => 'Unauthorized to edit this appointment'], 403);
        }

        $validatedData = $request->validate([
            'status' => 'required|string',
        ]);

        $appointment->update($validatedData);

        return response()->json([
            'message' => 'Appointment updated successfully', 
        ], 200);
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy($id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $appointment->delete();
        return response()->json(['message' => 'Appointment deleted successfully'], 200);
    }
}
