<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['reservation_id'])) {
    die("Reservation ID missing");
}

$reservationId = intval($_GET['reservation_id']);
$conn = getDBConnection();
$conn->begin_transaction();

try {

    $stmt0 = $conn->prepare("SELECT DesignID FROM reservation WHERE ReservationID = ?");
    $stmt0->bind_param("i", $reservationId);
    $stmt0->execute();
    $stmt0->bind_result($designId);
    $stmt0->fetch();
    $stmt0->close();

    if (!$designId) {
        throw new Exception("DesignID not found");
    }

    $stmtType = $conn->prepare("SELECT Type FROM design WHERE DesignID = ?");
    $stmtType->bind_param("i", $designId);
    $stmtType->execute();
    $stmtType->bind_result($designType);
    $stmtType->fetch();
    $stmtType->close();

    if (!$designType) {
        throw new Exception("Design Type not found");
    }

    $stmt1 = $conn->prepare("DELETE FROM reservation WHERE ReservationID = ?");
    $stmt1->bind_param("i", $reservationId);
    $stmt1->execute();

    if ($designType === "customized") {

        $stmt2 = $conn->prepare("DELETE FROM customizedesign WHERE DesignID = ?");
        $stmt2->bind_param("i", $designId);
        $stmt2->execute();

        $stmt3 = $conn->prepare("DELETE FROM design WHERE DesignID = ?");
        $stmt3->bind_param("i", $designId);
        $stmt3->execute();
    }

    $conn->commit();

    header("Location: viewReservations.php?deleted=1");
    exit;

} catch (Exception $e) {

    $conn->rollback();
    echo "Error deleting reservation: " . $e->getMessage();
}
?>
