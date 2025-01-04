<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
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

        $appointments = Appointment::with(['patient', 'doctor'])
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
                return [
                    'id' => $appointment->id,
                    'doctor' => $appointment->doctor->user->name,
                    'specialization' => $appointment->doctor->specialization,
                    'patient' => $appointment->patient->user->name,
                    'appointment_date' => $appointment->appointment_date,
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
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $appointment = Appointment::create($validatedData);
        return response()->json(['message' => 'Appointment created successfully', 'data' => $appointment], 201);
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
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date', 'asc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'doctor' => $appointment->doctor->user->name,
                    'specialization' => $appointment->doctor->specialization,
                    'patient' => $appointment->patient->user->name,
                    'appointment_date' => $appointment->appointment_date,
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

        return response()->json($appointment, 200);
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $validatedData = $request->validate([
            'patient_id' => 'exists:patients,id',
            'doctor_id' => 'exists:doctors,id',
            'appointment_date' => 'date',
            'status' => 'string',
        ]);

        $appointment->update($validatedData);
        return response()->json(['message' => 'Appointment updated successfully', 'data' => $appointment], 200);
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
