<?php

namespace App\Http\Controllers;

use App\Models\Formular;
use Illuminate\Http\Request;

class Controller_rest
{
    public function find($id)
    {
        if ($id === 'all') {
            return response()->json(Formular::all());
        }

        return response()->json(Formular::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validateFormular();

        $entry = Formular::create([
            'first_name' => $validated['firstName'],
            'last_name'  => $validated['lastName'],
            'birthdate'  => $validated['birthdate'] ?? null,
            'email'      => $validated['email'],
            'address'    => $validated['address'] ?? null,
            'zip_code'   => $validated['zipCode'] ?? null,
            'city'       => $validated['city'] ?? null,
            'subject'    => $validated['subject'],
            'message'    => $validated['message'] ?? null,
        ]);

        return response()->json($entry, 201);
    }

    public function update(Request $request)
    {
        $validated = $request->validateFormular();

        $id = $request->input('oid') ?? $request->input('id');

        $entry = Formular::findOrFail($id);

        $entry->update([
            'first_name' => $validated['firstName'],
            'last_name'  => $validated['lastName'],
            'birthdate'  => $validated['birthdate'] ?? null,
            'email'      => $validated['email'],
            'address'    => $validated['address'] ?? null,
            'zip_code'   => $validated['zipCode'] ?? null,
            'city'       => $validated['city'] ?? null,
            'subject'    => $validated['subject'],
            'message'    => $validated['message'] ?? null,
        ]);

        return response()->json($entry);
    }

    public function destroy(Request $request, $id)
    {
        if ($id === 't') {
            Formular::truncate();

            return response()->json([
                'success' => true,
                'message' => 'Tabelle erfolgreich geleert.'
            ]);
        }

        $entry = Formular::findOrFail($id);
        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => "Eintrag {$id} erfolgreich gelöscht."
        ]);
    }
}