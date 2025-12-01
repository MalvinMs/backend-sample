<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $data = FormSubmission::paginate(10);
    return response()->json([
      'status' => 'success',
      'message' => 'Form submissions retrieved successfully',
      'data' => $data
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $preValidated = $request->validate([
      'form_id' => 'required|exists:forms,id',
      'submission_data' => 'required|json',
    ]);
    $form = Form::findOrFail($preValidated['form_id']);
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
