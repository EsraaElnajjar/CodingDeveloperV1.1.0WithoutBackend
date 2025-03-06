<?php

namespace App\Http\Controllers;

use App\Models\JoinUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JoinUsController extends Controller
{
    public function index(Request $request)
    {
      $traning = JoinUs::all();
      return response()->json($traning, 200);
    }
  
    public function store(Request $request)
    {
  
      $messages = [
        'name.required' => 'الاسم مطلوب.',
        'name.string' => 'يجب أن يكون الاسم نصًا.',
        'name.max' => 'يجب ألا يزيد الاسم عن 255 حرفًا.',
        'email.required' => 'البريد الإلكتروني مطلوب.',
        'email.string' => 'يجب أن يكون البريد الإلكتروني نصًا.',
        'email.email' => 'يجب أن يكون البريد الإلكتروني بتنسيق صحيح.',
        'email.max' => 'يجب ألا يزيد البريد الإلكتروني عن 255 حرفًا.',
        'email.unique' => 'هذا البريد الإلكتروني مسجل مسبقًا.',
      ];
  
      $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
      ], $messages);
  
      if ($validator->fails()) {
        return response()->json([
          'error' => $validator->errors()->first(),
        ], 422);
      };
  
  
      $user = JoinUs::create([
        'name' => $request->name,
        'email' => $request->email,
      ]);
      return response()->json($user);
    }
  
    public function show($id)
    {
      $user = JoinUs::find($id);
      return response()->json($user);
    }
  
    public function update(Request $request, $id)
    {
      $messages = [
        'name.required' => 'الاسم مطلوب.',
        'name.string' => 'يجب أن يكون الاسم نصًا.',
        'name.max' => 'يجب ألا يزيد الاسم عن 255 حرفًا.',
        'email.required' => 'البريد الإلكتروني مطلوب.',
        'email.string' => 'يجب أن يكون البريد الإلكتروني نصًا.',
        'email.email' => 'يجب أن يكون البريد الإلكتروني بتنسيق صحيح.',
        'email.max' => 'يجب ألا يزيد البريد الإلكتروني عن 255 حرفًا.',
        'email.unique' => 'هذا البريد الإلكتروني مسجل مسبقًا.',
      ];
  
      $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
      ], $messages);
  
      if ($validator->fails()) {
        return response()->json([
          'error' => $validator->errors()->first(),
        ], 422);
      };
  
      $user = JoinUs::findOrFail($id);
  
  
      $date = [
        'name' => $request->name,
        'email' => $request->email,

      ];
  
      $user->update($date);
  
  
      return response()->json($user);
    }
  
    public function destroy($id)
    {
      $user = JoinUs::findOrFail($id);
      $user->delete();
      return response()->json([
        'succec' => 'تم حذف المستخدم بنجاح',
      ]);
    }
}
