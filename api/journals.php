<?php
// api/journals.php — Full CRUD for Journal entries
header('Content-Type: application/json');
require_once '../db.php';

$pdo    = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true);

switch ($method) {

    // READ — get all or single journal
    case 'GET':
        if (!empty($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM journals WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch() ?: ['error' => 'Not found']);
        } else {
            $stmt = $pdo->query("SELECT * FROM journals ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll());
        }
        break;

    // CREATE — new journal entry
    case 'POST':
        if (empty($input['title'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Title required']);
            break;
        }
        $stmt = $pdo->prepare("INSERT INTO journals (title, content) VALUES (?, ?)");
        $stmt->execute([trim($input['title']), trim($input['content'] ?? '')]);
        $id   = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM journals WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
        break;

    // UPDATE — edit journal
    case 'PUT':
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            break;
        }
        $stmt = $pdo->prepare("UPDATE journals SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([trim($input['title']), trim($input['content'] ?? ''), $input['id']]);
        echo json_encode(['success' => true]);
        break;

    // DELETE — remove journal
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID required']); break; }
        $stmt = $pdo->prepare("DELETE FROM journals WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
