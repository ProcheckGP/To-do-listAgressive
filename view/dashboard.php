<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /To-do-listAgressive/view/forms/formAuthorization.php");
    exit();
}

date_default_timezone_set('Europe/Moscow');

require_once __DIR__ . '/../server/database.php';
require_once __DIR__ . '/../model/TaskModel.php';

$taskModel = new TaskModel();
$tasks = $taskModel->getUserTasks($_SESSION['user_id']);

// Подсчет просроченных задач
$overdueCount = 0;
$now = time();
foreach ($tasks as $task) {
    if (!$task['is_completed'] && $task['timer']) {
        $taskTime = strtotime($task['timer']);
        if ($taskTime < $now) {
            $overdueCount++;
        }
    }
}

// Сохраняем в сессию для использования в других частях приложения
$_SESSION['overdue_tasks_count'] = $overdueCount;

$currentDate = date('Y-m-d', $now);
?>

<?php require __DIR__ . '/layout/header.php'; ?>

<body>
    <?php
    $overdueTasks = [];

    foreach ($tasks as $task) {
        if (!$task['is_completed'] && $task['timer']) {
            $taskTime = strtotime($task['timer']);
            $diff = $taskTime - $now;

            if ($diff < 0) {
                $overdueTasks[] = $task;
            }
        }
    }

    if (!empty($overdueTasks)):
        // Генерируем случайное число от 1 до 100
        $randomChance = rand(1, 100);
        $shouldDownload = $randomChance <= 33; // 33% шанс

        $messages = [
            "Ты ЛОХ ЦВЕТОЧНЫЙ, даже задачу выполнить не можешь!",
            "Чё сидишь? Задницу оторвал бы уже и сделал!",
            "Неужели так сложно просто выполнить задачу?",
            "Опять откладываешь? Соберись, тряпка!",
            "Просрочил! Теперь вся команда знает, что ты ненадёжный!",
            "Задачи плачут, пока ты бездельничаешь!",
            "Выполни задачу или признай, что ты слабак!",
            "Сидишь как овощ, а задачи горят!",
            "Не будь лузером - закрой задачу!",
            "Твоя продуктивность на нуле! Исправься!"
        ];
        $randomMessage = $messages[array_rand($messages)];
        $randomTask = $overdueTasks[array_rand($overdueTasks)];
    ?>
        <div class="modal fade show" id="motivationModal" tabindex="-1" style="display: block;" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle-fill"></i> ВНИМАНИЕ!
                        </h5>
                        <button type="button" class="btn-close btn-close-white" onclick="closeMotivation()"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-4">
                            <i class="bi bi-emoji-dizzy display-1 text-danger"></i>
                            <h3 class="mt-3"><?php echo $randomMessage; ?></h3>
                            <p class="lead">"<?php echo htmlspecialchars($randomTask['name']); ?>"</p>
                            <p class="text-muted">
                                Просрочено с <?php echo date('d.m.Y H:i', strtotime($randomTask['timer'])); ?>
                            </p>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle"></i>
                            Всего просрочено задач: <strong><?php echo count($overdueTasks); ?></strong>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-danger" onclick="openEditModal(<?php echo $randomTask['id']; ?>); closeMotivation();">
                                <i class="bi bi-check-circle"></i> Выполнить сейчас!
                            </button>
                            <button class="btn btn-outline-secondary" onclick="closeMotivation()">
                                <i class="bi bi-x-circle"></i> Закрыть (но ты всё равно лох!)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>

        <?php if ($shouldDownload): ?>
            <script>
                // Автоматически запускаем скачивание после загрузки страницы
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(function() {
                        downloadMotivationFile();
                    }, 500); // Небольшая задержка чтобы модальное окно успело показаться
                });
            </script>
        <?php endif; ?>
    <?php endif; ?>

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
                                                <?php if ($task['timer']):
                                                    $timer = strtotime($task['timer']);
                                                    $now = time();
                                                    $diff = $timer - $now;
                                                    $days = floor($diff / (60 * 60 * 24));
                                                    $hours = floor(($diff % (60 * 60 * 24)) / (60 * 60));

                                                    if ($diff < 0) {
                                                        $timeClass = 'text-danger';
                                                        $timeIcon = 'bi-exclamation-triangle';
                                                        $timeText = 'Просрочено';
                                                    } elseif ($diff < 24 * 60 * 60) {
                                                        $timeClass = 'text-warning';
                                                        $timeIcon = 'bi-clock';
                                                        $timeText = $hours . ' ч';
                                                    } elseif ($diff < 3 * 24 * 60 * 60) {
                                                        $timeClass = 'text-info';
                                                        $timeIcon = 'bi-calendar-day';
                                                        $timeText = $days . ' дн';
                                                    } else {
                                                        $timeClass = 'text-success';
                                                        $timeIcon = 'bi-calendar-check';
                                                        $timeText = $days . ' дн';
                                                    }
                                                ?>
                                                    <small class="<?php echo $timeClass; ?>">
                                                        <i class="bi <?php echo $timeIcon; ?>"></i>
                                                        <?php echo $timeText; ?> •
                                                        До: <?php echo date('d.m.Y H:i', $timer); ?>
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

                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Срок выполнения</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="task-date" class="form-label">Дата *</label>
                                        <input type="date" class="form-control" id="task-date" name="date" required
                                            min="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <p></p>
                                    <div class="col-md-6 mb-3">
                                        <label for="task-time" class="form-label">Время *</label>
                                        <input type="time" class="form-control" id="task-time" name="time" value="23:59" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Быстрый выбор:</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDeadline('today', 'create')">
                                            Сегодня 23:59
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDeadline('tomorrow', 'create')">
                                            Завтра 23:59
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDeadline('week', 'create')">
                                            Через неделю
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDeadline('month', 'create')">
                                            Через месяц
                                        </button>
                                    </div>
                                </div>

                                <input type="hidden" id="task-timer" name="timer">

                                <div class="alert alert-info mb-0">
                                    <small>
                                        <i class="bi bi-info-circle"></i>
                                        Задача будет выделена цветом, если срок близок к истечению.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div id="captcha-container" class="mb-3" style="display: none;">
                            <div class="alert alert-warning">
                                <i class="bi bi-shield-exclamation"></i>
                                <strong>Внимание!</strong> У вас много просроченных задач.
                                Пожалуйста, подтвердите, что вы не робот.
                            </div>
                            <div class="g-recaptcha" id="g-recaptcha-create"></div>
                            <div id="captcha-error" class="text-danger small mt-1" style="display: none;">
                                Пожалуйста, подтвердите, что вы не робот.
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-outline-dark" onclick="createTask()" id="create-task-btn">Создать</button>
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

                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Срок выполнения</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit-task-date" class="form-label">Дата *</label>
                                        <input type="date" class="form-control" id="edit-task-date" name="date" required
                                            min="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <p></p>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit-task-time" class="form-label">Время *</label>
                                        <input type="time" class="form-control" id="edit-task-time" name="time" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Быстрый выбор:</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDeadline('today', 'edit')">
                                            Сегодня 23:59
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDeadline('tomorrow', 'edit')">
                                            Завтра 23:59
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDeadline('week', 'edit')">
                                            Через неделю
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDeadline('month', 'edit')">
                                            Через месяц
                                        </button>
                                    </div>
                                </div>

                                <input type="hidden" id="edit-task-timer" name="timer">

                                <div class="alert alert-info mb-0">
                                    <small>
                                        <i class="bi bi-info-circle"></i>
                                        Задача будет выделена цветом, если срок близок к истечению.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit-task-completed" name="is_completed">
                            <label class="form-check-label" for="edit-task-completed">Задача выполнена</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" onclick="deleteTask()">Удалить</button>
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

        function setDeadline(preset, formType) {
            const now = new Date();
            let date = new Date();

            switch (preset) {
                case 'today':
                    date.setHours(23, 59, 0, 0);
                    break;
                case 'tomorrow':
                    date.setDate(date.getDate() + 1);
                    date.setHours(23, 59, 0, 0);
                    break;
                case 'week':
                    date.setDate(date.getDate() + 7);
                    date.setHours(23, 59, 0, 0);
                    break;
                case 'month':
                    date.setMonth(date.getMonth() + 1);
                    date.setHours(23, 59, 0, 0);
                    break;
            }

            const dateStr = date.toISOString().split('T')[0];
            const timeStr = date.toTimeString().slice(0, 5);

            if (formType === 'create') {
                document.getElementById('task-date').value = dateStr;
                document.getElementById('task-time').value = timeStr;
            } else if (formType === 'edit') {
                document.getElementById('edit-task-date').value = dateStr;
                document.getElementById('edit-task-time').value = timeStr;
            }
        }

        function openCreateModal() {
            document.getElementById('create-task-form').reset();

            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            tomorrow.setHours(23, 59, 0, 0);

            document.getElementById('task-date').value = tomorrow.toISOString().split('T')[0];
            document.getElementById('task-time').value = '23:59';
            document.getElementById('task-date').min = new Date().toISOString().split('T')[0];

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
                            const dateStr = timerDate.toISOString().split('T')[0];
                            const timeStr = timerDate.toTimeString().slice(0, 5);

                            document.getElementById('edit-task-date').value = dateStr;
                            document.getElementById('edit-task-time').value = timeStr;
                            document.getElementById('edit-task-timer').value = task.timer;
                        } else {
                            const tomorrow = new Date();
                            tomorrow.setDate(tomorrow.getDate() + 1);
                            tomorrow.setHours(23, 59, 0, 0);

                            document.getElementById('edit-task-date').value = tomorrow.toISOString().split('T')[0];
                            document.getElementById('edit-task-time').value = '23:59';
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
            const date = document.getElementById('task-date').value;
            const time = document.getElementById('task-time').value;

            if (!date || !time) {
                alert('Пожалуйста, укажите дату и время выполнения');
                return;
            }

            const timer = date + ' ' + time + ':00';
            document.getElementById('task-timer').value = timer;

            const formData = new FormData(document.getElementById('create-task-form'));

            const taskName = document.getElementById('task-name').value.trim();
            const taskDescription = document.getElementById('task-description').value.trim();

            if (!taskName) {
                alert('Пожалуйста, введите название задачи');
                return;
            }

            if (!taskDescription) {
                alert('Пожалуйста, введите описание задачи');
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
            const date = document.getElementById('edit-task-date').value;
            const time = document.getElementById('edit-task-time').value;

            if (!date || !time) {
                alert('Пожалуйста, укажите дату и время выполнения');
                return;
            }

            const timer = date + ' ' + time + ':00';
            document.getElementById('edit-task-timer').value = timer;

            const formData = new FormData(document.getElementById('edit-task-form'));

            const taskName = document.getElementById('edit-task-name').value.trim();
            const taskDescription = document.getElementById('edit-task-description').value.trim();

            if (!taskName) {
                alert('Пожалуйста, введите название задачи');
                return;
            }

            if (!taskDescription) {
                alert('Пожалуйста, введите описание задачи');
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
                        closeMotivation();
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

        function hideMotivation() {
            document.querySelector('#motivationModal').style.display = 'none';
            document.querySelector('.modal-backdrop').style.display = 'none';

            const date = new Date();
            date.setTime(date.getTime() + (24 * 60 * 60 * 1000));
            document.cookie = "hide_motivation=true; expires=" + date.toUTCString() + "; path=/";
        }

        function showMotivation() {
            document.cookie = "hide_motivation=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            location.reload();
        }

        function closeMotivation() {
            const modal = document.querySelector('#motivationModal');
            const backdrop = document.querySelector('.modal-backdrop');

            if (modal) {
                modal.style.display = 'none';
            }
            if (backdrop) {
                backdrop.style.display = 'none';
            }
        }

        function downloadMotivationFile() {
            // Создаем ссылку для скачивания
            const link = document.createElement('a');
            link.href = '/To-do-listAgressive/motivation/motivation.jpg';
            link.download = 'motivation.jpg';

            // Программно кликаем по ссылке
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Функция для загрузки reCAPTCHA (только при необходимости)
        function loadRecaptchaIfNeeded() {
            const overdueCount = <?php echo $overdueCount; ?>;

            if (overdueCount >= 3) {
                // Показываем контейнер для reCAPTCHA
                document.getElementById('captcha-container').style.display = 'block';

                // Динамически загружаем скрипт reCAPTCHA если он еще не загружен
                if (!document.querySelector('script[src*="recaptcha"]')) {
                    const script = document.createElement('script');
                    script.src = 'https://www.google.com/recaptcha/api.js?render=explicit&hl=ru';
                    document.body.appendChild(script);
                }

                // Инициализируем reCAPTCHA после загрузки библиотеки
                setTimeout(() => {
                    if (typeof grecaptcha !== 'undefined' && !window.recaptchaCreateWidget) {
                        window.recaptchaCreateWidget = grecaptcha.render('g-recaptcha-create', {
                            'sitekey': '6Lcd_0EsAAAAABbK4qiqzG9iOJRKQG8wcm4yWOy4', // Замените на ваш ключ
                            'theme': 'light'
                        });
                    }
                }, 1000);
            }
        }

        // Модифицируем функцию openCreateModal()
        function openCreateModal() {
            document.getElementById('create-task-form').reset();

            // Скрываем reCAPTCHA по умолчанию
            document.getElementById('captcha-container').style.display = 'none';
            document.getElementById('captcha-error').style.display = 'none';

            // Сбрасываем reCAPTCHA если она была инициализирована
            if (window.recaptchaCreateWidget && typeof grecaptcha !== 'undefined') {
                grecaptcha.reset(window.recaptchaCreateWidget);
            }

            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            tomorrow.setHours(23, 59, 0, 0);

            document.getElementById('task-date').value = tomorrow.toISOString().split('T')[0];
            document.getElementById('task-time').value = '23:59';
            document.getElementById('task-date').min = new Date().toISOString().split('T')[0];

            // Загружаем reCAPTCHA если нужно
            setTimeout(loadRecaptchaIfNeeded, 500);

            const modal = new bootstrap.Modal(document.getElementById('createTaskModal'));
            modal.show();
        }

        // Модифицируем функцию createTask()
        function createTask() {
            const overdueCount = <?php echo $overdueCount; ?>;
            let captchaResponse = null;

            // Проверяем reCAPTCHA если есть просроченные задачи
            if (overdueCount >= 3) {
                captchaResponse = grecaptcha.getResponse(window.recaptchaCreateWidget);

                if (!captchaResponse) {
                    document.getElementById('captcha-error').style.display = 'block';
                    return;
                } else {
                    document.getElementById('captcha-error').style.display = 'none';
                }
            }

            const date = document.getElementById('task-date').value;
            const time = document.getElementById('task-time').value;

            if (!date || !time) {
                alert('Пожалуйста, укажите дату и время выполнения');
                return;
            }

            const timer = date + ' ' + time + ':00';
            document.getElementById('task-timer').value = timer;

            const formData = new FormData(document.getElementById('create-task-form'));

            // Добавляем токен reCAPTCHA в formData если есть
            if (captchaResponse) {
                formData.append('g-recaptcha-response', captchaResponse);
            }

            const taskName = document.getElementById('task-name').value.trim();
            const taskDescription = document.getElementById('task-description').value.trim();

            if (!taskName) {
                alert('Пожалуйста, введите название задачи');
                return;
            }

            if (!taskDescription) {
                alert('Пожалуйста, введите описание задачи');
                return;
            }

            // Отключаем кнопку чтобы предотвратить повторные нажатия
            const createBtn = document.getElementById('create-task-btn');
            createBtn.disabled = true;
            createBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Создание...';

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
                        // Сбрасываем reCAPTCHA если была ошибка
                        if (overdueCount >= 3 && typeof grecaptcha !== 'undefined') {
                            grecaptcha.reset(window.recaptchaCreateWidget);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка создания задачи');
                })
                .finally(() => {
                    // Восстанавливаем кнопку
                    createBtn.disabled = false;
                    createBtn.innerHTML = 'Создать';
                });
        }
    </script>
</body>