<?php
require __DIR__ . '/../server/database.php';
require __DIR__ . '/../model/UserModel.php';
require __DIR__ . '/Controller.php';

class UserController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = new UserModel();
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
                    session_start();
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
        session_start();
        session_destroy();
        $this->redirect('/To-do-listAgressive/index.php');
    }
}
