<?php
// deleteDesign.php
require_once 'db_config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['design_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$design_id = intval($input['design_id']);
$conn = getDBConnection();

// check reservations that reference this design
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM reservation WHERE DesignID = ?");
$stmt->bind_param('i', $design_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if ($row && intval($row['cnt']) > 0) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete design: there are existing reservations that use this design. Cancel those reservations first.']);
    $conn->close();
    exit;
}

// fetch image name
$stmt = $conn->prepare("SELECT Image_URL FROM design WHERE DesignID = ?");
$stmt->bind_param('i', $design_id);
$stmt->execute();
$res = $stmt->get_result();
$d = $res->fetch_assoc();
$stmt->close();

if (!$d) {
    echo json_encode(['success' => false, 'message' => 'Design not found']);
    $conn->close();
    exit;
}

// delete record
$stmt = $conn->prepare("DELETE FROM design WHERE DesignID = ?");
$stmt->bind_param('i', $design_id);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    if (!empty($d['Image_URL'])) {
        $imgPath = __DIR__ . '/uploads/' . $d['Image_URL'];
        if (file_exists($imgPath)) @unlink($imgPath);
    }
    echo json_encode(['success' => true, 'message' => 'Design deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete design: database error']);
}

$conn->close();
