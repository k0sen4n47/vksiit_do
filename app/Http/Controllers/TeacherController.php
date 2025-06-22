<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacher = Auth::user();
        $teacher = \App\Models\User::with('isCurator')->find($teacher->id);
        $curatorGroup = $teacher->isCurator;
        return view('teacher.dashboard', compact('teacher', 'curatorGroup'));
    }

    public function uploadPhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|max:2048',
            ]);
            $teacher = Auth::user();
            $user = \App\Models\User::find($teacher->id);
            $file = $request->file('photo');
            if (!$file) {
                return back()->with('error', 'Файл не был передан!')->withInput();
            }
            if (!$file->isValid()) {
                return back()->with('error', 'Ошибка загрузки файла!')->withInput();
            }
            if ($user->photo && $user->photo !== 'default-teacher.png' && file_exists(public_path('images/' . $user->photo))) {
                @unlink(public_path('images/' . $user->photo));
            }
            $filename = 'teacher_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $user->photo = $filename;
            $user->save();
            return redirect()->back()->with('success', 'Фото успешно загружено!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка загрузки: ' . $e->getMessage())->withInput();
        }
    }
} 