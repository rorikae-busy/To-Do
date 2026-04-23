<?php
// api/todos.php — Todo items
header('Content-Type: application/json');
require_once '../db.php';

$pdo    = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true);

switch ($method) {

    // READ — get all todos
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM todos ORDER BY created_at DESC");
        echo json_encode($stmt->fetchAll());
        break;

    // CREATE — add new todo
    case 'POST':
        if (empty($input['task'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Task cannot be empty']);
            break;
        }
        $stmt = $pdo->prepare("INSERT INTO todos (task) VALUES (?)");
        $stmt->execute([trim($input['task'])]);
        $id   = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM todos WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
        break;

    // UPDATE — toggle done/undone
    case 'PUT':
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            break;
        }
        $stmt = $pdo->prepare("UPDATE todos SET is_done = ? WHERE id = ?");
        $stmt->execute([$input['is_done'], $input['id']]);
        echo json_encode(['success' => true]);
        break;

    // DELETE — remove a todo
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID required']); break; }
        $stmt = $pdo->prepare("DELETE FROM todos WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
