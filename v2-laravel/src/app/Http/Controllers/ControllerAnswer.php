<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ControllerAnswer extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        $query = Answer::query();

        if ($auth['role'] !== 'A') {
            $query->whereHas('formular', function ($q) use ($auth) {
                $q->where('user_id', $auth['id']);
            });
        }

        if ($request->filled('form_id')) {
            $query->where('form_id', (int)$request->input('form_id'));
        }

        return response()->json($query->get());
    }

    public function show(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');
        $answer = Answer::with('formular')->findOrFail((int)$id);

        if ($auth['role'] !== 'A' && (int)$answer->formular->user_id !== (int)$auth['id']) {
            return response()->json(['error' => 'Zugriff verweigert'], 403);
        }

        return response()->json($answer);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(Answer::validationRules());

        $answer = Answer::create($validated);

        return response()->json($answer, 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $answer = Answer::findOrFail((int)$id);

        $validated = $request->validate(Answer::updateRules());

        $answer->update($validated);

        return response()->json($answer);
    }

    public function delete($id): JsonResponse
    {
        $answer = Answer::findOrFail((int)$id);
        $answer->delete();

        return response()->json([
            'success' => true,
            'message' => "Antwort {$id} gelöscht."
        ]);
    }
}