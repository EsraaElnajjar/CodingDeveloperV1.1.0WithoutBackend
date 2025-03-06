<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    public function index(Request $request)
    // {
    // $images = Image::all();
    // return response()->json($images, 200);
    // }
    {

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search', ''); 
        
        // $query = Image::query();
        $query = Image::with('user:id,name');
        
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
        $message = [
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'يجب أن يكون الاسم نصًا.',
            'name.max' => 'يجب ألا يزيد الاسم عن 255 حرفًا.',
            'image.required' => 'الصورة مطلوبة.',
            'image.image' => 'الملف يجب أن يكون صورة.',
            'image.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png, jpg, gif.',
            'image.max' => 'يجب ألا يزيد حجم الصورة عن 2 ميجابايت.',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'required',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_add_id' => 'nullable',
        ],  $message);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        };


        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $imageFile) {
                $imageName = time() . '_' . $imageFile->getClientOriginalName();
                $imageFile->move(public_path('images'), $imageName);
                $imagePath = env('APP_URL') . '/public/images/' . $imageName;


                Image::create([
                    'name' => $request->name,
                    'image' => $imagePath,
                    'user_add_id' => $request->user_add_id,
                ]);
            }
        }

        return response()->json(['message' => 'تم رفع الصور بنجاح'], 200);
    }

    public function show($id)
    {
        $image = Image::find($id);
        return response()->json($image);
    }

    public function update(Request $request, $id)
    {
        $message = [
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'يجب أن يكون الاسم نصًا.',
            'name.max' => 'يجب ألا يزيد الاسم عن 255 حرفًا.',
            'image.image' => 'الملف يجب أن يكون صورة.',
            'image.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png, jpg, gif.',
            'image.max' => 'يجب ألا يزيد حجم الصورة عن 2 ميجابايت.',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],  $message);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        };

        $image = Image::findOrFail($id);

        $data = [
            'name' => $request->name,
        ];

        if ($request->hasFile('image')) {

            $oldImagePath = public_path(str_replace(env('APP_URL') . '/public/', '', $image->image));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $imageName);
            $data['image'] = env('APP_URL') . '/public/images/' . $imageName;
        }

        $image->update($data);

        return response()->json(['message' => 'تم تحديث الصورة بنجاح', 'image' => $image], 200);
    }

    public function destroy($id)
    {
        $image = Image::find($id);
        if (!$image) {
            return response()->json(['error' => 'الصورة غير موجودة'], 404);
        }

        $imagePath = public_path(str_replace(env('APP_URL') . '/public/', '', $image->image));
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $image->delete();
        return response()->json(['message' => 'تم حذف الصورة بنجاح'], 200);
    }
}
