<?php
// api Note
header('Content-Type: application/json');
require_once '../db.php';

$pdo    = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true);

switch ($method) {

    // READ
    case 'GET':
        if (!empty($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch() ?: ['error' => 'Not found']);
        } else {
            $stmt = $pdo->query("SELECT * FROM notes ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll());
        }
        break;

    // CREATE 
    case 'POST':
        if (empty($input['title'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Title required']);
            break;
        }
        $stmt = $pdo->prepare("INSERT INTO notes (title, content) VALUES (?, ?)");
        $stmt->execute([trim($input['title']), trim($input['content'] ?? '')]);
        $id   = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
        break;

    // UPDATE 
    case 'PUT':
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            break;
        }
        $stmt = $pdo->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([trim($input['title']), trim($input['content'] ?? ''), $input['id']]);
        echo json_encode(['success' => true]);
        break;

    // DELETE 
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID required']); break; }
        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
