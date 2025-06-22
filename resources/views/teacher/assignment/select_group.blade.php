@extends('layouts.app')


@section('title-page')
Выбор группы
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Выберите группу для предмета "{{ $subject->name }}"</h4>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($groups->isEmpty())
                        <div class="alert alert-info">
                            У вас нет доступных групп для этого предмета.
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($groups as $group)
                                <a href="{{ route('teacher.assignments.create', ['subjectId' => $subject->id, 'groupId' => $group->id]) }}" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $group->name }}</h5>
                                    </div>
                                    <p class="mb-1">Количество студентов: {{ $group->students_count ?? 0 }}</p>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('teacher.assignments.subjects.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Назад к выбору предмета
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.groups-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.group-card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.group-card h3 {
    margin-bottom: 15px;
    color: #333;
}

.btn-primary {
    background-color: #4a90e2;
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn-primary:hover {
    background-color: #357abd;
}
</style>
@endsection 