<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard()
    {
        // Получаем текущего авторизованного студента
        $student = Auth::user();
        $student = \App\Models\User::with(['group.curator'])->find($student->id);
        $group = $student->group ?? null;
        $curator = $group && $group->curator ? $group->curator : null;
        // Ближайшие задания
        $now = now();
        $upcomingAssignments = \App\Models\Assignment::where('group_id', $student->group_id)
            //->where('deadline', '>=', $now)
            ->orderBy('deadline')
            ->with('subject')
            ->take(5)
            ->get();
        return view('student.dashboard', compact('student', 'group', 'curator', 'upcomingAssignments'));
    }

    public function uploadPhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|max:2048',
            ]);
            $student = Auth::user();
            $user = \App\Models\User::find($student->id);
            // Удаляем старое фото, если оно не default
            if ($user->photo && $user->photo !== 'default-student.png' && file_exists(public_path('images/' . $user->photo))) {
                @unlink(public_path('images/' . $user->photo));
            }
            $file = $request->file('photo');
            $filename = 'student_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
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