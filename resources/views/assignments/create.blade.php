@extends('layouts.app')

@section('title-page', 'Создание задания')

@section('content')
<div class="assignment-create">
    <form action="{{ route('assignments.store') }}" method="POST" class="assignment-create__form">
        @csrf
        <div class="assignment-create__form-group form-group">
            <label for="title">Название задания</label>
            <input type="text" id="title" name="title" class="assignment-create__input" required>
        </div>

        <div class="assignment-create__form-group form-group">
            <label for="description">Описание задания</label>
            <textarea id="description" name="description" class="assignment-create__input" rows="4" required></textarea>
        </div>

        <div class="assignment-create__form-group form-group">
            <label for="subject">Предмет</label>
            <select id="subject" name="subject_id" class="assignment-create__input" required>
                <option value="">Выберите предмет</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="assignment-create__form-group form-group">
            <label for="group">Группа</label>
            <select id="group" name="group_id" class="assignment-create__input" required>
                <option value="">Выберите группу</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="assignment-create__form-group form-group">
            <label for="deadline">Срок сдачи</label>
            <input type="datetime-local" id="deadline" name="deadline" class="assignment-create__input" required>
        </div>

        <div class="assignment-create__form-group form-group">
            <label for="max_attempts">Максимальное количество попыток</label>
            <input type="number" id="max_attempts" name="max_attempts" class="assignment-create__input" min="1" value="3" required>
        </div>

        <div class="assignment-create__form-group form-group">
            <label for="points">Баллы за задание</label>
            <input type="number" id="points" name="points" class="assignment-create__input" min="1" value="10" required>
        </div>

        <div class="assignment-create__form-group form-group">
            <label>Тип задания</label>
            <div class="assignment-create__radio-group">
                <label class="assignment-create__radio">
                    <input type="radio" name="type" value="text" checked>
                    <span>Текстовое</span>
                </label>
                <label class="assignment-create__radio">
                    <input type="radio" name="type" value="code">
                    <span>Программирование</span>
                </label>
                <label class="assignment-create__radio">
                    <input type="radio" name="type" value="file">
                    <span>Файл</span>
                </label>
            </div>
        </div>

        <div class="assignment-create__form-group form-group">
            <label>Язык программирования</label>
            <div class="assignment-create__radio-group">
                <label class="assignment-create__radio">
                    <input type="radio" name="programming_language" value="javascript" checked>
                    <span>JavaScript</span>
                </label>
                <label class="assignment-create__radio">
                    <input type="radio" name="programming_language" value="python">
                    <span>Python</span>
                </label>
                <label class="assignment-create__radio">
                    <input type="radio" name="programming_language" value="php">
                    <span>PHP</span>
                </label>
                <label class="assignment-create__radio">
                    <input type="radio" name="programming_language" value="java">
                    <span>Java</span>
                </label>
                <label class="assignment-create__radio">
                    <input type="radio" name="programming_language" value="cpp">
                    <span>C++</span>
                </label>
                <label class="assignment-create__radio">
                    <input type="radio" name="programming_language" value="html">
                    <span>HTML</span>
                </label>
                <label class="assignment-create__radio">
                    <input type="radio" name="programming_language" value="css">
                    <span>CSS</span>
                </label>
            </div>
        </div>

        <div class="assignment-create__form-group form-group">
            <label for="initial_code">Начальный код</label>
            <div class="code-editor-container">
                <div class="code-editor-wrapper">
                    <div class="code-editor-header">
                        <span class="code-editor-title">HTML</span>
                        <div class="code-editor-tools">
                            <button type="button" class="code-editor-tool" title="Форматировать код">
                                <i class="fas fa-indent"></i>
                            </button>
                            <button type="button" class="code-editor-tool" title="Минифицировать код">
                                <i class="fas fa-compress-alt"></i>
                            </button>
                            <button type="button" class="code-editor-tool" title="Очистить код">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <textarea id="html_code" class="code-editor" data-language="htmlmixed"></textarea>
                </div>
                <div class="code-editor-wrapper">
                    <div class="code-editor-header">
                        <span class="code-editor-title">CSS</span>
                        <div class="code-editor-tools">
                            <button type="button" class="code-editor-tool" title="Форматировать код">
                                <i class="fas fa-indent"></i>
                            </button>
                            <button type="button" class="code-editor-tool" title="Минифицировать код">
                                <i class="fas fa-compress-alt"></i>
                            </button>
                            <button type="button" class="code-editor-tool" title="Очистить код">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <textarea id="css_code" class="code-editor" data-language="css"></textarea>
                </div>
            </div>
            <div class="code-preview">
                <div class="code-preview-header">
                    <span class="code-preview-title">Предпросмотр</span>
                    <div class="code-preview-tools">
                        <button type="button" class="code-preview-tool" title="Обновить">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button type="button" class="code-preview-tool" title="На весь экран">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
                <div class="code-preview-content"></div>
            </div>
        </div>

        <div class="assignment-create__form-group form-group">
            <label for="test_cases">Тестовые случаи</label>
            <div id="test-cases">
                <div class="test-case">
                    <input type="text" name="test_cases[0][input]" placeholder="Входные данные" class="assignment-create__input">
                    <input type="text" name="test_cases[0][output]" placeholder="Ожидаемый результат" class="assignment-create__input">
                    <button type="button" class="assignment-create__remove-test-case">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <button type="button" id="add-test-case" class="assignment-create__add-test-case">
                <i class="fas fa-plus"></i> Добавить тестовый случай
            </button>
        </div>

        <div class="assignment-create__form-group form-group">
            <label for="solution">Решение</label>
            <textarea id="solution" name="solution" class="assignment-create__input" rows="4"></textarea>
        </div>

        <div class="assignment-create__form-group form-group">
            <label for="hints">Подсказки</label>
            <div id="hints">
                <div class="hint">
                    <textarea name="hints[0]" placeholder="Введите подсказку" class="assignment-create__input"></textarea>
                    <button type="button" class="assignment-create__remove-hint">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <button type="button" id="add-hint" class="assignment-create__add-hint">
                <i class="fas fa-plus"></i> Добавить подсказку
            </button>
        </div>

        <div class="assignment-create__form-group form-group">
            <label for="resources">Дополнительные материалы</label>
            <div id="resources">
                <div class="resource">
                    <input type="text" name="resources[0][title]" placeholder="Название ресурса" class="assignment-create__input">
                    <input type="url" name="resources[0][url]" placeholder="URL ресурса" class="assignment-create__input">
                    <button type="button" class="assignment-create__remove-resource">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <button type="button" id="add-resource" class="assignment-create__add-resource">
                <i class="fas fa-plus"></i> Добавить ресурс
            </button>
        </div>

        <div class="assignment-create__form-group form-group">
            <button type="submit" class="assignment-create__submit">
                <i class="fas fa-save"></i> Создать задание
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/assignment-create.js'])
@endpush 