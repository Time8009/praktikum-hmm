<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\File;
use App\Models\Formular;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ControllerAnswer extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');
        $query = Answer::with(['user', 'files']);

        if ($request->filled('form_id')) {
            $formId = (int)$request->input('form_id');
            $formular = Formular::find($formId);

            if (!$formular) {
                return response()->json(['error' => 'Formular nicht gefunden'], 404);
            }

            if ($auth['role'] !== 'A' && (int)$formular->user_id !== (int)$auth['id']) {
                return response()->json(['error' => 'Zugriff verweigert'], 403);
            }

            $query->where('form_id', $formId);
        } else {
            if ($auth['role'] !== 'A') {
                $query->where('user_id', (int)$auth['id']);
            }
        }

        return response()->json($query->get());
    }

    public function show(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');
        $answer = Answer::with(['user', 'formular', 'files'])->find((int)$id);

        if (!$answer) {
            return response()->json(['error' => 'Antwort nicht gefunden'], 404);
        }

        if ($auth['role'] !== 'A') {
            $isOwnAnswer = (int)$answer->user_id === (int)$auth['id'];
            $isOwnTicket = $answer->formular && (int)$answer->formular->user_id === (int)$auth['id'];

            if (!$isOwnAnswer && !$isOwnTicket) {
                return response()->json(['error' => 'Zugriff verweigert'], 403);
            }
        }

        return response()->json($answer);
    }

    public function store(Request $request): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        $validated = $request->validate([
            'form_id' => 'required|integer|exists:tbl_formular,form_id',
            'message' => 'required|string',
            'file'    => 'nullable|file|max:20480',
        ]);

        $formular = Formular::findOrFail((int)$validated['form_id']);

        if ($auth['role'] !== 'A' && (int)$formular->user_id !== (int)$auth['id']) {
            return response()->json(['error' => 'Sie können nur auf eigene Formulare antworten'], 403);
        }

        $answer = Answer::create([
            'form_id' => $formular->form_id,
            'user_id' => $auth['id'],
            'message' => $validated['message'],
        ]);

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $uploadedFile = $request->file('file');
            $storedPath = Storage::disk('s3')->putFile('answer-attachments', $uploadedFile);

            if ($storedPath) {
                File::create([
                    'form_id'       => $formular->form_id,
                    'response_id'   => $answer->answer_id,
                    'user_id'       => $auth['id'],
                    'name'          => $uploadedFile->getClientOriginalName(),
                    'path'          => $storedPath,
                    'disk'          => 's3',
                    'mime_type'     => $uploadedFile->getClientMimeType(),
                    'size_in_bytes' => $uploadedFile->getSize(),
                ]);
            }
        }

        return response()->json($answer->load(['user', 'files']), 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');
        $answer = Answer::find((int)$id);

        if (!$answer) {
            return response()->json(['error' => 'Antwort nicht gefunden'], 404);
        }

        // Bearbeiten darf NUR der ursprüngliche Ersteller der Nachricht (auch Admins nicht fremde Antworten)
        if ((int)$answer->user_id !== (int)$auth['id']) {
            return response()->json(['error' => 'Zugriff verweigert. Sie können nur Ihre eigenen Antworten bearbeiten.'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $answer->update([
            'message' => $validated['message'],
        ]);

        return response()->json($answer->load(['user', 'files']));
    }

    public function delete(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');
        $answer = Answer::with('files')->find((int)$id);

        if (!$answer) {
            return response()->json(['error' => 'Antwort nicht gefunden'], 404);
        }

        if ($auth['role'] !== 'A' && (int)$answer->user_id !== (int)$auth['id']) {
            return response()->json(['error' => 'Zugriff verweigert. Sie können nur eigene Antworten löschen.'], 403);
        }

        foreach ($answer->files as $file) {
            $disk = Storage::disk($file->disk ?: 's3');
            if (!empty($file->path) && $disk->exists($file->path)) {
                $disk->delete($file->path);
            }
            $file->delete();
        }

        $answer->delete();

        return response()->json([
            'success' => true,
            'message' => "Antwort {$id} und zugehörige Dateien erfolgreich gelöscht."
        ]);
    }
}