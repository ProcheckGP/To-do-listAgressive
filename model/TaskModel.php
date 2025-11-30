<?php
class TaskModel
{
    protected $database;

    public function __construct()
    {
        return $this->database = Database::getInstance();
    }

    public function createTask($userId, $name, $description, $timer)
    {
        return Database::execute(
            "INSERT INTO `tasks` (user_id, name, description, timer) VALUES (?, ?, ?, ?)",
            [$userId, $name, $description, $timer]
        );
    }

    public function getUserTasks($userId)
    {
        $stmt = Database::execute(
            "SELECT * FROM `tasks` WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
        return $stmt->fetchAll();
    }

    public function getTaskById($taskId, $userId)
    {
        $stmt = Database::execute(
            "SELECT * FROM `tasks` WHERE id = ? AND user_id = ?",
            [$taskId, $userId]
        );
        return $stmt->fetch();
    }

    public function updateTask($taskId, $userId, $name, $description, $timer, $isCompleted)
    {
        return Database::execute(
            "UPDATE `tasks` SET name = ?, description = ?, timer = ?, is_completed = ? WHERE id = ? AND user_id = ?",
            [$name, $description, $timer, $isCompleted, $taskId, $userId]
        );
    }

    public function deleteTask($taskId, $userId)
    {
        return Database::execute(
            "DELETE FROM `tasks` WHERE id = ? AND user_id = ?",
            [$taskId, $userId]
        );
    }

    public function toggleTaskCompletion($taskId, $userId)
    {
        $task = $this->getTaskById($taskId, $userId);
        if ($task) {
            $newStatus = !$task['is_completed'];
            return Database::execute(
                "UPDATE `tasks` SET is_completed = ? WHERE id = ? AND user_id = ?",
                [$newStatus, $taskId, $userId]
            );
        }
        return false;
    }
}
