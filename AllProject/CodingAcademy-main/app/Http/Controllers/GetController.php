<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Get;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetController extends Controller
{

    public function index(Request $request)
    {

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');


        $query = Get::with('user:id,name,phone', 'course:id,name')
            ->join('users', 'users.id', '=', 'get.user_id')
            ->select('get.*');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', '%' . $search . '%')
                    ->orWhere('users.phone', 'like', '%' . $search . '%')
                    ->orWhere('get.id', $search);
            });
        }

        $total = $query->count();
        $data = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        $totalPages = ceil($total / $perPage);

        return response()->json([
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
        ]);
    }
    /**
     * وظيفة الاشتراك في كورس.
     */
    public function subscribe(Request $request)
    {

        $massage = [
            'user_id.required' => 'يجب تحديد المستخدم.',
            'user_id.exists' => 'المستخدم غير موجود.',
            'courses_id.required' => 'يجب تحديد الكورس.',
            'courses_id.exists' => 'الكورس غير موجود.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.numeric' => 'يجب أن يحتوي رقم الهاتف على أرقام فقط.',
            'phone.digits_between' => 'يجب أن يكون رقم الهاتف بين 8 و15 رقمًا.',
            'image.image' => 'يجب أن تكون الصورة من نوع صورة.',
            'image.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png, jpg, gif.',
        ];

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'courses_id' => 'required|exists:courses,id',
            'phone' => 'required|numeric|digits_between:8,15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $massage);


        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        };


        // التحقق من الاشتراك الحالي
        $existingSubscription = Get::where('user_id', $request->user_id)
            ->where('courses_id', $request->courses_id)
            ->first();

        if ($existingSubscription) {
            return response()->json(['message' => 'المستخدم مشترك بالفعل في هذا الكورس.'], 409);
        }

        $imagePath = url('coding_academy/public/images/def.png'); // رابط الصورة الافتراضية بالكامل

        // التحقق من وجود صورة مرفوعة
        if ($request->hasFile('image')) {
            // رفع الصورة وتخزينها في مجلد التخزين
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $imageName);
            $imagePath = env('APP_URL') . '/public/images/' . $imageName;
        }

        // إنشاء اشتراك جديد
        $subscription = Get::create([
            'user_id' => $request->user_id,
            'courses_id' => $request->courses_id,
            'phone' => $request->phone,
            'image' => $imagePath,
        ]);

        return response()->json(['message' => 'تم الاشتراك في الكورس بنجاح.', 'subscription' => $subscription], 201);
    }

    public function show($id)
    {
        $user = Get::find($id);
        return response()->json($user);

    }
    /**
     * وظيفة تحديث الاشتراك في كورس.
     */
    public function updateSubscription(Request $request , $id)
    {
        $messages = [
            'user_id.required' => 'يجب تحديد المستخدم.',
            'user_id.exists' => 'المستخدم غير موجود.',
            'courses_id.required' => 'يجب تحديد الكورس.',
            'courses_id.exists' => 'الكورس غير موجود.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.numeric' => 'يجب أن يحتوي رقم الهاتف على أرقام فقط.',
            'phone.digits_between' => 'يجب أن يكون رقم الهاتف بين 8 و15 رقمًا.',
            'image.image' => 'يجب أن تكون الصورة من نوع صورة.',
            'image.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png, jpg, gif.',
        ];

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'courses_id' => 'required|exists:courses,id',
            'phone' => 'required|numeric|digits_between:8,15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        };

        // // البحث عن الاشتراك الحالي
        // $subscription = Get::where('user_id', $request->user_id)
        //     ->where('courses_id', $request->courses_id)
        //     ->first();

        // if (!$subscription) {
        //     return response()->json(['message' => 'لم يتم العثور على اشتراك لهذا المستخدم في هذا الكورس.'], 404);
        // }
        $booked = Get::findOrFail($id);

        $data = [
            'user_id' => $request->user_id,
            'courses_id'=> $request->courses_id,
            'phone' => $request->phone,
        ];

        // تحديث الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $imageName);
            $data['image'] = env('APP_URL') . '/public/images/' . $imageName;
        }

        // تحديث باقي البيانات
        $booked->update($data);
       

        return response()->json(['message' => 'تم تحديث بيانات الاشتراك بنجاح.', 'subscription' => $booked], 200);
    }

    public function destroy($id)
    {
        $user = Get::findOrFail($id);
        $user->delete();
        return response()->json([
            'succec'=>'تم الغاء الاشتراك بنجاح',
        ]);
    }


       /**
     * وظيفة عرض قائمة المستخدمين  المشتركين فى هذا الكورس.
     */

     public function getCourseUsers($courses_id)
     {
         $course = Course::find($courses_id);
 
         if (!$course) {
             return response()->json(['message' => 'الكورس غير موجود.'], 404);
         }
 
         // استرجاع الكورسات التي اشترك فيها المستخدم
         $courses = $course->subscribers()->with('user')->get();
 
         return response()->json(['message' => 'قائمة المستخدمين المشتركين بهذا الكورس.', 'courses' => $courses]);
     }

         /**
     * وظيفة عرض قائمة الكورسات التي اشترك بها المستخدم.
     */
    public function getUserCourses($user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير موجود.'], 404);
        }

        // استرجاع الكورسات التي اشترك فيها المستخدم 
        $courses = $user->subscriptions()->with('course')->get();

        return response()->json(['message' => 'قائمة الكورسات المشترك بها المستخدم.', 'courses' => $courses]);
    }

        /**
     * وظيفة إلغاء الاشتراك من كورس.
     */
    public function unsubscribe(Request $request)
    {
        $massage = [
            'user_id.required' => 'يجب تحديد المستخدم.',
            'user_id.exists' => 'المستخدم غير موجود.',
            'courses_id.required' => 'يجب تحديد الكورس.',
            'courses_id.exists' => 'الكورس غير موجود.',
        ];

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'courses_id' => 'required|exists:courses,id',
        ], $massage);

        // البحث عن الاشتراك
        $subscription = Get::where('user_id', $request->user_id)
            ->where('courses_id', $request->courses_id)
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'المستخدم غير مشترك في هذا الكورس.'], 404);
        }

        // حذف الاشتراك
        $subscription->delete();

        return response()->json(['message' => 'تم إلغاء الاشتراك من الكورس بنجاح.']);
    }
}
