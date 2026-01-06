<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /To-do-listAgressive/view/forms/formAuthorization.php");
    exit();
}

require_once __DIR__ . '/../server/database.php';
require_once __DIR__ . '/../model/TaskModel.php';

$taskModel = new TaskModel();
$tasks = $taskModel->getUserTasks($_SESSION['user_id']);
?>

<?php require __DIR__ . '/layout/header.php'; ?>

<body>
    <section>
        <div class="row">
            <div class="col-2 left-half">
                <div class="menu-top">
                    <button class="btn btn-outline-dark w-100 mb-3" onclick="loadAllTasks()">
                        <strong>Все задачи</strong>
                    </button>
                    <button class="btn btn-outline-dark w-100 mb-3" onclick="filterTasks('active')">
                        <strong>Активные</strong>
                    </button>
                    <button class="btn btn-outline-dark w-100 mb-3" onclick="filterTasks('completed')">
                        <strong>Завершённые</strong>
                    </button>
                </div>
                <div class="menu-bottom">
                    <div class="row align-items-center">
                        <div class="col-8 text-center">
                            <img src="/To-do-listAgressive/view/resources/image/user.png" alt="User" height="50px" width="50px" class="mb-2">
                            <p class="mb-0"><strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
                            <small class="text-muted">Пользователь</small>
                        </div>
                        <div class="col-4 text-center">
                            <a href="/To-do-listAgressive/router.php?action=logout" class="btn btn-sm btn-outline-dark">
                                Выйти
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-10 right-half">
                <div id="tasks-container">
                    <?php if (empty($tasks)): ?>
                        <div class="text-center mt-5">
                            <h3>Пока нет задач</h3>
                            <p>Нажмите кнопку "Добавить задачу" чтобы создать первую задачу</p>
                        </div>
                    <?php else: ?>
                        <h3 class="mb-4">Мои задачи (<?php echo count($tasks); ?>)</h3>
                        <div id="tasks-list">
                            <?php foreach ($tasks as $task): ?>
                                <div class="task-item card mb-3" onclick="openEditModal(<?php echo $task['id']; ?>)">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input me-3" type="checkbox"
                                                <?php echo $task['is_completed'] ? 'checked' : ''; ?>
                                                onchange="event.stopPropagation(); toggleTask(<?php echo $task['id']; ?>)">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-1 <?php echo $task['is_completed'] ? 'text-decoration-line-through text-muted' : ''; ?>">
                                                    <?php echo htmlspecialchars($task['name']); ?>
                                                </h5>
                                                <?php if (!empty($task['description'])): ?>
                                                    <p class="card-text mb-1"><?php echo htmlspecialchars($task['description']); ?></p>
                                                <?php endif; ?>
                                                <?php if ($task['timer']): ?>
                                                    <small class="text-muted">
                                                        До: <?php echo date('d.m.Y H:i', strtotime($task['timer'])); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="task-status">
                                                <?php if ($task['is_completed']): ?>
                                                    <span>Выполнено</span>
                                                <?php else: ?>
                                                    <span>В процессе</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button class="btn btn-outline-dark position-fixed"
                    style="bottom: 20px; right: 20px; z-index: 1000;"
                    onclick="openCreateModal()">
                    + Добавить задачу
                </button>
            </div>
        </div>
    </section>

    <div class="modal fade" id="createTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Создать задачу</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="create-task-form">
                        <div class="mb-3">
                            <label for="task-name" class="form-label">Название задачи *</label>
                            <input type="text" class="form-control" id="task-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="task-description" class="form-label">Описание *</label>
                            <textarea class="form-control" id="task-description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="task-timer" class="form-label">Таймер выполнения *</label>
                            <input type="datetime-local" class="form-control" id="task-timer" name="timer" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-outline-dark" onclick="createTask()">Создать</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Редактировать задачу</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-task-form">
                        <input type="hidden" id="edit-task-id" name="task_id">
                        <div class="mb-3">
                            <label for="edit-task-name" class="form-label">Название задачи *</label>
                            <input type="text" class="form-control" id="edit-task-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-task-description" class="form-label">Описание *</label>
                            <textarea class="form-control" id="edit-task-description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit-task-timer" class="form-label">Таймер выполнения *</label>
                            <input type="datetime-local" class="form-control" id="edit-task-timer" name="timer" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit-task-completed" name="is_completed">
                            <label class="form-check-label" for="edit-task-completed">Задача выполнена</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" onclick="deleteTask()">Удалить</button>
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-outline-dark" onclick="updateTask()">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadAllTasks() {
            location.reload();
        }

        function filterTasks(status) {
            const tasks = document.querySelectorAll('.task-item');
            tasks.forEach(task => {
                const isCompleted = task.querySelector('.form-check-input').checked;
                if (status === 'active') {
                    task.style.display = !isCompleted ? 'block' : 'none';
                } else if (status === 'completed') {
                    task.style.display = isCompleted ? 'block' : 'none';
                }
            });

            const count = document.querySelectorAll('.task-item[style*="display: block"]').length;
            document.querySelector('h3').textContent = status === 'active' ?
                `Активные задачи (${count})` :
                status === 'completed' ?
                `Завершённые задачи (${count})` :
                `Мои задачи (${count})`;
        }

        function openCreateModal() {
            document.getElementById('create-task-form').reset();
            const modal = new bootstrap.Modal(document.getElementById('createTaskModal'));
            modal.show();
        }

        function openEditModal(taskId) {
            fetch(`/To-do-listAgressive/router.php?action=get_task&task_id=${taskId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const task = data.task;
                        document.getElementById('edit-task-id').value = task.id;
                        document.getElementById('edit-task-name').value = task.name;
                        document.getElementById('edit-task-description').value = task.description || '';

                        if (task.timer) {
                            const timerDate = new Date(task.timer);
                            const formattedTimer = timerDate.toISOString().slice(0, 16);
                            document.getElementById('edit-task-timer').value = formattedTimer;
                        } else {
                            document.getElementById('edit-task-timer').value = '';
                        }

                        document.getElementById('edit-task-completed').checked = task.is_completed == 1;

                        const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                        modal.show();
                    } else {
                        alert('Ошибка загрузки задачи');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка загрузки задачи');
                });
        }

        function createTask() {
            const formData = new FormData(document.getElementById('create-task-form'));

            const taskName = document.getElementById('task-name').value.trim();
            const taskDescription = document.getElementById('task-description').value.trim();
            const taskTimer = document.getElementById('task-timer').value;

            if (!taskName) {
                alert('Пожалуйста, введите название задачи');
                return;
            }

            if (!taskDescription) {
                alert('Пожалуйста, введите описание задачи');
                return;
            }

            if (!taskTimer) {
                alert('Пожалуйста, укажите таймер выполнения');
                return;
            }

            fetch('/To-do-listAgressive/router.php?action=create_task', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createTaskModal'));
                        modal.hide();
                        location.reload();
                    } else {
                        alert('Ошибка создания задачи: ' + (data.message || 'Неизвестная ошибка'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка создания задачи');
                });
        }

        function updateTask() {
            const formData = new FormData(document.getElementById('edit-task-form'));

            const taskName = document.getElementById('edit-task-name').value.trim();
            const taskDescription = document.getElementById('edit-task-description').value.trim();
            const taskTimer = document.getElementById('edit-task-timer').value;

            if (!taskName) {
                alert('Пожалуйста, введите название задачи');
                return;
            }

            if (!taskDescription) {
                alert('Пожалуйста, введите описание задачи');
                return;
            }

            if (!taskTimer) {
                alert('Пожалуйста, укажите таймер выполнения');
                return;
            }

            fetch('/To-do-listAgressive/router.php?action=update_task', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editTaskModal'));
                        modal.hide();
                        location.reload();
                    } else {
                        alert('Ошибка обновления задачи: ' + (data.message || 'Неизвестная ошибка'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка обновления задачи');
                });
        }

        function toggleTask(taskId) {
            const formData = new FormData();
            formData.append('task_id', taskId);

            fetch('/To-do-listAgressive/router.php?action=toggle_task', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Ошибка обновления статуса задачи');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка обновления статуса задачи');
                });
        }

        function deleteTask() {
            const taskId = document.getElementById('edit-task-id').value;
            if (confirm('Вы уверены, что хотите удалить эту задачу?')) {
                const formData = new FormData();
                formData.append('task_id', taskId);

                fetch('/To-do-listAgressive/router.php?action=delete_task', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editTaskModal'));
                            modal.hide();
                            location.reload();
                        } else {
                            alert('Ошибка удаления задачи');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ошибка удаления задачи');
                    });
            }
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal.show');
                modals.forEach(modal => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>