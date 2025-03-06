<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
   
    public function register(Request $request)
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
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.string' => 'يجب أن تكون كلمة المرور نصًا.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 8 أحرف.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.numeric' => 'يجب أن يحتوي رقم الهاتف على أرقام فقط.',
            'phone.digits_between' => 'يجب أن يكون رقم الهاتف بين 8 و15 رقمًا.',
            'image.image' => 'يجب أن تكون الصورة من نوع صورة.',
            'image.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png, jpg, gif.',
            'image.max' => 'يجب ألا يزيد حجم الصورة عن 2 ميجابايت.',
        ];

        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|numeric|digits_between:8,15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],$messages);

        if ($validator->fails()) {
            return response()->json([
                'error'=> $validator->errors()->first(),
            ], 422);
        };

        // $type = $request->type ?? 0; 

        // $imagePath = url('public/images/def.png'); // رابط الصورة الافتراضية بالكامل

        $imagePath = "";
        // التحقق من وجود صورة مرفوعة
        if ($request->hasFile('image')) {
            // رفع الصورة وتخزينها في مجلد التخزين
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $imageName);
            $imagePath = env('APP_URL') . '/public/images/' . $imageName;
        }


        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
            'phone' => $request->phone,
            // 'type' => $type,
            'image' => $imagePath,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            // 'access_token' => $token,
            'success'=> 'تم التسجيل بنجاح',
            'token_type' => 'Bearer',
        ], 200);

    }


    public function login(Request $request)
    {
        $messages = [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.string' => 'يجب أن يكون البريد الإلكتروني نصًا.',
            'email.email' => 'يجب أن يكون البريد الإلكتروني بتنسيق صحيح.',
            'email.max' => 'يجب ألا يزيد البريد الإلكتروني عن 255 حرفًا.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.string' => 'يجب أن تكون كلمة المرور نصًا.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 8 أحرف.',
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'error'=> $validator->errors()->first(),
            ], 422);
        };

        $user = User::where('email', $request->email)->first();


        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Email or password is incorrect.'
            ], 401);
        }
        

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'id'=> $user->id,
            'role'=> $user->role,
        ], 200);


    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        
        return response()->json([
            'success'=> 'Logged out successfully',
        ]);
    }

}
