<?php

namespace App\Http\Controllers;

use App\Models\Saw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SawController extends Controller
{
    public function index()
    {
        $saws = Saw::with('user')->paginate(10); 
        return response()->json($saws);
    }
    
    // public function index(Request $request)
    // {
        

    //     // $perPage = $request->get('per_page', 10);
    //     // $page = $request->get('page', 1); 
    //     // $search = $request->get('search', ''); 
        
    //     // // $query = Question::query();
    //     // $query = Saw::with('user:id,name');

    //     // if (!empty($search)) {
    //     //     $query->where(function ($q) use ($search) {
    //     //         $q->where('description', 'like', '%' . $search . '%')
    //     //           ->orWhere('id', $search);
    //     //     });
    //     // }

    //     // $total = $query->count(); 
    //     // $data = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
    
    //     // $totalPages = ceil($total / $perPage);
    
    //     // return response()->json([
    //     //     'data' => $data,
    //     //     'current_page' => $page,
    //     //     'per_page' => $perPage,
    //     //     'total' => $total,
    //     //     'total_pages' => $totalPages,
    //     // ]);
    // }

  
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        $saw = Saw::create([
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'تم إضافة الوصف بنجاح',
            'data' => $saw,
        ], 201);
    }

    public function show($id)
    {
        $saw = Saw::find($id);
        return response()->json($saw);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        $saw = Saw::findOrFail($id)->update([
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'تم تعديل الوصف بنجاح',
            'data' => $saw,
        ]);
    }

    public function destroy($id)
    {
        $saw = Saw::findOrFail($id);
        $saw->delete();
        return response()->json([
            'succec' => '  تم حذف بنجاح ',
        ]);
    }
}
