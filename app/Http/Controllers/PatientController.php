<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the patients.
     */
    public function index()
    {
        $patients = Patient::with('user')->get();
        return response()->json($patients, 200);
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id|unique:patients,user_id',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'phone_number' => 'required|string|max:15',
        ]);

        $patient = Patient::create($validatedData);
        return response()->json(['message' => 'Patient created successfully', 'data' => $patient], 201);
    }

    /**
     * Display the specified patient.
     */
    public function show($id)
    {
        $patient = Patient::with('user')->find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        return response()->json($patient, 200);
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        $validatedData = $request->validate([
            'user_id' => 'exists:users,id|unique:patients,user_id,' . $id,
            'birth_date' => 'date',
            'gender' => 'in:male,female',
            'phone_number' => 'string|max:15',
        ]);

        $patient->update($validatedData);
        return response()->json(['message' => 'Patient updated successfully', 'data' => $patient], 200);
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy($id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        $patient->delete();
        return response()->json(['message' => 'Patient deleted successfully'], 200);
    }
}
