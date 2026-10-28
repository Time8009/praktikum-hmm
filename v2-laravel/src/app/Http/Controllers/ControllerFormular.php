<?php

namespace App\Http\Controllers;

use App\Models\Formular;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ControllerFormular extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        $query = Formular::query();

        // Wenn all=true gesetzt ist, lade immer alle Formulare mit allen Relationen
        if ($request->boolean('all')) {
            return response()->json(
                $query->with([
                    'user:id,first_name,last_name,birthdate,email,adress,zip_code,city',
                    'subject:id,name,note',
                    'answers:answer_id,form_id,message,created_at,updated_at'
                ])->get()
            );
        }

        // Standard (/formular): U sieht nur eigene, A sieht alle
        if ($auth['role'] !== 'A') {
            $query->where('user_id', $auth['id']);
        }

        return response()->json($query->get());
    }

    public function show(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        $query = Formular::query();

        // Wenn all=true gesetzt ist, zeige das Formular mit Relationen ohne User-Sperre
        if ($request->boolean('all')) {
            $entry = $query->with([
                'user:id,first_name,last_name,birthdate,email,adress,zip_code,city',
                'subject:id,name,note',
                'answers:answer_id,form_id,message,created_at,updated_at'
            ])->findOrFail((int)$id);

            return response()->json($entry);
        }

        // Standard (/formular/{id}): U darf nur eigenes Formular sehen
        $entry = $query->findOrFail((int)$id);

        if ($auth['role'] !== 'A' && (int)$entry->user_id !== (int)$auth['id']) {
            return response()->json(['error' => 'Zugriff verweigert'], 403);
        }

        return response()->json($entry);
    }

    public function store(Request $request): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        $validated = $request->validate([
            'subject_id' => 'required|integer|exists:tbl_subject,id',
            'message'    => 'nullable|string',
            'user_id'    => 'sometimes|integer|exists:tbl_user,id',
        ]);

        $userId = ($auth['role'] === 'A' && !empty($validated['user_id']))
            ? $validated['user_id']
            : $auth['id'];

        $entry = Formular::create([
            'user_id'    => $userId,
            'subject_id' => $validated['subject_id'],
            'message'    => $validated['message'] ?? '',
        ]);

        return response()->json($entry, 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');
        $entry = Formular::findOrFail((int)$id);

        if ($auth['role'] !== 'A' && (int)$entry->user_id !== (int)$auth['id']) {
            return response()->json(['error' => 'Zugriff verweigert'], 403);
        }

        $validated = $request->validate([
            'subject_id' => 'sometimes|integer|exists:tbl_subject,id',
            'message'    => 'nullable|string',
            'user_id'    => 'sometimes|integer|exists:tbl_user,id',
        ]);

        if ($auth['role'] !== 'A') {
            unset($validated['user_id']);
        }

        $entry->update($validated);

        return response()->json($entry);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');
        $entry = Formular::findOrFail((int)$id);

        if ($auth['role'] !== 'A' && (int)$entry->user_id !== (int)$auth['id']) {
            return response()->json(['error' => 'Zugriff verweigert'], 403);
        }

        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => "Formular {$id} gelöscht."
        ]);
    }
}