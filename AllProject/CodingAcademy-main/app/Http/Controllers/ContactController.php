<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
  public function index(Request $request)
  {
    $traning = Contact::all();
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
      'phone.required' => 'رقم الهاتف مطلوب.',
      'phone.numeric' => 'يجب أن يحتوي رقم الهاتف على أرقام فقط.',
      'phone.digits_between' => 'يجب أن يكون رقم الهاتف بين 8 و15 رقمًا.',
      'description.required' => 'الوصف مطلوب.',
      'description.string' => 'يجب أن يكون الوصف نصًا.',
    ];

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'phone' => 'required|numeric|digits_between:8,15',
      'description' => 'required|string',
    ], $messages);

    if ($validator->fails()) {
      return response()->json([
        'error' => $validator->errors()->first(),
      ], 422);
    };


    $user = Contact::create([
      'name' => $request->name,
      'email' => $request->email,
      'phone' => $request->phone,
      'description' => $request->description,
    ]);
    return response()->json($user);
  }

  public function show($id)
  {
    $user = Contact::find($id);
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
      'phone.required' => 'رقم الهاتف مطلوب.',
      'phone.numeric' => 'يجب أن يحتوي رقم الهاتف على أرقام فقط.',
      'phone.digits_between' => 'يجب أن يكون رقم الهاتف بين 8 و15 رقمًا.',
      'description.required' => 'الوصف مطلوب.',
      'description.string' => 'يجب أن يكون الوصف نصًا.',
    ];

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'phone' => 'required|numeric|digits_between:8,15',
      'description' => 'required|string',
    ], $messages);

    if ($validator->fails()) {
      return response()->json([
        'error' => $validator->errors()->first(),
      ], 422);
    };

    $user = Contact::findOrFail($id);


    $date = [
      'name' => $request->name,
      'email' => $request->email,
      'phone' => $request->phone,
      'description' => $request->description,
    ];

    $user->update($date);


    return response()->json($user);
  }

  public function destroy($id)
  {
    $user = Contact::findOrFail($id);
    $user->delete();
    return response()->json([
      'succec' => 'تم حذف المستخدم بنجاح',
    ]);
  }
}
