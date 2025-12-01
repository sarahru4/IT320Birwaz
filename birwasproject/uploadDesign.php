<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: addDesignsAdmin.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if (!$name || !$description || !isset($_FILES['image'])) {
    die('Missing required fields.');
}

$file = $_FILES['image'];
$allowed = ['image/jpeg','image/png','image/gif','image/webp'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    die('Upload error (code: ' . $file['error'] . ').');
}

$allowedExt = ['jpg','jpeg','png','gif','webp'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExt)) {
    die('Invalid image type (extension not allowed).');
}

$targetDir = __DIR__ . '/uploads/';
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'design_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$targetPath = $targetDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    die('Failed to move uploaded file.');
}

$conn = getDBConnection();
$stmt = $conn->prepare("INSERT INTO design (Name, Description, Image_URL, Type) VALUES (?, ?, ?, 'Ready')");
$stmt->bind_param('sss', $name, $description, $filename);
$exec = $stmt->execute();
$stmt->close();
$conn->close();

header('Location: viewDesignsAdmin.php?added=1');
exit;
