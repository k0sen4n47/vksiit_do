<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroupNameComponent;
use Illuminate\Support\Facades\Validator;

class GroupNameComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $components = GroupNameComponent::all();
        return view('admin.group_name_components.index', compact('components'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.group_name_components.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255|unique:group_name_components,full_name',
            'short_name' => 'required|string|max:255|unique:group_name_components,short_name',
        ]);

        // Проверка, что short_name является подстрокой full_name
        if ($validator->passes() && !str_contains($request->input('full_name'), $request->input('short_name'))) {
            $validator->errors()->add('short_name', 'Короткое название должно быть частью полного названия.');
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        GroupNameComponent::create($validator->validated());

        return redirect()->route('admin.group-name-components.index')->with('success', 'Компонент названия группы успешно создан!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GroupNameComponent $groupNameComponent)
    {
        return view('admin.group_name_components.edit', compact('groupNameComponent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GroupNameComponent $groupNameComponent)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255|unique:group_name_components,full_name,' . $groupNameComponent->id,
            'short_name' => 'required|string|max:255|unique:group_name_components,short_name,' . $groupNameComponent->id,
        ]);

        // Проверка, что short_name является подстрокой full_name
        if ($validator->passes() && !str_contains($request->input('full_name'), $request->input('short_name'))) {
            $validator->errors()->add('short_name', 'Короткое название должно быть частью полного названия.');
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $groupNameComponent->update($validator->validated());

        return redirect()->route('admin.group-name-components.index')->with('success', 'Компонент названия группы успешно обновлен!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GroupNameComponent $groupNameComponent)
    {
        $groupNameComponent->delete();

        return redirect()->route('admin.group-name-components.index')->with('success', 'Компонент названия группы успешно удален!');
    }
}
