<?php
require __DIR__ . '/controller/UserController.php';

$action = $_GET['action'] ?? '';

$controller = new UserController();

if ($action === 'register') {
    $controller->registration();
} elseif ($action === 'login') {
    $controller->authorization();
} elseif ($action === 'logout') {
    $controller->logout();
} else {
    header("Location: /To-do-listAgressive/view/forms/formAuthorization.php");
    exit();
}
