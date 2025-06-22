<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('assignments/images', 'public');
            
            return response()->json([
                'location' => asset('storage/' . $path)
            ]);
        }
        
        return response()->json(['error' => 'No file uploaded'], 400);
    }
} 