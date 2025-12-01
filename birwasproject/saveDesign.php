<?php
require_once 'db_config.php';

$data = json_decode(file_get_contents("php://input"), true);

$reservation_id = $data['reservation_id'];
$designJson = json_encode($data['design']);
$totalPrice = $data['total_price'];

$stmt = $pdo->prepare("
  UPDATE designs_details
  SET design_data = ?
  WHERE ReservationID = ?
");
$stmt->execute([$designJson, $reservation_id]);

$stmt2 = $pdo->prepare("
  UPDATE reservation
  SET total_price = ?
  WHERE ReservationID = ?
");
$stmt2->execute([$totalPrice, $reservation_id]);

echo json_encode(['status' => 'success']);
