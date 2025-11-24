<?php
session_start();
require_once 'db_config.php'; 

if (!isset($_SESSION['user_id'])) {
    
    header('Location: login.php?error=login_required');
    exit;
}


$conn = getDBConnection();

$designs = [];
$sql = "SELECT DesignID, Name, Description, Image_URL FROM Design";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $designs[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>BIRWAZ Designs</title>
        <style>

            *{
                box-sizing:border-box
            }
            html, body{
                height:100%;
            }

            body{
                margin:0;
                font-family:Inter,system-ui,Arial,sans-serif;
                color:#1f2937;
                background:radial-gradient(1200px 600px at 80% -10%, #fff 0%, #fff6bf 40%, #fffbeb 65%);

            }


            main.container{

                min-height: calc(100vh - 190px);
            }



            a{
                color:inherit;
                text-decoration:none
            }

            header{
                position:static;
                top:0;
                z-index:20;
                background:linear-gradient(90deg, #EBE389, #fae588);
                border-bottom:1px solid rgba(0,0,0,.05);
                box-shadow:0 6px 20px rgba(245,158,11,.15);
            }
            .container{
                max-width:1100px;
                margin-inline:auto;
                padding:18px 22px
            }
            .nav{
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:14px;
                min-height:64px;
            }
            .brand{
                display:flex;
                align-items:center;
                gap:10px;
                line-height:1;
            }

            /* logo  */
            .brand img{
                height:55px;
                width:auto;
                object-fit:contain;
                display:block;
                -webkit-user-drag:none;
                background:#fff;
                padding:1px;
                border-radius:12px;
                border:1px solid #f1e9b6;
                box-shadow:0 4px 10px rgba(245,158,11,.12);
                filter: drop-shadow(0 1px 1px rgba(0,0,0,.08));
            }

            .brand strong{
                font-weight:900;
                letter-spacing:.3px;
                color:#111;
                font-size:clamp(18px, 2.4vw, 20px);
            }
            .links{
                display:flex;
                gap:8px;
                flex-wrap:wrap
            }
            .links a{
                padding:8px 12px;
                border-radius:12px;
                color:#111;
                font-weight:600;
                border:1px solid transparent;
                transition:.2s
            }
            .links a:hover{
                background:rgba(255,255,255,.7);
                border-color:#fef3c7
            }
            .links a[aria-current="page"]{
                background:#fff;
                border-color:#fde68a;
                box-shadow:0 4px 10px rgba(0,0,0,.05)
            }

            .hero{
                padding:28px 8px 6px
            }
            .hero h1{
                margin:0 0 6px;
                font-size:clamp(24px,3vw,32px);
                color:#111
            }
            .hero p{
                margin:0;
                color:#6b7280
            }

            .toolbar{
                display:flex;
                gap:10px;
                flex-wrap:wrap;
                margin:16px 0
            }
            .input,.btn{
                height:44px;
                border-radius:12px;
                border:1px solid #f1e9b6;
                padding:0 12px;
                font-size:14px;
                background:#fff8e1
            }
            .btn{
                cursor:pointer
            }
            .btn.ghost{
                background:#fff;
                border-color:#fce7a2
            }

            .grid{
                display:grid;
                grid-template-columns:repeat(12,1fr);
                gap:18px
            }
            .col-4{
                grid-column:span 4;
            }
            @media(max-width:900px){
                .col-4{
                    grid-column:span 12
                }
            }

            .card{
                background:linear-gradient(180deg,#fff8d6,#fffbe6);
                border:1px solid #f1e9b6;
                border-radius:16px;
                overflow:hidden;
                display:flex;
                flex-direction:column;
                box-shadow:0 10px 24px rgba(245,158,11,.12);
                transition:transform .15s ease, box-shadow .15s ease;
            }
            .card:hover{
                transform:translateY(-3px);
                box-shadow:0 14px 28px rgba(245,158,11,.18)
            }

            .thumb{
                width:100%;
                height:400px;
                aspect-ratio:16/10;
                overflow:hidden;
                background:#fff4b5;
                border-bottom:1px solid #f1e9b6;
                margin-top:0;
                margin-left:0;
                margin-right:0;
            }
            .thumb img{
                width:100%;
                height:100%;
                object-fit:cover;
                display:block
            }

            .body{
                padding:14px;
                display:flex;
                flex-direction:column;
                gap:10px;
                flex: 1;
            }
            .grid {
                align-items: stretch;
            }
            .body h3{
                margin:0 0 4px;
                font-size:18px;
                color:#111
            }
            .body p{
                margin:0;
                color:#6b7280;
                font-size:14px
            }

            .btn.primary{
                box-shadow:0 6px 14px rgba(250,204,21,.25);
                font-size:15px;
                background:#fff;
                border:1px solid #fde68a;
                padding:4px 8px;
                border-radius:999px;
                color:#6b4f00;
                font-weight:700;
                display:inline-grid;
                place-items:center;
                margin-top: auto;
            }
            .full{
                width:100%
            }

            .notice{
                padding:12px 14px;
                border:1px dashed #f1e9b6;
                border-radius:12px;
                background:#fffef3;
                margin-top:12px;
                color:#6b4f00;
                display:none
            }
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
            <div class="container nav">
                <a class="brand" href="homeUser.php">
                    <img src="images/logo1.png" alt="BIRWAZ logo">
                    <strong>BIRWAZ</strong>
                </a>
                <nav class="links">
                    <a href="homeUser.php">Home</a>
                    <a href="designs.php" aria-current="page">Designs</a>
                    <a href="viewReservations.php">View Reservations</a>
                    <a href="logout.php">Log Out</a>
                </nav>
            </div>
        </header>

        <main class="container">
            <section class="hero">
                <h1>Ready-made Designs</h1>
                <p>Search designs (e.g., graduation, birthday) and pick one to reserve.</p>
            </section>

            <div class="toolbar">
                <input id="q" class="input" type="search" placeholder="Search designs…" aria-label="Search designs"/>
                <button id="clear" class="btn ghost" type="button">Clear</button>
            </div>

            <section id="results" class="grid">
                <?php if (!empty($designs)): ?>
                    <?php foreach ($designs as $design): ?>
                        <div class="col-4 card" data-name="<?php echo htmlspecialchars($design['Name']); ?>">
                            <figure class="thumb">

                                <img src="uploads/<?php echo htmlspecialchars($design['Image_URL']); ?>"
                                     alt="<?php echo htmlspecialchars($design['Name']); ?> preview">
                            </figure>
                            <div class="body">
                                <h3><?php echo htmlspecialchars($design['Name']); ?></h3>
                                <p><?php echo htmlspecialchars($design['Description']); ?></p>


                                <a class="btn primary full"
                                   href="booking.php?designId=<?php echo urlencode($design['DesignID']); ?>">
                                    Select Design
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No designs available yet.</p>
                <?php endif; ?>
            </section>

            <div id="empty" class="notice">No designs found. Try another keyword or clear filters.</div>
        </main>
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

        <script>
            const qEl = document.getElementById('q');
            const cards = Array.from(document.querySelectorAll('#results .card'));
            const empty = document.getElementById('empty');

            function filter() {
                const term = (qEl.value || '').trim().toLowerCase();
                let shown = 0;

                cards.forEach(card => {
                    const name = (card.dataset.name || '').toLowerCase();
                    const match = !term || name.includes(term);
                    card.style.display = match ? '' : 'none';
                    if (match)
                        shown++;
                });

                empty.style.display = shown ? 'none' : 'block';
            }

            if (qEl) {
                qEl.addEventListener('input', filter);
            }
            const clearBtn = document.getElementById('clear');
            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    qEl.value = '';
                    filter();
                    qEl.focus();
                });
            }
            filter();
        </script>
    </body>
</html>
