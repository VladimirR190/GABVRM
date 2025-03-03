@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Task List</h5>
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">Add Task</a>
        </div>

        <!-- Фильтр перенесен внутрь карточки -->
        <div class="card-body">
            <div class="mb-4">
                <div class="row g-3 align-items-center">
                    <!-- Фильтр -->
                    <div class="col-md-4">
                        <form action="{{ route('tasks.index') }}" method="GET">
                            <div class="input-group">
                                <select class="form-select" name="filter">
                                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All Tasks</option>
                                    <option value="completed" {{ request('filter') == 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="in_progress" {{ request('filter') == 'in_progress' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="not_started" {{ request('filter') == 'not_started' ? 'selected' : '' }}>Not
                                        Completed</option>
                                </select>
                                <button type="submit" class="btn btn-outline-secondary">Filter</button>
                            </div>
                        </form>
                    </div>

                    <!-- Кнопки сортировки -->
                    <div class="col-md-8">
                        <div class="btn-group" role="group">
                            <a href="{{ route('tasks.index', [
        'sort_by' => 'due_date',
        'sort_direction' => $sortDirection === 'asc' ? 'desc' : 'asc',
        'filter' => request('filter') // Сохраняем текущий фильтр
    ]) }}" class="btn btn-outline-secondary {{ $sortBy === 'due_date' ? 'active' : '' }}">
                                Sort by Date
                                @if($sortBy === 'due_date')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>

                            <a href="{{ route('tasks.index', [
        'sort_by' => 'title',
        'sort_direction' => $sortDirection === 'asc' ? 'desc' : 'asc',
        'filter' => request('filter') // Сохраняем текущий фильтр
    ]) }}" class="btn btn-outline-secondary {{ $sortBy === 'title' ? 'active' : '' }}">
                                Sort by Title
                                @if($sortBy === 'title')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('tasks.index', ['sort_by' => 'title', 'sort_direction' => $sortBy === 'title' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-decoration-none text-dark">
                                        Title
                                        @if($sortBy === 'title')
                                            <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Description</th>
                                <th>
                                    <a href="{{ route('tasks.index', ['sort_by' => 'created_at', 'sort_direction' => $sortBy === 'created_at' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-decoration-none text-dark">
                                        Created At
                                        @if($sortBy === 'created_at')
                                            <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                                    <tr>
                                                        <td>{{ $task->title }}</td>
                                                        <td>{{ $task->description }}</td> <!-- Добавленная колонка -->
                                                        <td>{{ date('d M Y, H:i', strtotime($task->due_date)) }}</td>
                                                        <td>
                                                            @php
                                                                $statusClasses = [
                                                                    'completed' => 'bg-success',
                                                                    'in_progress' => 'bg-warning text-dark',
                                                                    'not_started' => 'bg-danger'
                                                                ];
                                                            @endphp
                                                            <span class="badge {{ $statusClasses[$task->status] }}">
                                                                {{ \App\Models\Task::getStatuses()[$task->status] }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-warning edit-task" data-bs-toggle="modal"
                                                                data-bs-target="#editModal" data-task-id="{{ $task->id }}"
                                                                data-task-title="{{ $task->title }}"
                                                                data-task-description="{{ $task->description }}"
                                                                data-task-due-date="{{ $task->due_date instanceof \Carbon\Carbon ? $task->due_date->format('Y-m-d\TH:i') : \Carbon\Carbon::parse($task->due_date)->format('Y-m-d\TH:i') }}"
                                                                data-task-status="{{ $task->status }}">
                                                                <i class="bi bi-pencil"></i> Edit
                                                            </button>
                                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline"
                                                                onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    <i class="bi bi-trash"></i> Delete
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $tasks->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editTaskForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" id="editTitle" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="editDescription" rows="3"
                                required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="datetime-local" class="form-control" name="due_date" id="editDueDate" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="editStatus">
                                @foreach(\App\Models\Task::getStatuses() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const editButtons = document.querySelectorAll('.edit-task');
                const editModal = document.getElementById('editModal');

                editButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        // Получаем данные из data-атрибутов
                        const taskId = this.dataset.taskId;
                        const taskTitle = this.dataset.taskTitle;
                        const taskDescription = this.dataset.taskDescription;
                        const taskDueDate = this.dataset.taskDueDate;
                        const taskStatus = this.dataset.taskStatus;

                        // Заполняем форму
                        document.getElementById('editTitle').value = taskTitle;
                        document.getElementById('editDescription').value = taskDescription;
                        document.getElementById('editDueDate').value = taskDueDate;
                        document.getElementById('editStatus').value = taskStatus;

                        // Обновляем action формы
                        document.getElementById('editTaskForm').action = `/tasks/${taskId}`;
                    });
                });

                // Обработка отправки формы
                document.getElementById('editTaskForm').addEventListener('submit', function (e) {
                    e.preventDefault();

                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'PUT',
                            title: document.getElementById('editTitle').value,
                            description: document.getElementById('editDescription').value,
                            due_date: document.getElementById('editDueDate').value,
                            status: document.getElementById('editStatus').value
                        })
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Network error');
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                });
            });
        </script>
    @endpush
    <style>
        .btn-group .btn.active {
            background-color: #6c757d;
            color: white;
            border-color: #6c757d;
        }
    </style>
@endsection