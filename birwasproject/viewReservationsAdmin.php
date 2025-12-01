<?php
// viewReservationsAdmin.php
require_once 'db_config.php';
$conn = getDBConnection();

$sql = "SELECT r.ReservationID, r.Date, r.time_slot, r.total_price, r.statues, u.Name AS user_name, u.Email, u.Phone, d.Name AS design_name, d.Image_URL
        FROM reservation r
        JOIN `user` u ON r.UserID = u.UserID
        LEFT JOIN design d ON r.DesignID = d.DesignID
        ORDER BY r.Date ASC, r.time_slot ASC";
$res = $conn->query($sql);
$reservations = [];
if ($res) {
    while ($row = $res->fetch_assoc()) $reservations[] = $row;
    $res->free();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>View Reservations - Admin</title>

<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:Inter,Arial,sans-serif}
body{background:radial-gradient(1200px 600px at 80% -10%, #fff 0%, #fff6bf 40%, #fffbeb 65%);color:#1f2937;}
header{background:linear-gradient(90deg,#EBE389,#fae588);border-bottom:1px solid rgba(0,0,0,.06);box-shadow:0 6px 20px rgba(245,158,11,.15);}
.nav{max-width:1100px;margin:auto;padding:18px 22px;display:flex;justify-content:space-between;align-items:center;}
.brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
.brand img{height:55px;background:white;padding:1px;border-radius:12px;border:1px solid #f1e9b6;}
.brand strong{font-weight:900;color:#111;font-size:22px;}
.links{display:flex;gap:12px;}
.links a{padding:8px 14px;border-radius:12px;color:#111;text-decoration:none;font-weight:600;border:1px solid transparent;}
.links a:hover{border-color:#d9c46a;}
.hero{max-width:1100px;margin:auto;padding:28px 22px 10px;}
.hero h1{font-size:30px;margin-bottom:6px;}
.hero p{color:#6b7280;}
.toolbar{max-width:1100px;margin:auto;padding:0 22px;margin-top:14px;margin-bottom:14px;display:flex;justify-content:flex-start;}
.btn{background:linear-gradient(90deg,#EBE389,#fae588);border:1px solid #f1e9b6;padding:10px 16px;border-radius:14px;cursor:pointer;font-weight:700;box-shadow:0 5px 12px rgba(245,158,11,.3);}
.grid{max-width:1100px;margin:auto;padding:10px 22px 40px;display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;}
.card{background:linear-gradient(180deg,#fff8d6,#fffbe6);border:1px solid #f1e9b6;border-radius:18px;box-shadow:0 10px 24px rgba(245,158,11,.12);overflow:hidden;display:flex;flex-direction:column;}
.thumb{width:100%;height:250px;border-bottom:1px solid #f1e9b6;}
.thumb img{width:100%;height:100%;object-fit:cover;display:block;}
.body{padding:16px;flex:1;display:flex;flex-direction:column;gap:10px;}
.body h3{font-size:18px;margin-bottom:4px;}
.body p{font-size:14px;color:#6b7280;}
.delete-btn{margin-top:auto;background:white;border:1px solid #fca5a5;color:#b91c1c;padding:10px;text-align:center;border-radius:12px;cursor:pointer;font-weight:700;}
.notice{text-align:center;padding:14px;border-radius:12px;border:1px dashed #d9c46a;background:#fffce8;color:#7b6a00;}
.modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.35);display:flex;justify-content:center;align-items:center;opacity:0;visibility:hidden;transition:.2s;z-index:1000;}
.modal-overlay.active{opacity:1;visibility:visible;}
.modal{background:#fffbe6;padding:28px 32px;width:300px;border-radius:16px;text-align:center;box-shadow:0 10px 24px rgba(245,158,11,.3);}
.modal h2{margin-bottom:10px;}
.modal p{color:#a2771e;margin-bottom:18px;}
.modal-buttons{display:flex;justify-content:space-between;}
.modal-buttons button{padding:10px 20px;border-radius:12px;border:none;cursor:pointer;font-weight:700;}
.yes{background:#fca5a5;color:#7f1d1d;}
.no{background:#fde68a;color:#7b5c0c;}
/* Footer with socials */
            footer{
                background:#f6eaa6;
                border-top:1px solid #f1e9b6;
                margin-top:28px;
            }
            .footer-inner{
                max-width:1100px;
                margin:auto;
                padding:18px 22px;
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:12px;
                flex-wrap:wrap;
            }
            .copy{
                color:#374151;
                font-size:16px
            }
            .socials{
                display:flex;
                gap:12px;
                align-items:center;
            }
            .socials a{
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
            .socials a:hover{
                transform:translateY(-2px);
                box-shadow:0 10px 20px rgba(245,158,11,.18);
                background:#fff;
            }
            .socials img{
                width:22px;
                height:22px;
                object-fit:contain;
                display:block;
            }
</style>
</head>
<body>


<header>
    <div class="nav">
        <a class="brand" href="#">
            <img src="images/logo1.png">
            <strong>BIRWAZ</strong>
        </a>
        <div class="links">
            <a href="viewDesignsAdmin.php">View Designs</a>
            <a href="viewReservationsAdmin.php">View Reservation</a>
            <a href="logout.php" id="logoutLink">Log Out</a>

        </div>
    </div>
</header>


<section class="hero">
    <h1>View Reservations</h1>
    <p> all user reservations.</p>
</section>

<section class="grid">
<?php if(count($reservations) === 0): ?>
    <div class="notice" style="grid-column:1/-1;">No reservations available.</div>
<?php else: ?>
    <?php foreach($reservations as $r):
        $img = 'uploads/' . ($r['Image_URL'] ?: 'placeholder.png');
        if(!file_exists($img)) $img = 'uploads/placeholder.png';
    ?>
    <div class="card">
        <div class="thumb">
            <img src="<?=htmlspecialchars($img)?>" alt="<?=htmlspecialchars($r['design_name'] ?? 'Design')?>">
        </div>
        <div class="body">
            <h3><?=htmlspecialchars($r['user_name'])?></h3>
            <div class="info">Email: <?=htmlspecialchars($r['Email'])?></div>
            <div class="info">Phone: <?=htmlspecialchars($r['Phone'])?></div>
            <div class="info">Design: <?=htmlspecialchars($r['design_name'] ?? '—')?></div>
            <div class="info">Date: <?=htmlspecialchars($r['Date'])?></div>
            <div class="info">Time: <?=htmlspecialchars($r['time_slot'])?></div>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</section>

 <!-- Footer -->
        <footer>
            <div class="footer-inner">
                <div class="copy">© 2025 BIRWAZ.All rights reserved.</div>

                <div class="socials">
                    <a href="https://twitter.com" target="_blank">
                        <img src="images/twitter.png" alt="Twitter icon">
                    </a>
                    <a href="https://instagram.com" target="_blank">
                        <img src="images/instagram.png" alt="Instagram icon">
                    </a>
                    <a href="https://wa.me" target="_blank">
                        <img src="images/whatsapp.png" alt="WhatsApp icon">
                    </a>
                    <a href="mailto:birwaz@gmail.com" target="_blank" >
                        <img src="images/email.png" alt="Email icon">
                    </a>
                </div>
            </div>
        </footer>
 <!-- LOGOUT MODAL -->
<div class="modal-overlay" id="logoutModal">
    <div class="modal">
        <h2>Log Out</h2>
        <p>Are you sure you want to log out?</p>
        <div class="modal-buttons">
            <button class="no" id="cancelLogout">Cancel</button>
            <button class="yes" id="confirmLogout">Ok</button>
        </div>
    </div>
</div>
<script>
// LOGOUT CONFIRMATION
const logoutLink    = document.getElementById('logoutLink');
const logoutModal   = document.getElementById('logoutModal');
const cancelLogout  = document.getElementById('cancelLogout');
const confirmLogout = document.getElementById('confirmLogout');

if (logoutLink) {
    logoutLink.addEventListener('click', function (e) {
        e.preventDefault();                
        logoutModal.classList.add('active'); 
    });
}

if (cancelLogout) {
    cancelLogout.addEventListener('click', function () {
        logoutModal.classList.remove('active'); 
    });
}

if (confirmLogout) {
    confirmLogout.addEventListener('click', function () {
        window.location.href = 'logout.php'; 
    });
}


if (logoutModal) {
    logoutModal.addEventListener('click', function (e) {
        if (e.target === logoutModal) {
            logoutModal.classList.remove('active');
        }
    });
}
</script>


</body>
</html>
