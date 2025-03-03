@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Add New Task</div>
    <div class="card-body">
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3"></textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Due Date</label>
                <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" name="due_date">
                @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Add Task</button>
        </form>
    </div>
</div>
@endsection