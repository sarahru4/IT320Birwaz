<?php
// addDesignsAdmin.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Design</title>
<style>
*{box-sizing:border-box}
body{margin:0;font-family:Inter,Arial,sans-serif;color:#1f2937;background:radial-gradient(1200px 600px at 80% -10%, #fff 0%, #fff6bf 40%, #fffbeb 65%);}
.container{max-width:1100px;margin:auto;padding:18px 22px}
header{background:linear-gradient(90deg,#EBE389,#fae588);border-bottom:1px solid rgba(0,0,0,.06);box-shadow:0 6px 20px rgba(245,158,11,.15)}
.brand{display:flex;align-items:center;gap:10px}
form{background:linear-gradient(180deg,#fff8d6,#fffbe6);border:1px solid #f1e9b6;border-radius:18px;padding:30px 40px;box-shadow:0 10px 24px rgba(245,158,11,.12);max-width:650px;margin:30px auto;display:flex;flex-direction:column;gap:18px;}
label{font-weight:600;color:#111}
input[type="text"], textarea, input[type="file"]{width:100%;border:1px solid #f1e9b6;border-radius:12px;padding:10px 12px;background:#fff;}
.buttons{display:flex;justify-content:center;gap:12px;margin-top:10px}
.btn{height:44px;border-radius:12px;border:1px solid #f1e9b6;padding:0 20px;font-size:15px;background:#fff8e1;cursor:pointer;font-weight:600}
</style>
</head>
<body>
<header class="container">
    <div class="brand">
        <a href="viewDesignsAdmin.php" style="text-decoration:none;color:#111;font-weight:700">&larr; Back to Designs</a>
        <div style="margin-left:auto;font-weight:900">BIRWAZ</div>
    </div>
</header>

<main class="container">
    <form id="designForm" action="uploadDesign.php" method="post" enctype="multipart/form-data">
        <div>
            <label for="designName">Design Name</label>
            <input type="text" id="designName" name="name" placeholder="Enter design name" required>
        </div>

        <div>
            <label for="designDescription">Description</label>
            <textarea id="designDescription" name="description" placeholder="Write a short description" required></textarea>
        </div>

        <div>
            <label for="designImage">Upload Image</label>
            <input type="file" id="designImage" name="image" accept="image/*" required>
        </div>

        <div class="buttons">
            <button type="submit" class="btn">Save Design</button>
            <button type="button" class="btn" onclick="window.location.href='viewDesignsAdmin.php'">Cancel</button>
        </div>
    </form>
</main>
</body>
</html>
