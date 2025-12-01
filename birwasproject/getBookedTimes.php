<?php
require_once "db_config.php";

header('Content-Type: application/json');

$conn = getDBConnection();

$date = $_GET['date'] ?? null;
$reservationId = isset($_GET['reservation_id']) ? (int)$_GET['reservation_id'] : 0;

$times = [];

if ($date) {
    $stmt = $conn->prepare("
        SELECT time_slot
        FROM reservation
        WHERE Date = ? AND ReservationID <> ?
    ");
    $stmt->bind_param("si", $date, $reservationId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $times[] = $row['time_slot'];
    }
}

echo json_encode($times);
