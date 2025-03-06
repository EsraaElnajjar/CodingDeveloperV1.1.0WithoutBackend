<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Course;
use App\models\Teach;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class CourseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', default: 8);
        $page = $request->get('page', 1);
        $search = $request->get('search', ''); 
    
        $query = Course::with([
            'user:id,name',
            'lecturers:id,name'
        ]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('id', $search);
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

    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'يجب أن يكون الاسم نصًا.',
            'name.max' => 'يجب ألا يزيد الاسم عن 255 حرفًا.',
            'contant.required' => 'المحتوى مطلوب.',
            'description.required' => 'الوصف مطلوب.',
            'description.string' => 'يجب أن يكون الوصف نصًا.',
            'price.required' => 'السعر مطلوب.',
            'price.numeric' => 'يجب أن يكون السعر رقمًا.',
            'time.required' => 'مدة الدراسة مطلوبة.',
            'time.integer' => 'مدة الدراسة يجب أن تكون عددًا صحيحًا.',
            'time.min' => 'مدة الدراسة يجب أن تكون على الأقل شهرًا واحدًا.',
            'time.max' => 'مدة الدراسة لا يمكن أن تتجاوز 6 شهور .',
            'image.image' => 'يجب أن تكون الصورة من نوع صورة.',
            'image.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png, jpg, gif.',
            'image.max' => 'يجب ألا يزيد حجم الصورة عن 2 ميجابايت.',
            'lecturer_id.required' => 'المحاضر مطلوب.',
            'lecturer_id.exists' => 'المحاضر المحدد غير موجود.',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contant' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'time' => 'required|integer|min:1|max:6',
            'user_add_id' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'lecturer_id' => 'required|exists:lecturer,id',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        };


        // التحقق من وجود صورة مرفوعة
        if ($request->hasFile('image')) {
            // رفع الصورة وتخزينها في مجلد التخزين
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $imageName);
            $imagePath = env('APP_URL') . '/public/images/' . $imageName;
        } else {
            $imagePath = url('coding_academy/public/images/def.png'); // رابط الصورة الافتراضية بالكامل 
        }


        $course = Course::create([
            'name' => $request->name,
            'contant' => $request->contant,
            'description' => $request->description,
            'price' => $request->price,
            'time' => $request->time,
            'user_add_id' => $request->user_add_id,
            'image' => $imagePath,
        ]);

        // $course->lecturers()->attach($request->lecturer_id);

        Teach::create([
            'courses_id' => $course->id,
            'lecturer_id' => $request->lecturer_id,
        ]);

        return response()->json($course);
    }

    public function  show($id)
    {
        $course = Course::findOrFail($id);
        $lecturer = Teach::where('courses_id', $id)->first();

        return response()->json([
        'course' => $course,
        'lecturer_id' => $lecturer ? $lecturer->lecturer_id : null,
    ]);
        // return response()->json($course);
    }

    public function update(Request $request, $id)
    {
        $messages = [
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'يجب أن يكون الاسم نصًا.',
            'name.max' => 'يجب ألا يزيد الاسم عن 255 حرفًا.',
            'contant.required' => 'المحتوى مطلوب.',
            'description.required' => 'الوصف مطلوب.',
            'description.string' => 'يجب أن يكون الوصف نصًا.',
            'price.required' => 'السعر مطلوب.',
            'price.numeric' => 'يجب أن يكون السعر رقمًا.',
            'time.required' => 'مدة الدراسة مطلوبة.',
            'time.integer' => 'مدة الدراسة يجب أن تكون عددًا صحيحًا.',
            'time.min' => 'مدة الدراسة يجب أن تكون على الأقل شهرًا واحدًا.',
            'time.max' => 'مدة الدراسة لا يمكن أن تتجاوز 6 شهور .',
            'image.image' => 'يجب أن تكون الصورة من نوع صورة.',
            'image.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png, jpg, gif.',
            'image.max' => 'يجب ألا يزيد حجم الصورة عن 2 ميجابايت.',
            'lecturer_id.required' => 'المحاضر مطلوب.',
            'lecturer_id.exists' => 'المحاضر غير موجود.',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contant' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'time' => 'required|integer|min:1|max:6',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'lecturer_id' => 'nullable|exists:lecturer,id',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        };

        $course = Course::findOrFail($id);

        $data = [
            'name' => $request->name,
            'contant' => $request->contant,
            'description' => $request->description,
            'price' => $request->price,
            'time' => $request->time,
        ];

        // التحقق من وجود صورة مرفوعة
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $imageName);
            $data['image'] = env('APP_URL') . '/public/images/' . $imageName;
        }

        
        if ($request->filled('lecturer_id')) {
            $teach = Teach::where('courses_id', $course->id)->first();
            if ($teach) {
                $teach->update(['lecturer_id' => $request->lecturer_id]);
            }
        }
        $course->update($data);

        return response()->json($course);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return response()->json([
            'succec' => 'بنجاح [ ' . $course->name . ' ] تم حذف كورس ',
        ]);
    }
}
