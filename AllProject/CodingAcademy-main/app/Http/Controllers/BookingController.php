<?php

namespace App\Http\Controllers;

use App\Models\booking;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
        // 🟢 جلب جميع الحجوزات الخاصة بالمستخدم الحالي
        public function index()
        {
            $bookings = Booking::all();
            return response()->json($bookings);
        }
    
        // 🟢 إنشاء حجز جديد
        public function store(Request $request)
        {
            if (!Auth::check()) {
                return response()->json(['error' => 'يجب تسجيل الدخول أولاً'], 401);
            }
        
            $messages = [
                'course_id.required' => 'يجب تحديد الكورس.',
                'course_id.exists' => 'الكورس غير موجود.',
                'phone.required' => 'رقم الهاتف مطلوب.',
                'phone.numeric' => 'يجب أن يحتوي رقم الهاتف على أرقام فقط.',
                'phone.digits_between' => 'يجب أن يكون رقم الهاتف بين 8 و15 رقمًا.',
                'email.required' => 'البريد الإلكتروني مطلوب.',
                'email.string' => 'يجب أن يكون البريد الإلكتروني نصًا.',
                'email.email' => 'يجب أن يكون البريد الإلكتروني بتنسيق صحيح.',
                'email.max' => 'يجب ألا يزيد البريد الإلكتروني عن 255 حرفًا.',
                'email.unique' => 'هذا البريد الإلكتروني مسجل مسبقًا.',
                'user_name.required' => 'الاسم مطلوب.',
                'user_name.string' => 'يجب أن يكون الاسم نصًا.',
                'user_name.max' => 'يجب ألا يزيد الاسم عن 255 حرفًا.',
                'image.required' => 'الصورة مطلوبة.',
                'image.image' => 'يجب أن تكون الصورة من نوع صورة.',
                'image.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png, jpg, gif.',
            ];
        
            $validatedData = $request->validate([
                'course_id' => 'required|exists:courses,id',
                'user_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'phone' => 'required|numeric|digits_between:8,15',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], $messages);
        
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->move(public_path('images'), $imageName);
                $imagePath = env('APP_URL') . '/images/' . $imageName;
            }
        
            $course = Course::findOrFail($validatedData['course_id']);
        
            $booking = Booking::create([
                'course_id' => $course->id,
                'user_name' => $request->user_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'image' => $imagePath,
                'user_id' => Auth::id(),
            ]);
        
            return response()->json([
                'message' => 'تم حجز الكورس بنجاح',
                'data' => $booking,
            ], 201);
        }
        
    
        // 🟢 جلب تفاصيل حجز معين
        public function show($id)
        {
            $booking = Booking::find($id);
            if (!$booking) {
                return response()->json(['error' => 'الحجز غير موجود'], 404);
            }
            return response()->json($booking);
        }
    
        // 🟢 تعديل الحجز
        public function update(Request $request, $id)
        {
            $booking = Booking::find($id);
        
            $validatedData = $request->validate([
                'user_name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:bookings,email,' . $id,
                'phone' => 'sometimes|numeric|digits_between:8,15',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->move(public_path('images'), $imageName);
                $validatedData['image'] = env('APP_URL') . '/images/' . $imageName;
            }
        
            $booking->update($validatedData);
        
            return response()->json([
                'message' => 'تم تحديث الحجز بنجاح',
                'data' => $booking,
            ]);
        }
    
        // 🟢 حذف الحجز
        public function destroy($id)
        {
            $booking = Booking::find($id);
            if (!$booking) {
                return response()->json(['error' => 'الحجز غير موجود'], 404);
            }
            $booking->delete();
            return response()->json(['message' => 'تم حذف الحجز بنجاح']);
        }
        
}
