<?php

namespace App\Http\Controllers;

use App\Models\Formular;
use Illuminate\Http\Request;

class Controller
{
    public function create()
    {
        return view('formular.formular');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|min:2',
            
            'lastName'  => 'required|min:2',
            'birthdate' => 'nullable|date|before:-12 years',
            'email'     => 'required|email',
            'address'   => 'nullable|string',
            'zipCode'   => 'nullable|regex:/^[0-9]{5}$/',
            'city'      => 'nullable|min:3',
            'subject'   => 'required|string',
            'message'   => 'nullable|string',
        ]);

        Formular::create([
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

        return redirect('/formular_viewer.formular_viewer')->with('success', 'Eintrag erfolgreich gespeichert!');
    }

    public function index()
    {
        $entries = Formular::all();
        return view('formular_viewer.formular_viewer', compact('entries'));
    }

    public function edit($id)
    {
        $entry = Formular::findOrFail($id);
        return view('formular_viewer.formular_update', compact('entry'));
    }

    public function update(Request $request, $id)
    {
        if ($request->input('admin_password') !== 'php!?*') {
            return back()->with('error', 'Falsches Passwort!');
        }

        $entry = Formular::findOrFail($id);

        $validated = $request->validate([
            'firstName' => 'required|min:2',
            'lastName'  => 'required|min:2',
            'birthdate' => 'nullable|date|before_or_equal:today',
            'email'     => 'required|email',
            'address'   => 'nullable|string',
            'zipCode'   => 'nullable|regex:/^[0-9]{5}$/',
            'city'      => 'nullable|min:3',
            'subject'   => 'required|string',
            'message'   => 'nullable|string',
        ]);

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

        return redirect('/formular_viewer')->with('success', 'Eintrag erfolgreich aktualisiert!');
    }

    public function destroy(Request $request)
    {
        if ($request->input('admin_password') !== 'php!?*') {
            return redirect('/formular_viewer')->with('error', 'Falsches Passwort!');
        }

        $id = $request->input('selected');
        if ($id) {
            Formular::destroy($id);
        }

        return redirect('/formular_viewer')->with('success', 'Eintrag gelöscht!');
    }

    public function truncate(Request $request)
    {
        if ($request->input('admin_password') !== 'php!?*') {
            return redirect('/formular_viewer.formular_viewer')->with('error', 'Falsches Passwort!');
        }

        Formular::truncate();

        return redirect('/formular_viewer.formular_viewer')->with('success', 'Tabelle zurückgesetzt!');
    }
}