<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

// جلب بيانات التصميم من الـ design_id
$design_id = $_GET['design_id'] ?? $_SESSION['last_design_id'] ?? null;
$design_data = null;
$total_cost = 50; // السعر الأساسي

if ($design_id) {
    // جلب بيانات التصميم الأساسية
    $stmt = $conn->prepare("SELECT * FROM design WHERE DesignID = ?");
    $stmt->bind_param("i", $design_id);
    $stmt->execute();
    $design_result = $stmt->get_result();
    $design_data = $design_result->fetch_assoc();
    $stmt->close();

    if ($design_data && $design_data['Type'] == 'customized') {
        // جلب بيانات الكستمايزد
        $stmt = $conn->prepare("SELECT * FROM customizedesign WHERE DesignID = ?");
        $stmt->bind_param("i", $design_id);
        $stmt->execute();
        $custom_result = $stmt->get_result();
        $custom_data = $custom_result->fetch_assoc();
        $stmt->close();

        if ($custom_data) {
            $total_cost += $custom_data['background_price'] + $custom_data['lightning_price'] + $custom_data['decoration_price'];
        }
    }
}

// تعريف الأوقات المتاحة
$available_times = ['10:00 AM - 11:00 AM', '12:00 PM - 1:00 PM', '3:00 PM - 4:00 PM', '5:00 PM - 6:00 PM'];

// جلب الحجوزات الموجودة للتحقق من الأوقات المحجوزة
$booked_slots = [];
$stmt = $conn->prepare("SELECT Date, time_slot FROM reservation WHERE statues = 'confirmed'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $booked_slots[$row['Date']][] = $row['time_slot'];
}
$stmt->close();
$conn->close();

// معالجة الحجز
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();

    $reservation_date = $_POST['date'];
    $time_slot = $_POST['time'];
    $total_price = floatval($_POST['total_price']);
    $design_id = $_POST['design_id'];

    try {
        $conn->begin_transaction();

        // إدخال الحجز
        $stmt = $conn->prepare("
    INSERT INTO reservation (UserID, DesignID, Date, time_slot, total_price, statues)
    VALUES (?, ?, ?, ?, ?, 'confirmed')
");
        $stmt->bind_param("iissd", $user_id, $design_id, $reservation_date, $time_slot, $total_price);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting reservation: " . $stmt->error);
        }

        $reservation_id = $conn->insert_id;
        $stmt->close();

        // إدخال إشعار
        $message = "Your studio reservation #$reservation_id is confirmed for $reservation_date at $time_slot";
        $stmt = $conn->prepare("INSERT INTO notification (ReservationID, Message, Scheduled_At) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $reservation_id, $message);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => "✅ Booking confirmed! Reservation ID: #$reservation_id",
            'reservation_id' => $reservation_id
        ]);
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => "❌ Booking error: " . $e->getMessage()
        ]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BIRWAZ - Book Your Studio</title>
        <style>
            * {
                box-sizing: border-box;
                margin: 0;
            }
            body {
                font-family: Inter, system-ui, Arial, sans-serif;
                color: #1f2937;
                background: radial-gradient(1200px 600px at 80% -10%, #fff 0%, #fff6bf 40%, #fffbeb 65%);
            }
            a {
                color: inherit;
                text-decoration: none;
            }
            .container {
                max-width: 1100px;
                margin: auto;
                padding: 18px 22px;
            }

            header {
                background: linear-gradient(90deg, #EBE389, #fae588);
                border-bottom: 1px solid rgba(0,0,0,.05);
                box-shadow: 0 6px 20px rgba(245,158,11,.15);
            }
            .nav {
                display: flex;
                align-items: center;
                justify-content: space-between;
                min-height: 64px;
            }
            .brand {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .brand img {
                height: 55px;
                background: #fff;
                padding: 1px;
                border-radius: 12px;
                border: 1px solid #f1e9b6;
                box-shadow: 0 4px 10px rgba(245,158,11,.12);
            }
            .brand strong {
                font-weight: 900;
                color: #111;
                font-size: clamp(18px, 2.4vw, 20px);
            }
            .links {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }
            .links a {
                padding: 8px 12px;
                border-radius: 12px;
                font-weight: 600;
                border: 1px solid transparent;
                transition: .2s;
            }
            .links a:hover {
                background: rgba(255,255,255,.7);
                border-color: #fef3c7;
            }

            .booking-container {
                max-width: 700px;
                background: #fff;
                margin: 60px auto;
                border-radius: 16px;
                padding: 25px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            }

            .back-btn {
                padding: 10px 20px;
                background-color: #F9DA57;
                color: #333;
                font-weight: 700;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(249,218,87,0.4);
                transition: background-color 0.2s, transform 0.2s;
                display: inline-block;
                margin-bottom: 20px;
            }
            .back-btn:hover {
                background-color: #FFD700;
                transform: translateY(-2px);
            }

            .header-row {
                display: flex;
                align-items: center;
                gap: 18px;
                margin-bottom: 25px;
            }

            h1 {
                color: #4B0082;
            }
            label {
                display: block;
                margin: 15px 0 5px;
                font-weight: 600;
                text-align: left;
            }
            input, select {
                width: 100%;
                padding: 10px;
                border-radius: 8px;
                border: 1px solid #ccc;
            }

            .preview-box {
                margin-top: 20px;
                border: 1px solid #eee;
                border-radius: 12px;
                padding: 15px;
                background-color: #fafafa;
            }
            .preview-content {
                display: flex;
                align-items: center;
                gap: 20px;
                text-align: left;
            }
            .design-preview {
                width: 200px;
                height: 150px;
                border-radius: 12px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                border: 2px solid #ddd;
                background-size: cover;
                background-position: center;
            }
            .design-placeholder {
                width: 200px;
                height: 150px;
                background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #666;
                font-weight: bold;
                border: 2px dashed #ccc;
            }
            .preview-text {
                flex: 1;
            }
            .preview-text p {
                margin: 8px 0;
                font-size: 16px;
            }

            .buttons {
                margin-top: 20px;
                display: flex;
                justify-content: space-between;
            }
            button {
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                cursor: pointer;
                font-weight: 600;
                transition: 0.2s;
            }
            .confirm {
                background-color: #F9DA57;
            }
            .confirm:hover {
                background-color: #FFD700;
            }
            .cancel {
                background-color: #eee;
            }
            .cancel:hover {
                background-color: #ddd;
            }

            .price-details {
                background: #f8f9fa;
                border-radius: 8px;
                padding: 15px;
                margin-top: 15px;
                border-left: 4px solid #4B0082;
            }
            .price-item {
                display: flex;
                justify-content: space-between;
                margin: 5px 0;
            }
            .price-total {
                border-top: 2px solid #4B0082;
                margin-top: 10px;
                padding-top: 10px;
                font-weight: bold;
                font-size: 18px;
                color: #4B0082;
            }

            .time-slots {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                margin-top: 10px;
            }
            .time-slot {
                padding: 12px;
                border: 2px solid #ddd;
                border-radius: 8px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s;
            }
            .time-slot:hover {
                border-color: #F9DA57;
                background: #fffdf0;
            }
            .time-slot.selected {
                border-color: #4B0082;
                background: #4B0082;
                color: white;
            }
            .time-slot.booked {
                background: #f8d7da;
                color: #721c24;
                border-color: #f5c6cb;
                cursor: not-allowed;
            }

            /* Footer Styles */
            footer {
                background:#f6eaa6;
                border-top:1px solid #f1e9b6;
                margin-top:50px;
            }
            .footer-inner {
                max-width:1100px;
                margin:auto;
                padding:18px 22px;
                display:flex;
                align-items:center;
                justify-content:space-between;
                flex-wrap:wrap;
            }
            .socials {
                display:flex;
                gap:12px;
                align-items:center;
            }
            .socials a {
                width:48px;
                height:48px;
                border-radius:50%;
                display:grid;
                place-items:center;
                background: linear-gradient(180deg,#fff8d6,#fffbe6);
                border:1px solid #f1e9b6;
                box-shadow:0 6px 14px rgba(245,158,11,.12);
                transition:transform .15s ease, box-shadow .15s ease, background .2s ease;
            }
            .socials a:hover {
                transform:translateY(-2px);
                box-shadow:0 10px 20px rgba(245,158,11,.18);
                background:#fff;
            }
            .socials img {
                width:22px;
                height:22px;
                object-fit:contain;
            }

            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 10px;
                color: white;
                font-weight: 600;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                z-index: 1000;
                max-width: 350px;
                display: flex;
                align-items: center;
                gap: 10px;
                transform: translateX(400px);
                transition: transform 0.3s ease;
            }
            .notification.show {
                transform: translateX(0);
            }
            .notification.success {
                background: linear-gradient(135deg, #4CAF50, #45a049);
                border-left: 5px solid #2E7D32;
            }
            .notification.warning {
                background: linear-gradient(135deg, #ff9800, #e68900);
                border-left: 5px solid #E65100;
            }

            /* Cancel Confirmation Modal */
            .cancel-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2000;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
            .cancel-modal.show {
                opacity: 1;
                visibility: visible;
            }
            .cancel-modal-content {
                background: white;
                border-radius: 16px;
                padding: 30px;
                max-width: 400px;
                width: 90%;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                transform: translateY(-20px);
                transition: transform 0.3s ease;
            }
            .cancel-modal.show .cancel-modal-content {
                transform: translateY(0);
            }
            .cancel-modal-header {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 20px;
            }
            .cancel-modal-icon {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #ff9800, #e68900);
                color: white;
                font-size: 24px;
            }
            .cancel-modal-title {
                font-size: 20px;
                font-weight: bold;
                color: #4B0082;
                margin: 0;
            }
            .cancel-modal-message {
                margin-bottom: 25px;
                line-height: 1.6;
                color: #555;
            }
            .cancel-modal-buttons {
                display: flex;
                gap: 15px;
                justify-content: flex-end;
            }
            .cancel-modal-btn {
                padding: 10px 20px;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .cancel-modal-btn.no {
                background: #f0f0f0;
                color: #666;
            }
            .cancel-modal-btn.no:hover {
                background: #e0e0e0;
            }
            .cancel-modal-btn.yes {
                background: #F9DA57;
                color: #333;
            }
            .cancel-modal-btn.yes:hover {
                background: #FFD700;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(249,218,87,0.4);
            }

            @media (max-width: 768px) {
                .booking-container {
                    margin: 30px 20px;
                    padding: 20px;
                }
                .preview-content {
                    flex-direction: column;
                    text-align: center;
                }
                .time-slots {
                    grid-template-columns: 1fr;
                }
                .footer-inner {
                    flex-direction: column;
                    gap: 15px;
                    text-align: center;
                }
                .cancel-modal-buttons {
                    flex-direction: column;
                }
            }
        </style>
    </head>
    <body>
        <header>
            <div class="container nav">
                <a class="brand" href="homeUser.php">
                    <img src="images/logo1.png" alt="BIRWAZ logo">
                    <strong>BIRWAZ</strong>
                </a>
                <nav class="links">
                    <a href="homeUser.php">Home</a>
                    <a href="Designs.php">Designs</a>
                    <a href="viewReservations.php">View Reservations</a>
                    <a href="logout.php">Log Out</a>
                </nav>
            </div>
        </header>

        <main>
            <div class="booking-container">
                <a href="javascript:history.back()" class="back-btn">← Back</a>
                <h1>Book Your Studio</h1>

<?php if ($design_data): ?>
                    <div class="preview-box">
                        <div class="preview-content">
    <?php if ($design_data['Image_URL']): ?>
                                <div class="design-preview" style="background-image:url('uploads/<?= $design_data['Image_URL'] ?>')"></div>
                            <?php else: ?>
                                <div class="design-placeholder">Design Preview</div>
                            <?php endif; ?>
                            <div class="preview-text">
                                <p><strong>Design:</strong> <?= htmlspecialchars($design_data['Name']) ?></p>
                                <p><strong>Type:</strong> <?= htmlspecialchars($design_data['Type']) ?></p>
                                <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($design_data['Description'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="price-details">
                        <h3 style="margin-top: 0; color: #4B0082;">Price Breakdown</h3>
                        <div class="price-item"><span>Studio Session Fee</span><span>50 SAR</span></div>
    <?php if ($design_data['Type'] == 'customized' && isset($custom_data)): ?>
                            <div class="price-item"><span>Background</span><span><?= $custom_data['background_price'] ?> SAR</span></div>
                            <div class="price-item"><span>Lighting</span><span><?= $custom_data['lightning_price'] ?> SAR</span></div>
                            <div class="price-item"><span>Decorations</span><span><?= $custom_data['decoration_price'] ?> SAR</span></div>
    <?php endif; ?>
                        <div class="price-total"><span>Total Price</span><span><?= $total_cost ?> SAR</span></div>
                    </div>
<?php else: ?>
                    <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin: 20px 0;">
                        ❌ No design selected. Please go back and choose a design first.
                    </div>
<?php endif; ?>

                <form id="bookingForm" method="POST" >
                    <input type="hidden" name="design_id" value="<?= $design_id ?>">
                    <input type="hidden" name="total_price" value="<?= $total_cost ?>">

                    <label for="date">Select Date:</label>
                    <input type="date" id="date" name="date" required>

                    <label for="time">Select Time Slot:</label>
                    <div class="time-slots" id="timeSlots">
<?php foreach ($available_times as $time): ?>
                            <div class="time-slot" data-time="<?= $time ?>"><?= $time ?></div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="selectedTime" name="time" required>

                    <div class="buttons">
                        <button type="button" class="cancel" onclick="showCancelConfirmation()">Cancel</button>
                        <button type="submit" class="confirm" <?= !$design_data ? 'disabled' : '' ?>>Confirm Booking</button>
                    </div>
                </form>
            </div>
        </main>

        <!-- Footer -->
        <footer>
            <div class="footer-inner">
                <div class="copy">© 2025 BIRWAZ. All rights reserved.</div>
                <div class="socials">
                    <a href="https://twitter.com" target="_blank"><img src="images/twitter.png" alt="Twitter"></a>
                    <a href="https://instagram.com" target="_blank"><img src="images/instagram.png" alt="Instagram"></a>
                    <a href="https://wa.me" target="_blank"><img src="images/whatsapp.png" alt="WhatsApp"></a>
                    <a href="mailto:birwaz@gmail.com" target="_blank"><img src="images/email.png" alt="Email"></a>
                </div>
            </div>
        </footer>

        <!-- Cancel Confirmation Modal -->
        <div class="cancel-modal" id="cancelModal">
            <div class="cancel-modal-content">
                <div class="cancel-modal-header">
                    <div class="cancel-modal-icon">⚠️</div>
                    <h3 class="cancel-modal-title">Cancel Booking</h3>
                </div>
                <div class="cancel-modal-message">
                    Are you sure you want to cancel your booking? All your booking progress will be lost.
                </div>
                <div class="cancel-modal-buttons">
                    <button type="button" class="cancel-modal-btn no" onclick="hideCancelConfirmation()">No, Keep Booking</button>
                    <button type="button" class="cancel-modal-btn yes" onclick="confirmCancel()">Yes, Cancel</button>
                </div>
            </div>
        </div>

        <script>
            const bookedSlots = <?= json_encode($booked_slots) ?>;

            // دوال نافذة التأكيد
            function showCancelConfirmation() {
                document.getElementById('cancelModal').classList.add('show');
            }

            function hideCancelConfirmation() {
                document.getElementById('cancelModal').classList.remove('show');
            }

            function confirmCancel() {
                hideCancelConfirmation();
                window.location.href = 'homeUser.php';
            }

            // إغلاق النافذة عند الضغط خارجها أو زر Escape
            document.getElementById('cancelModal').addEventListener('click', function (e) {
                if (e.target === this)
                    hideCancelConfirmation();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape')
                    hideCancelConfirmation();
            });

            document.addEventListener('DOMContentLoaded', function () {
                const dateInput = document.getElementById('date');
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                dateInput.min = tomorrow.toISOString().split('T')[0];
                dateInput.value = tomorrow.toISOString().split('T')[0];

                updateTimeSlots();

                dateInput.addEventListener('change', updateTimeSlots);

                // اختيار الوقت
                document.querySelectorAll('.time-slot').forEach(slot => {
                    slot.addEventListener('click', function () {
                        if (!this.classList.contains('booked')) {
                            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                            this.classList.add('selected');
                            document.getElementById('selectedTime').value = this.dataset.time;
                        }
                    });
                });
            });

            function updateTimeSlots() {
                const selectedDate = document.getElementById('date').value;
                const bookedTimes = bookedSlots[selectedDate] || [];

                document.querySelectorAll('.time-slot').forEach(slot => {
                    const time = slot.dataset.time;
                    if (bookedTimes.includes(time)) {
                        slot.classList.add('booked');
                        slot.classList.remove('selected');
                        slot.innerHTML = time + '<br><small>Not Available</small>';
                    } else {
                        slot.classList.remove('booked');
                        slot.innerHTML = time;
                    }
                });

                // إعادة تعيين الوقت المختار
                document.getElementById('selectedTime').value = '';
                document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
            }

            // إرسال النموذج
            document.getElementById('bookingForm').addEventListener('submit', function (e) {
                e.preventDefault();

                if (!document.getElementById('selectedTime').value) {
                    showNotification('Please select a time slot', 'warning');
                    return;
                }

                fetch('', {method: 'POST', body: new FormData(this)})
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification(data.message, 'success');
                                setTimeout(() => window.location.href = 'viewReservations.php', 2000);
                            } else {
                                showNotification(data.message, 'warning');
                            }
                        })
                        .catch(error => {
                            showNotification('Booking error. Please try again.', 'warning');
                        });
            });

            function showNotification(message, type = 'success', duration = 4000) {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.innerHTML = message;
                document.body.appendChild(notification);
                setTimeout(() => notification.classList.add('show'), 100);
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => document.body.removeChild(notification), 300);
                }, duration);
            }
        </script>
    </body>
</html>