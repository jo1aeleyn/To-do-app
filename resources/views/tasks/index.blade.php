    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body{
            background-color:#F3F7EC;
        }
    </style>
    </head>
    <body>
    <div class="container my-5 px-3">
    <!-- Main Container with responsiveness -->
    <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6 p-3 border">
    <h1 class="text-center">To-Do List</h1>

    <form action="/tasks" method="POST" class="my-4">
    @csrf
    <div class="input-group mb-3">
    <!-- Input field for task title -->
    <input type="text" name="title" class="form-control flex-grow-1" placeholder="Add a new task" required>

    <!-- Dropdown for priority level -->
    <select name="priority_level" id="priority_level" class="form-select" required>
    <option value="" disabled selected>Select Priority Level</option>
    <option value="low">Low</option>
    <option value="medium">Medium</option>
    <option value="high">High</option>
    </select>

    <!-- Add button with icon -->
    <button class="btn btn-primary" type="submit">
    <i class="fa fa-plus"></i> Add <!-- Add icon -->
    </button>
    </div>
    </form>


    <!-- Multi-task action buttons and filter options in one line -->
    <div class="my-3 d-flex flex-column flex-sm-row justify-content-between align-items-center">
    <!-- Multi-task action buttons -->
    <div class="mb-2 mb-sm-0">
    <button id="completeSelected" class="btn btn-success me-2">Complete Selected</button>
    <button id="deleteSelected" class="btn btn-danger">Delete Selected</button>
    </div>

    <!-- Filter options with icon -->
    <div>
    <select id="taskFilter" class="form-select d-inline-block">
    <option value="all">All Tasks</option>
    <option value="completed">Completed Tasks</option>
    <option value="pending">Pending Tasks</option>
    </select>
    </div>
    </div>

        <!-- Task List -->
        <ul class="list-group" id="taskList">
        @foreach ($tasks as $task)
        <li class="list-group-item d-flex justify-content-between align-items-center task-item mb-2 
                                    {{ $task->priority_level === 'low' ? 'list-group-item-success' : '' }}
                                    {{ $task->priority_level === 'medium' ? 'list-group-item-warning' : '' }}
                                    {{ $task->priority_level === 'high' ? 'list-group-item-danger' : '' }}"
        data-task-id="{{ $task->id }}" 
        data-status="{{ $task->isComplete ? 'completed' : 'pending' }}">
        <div>
        <input type="checkbox" class="form-check-input me-2 task-checkbox"
        data-task-id="{{ $task->id }}"
        {{ $task->isComplete ? 'checked' : '' }}>
        <span class="{{ $task->isComplete ? 'text-decoration-line-through' : '' }} task-title"
        data-task-id="{{ $task->id }}"
        ondblclick="editTask(this, {{ $task->id }})">
        {{ $task->title }}
        </span>
        <small class="text-muted ms-3">({{ ucfirst($task->priority_level) }} Priority)</small>
        </div>
        <div>
        <form action="/tasks/{{ $task->id }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm" type="submit">
        <i class="fa fa-trash-alt"></i> <!-- Trash icon -->
        </button>
        </form>
        </div>
        </li>
        @endforeach
        </ul>
        </div>
        </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Edit task functionality
    function editTask(element, taskId) {
        const currentText = element.textContent.trim();
        const input = document.createElement('input');
        input.type = 'text';
        input.value = currentText;
        input.className = 'form-control';
        input.style.display = 'inline-block';
        input.style.width = 'auto';
        
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const newValue = input.value.trim();
                
                if (newValue && newValue !== currentText) {
                    // Update task title via AJAX
                    fetch(`/tasks/${taskId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ title: newValue })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            element.textContent = newValue;
                            input.remove();
                        } else {
                            alert('Failed to update task');
                        }
                    })
                    .catch(() => alert('An error occurred while updating the task.'));
                } else {
                    element.textContent = currentText;
                    input.remove();
                }
            }
        });
        
        element.textContent = '';
        element.appendChild(input);
        input.focus();
    }

    // Get selected task IDs
    function getSelectedTasks() {
        const selectedTasks = [];
        document.querySelectorAll('.task-checkbox:checked').forEach(checkbox => {
            selectedTasks.push(checkbox.getAttribute('data-task-id'));
        });
        return selectedTasks;
    }

    document.getElementById('completeSelected').addEventListener('click', function () {
        const selectedTasks = getSelectedTasks();
        if (selectedTasks.length > 0) {
            fetch('/tasks/complete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ taskIds: selectedTasks })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    selectedTasks.forEach(taskId => {
                        const taskElement = document.querySelector(`input[data-task-id='${taskId}']`);
                        const taskSpan = document.querySelector(`span[data-task-id='${taskId}']`);
                        taskElement.checked = true;
                        taskSpan.classList.add('text-decoration-line-through');
                    });
                } else {
                    alert(data.message || 'Failed to complete tasks');
                }
            })
            .catch(() => alert('An error occurred while completing tasks.'));
        } else {
            alert('No tasks selected');
        }
    });

    // Delete selected tasks
    document.getElementById('deleteSelected').addEventListener('click', function () {
        const selectedTasks = getSelectedTasks();
        if (selectedTasks.length > 0) {
            fetch('/tasks/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ taskIds: selectedTasks })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    selectedTasks.forEach(taskId => {
                        const taskElement = document.querySelector(`li[data-task-id='${taskId}']`);
                        if (taskElement) taskElement.remove();
                    });
                } else {
                    alert(data.message || 'Failed to delete tasks');
                }
            })
            .catch(() => alert('An error occurred while deleting tasks.'));
        } else {
            alert('No tasks selected');
        }
    });

    document.getElementById('taskFilter').addEventListener('change', function () {
        const filterValue = this.value;
        
        fetch(`/tasks?filter=${filterValue}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.text())  // Expect HTML (full page)
        .then(html => {
            // Create a temporary div to hold the response HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Extract the updated task list from the response
            const updatedTaskList = tempDiv.querySelector('#taskList');
            
            // Update the task list in the current page
            document.querySelector('#taskList').innerHTML = updatedTaskList.innerHTML;
        })
        .catch(error => console.error('Error:', error));
    });
    </script>
    </body>
    </html>
