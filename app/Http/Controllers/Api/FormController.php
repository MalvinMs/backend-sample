<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $forms = Form::paginate(10);
    return response()->json([
      'status' => 'success',
      'message' => 'Forms retrieved successfully',
      'data' => $forms
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'json_schema' => 'required|json',
    ]);

    $form = Form::create($validated);

    return response()->json([
      'status' => 'success',
      'message' => 'Form created successfully',
      'data' => $form
    ], 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $form = Form::find($id);
    if (!$form) {
      return response()->json([
        'status' => 'error',
        'message' => 'Form not found'
      ], 404);
    }

    return response()->json([
      'status' => 'success',
      'message' => 'Form retrieved successfully',
      'data' => $form
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $form = Form::find($id);
    if (!$form) {
      return response()->json([
        'status' => 'error',
        'message' => 'Form not found'
      ], 404);
    }

    $validated = $request->validate([
      'name' => 'sometimes|required|string|max:255',
      'json_schema' => 'sometimes|required|json',
    ]);

    $form->update($validated);

    return response()->json([
      'status' => 'success',
      'message' => 'Form updated successfully',
      'data' => $form
    ]);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    $form = Form::find($id);
    if (!$form) {
      return response()->json([
        'status' => 'error',
        'message' => 'Form not found'
      ], 404);
    }

    $form->delete();

    return response()->json([
      'status' => 'success',
      'message' => 'Form deleted successfully'
    ]);
  }
}
