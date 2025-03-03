@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">Edit Task</div>
        <div class="card-body">
            <form method="POST" action="{{ route('tasks.update', $task) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title"
                        value="{{ old('title', $task->title) }}">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" name="description"
                        rows="3">{{ old('description', $task->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror"
                        name="due_date" value="{{ old(
        'due_date',
        $task->due_date instanceof \Carbon\Carbon
        ? $task->due_date->format('Y-m-d\TH:i')
        : \Carbon\Carbon::parse($task->due_date)->format('Y-m-d\TH:i')
    ) }}">
                    @error('due_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Статус</label>
                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                        @foreach(\App\Models\Task::getStatuses() as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $task->status) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-warning">Update Task</button>
            </form>
        </div>
    </div>
@endsection