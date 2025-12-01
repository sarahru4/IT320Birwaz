<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$conn = getDBConnection();

$sql = "
    SELECT 
        r.ReservationID,
        r.Date,
        r.time_slot,
        r.total_price,
        r.statues,
        d.Name AS design_name,
        d.Image_URL AS design_file,
        d.Type
    FROM reservation r
    LEFT JOIN design d
        ON d.DesignID = r.DesignID
    WHERE r.UserID = ?
    ORDER BY r.Date DESC, r.ReservationID DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewResesrvations" content="width=device-width, initial-scale=1" />
<title>View Reservation</title>
<style>
*{ box-sizing:border-box }
body{
    margin:0;
    font-family:Inter,system-ui,Arial,sans-serif;
    color:#1f2937;
    background:radial-gradient(1200px 600px at 80% -10%, #fff 0%, #fff6bf 40%, #fffbeb 65%);
}
a{ color:inherit; text-decoration:none }

header{
    position:static;
    top:0;
    z-index:20;
    background:linear-gradient(90deg, #EBE389, #fae588);
    border-bottom:1px solid rgba(0,0,0,.05);
    box-shadow:0 6px 20px rgba(245,158,11,.15);
}
.container{ max-width:1100px; margin-inline:auto; padding:18px 22px }
.nav{ display:flex; align-items:center; justify-content:space-between; gap:14px; min-height:64px; }
.brand{ display:flex; align-items:center; gap:10px; line-height:1; }
.brand img{ height:55px; width:auto; object-fit:contain; display:block; background:#fff; padding:1px; border-radius:12px; border:1px solid #f1e9b6; box-shadow:0 4px 10px rgba(245,158,11,.12); }
.brand strong{ font-weight:900; letter-spacing:.3px; color:#111; font-size:clamp(18px, 2.4vw, 20px); }

.links{ display:flex; gap:8px; flex-wrap:wrap }
.links a{
    padding:8px 12px;
    border-radius:12px;
    color:#111;
    font-weight:600;
    border:1px solid transparent;
    transition:.2s
}
.links a:hover{ background:rgba(255,255,255,.7); border-color:#fef3c7 }
.links a[aria-current="page"]{ background:#fff; border-color:#fde68a; box-shadow:0 4px 10px rgba(0,0,0,.05) }

.hero{ padding:28px 8px 6px; text-align:center; }
.hero h1{ margin:0 0 6px; font-size:clamp(24px,3vw,32px); color:#111 }
.hero p{ margin:0; color:#6b7280 }

.cards{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
    gap:28px;
    justify-items:center;
    padding-bottom:40px;
}
.card{
    width:280px;
    border-radius:16px;
    background:linear-gradient(180deg,#fff8d6,#fffbe6);
    border:1px solid #f1e9b6;
    box-shadow:0 10px 24px rgba(245,158,11,.12);
    overflow:hidden;
    transition:.25s;
    display:flex;
    flex-direction:column;
}
.card:hover{ transform:translateY(-4px); }
.card img{
    width:100%;
    height:180px;
    object-fit:cover;
    border-bottom:1px solid #f1e9b6;
}
.card-content{
    padding:16px 14px 20px;
    text-align:center;
    flex:1;
    display:flex;
    flex-direction:column;
    gap:6px;
}
.card-content h3{ margin:0; font-size:18px; color:#111; }
.info{ font-size:14px; color:#6b7280; }
.status{ font-weight:600; font-size:13px; color:#2563eb; }

.btns{
    display:flex;
    justify-content:center;
    gap:8px;
    margin-top:10px;
}

.delete-btn{
    background:#fff;
    border:1px solid #fca5a5;
    color:#b91c1c;
    font-weight:600;
    border-radius:10px;
    padding:8px 16px;
    cursor:pointer;
    transition:.2s;
    box-shadow:0 6px 14px rgba(220,38,38,.15);
}
.delete-btn:hover{ background:#fee2e2; }
.btn{
    background: #fff;     
    border: 1px solid #EBCF8C; 
    color: #7A4C00;               
    font-weight: 600;
    border-radius: 10px;
    padding: 8px 16px;
    cursor: pointer;
    transition: .2s;
    box-shadow: 0 4px 6px rgba(200,150,50,0.25);
}

.btn:hover{
    background: #FFEFB3;       
}

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
.copy{ color:#374151; font-size:16px }
.socials{ display:flex; gap:12px; align-items:center; }
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
.socials img{ width:22px; height:22px; object-fit:contain; display:block; }
/* Logout confirmation modal */
.modal-overlay{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,.35);
    display:flex;
    align-items:center;
    justify-content:center;
    opacity:0;
    visibility:hidden;
    transition:.2s;
    z-index:1500;
}
.modal-overlay.active{
    opacity:1;
    visibility:visible;
}
.modal-box{
    background:#fffbe6;
    padding:24px 28px;
    width:300px;
    border-radius:16px;
    text-align:center;
    box-shadow:0 10px 24px rgba(245,158,11,.3);
}
.modal-box h3{
    margin:0 0 8px;
    color:#4b4b00;
}
.modal-box p{
    margin:0 0 18px;
    color:#7a5a15;
}
.modal-actions{
    display:flex;
    justify-content:space-between;
    gap:10px;
}
.modal-btn{
    flex:1;
    padding:10px 16px;
    border-radius:10px;
    border:none;
    cursor:pointer;
    font-weight:600;
}
.modal-btn.cancel{
    background:#f3f4f6;
    color:#374151;
}
.modal-btn.confirm{
    background:#f87171;     
    color:#7f1d1d;           
    border:1px solid #ef4444;
}
.modal-btn.confirm:hover{
    background:#ef4444;      
    color:#ffffff;           
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
                    <a href="designs.php" >Designs</a>
                    <a href="viewReservations.php" aria-current="page">View Reservations</a>
                    <a href="logout.php" id="logoutLink">Log Out</a>

                </nav>
            </div>
        </header>

<main class="container">
    <section class="hero">
        <h1>View Reservations</h1>
        <p>Here are your upcoming studio bookings.</p>
    </section>
<?php if (isset($_GET['deleted'])): ?>
<div style="text-align:center; color:#2e7d32; font-weight:600; margin-bottom:15px;">
Reservation cancelled successfully ✓
</div>
<?php endif; ?>

    <section class="cards" id="results">

        <?php if (empty($reservations)): ?>
            <p style="grid-column:1 / -1; text-align:center; color:#6b7280;">
                You don't have any reservations yet.
            </p>
        <?php else: ?>
            <?php foreach ($reservations as $res): ?>

                <?php
$imgSrc = 'images/default_studio.png'; 

if (!empty($res['design_file'])) {

    $file = htmlspecialchars($res['design_file']);

    if (file_exists("images/$file")) {
        $imgSrc = "images/$file";
    }
    elseif (file_exists("customizedesign/$file")) {
        $imgSrc = "customizedesign/$file";
    }
    elseif (file_exists("uploads/$file")) {
        $imgSrc = "uploads/$file";
    }
}


$designName = !empty($res['design_name']) ? $res['design_name'] : 'Customized Design';
?>


                <div class="card">
    <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($designName); ?>">
    <div class="card-content">
        <h3><?php echo htmlspecialchars($designName); ?></h3>

        <p class="status">
            Status: <?php echo htmlspecialchars($res['statues']); ?>
        </p>

        <div class="btns">
            <a href="ViewDesign.php?reservation_id=<?php echo $res['ReservationID']; ?>">
                <button class="btn" type="button">View</button>
            </a>

            <?php if ($res['Type'] === 'customized'): ?>
    <a href="EditDesign.php?reservation_id=<?php echo $res['ReservationID']; ?>">
        <button class="btn" type="button">Edit</button>
    </a>
<?php endif; ?>


            <a href="cancelReservation.php?reservation_id=<?php echo $res['ReservationID']; ?>"
               onclick="return confirm('Are you sure you want to cancel this reservation?');">
                <button class="delete-btn" type="button">Cancel</button>
            </a>
            

        </div>
    </div>
</div>

            <?php endforeach; ?>
        <?php endif; ?>

    </section>
</main>

<footer>
    <div class="footer-inner">
        <div class="copy">© 2025 BIRWAZ. All rights reserved.</div>
        <div class="socials">
            <a href="https://twitter.com" target="_blank"><img src="images/twitter.png" alt="Twitter icon"></a>
            <a href="https://instagram.com" target="_blank"><img src="images/instagram.png" alt="Instagram icon"></a>
            <a href="https://wa.me" target="_blank"><img src="images/whatsapp.png" alt="WhatsApp icon"></a>
            <a href="mailto:birwaz@gmail.com" target="_blank"><img src="images/email.png" alt="Email icon"></a>
        </div>
    </div>
</footer>
    <!-- Logout Confirmation Modal -->
<div class="modal-overlay" id="logoutModal">
    <div class="modal-box">
        <h3>Log Out</h3>
        <p>Are you sure you want to log out?</p>
        <div class="modal-actions">
            <button type="button" class="modal-btn cancel" id="cancelLogout">Cancel</button>
            <button type="button" class="modal-btn confirm" id="confirmLogout">OK</button>
        </div>
    </div>
</div>

<script>
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
