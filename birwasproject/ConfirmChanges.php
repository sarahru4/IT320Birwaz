<?php
session_start();

$host = 'localhost';
$dbname = 'birwaz';
$username = 'root';
$password = 'root';
$port = 8889;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$reservation_id = $_GET['reservation_id'] ?? null;

// بيانات الحجز الحالية لو كان تعديل
$existingReservation = null;
if ($reservation_id) {
    $stmt = $pdo->prepare("SELECT Date, time_slot, total_price FROM reservation WHERE ReservationID = ? AND UserID = ?");
    $stmt->execute([$reservation_id, $user_id]);
    $existingReservation = $stmt->fetch(PDO::FETCH_ASSOC);
}

// الأوقات المتاحة
try {
    $stmt = $pdo->query("SELECT time_slot FROM available_times WHERE is_available = TRUE");
    $available_times = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) {
    $available_times = ['10:00 AM - 11:00 AM', '12:00 PM - 1:00 PM', '3:00 PM - 4:00 PM'];
}

//  إنشاء أو تعديل الحجز
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $reservation_date = $_POST['date'];
    $time_slot = $_POST['time'];
    $total_price = $_POST['total_price'];
    $design_data = $_POST['design_data'];
    $design_image = $_POST['design_image'];

    try {
        $pdo->beginTransaction();

        if ($reservation_id) {
            // تحديث حجز موجود
            $stmt = $pdo->prepare("UPDATE reservation SET Date = ?, time_slot = ?, total_price = ?, statues = 'confirmed' WHERE ReservationID = ? AND UserID = ?");
            $stmt->execute([$reservation_date, $time_slot, $total_price, $reservation_id, $user_id]);
        } else {
            // إنشاء حجز جديد
            $stmt = $pdo->prepare("INSERT INTO reservation (UserID, Date, time_slot, total_price, statues) VALUES (?, ?, ?, ?, 'confirmed')");
            $stmt->execute([$user_id, $reservation_date, $time_slot, $total_price]);
            $reservation_id = $pdo->lastInsertId();
        }

        $design_array = json_decode($design_data, true);
        $background_price = $design_array['background'] == 'white' ? 0 : ($design_array['background'] == 'beige' ? 25 : ($design_array['background'] == 'black' ? 30 : 20));
        $lighting_price = $design_array['lighting'] == 'white' ? 0 : ($design_array['lighting'] == 'yellow' ? 15 : 10);

        $stmt = $pdo->prepare("REPLACE INTO designs_details (ReservationID, background_type, background_price, lighting_type, lighting_price, design_data, design_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$reservation_id, $design_array['background'], $background_price, $design_array['lighting'], $lighting_price, $design_data, $design_image]);

        $pdo->commit();

        echo json_encode(['success' => true, 'reservation_id' => $reservation_id]);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Confirm Design - BIRWAZ</title>
</head>
<body>
<script>
const baseSessionPrice = 50;

document.addEventListener('DOMContentLoaded', function() {
  loadDesignData();
});

function loadDesignData() {
  const designData = localStorage.getItem('birwaz_current_design');
  const designImage = localStorage.getItem('birwaz_design_image');

  document.getElementById('designDataInput').value = designData;
  document.getElementById('designImageInput').value = designImage;
}

document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('confirmForm').addEventListener('submit', function(e) {
    e.preventDefault();

    fetch(window.location.href, {
      method: 'POST',
      body: new FormData(this)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            alert('✅ Design Updated Successfully');
            window.location.href = 'viewReservations.php';
        } else {
            alert('❌ ' + data.message);
        }
    });
  });
});
</script>

<form id="confirmForm" method="POST">

    <label>Edit Reservation Date</label><br>
    <input type="date" name="date" value="<?= htmlspecialchars($currentDate) ?>" required>

    <br><br>

    <label>Edit Reservation Time</label><br>
    <select name="time" required>
        <?php foreach($available_times as $time): ?>
        <option value="<?= htmlspecialchars($time) ?>" 
            <?= ($time == $currentTime) ? 'selected' : '' ?>>
            <?= htmlspecialchars($time) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <input type="hidden" name="total_price" value="115">
    <input type="hidden" id="designDataInput" name="design_data">
    <input type="hidden" id="designImageInput" name="design_image">

    <br><br>
    <button type="submit">Confirm Changes</button>
</form>
</body>
</html>
