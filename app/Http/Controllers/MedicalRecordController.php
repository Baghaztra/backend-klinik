<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the medical records.
     */
    public function index()
    {
        $user = auth('sanctum')->user();

        $medicalRecords = MedicalRecord::with(['patient', 'doctor'])
            ->when($user->role === 'patient', function ($query) use ($user) {
                return $query->where('patient_id', $user->patient->id);
            })
            ->when($user->role === 'doctor', function ($query) use ($user) {
                return $query->where('doctor_id', $user->doctor->id);
            })
            ->get()
            ->map(function ($medicalRecord) {
                return [
                    'id' => $medicalRecord->id,
                    'doctor' => $medicalRecord->doctor->user->name,
                    'specialization' => $medicalRecord->doctor->specialization,
                    'patient' => $medicalRecord->patient->user->name,
                    'diagnosis' => $medicalRecord->diagnosis,
                    'treatment' => $medicalRecord->treatment,
                    'date' => $medicalRecord->date,
                ];
            });

        return response()->json($medicalRecords, 200);
    }

    /**
     * Store a newly created medical record in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'date' => 'required|date',
        ]);

        $medicalRecord = MedicalRecord::create($validatedData);
        return response()->json(['message' => 'Medical record created successfully', 'data' => $medicalRecord], 201);
    }

    /**
     * Display the specified medical record.
     */
    public function show($id)
    {
        $medicalRecord = MedicalRecord::with(['patient', 'doctor'])->find($id);

        if (!$medicalRecord) {
            return response()->json(['message' => 'Medical record not found'], 404);
        }

        return response()->json($medicalRecord, 200);
    }

    /**
     * Update the specified medical record in storage.
     */
    public function update(Request $request, $id)
    {
        $medicalRecord = MedicalRecord::find($id);

        if (!$medicalRecord) {
            return response()->json(['message' => 'Medical record not found'], 404);
        }

        $validatedData = $request->validate([
            'patient_id' => 'exists:patients,id',
            'doctor_id' => 'exists:doctors,id',
            'diagnosis' => 'string',
            'treatment' => 'string',
            'date' => 'date',
        ]);

        $medicalRecord->update($validatedData);
        return response()->json(['message' => 'Medical record updated successfully', 'data' => $medicalRecord], 200);
    }

    /**
     * Remove the specified medical record from storage.
     */
    public function destroy($id)
    {
        $medicalRecord = MedicalRecord::find($id);

        if (!$medicalRecord) {
            return response()->json(['message' => 'Medical record not found'], 404);
        }

        $medicalRecord->delete();
        return response()->json(['message' => 'Medical record deleted successfully'], 200);
    }
}
