<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\models\Lecturer;
use App\models\User;

class LecturerController extends Controller
{
    public function index(Request $request)
    {
        // $lecturers = Lecturer::all();
        // return response()->json([
        //     "Lecturers : "=> $lecturers,
        // ]);

        $perPage = $request->get('per_page', 10); 
        $page = $request->get('page', 1); 
        $search = $request->get('search', ''); 
        
        // $query = Lecturer::query();
        $query = Lecturer::with('user:id,name');

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
            'notes.required' => 'الوصف مطلوب.',
            'notes.string' => 'يجب أن يكون الوصف نصًا.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.numeric' => 'يجب أن يحتوي رقم الهاتف على أرقام فقط.',
            'phone.digits_between' => 'يجب أن يكون رقم الهاتف بين 10 و15 رقمًا.',
            'image.image' => 'يجب أن تكون الصورة من نوع صورة.',
            'image.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png, jpg, gif.',
            'image.max' => 'يجب ألا يزيد حجم الصورة عن 2 ميجابايت.',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'notes' => 'required|string',
            'phone' => 'required|numeric|digits_between:10,15',
            'user_add_id' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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


        $lecturer = Lecturer::create([
            'name' => $request->name,
            'notes' => $request->notes,
            'phone' => $request->phone,
            'user_add_id' => $request->user_add_id,
            'image' => $imagePath,
        ]);

        return response()->json($lecturer, 200);
    }

    public function show($id)
    {
        $lecturer = Lecturer::find($id);
        return response()->json($lecturer);
    }

    public function update(Request $request, $id)
    {
        $messages = [
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'يجب أن يكون الاسم نصًا.',
            'name.max' => 'يجب ألا يزيد الاسم عن 255 حرفًا.',
            'notes.required' => 'الوصف مطلوب.',
            'notes.string' => 'يجب أن يكون الوصف نصًا.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.numeric' => 'يجب أن يحتوي رقم الهاتف على أرقام فقط.',
            'phone.digits_between' => 'يجب أن يكون رقم الهاتف بين 8 و15 رقمًا.',
            'image.image' => 'يجب أن تكون الصورة من نوع صورة.',
            'image.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png, jpg, gif.',
            'image.max' => 'يجب ألا يزيد حجم الصورة عن 2 ميجابايت.',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'notes' => 'required|string',
            'phone' => 'required|numeric|digits_between:8,15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        };


        $lecturer = Lecturer::findOrFail($id);

        $data = [
            'name' => $request->name,
            'notes' => $request->notes,
            'phone' => $request->phone,
        ];

        // التحقق من وجود صورة مرفوعة
        if ($request->hasFile('image')) {
            // رفع الصورة وتخزينها في مجلد التخزين
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $imageName);
            $data['image'] = env('APP_URL') . '/public/images/' . $imageName;
        }

        $lecturer->update($data);

        return response()->json($lecturer);
    }

    public function destroy($id)
    {
        $lecturer = Lecturer::findOrFail($id);
        $lecturer->delete();
        return response()->json([
            'succec' => 'بنجاح [ ' . $lecturer->name . ' ] تم حذف المحاضر ',
        ]);
    }
}
