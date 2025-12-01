<?php
session_start();
require_once 'db_config.php';
header('Content-Type: application/json');


try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=birwaz;charset=utf8",
        "root",
        "root",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'DB Connection Failed']);
    exit;
}


$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['reservation_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid Data']);
    exit;
}

$reservationId = $data['reservation_id'];
$background    = $data['background_type'];
$lighting      = $data['lighting_type'];
$designData    = $data['design_data'];
$designImage   = $data['design_image']; 

$stmt = $pdo->prepare("SELECT DesignID FROM reservation WHERE ReservationID=?");
$stmt->execute([$reservationId]);
$DesignID = $stmt->fetchColumn();

if (!$DesignID) {
    echo json_encode(['success' => false, 'error' => 'DesignID not found']);
    exit;
}


$folder = "customizedesign/";
if (!file_exists($folder)) {
    mkdir($folder, 0777, true);
}

$cleanBase64 = preg_replace('#^data:image/\w+;base64,#i', '', $designImage);
$cleanBase64 = str_replace(' ', '+', $cleanBase64);

$decodedImg = base64_decode($cleanBase64);

if (!$decodedImg) {
    echo json_encode(['success' => false, 'error' => 'Base64 decode failed']);
    exit;
}

$filename = "design_" . time() . ".png";
$fullPath = $folder . $filename;

file_put_contents($fullPath, $decodedImg);

try {
    $stmt = $pdo->prepare("
        UPDATE customizedesign
        SET 
            Background       = ?,
            Lighting         = ?,
            design_data      = ?,
            design_image     = ?
        WHERE DesignID = ?
    ");

    $stmt->execute([
        $background,
        $lighting,
        $designData,
        $filename,   // 
        $DesignID
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => "customizedesign update error: " . $e->getMessage()]);
    exit;
}


$stmt = $pdo->prepare("UPDATE design SET Image_URL = ? WHERE DesignID = ?");
$stmt->execute([$filename, $DesignID]);

echo json_encode(['success' => true]);
?>
