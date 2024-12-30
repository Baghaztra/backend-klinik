<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the doctors.
     */
    public function index()
    {
        $doctors = Doctor::with('user')->get()->map(function ($doctor) {
            return [
                'id' => $doctor->id,
                'name' => $doctor->user->name,
                'specialization' => $doctor->specialization,
                'schedule' => $doctor->schedule,
                'appointments' => $doctor->appointments->count(),
                'medical_records' => $doctor->medicalRecords->count(),
            ];
        });
        return response()->json($doctors, 200);
    }

    /**
     * Store a newly created doctor in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id|unique:doctors,user_id',
            'specialization' => 'required|string|max:255',
            'schedule' => 'required|string|max:255',
        ]);

        $doctor = Doctor::create($validatedData);
        return response()->json(['message' => 'Doctor created successfully', 'data' => $doctor], 201);
    }

    /**
     * Display the specified doctor.
     */
    public function show($id)
    {
        $doctor = Doctor::with('user')->find($id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        return response()->json([
            'id' => $doctor->id,
            'name' => $doctor->user->name,
            'specialization' => $doctor->specialization,
            'schedule' => $doctor->schedule,
            'appointments' => $doctor->appointments,
            'medical_records' => $doctor->medicalRecords,
        ], 200);
    }

    /**
     * Update the specified doctor in storage.
     */
    public function update(Request $request, $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $validatedData = $request->validate([
            'user_id' => 'exists:users,id|unique:doctors,user_id,' . $id,
            'specialization' => 'string|max:255',
            'schedule' => 'string|max:255',
        ]);

        $doctor->update($validatedData);
        return response()->json(['message' => 'Doctor updated successfully', 'data' => $doctor], 200);
    }

    /**
     * Remove the specified doctor from storage.
     */
    public function destroy($id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $doctor->delete();
        return response()->json(['message' => 'Doctor deleted successfully'], 200);
    }
}
