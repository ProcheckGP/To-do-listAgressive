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
} elseif ($action === 'dashboard') {
    $controller->dashboard();
} elseif ($action === 'create_task') {
    $controller->createTask();
} elseif ($action === 'update_task') {
    $controller->updateTask();
} elseif ($action === 'toggle_task') {
    $controller->toggleTask();
} elseif ($action === 'get_task') {
    $controller->getTask();
} elseif ($action === 'delete_task') {
    $controller->deleteTask();
} else {
    header("Location: /To-do-listAgressive/index.php");
    exit();
}
