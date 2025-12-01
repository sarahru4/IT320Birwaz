<?php
session_start();
require_once "db_config.php";

if (!isset($_GET['reservation_id'])) {
    die("Reservation not specified.");
}

$reservationID = (int) $_GET['reservation_id'];
$conn = getDBConnection();

// جلب التاريخ والوقت القديمين
$sql = "SELECT Date, time_slot FROM reservation WHERE ReservationID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reservationID);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (!$res) {
    die("Reservation not found.");
}

$oldDate = $res['Date'];
$oldTime = $res['time_slot'];

// الأوقات المتاحة
$available_times = [
    '10:00 AM - 11:00 AM',
    '12:00 PM - 1:00 PM',
    '3:00 PM - 4:00 PM',
    '5:00 PM - 6:00 PM'
];

// لو حفظ من الفورم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $newDate = $_POST['date'];
    $newTime = $_POST['time'];

    $sql = "UPDATE reservation SET Date=?, time_slot=? WHERE ReservationID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $newDate, $newTime, $reservationID);
    $stmt->execute();

    header("Location: viewReservations.php?updated=1");
    exit();
}

// الأوقات المحجوزة لليوم الحالي (عشان أول ما تفتح الصفحة)
$stmt2 = $conn->prepare("
    SELECT time_slot 
    FROM reservation 
    WHERE Date = ? AND ReservationID != ?
");
$stmt2->bind_param("si", $oldDate, $reservationID);
$stmt2->execute();
$bookedTimesResult = $stmt2->get_result();

$bookedTimes = [];
while ($row = $bookedTimesResult->fetch_assoc()) {
    $bookedTimes[] = $row['time_slot'];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Booking</title>

<style>
body{
    font-family: Arial, sans-serif;
    background: #fff7d6;
    margin:0;
}
.container{
    max-width:600px;
    margin:60px auto;
    background:white;
    padding:25px;
    border-radius:14px;
    box-shadow:0 6px 20px rgba(0,0,0,0.1);
}
h2{
    margin-top:0;
    text-align:center;
    color:#4B0082;
}
label{
    font-weight:bold;
}
input, select{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
    margin-top:6px;
}
button{
    width:100%;
    padding:12px;
    margin-top:18px;
    border:none;
    background:#F9DA57;
    font-weight:bold;
    border-radius:8px;
    cursor:pointer;
}
button:hover{
    background:#FFD700;
}
.time-slot-box{
    padding:12px;
    border:2px solid #ddd;
    border-radius:8px;
    margin-top:8px;
    cursor:pointer;
    background:white;
}
.time-slot-box.selected{
    background:#4B0082;
    color:white;
    border-color:#4B0082;
}
.time-slot-box.booked{
    background:#e5e5e5 !important;
    color:#999 !important;
}
.time-slot-box.booked span{
    color:red;
    font-size:12px;
    font-weight:bold;
}
</style>

</head>
<body>

<div class="container">
    <h2>Edit Booking</h2>

    <form method="POST">

        <label>Select Date:</label>
        <input type="date" name="date" id="dateInput"
               value="<?php echo htmlspecialchars($oldDate); ?>" required>

        <label>Select Time:</label>

        <input type="hidden" id="selectedTime" name="time"
               value="<?php echo htmlspecialchars($oldTime); ?>" required>

        <?php foreach ($available_times as $t): ?>
            <?php
                $isBooked   = in_array($t, $bookedTimes);
                $isSelected = ($oldTime == $t);
            ?>
            <div class="time-slot-box
                <?php if ($isSelected) echo ' selected'; ?>
                <?php if ($isBooked)  echo ' booked'; ?>
            "
                 data-time="<?php echo htmlspecialchars($t); ?>"
                 style="<?php echo $isBooked ? 'pointer-events:none; opacity:0.4;' : ''; ?>"
            >
                <?php echo htmlspecialchars($t); ?>
                <?php if ($isBooked): ?>
                    <span>(Booked)</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="confirm"
                onclick="return confirm('Are you sure you want to update the booking?');">
            Save Changes
        </button>
    </form>

</div>

<script>
// عناصر HTML
const dateInput         = document.getElementById('dateInput');
const timeBoxes         = document.querySelectorAll('.time-slot-box');
const selectedTimeInput = document.getElementById('selectedTime');

// اختيار وقت
timeBoxes.forEach(box => {
    box.addEventListener('click', function () {

        // لو بوكس عليه booked لا تخليه يشتغل
        if (this.classList.contains('booked')) return;

        timeBoxes.forEach(b => b.classList.remove('selected'));
        this.classList.add('selected');

        selectedTimeInput.value = this.dataset.time;
    });
});

// عند تغيير التاريخ
dateInput.addEventListener('change', function () {
    const selectedDate = this.value;

    // فضّي الوقت المختار
    selectedTimeInput.value = '';
    timeBoxes.forEach(b => b.classList.remove('selected'));

    // جلب الأوقات المحجوزة لليوم الجديد
    fetch(
        "getBookedTimes.php?date=" +
        encodeURIComponent(selectedDate) +
        "&reservation_id=<?php echo $reservationID; ?>"
    )
        .then(res => res.json())
        .then(booked => {

            timeBoxes.forEach(box => {
                const slot = box.dataset.time;

                // إعادة ضبط الشكل
                box.classList.remove('booked');
                box.style.pointerEvents = "auto";
                box.style.opacity = "1";
                box.innerHTML = slot;

                // لو الوقت من ضمن الأوقات المحجوزة
                if (booked.includes(slot)) {
                    box.classList.add('booked');
                    box.style.pointerEvents = "none";
                    box.style.opacity = "0.4";
                    box.innerHTML = slot + " <span>(Booked)</span>";
                }
            });
        });
});
</script>

</body>
</html>
