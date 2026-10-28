<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ControllerSubject extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Subject::all());
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(Subject::with('formulars')->findOrFail($id));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:tbl_subject,name|max:100',
            'note' => 'nullable|string',
        ]);

        $subject = Subject::create($validated);

        return response()->json($subject, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'name' => "sometimes|string|unique:tbl_subject,name,{$id},id|max:100",
            'note' => 'nullable|string',
        ]);

        $subject->update($validated);

        return response()->json($subject);
    }

    public function delete(int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => "Betreff {$id} gelöscht."
        ]);
    }
}