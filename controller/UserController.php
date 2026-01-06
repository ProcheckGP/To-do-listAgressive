<?php
require __DIR__ . '/../server/database.php';
require __DIR__ . '/../model/UserModel.php';
require __DIR__ . '/../model/TaskModel.php';
require __DIR__ . '/Controller.php';

class UserController extends Controller
{
    private $user;
    private $task;

    public function __construct()
    {
        $this->user = new UserModel();
        $this->task = new TaskModel();
    }

    public function registration()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['username'], $_POST['password'], $_POST['email'])) {

                $existingUser = $this->user->getUserByEmail($_POST['email']);
                if (!$existingUser) {
                    $this->user->createNewUser(
                        $_POST['username'],
                        $_POST['password'],
                        $_POST['email']
                    );
                    $this->redirect('/To-do-listAgressive/index.php');
                } else {
                    $this->redirect('/To-do-listAgressive/view/forms/formRegistration.php');
                }
            }
        }
    }

    public function authorization()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['email'], $_POST['password'])) {
                $user = $this->user->verifyUser($_POST['email'], $_POST['password']);
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];

                    $this->redirect('/To-do-listAgressive/view/dashboard.php');
                } else {
                    $this->redirect('/To-do-listAgressive/view/forms/formAuthorization.php');
                }
            }
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/To-do-listAgressive/index.php');
    }

    public function dashboard()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/To-do-listAgressive/view/forms/formAuthorization.php');
            return;
        }

        $tasks = $this->task->getUserTasks($_SESSION['user_id']);
        $this->render('dashboard', ['tasks' => $tasks]);
    }

    public function createTask()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $timer = $_POST['timer'] ?? null;

            if (!empty($name)) {
                $this->task->createTask($_SESSION['user_id'], $name, $description, $timer);
                echo json_encode(['success' => true]);
                return;
            }
        }

        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }

    public function updateTask()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskId = $_POST['task_id'] ?? '';
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $timer = $_POST['timer'] ?? null;
            $isCompleted = isset($_POST['is_completed']) ? 1 : 0;

            if (!empty($taskId) && !empty($name)) {
                $this->task->updateTask($taskId, $_SESSION['user_id'], $name, $description, $timer, $isCompleted);
                echo json_encode(['success' => true]);
                return;
            }
        }

        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }

    public function toggleTask()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskId = $_POST['task_id'] ?? '';

            if (!empty($taskId)) {
                $this->task->toggleTaskCompletion($taskId, $_SESSION['user_id']);
                echo json_encode(['success' => true]);
                return;
            }
        }

        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }

    public function getTask()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authorized']);
            return;
        }

        $taskId = $_GET['task_id'] ?? '';
        if (!empty($taskId)) {
            $task = $this->task->getTaskById($taskId, $_SESSION['user_id']);
            echo json_encode(['success' => true, 'task' => $task]);
            return;
        }

        echo json_encode(['success' => false, 'message' => 'Task not found']);
    }

    public function deleteTask()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskId = $_POST['task_id'] ?? '';

            if (!empty($taskId)) {
                $this->task->deleteTask($taskId, $_SESSION['user_id']);
                echo json_encode(['success' => true]);
                return;
            }
        }

        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
}
