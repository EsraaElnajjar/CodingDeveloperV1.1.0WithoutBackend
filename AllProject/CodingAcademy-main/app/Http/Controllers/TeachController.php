<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Teach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeachController extends Controller
{
    // إضافة محاضر لكورس
    public function addLecturerToCourse(Request $request)
    {
        $massage = [
            'lecturer_id.required' => 'يجب تحديد المحاضر.',
            'lecturer_id.exists' => 'المحاضر غير موجود.',
            'courses_id.required' => 'يجب تحديد الكورس.',
            'courses_id.exists' => 'الكورس غير موجود.',
        ];
        $validator = Validator::make($request->all(),[
            'courses_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturer,id',
        ],$massage);

        if ($validator->fails()) {
            return response()->json([
                'error'=> $validator->errors()->first(),
            ], 422);
        };

        $exists = Teach::where('courses_id', $request->courses_id)
        ->where('lecturer_id', $request->lecturer_id)
        ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'هذا المحاضر مرتبط بالفعل بهذا الكورس.',
            ], 422);
        }

        $teach = Teach::create([
            'courses_id' => $request->courses_id,
            'lecturer_id' => $request->lecturer_id,
        ]);

        return response()->json(['message' => 'Lecturer added to course successfully!', 'data' => $teach]);
    }

    // تحديث محاضر الكورس
    public function updateLecturerOrCourse(Request $request, $teachId)
    {
        $massage = [
            'lecturer_id.required' => 'يجب تحديد المحاضر.',
            'lecturer_id.exists' => 'المحاضر غير موجود.',
            'courses_id.required' => 'يجب تحديد الكورس.',
            'courses_id.exists' => 'الكورس غير موجود.',
        ];

        $validator = Validator::make($request->all(),[
            'courses_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturer,id',
        ], $massage);

        if ($validator->fails()) {
            return response()->json([
                'error'=> $validator->errors()->first(),
            ], 422);
        };

        $exists = Teach::where('courses_id', $request->courses_id)
        ->where('lecturer_id', $request->lecturer_id)
        ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'هذا المحاضر مرتبط بالفعل بهذا الكورس.',
            ], 422);
        }

        $teach = Teach::findOrFail($teachId);
        $teach->update([
            'lecturer_id' => $request->lecturer_id,
            'courses_id' => $request->courses_id,
        ]);

        return response()->json(['message' => 'Lecturer updated successfully!', 'data' => $teach]);
    }

    // عرض المحاضرين لكورس معين
    public function getLecturersForCourse($courseId)
    {
        $course = Course::findOrFail( $courseId);
        $lecturers = $course->lecturers()->get();

        return response()->json(['course' => $course->name, 'lecturer' => $lecturers]);
    }

    // حذف محاضر من الكورس
    public function removeLecturerFromCourse($teachId)
    {
        $teach = Teach::findOrFail($teachId);
        $teach->delete();

        return response()->json(['message' => 'Lecturer removed from course successfully!']);
    }

    // حذف كورس مع محاضريه
    public function deleteCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        $course->delete();

        return response()->json(['message' => 'Course and its associated lecturers deleted successfully!']);
    }
}
