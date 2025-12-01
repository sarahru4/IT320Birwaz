<?php
session_start();
require_once 'db_config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = getDBConnection();

$signupError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $cpass = $_POST['cpass'] ?? '';

    if ($name == '' || $phone == '' || $email == '' || $pass == '' || $cpass == '') {
        $signupError = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signupError = 'Please enter a valid email.';
    } elseif ($pass !== $cpass) {
        $signupError = 'Passwords do not match.';
    } else {

        $checkStmt = $conn->prepare("SELECT UserID FROM user WHERE Email = ?");
        if ($checkStmt) {
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                $signupError = 'This email is already registered. Please use another email or log in.';
            } else {

                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $role = 'User';

                $stmt = $conn->prepare("INSERT INTO user (Name, Email, Password, Phone, Role) 
                                        VALUES (?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("sssss", $name, $email, $hash, $phone, $role);

                    if ($stmt->execute()) {
                        $userId = $stmt->insert_id;

                        $_SESSION['user_id'] = $userId;
                        $_SESSION['user_name'] = $name;
                        $_SESSION['role'] = $role;

                        header("Location: homeUser.php");
                        exit;
                    } else {
                        $signupError = 'Could not create account. Please try again.';
                    }
                    $stmt->close();
                } else {
                    $signupError = 'Database error. Please contact support.';
                }
            }

            $checkStmt->close();
        } else {
            $signupError = 'Database error. Please contact support.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>BIRWAZ Sign Up</title>
        <style>
            *{
                box-sizing:border-box
            }

            body{
                margin:0;
                font-family:Inter,system-ui,Arial,sans-serif;
                color:#1f2937;
                background:radial-gradient(1200px 600px at 80% -10%, #fff 0%, #fff6bf 40%, #fffbeb 65%);
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
                gap:14px
            }
            .brand{
                display:flex;
                align-items:center;
                gap:10px;
                line-height:1;
            }

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
                transition:.2s;
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

            .heder{
                padding:28px 8px 6px;
                text-align:center
            }
            .heder h1{
                margin:0 0 6px;
                font-size:clamp(24px,3vw,32px);
                color:#111
            }
            .heder p{
                margin:0;
                color:#6b7280
            }

            .wrap{
                display:grid;
                place-items:center;
                padding:16px
            }
            .card{
                width:min(620px, 94vw);
                background:#fff;
                border:1px solid #f1e9b6;
                border-radius:16px;
                padding:20px 18px;
                box-shadow:0 10px 24px rgba(245,158,11,.12);
            }

            .grid{
                display:grid;
                grid-template-columns:repeat(12,1fr);
                gap:12px
            }
            .col-6{
                grid-column:span 6
            }
            @media (max-width:720px){
                .col-6{
                    grid-column:span 12
                }
            }

            .field{
                display:grid;
                gap:6px;
                margin:12px 0
            }
            label{
                font-size:14px;
                color:#334155
            }
            input{
                height:44px;
                border-radius:12px;
                border:1px solid #f1e9b6;
                padding:0 12px;
                font-size:15px;
                background:#FFF;
                outline-color:#eab308;
                width:100%;
            }

            .btn{
                display:inline-grid;
                place-items:center;
                height:44px;
                border-radius:12px;
                border:none;
                cursor:pointer;
                font-weight:700;
                color:#111;
                background:linear-gradient(135deg,#EBE389,#fae588);
                box-shadow:0 6px 14px rgba(250,204,21,.25);
                width:100%;
            }

            .helper{
                color:#6b7280;
                font-size:14px;
                margin-top:10px;
                text-align:center
            }
            .error{
                display:none;
                color:#b91c1c;
                font-size:12px
            }
            .server-error{
                margin-top:8px;
                color:#b91c1c;
                font-size:14px;
                text-align:center;
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
            <div class="container nav" >
                <a class="brand" href="#" aria-label="BIRWAZ Home">
                    <img src="images/logo1.png" alt="BIRWAZ logo">
                    <strong>BIRWAZ</strong>
                </a>
            </div>
        </header>

        <main class="container">
            <section class="heder">
                <h1>Create your account</h1>
                <p>Sign up to save bookings, receive reminders, and pick your perfect studio design.</p>
            </section>

            <section class="wrap">
                <div class="card">
                    <h2 id="signupTitle" style="margin:0 0 8px; text-align:center; color:#111;">Sign Up</h2>

                    <?php if ($signupError !== ''): ?>
                        <div class="server-error"><?php echo htmlspecialchars($signupError); ?></div>
                    <?php endif; ?>

                    <form id="signupForm" method="post" novalidate>
                        <div class="field">
                            <label for="name">Full Name</label>
                            <input id="name" name="name" type="text" autocomplete="name" required />
                            <div id="nameErr" class="error">Please enter your name.</div>
                        </div>
                        <div class="field">
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" type="tel" placeholder="Phone number" autocomplete="tel" required />
                            <div id="phoneErr" class="error">Please enter valid phone number.</div>
                        </div>

                        <div class="field">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" autocomplete="email" required />
                            <div id="emailErr" class="error">Enter a valid email.</div>
                        </div>

                        <div class="grid">
                            <div class="col-6">
                                <div class="field">
                                    <label for="pass">Password</label>
                                    <input id="pass" name="password" type="password" autocomplete="new-password" minlength="8" required />
                                    <div id="passErr" class="error">At least 8 characters (letters & numbers recommended).</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="field">
                                    <label for="cpass">Confirm Password</label>
                                    <input id="cpass" name="cpass" type="password" minlength="8" required />
                                    <div id="cpassErr" class="error">Passwords do not match.</div>
                                </div>
                            </div>
                        </div>

                        <button class="btn" type="submit">Create Account</button>
                    </form>

                    <p class="helper">Already have an account? <a href="login.php">Log in</a>.</p>
                </div>
            </section>
        </main>

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
            const form = document.getElementById('signupForm');
            const nameI = document.getElementById('name');
            const email = document.getElementById('email');
            const pass = document.getElementById('pass');
            const cpass = document.getElementById('cpass');
            const phone = document.getElementById('phone');

            function isGeneralPhone(value) {
                const allowed = /^[+]?[\d\s().-]+$/;
                if (!allowed.test(value))
                    return false;
                const digits = value.replace(/\D/g, '');
                return digits.length == 10;
            }

            function show(id) {
                document.getElementById(id).style.display = 'block';
            }
            function hide(id) {
                document.getElementById(id).style.display = 'none';
            }

            form.addEventListener('submit', (e) => {
                let ok = true;

                if (!nameI.value.trim()) {
                    ok = false;
                    show('nameErr');
                } else
                    hide('nameErr');

                if (!email.value || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email.value)) {
                    ok = false;
                    show('emailErr');
                } else
                    hide('emailErr');

                if (!pass.value || pass.value.length < 8) {
                    ok = false;
                    show('passErr');
                } else
                    hide('passErr');

                if (cpass.value !== pass.value) {
                    ok = false;
                    show('cpassErr');
                } else
                    hide('cpassErr');

                if (!isGeneralPhone(phone.value.trim())) {
                    ok = false;
                    show('phoneErr');
                } else
                    hide('phoneErr');


                if (!ok) {
                    e.preventDefault();
                }
            });
        </script>
    </body>
</html>

