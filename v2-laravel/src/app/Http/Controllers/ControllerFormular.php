<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Formular;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ControllerFormular extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        $query = Formular::query();

        if ($request->boolean('all')) {
            return response()->json(
                $query->with([
                    'user:id,first_name,last_name,birthdate,email,adress,zip_code,city',
                    'subject:id,name,note',
                    'answers:answer_id,form_id,message,created_at,updated_at'
                ])->get()
            );
        }

        if ($auth['role'] !== 'A') {
            $query->where('user_id', $auth['id']);
        }

        return response()->json($query->get());
    }

    public function show(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        $query = Formular::query();

        if ($request->boolean('all')) {
            $entry = $query->with([
                'user:id,first_name,last_name,birthdate,email,adress,zip_code,city',
                'subject:id,name,note',
                'answers:answer_id,form_id,message,created_at,updated_at'
            ])->findOrFail((int)$id);

            return response()->json($entry);
        }

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
            'message'    => 'required|string',
            'file'       => 'nullable|file|max:20480',
        ]);

        $formular = Formular::create([
            'user_id'    => $auth['id'],
            'subject_id' => $validated['subject_id'],
            'message'    => $validated['message'],
        ]);

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $uploadedFile = $request->file('file');

            $storedPath = Storage::disk('s3')->putFile('formular-attachments', $uploadedFile);

            if (!$storedPath) {
                return response()->json(['error' => 'Fehler beim Hochladen auf den S3-Speicher'], 500);
            }

            File::create([
                'form_id'       => $formular->form_id,
                'response_id'   => null,
                'user_id'       => $auth['id'],
                'name'          => $uploadedFile->getClientOriginalName(),
                'path'          => $storedPath,
                'disk'          => 's3',
                'mime_type'     => $uploadedFile->getClientMimeType(),
                'size_in_bytes' => $uploadedFile->getSize(),
            ]);
        }

        return response()->json($formular->load('files'), 201);
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

    public function delete(Request $request, $id): JsonResponse
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