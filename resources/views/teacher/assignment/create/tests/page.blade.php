<div class="assignment-create__page test" data-page-index="{{ $pageIndex }}" data-page-type="test">
    <div class="assignment-create__page-header">
        <h3 class="assignment-create__page-title">Страница {{ $pageIndex + 1 }}</h3>
        <button type="button" class="assignment-create__page-remove" data-page-index="{{ $pageIndex }}">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="assignment-create__page-body">
        @include('teacher.assignment.create.tests.partials.header')
        @include('teacher.assignment.create.tests.partials.questions')
    </div>
</div>

@include('teacher.assignment.create.tests.partials.templates') 