<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// جلب بيانات الديكورات
$conn = getDBConnection();
$decorations = [];
$result = $conn->query("SELECT * FROM decoration");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $decorations[] = $row;
    }
    $result->free();
}
$conn->close();

$backgrounds = [
    ['id' => 'bg-white', 'name' => 'White', 'price' => 0],
    ['id' => 'bg-beige', 'name' => 'Beige', 'price' => 10],
    ['id' => 'bg-black', 'name' => 'Black', 'price' => 12],
    ['id' => 'bg-pink', 'name' => 'Pink', 'price' => 15]
];

$lightings = [
    ['id' => 'light-white', 'name' => 'White Light', 'price' => 0],
    ['id' => 'light-yellow', 'name' => 'Warm Yellow', 'price' => 15],
    ['id' => 'light-beige', 'name' => 'Warm Beige', 'price' => 10]
];

// حفظ التصميم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_design'])) {
    $conn = getDBConnection();
    
    $design_name = $conn->real_escape_string($_POST['design_name'] ?? 'Custom Design');
    $background = $conn->real_escape_string($_POST['background'] ?? 'white');
    $background_price = floatval($_POST['background_price'] ?? 0);
    $lighting = $conn->real_escape_string($_POST['lighting'] ?? 'white');
    $lighting_price = floatval($_POST['lighting_price'] ?? 0);
    $decoration_price = floatval($_POST['decoration_price'] ?? 0);
    $total_cost = floatval($_POST['total_cost'] ?? 50);
    $design_description = $conn->real_escape_string($_POST['design_description'] ?? 'Customized Studio Design');
    
    // حفظ الصورة
    $image_url = '';
    if (isset($_POST['design_image']) && !empty($_POST['design_image'])) {
        $image_data = $_POST['design_image'];
        $image_data = str_replace('data:image/png;base64,', '', $image_data);
        $image_data = base64_decode($image_data);
        
        $image_name = 'design_' . time() . '.png';
        $image_path = 'uploads/' . $image_name;
        
        if (!is_dir('uploads')) mkdir('uploads', 0755, true);
        
        if (file_put_contents($image_path, $image_data)) {
            $image_url = $image_name;
        }
    }
    
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("INSERT INTO design (Name, Description, Image_URL, Type) VALUES (?, ?, ?, 'customized')");
        $stmt->bind_param("sss", $design_name, $design_description, $image_url);
        if (!$stmt->execute()) throw new Exception("Error inserting into design: " . $stmt->error);
        
        $design_id = $conn->insert_id;
        $stmt->close();
        
        // إدخال في جدول customizedesign
        $stmt = $conn->prepare("
    INSERT INTO customizedesign 
    (DesignID, Background, background_price, Lighting, lightning_price, decoration_price, design_data, design_image) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isdsddss",
    $design_id,
    $background,
    $background_price,
    $lighting,
    $lighting_price,
    $decoration_price,
    $_POST['design_data'],
    $image_url
);

        if (!$stmt->execute()) throw new Exception("Error inserting into customizedesign: " . $stmt->error);
        
        $stmt->close();
        $conn->commit();
        
        $_SESSION['last_design_id'] = $design_id;
        $_SESSION['design_total_cost'] = $total_cost;
        
        header("Location: Booking.php?design_id=" . $design_id);
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $save_error = "Error saving design: " . $e->getMessage();
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BIRWAZ - Studio Customization</title>
  <style>
    * { box-sizing: border-box; margin: 0; }
    body {
      font-family: Inter, system-ui, Arial, sans-serif;
      color: #1f2937;
      background: radial-gradient(1200px 600px at 80% -10%, #fff 0%, #fff6bf 40%, #fffbeb 65%);
    }
    a { color: inherit; text-decoration: none; }
    .container { max-width: 1100px; margin: auto; padding: 18px 22px; }

    header {
      background: linear-gradient(90deg, #EBE389, #fae588);
      border-bottom: 1px solid rgba(0,0,0,.05);
      box-shadow: 0 6px 20px rgba(245,158,11,.15);
    }
    .nav {
      display: flex; align-items: center; justify-content: space-between;
      min-height: 64px;
    }
    .brand { display: flex; align-items: center; gap: 10px; }
    .brand img {
      height: 55px; background: #fff; padding: 1px; border-radius: 12px;
      border: 1px solid #f1e9b6; box-shadow: 0 4px 10px rgba(245,158,11,.12);
    }
    .brand strong { font-weight: 900; color: #111; font-size: 20px; }
    .links { display: flex; gap: 8px; flex-wrap: wrap; }
    .links a {
      padding: 8px 12px; border-radius: 12px; font-weight: 600;
      transition: .2s; border: 1px solid transparent;
    }
    .links a:hover { background: rgba(255,255,255,.7); border-color: #fef3c7; }
    .links a[aria-current="page"] {
      background: #fff; border-color: #fde68a; box-shadow: 0 4px 10px rgba(0,0,0,.05);
    }

    .customize-container { padding: 0 50px; margin-top: 30px; max-width: 1200px; margin-inline: auto; }
    .studio-preview-board {
      background-color: #E8E8E8; border-radius: 12px; overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      position: relative;
    }
    .studio-canvas-container {
      width: 100%; 
      height: 500px; 
      position: relative;
      background: url('images/studio.png') center/cover no-repeat;
    }
    #fabricCanvas {
      width: 100% !important;
      height: 100% !important;
      display: block;
    }
    .canvas-controls {
      position: absolute;
      top: 10px;
      left: 10px;
      z-index: 100;
      display: flex;
      gap: 10px;
    }
    .control-btn {
      padding: 8px 12px;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 6px;
      cursor: pointer;
      font-size: 12px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .control-btn:hover { background: #f5f5f5; }

    .book-design-button {
      display: block; width: fit-content; padding: 12px 35px;
      background-color: #F9DA57; color: #333; font-weight: 700; font-size: 18px;
      border-radius: 8px; margin-top: 25px; box-shadow: 0 4px 6px rgba(249,218,87,0.4);
      transition: background-color .2s, transform .2s;
      cursor: pointer; border: none; margin: 25px auto;
    }
    .book-design-button:hover {
      background-color: #FFD700; transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(249,218,87,0.6);
    }

    .total-cost { text-align: center; margin-top: 15px; }
    .total-cost-display {
      display: inline-block; padding: 12px 25px; background: #4B0082; color: white;
      border-radius: 10px; font-weight: bold; font-size: 20px; box-shadow: 0 4px 12px rgba(75, 0, 130, 0.4);
      border: 2px solid #6A0DAD;
    }

    .customization-options { padding-bottom: 50px; margin-top: 40px; text-align: left; }
    .category-section { margin-bottom: 40px; }
    .category-section h3 {
      margin-bottom: 20px; font-size: 22px; color: #374151;
      border-bottom: 2px solid #ccc; padding-bottom: 8px;
    }
    .options-carousel {
      display: flex; gap: 25px; overflow-x: auto; padding: 10px 5px 20px;
      scrollbar-width: none;
    }
    .options-carousel::-webkit-scrollbar { display: none; }
    .option-item {
      flex-shrink: 0; width: 140px; text-align: center; cursor: pointer;
    }
    .item-visual {
      width: 120px; height: 120px; border-radius: 50%; background-color: #ddd;
      margin-bottom: 10px; border: 3px solid transparent; transition: all .3s;
      background-size: contain; background-position: center; background-repeat: no-repeat;
      margin: 0 auto 10px;
    }
    .option-item:hover .item-visual {
      border-color: #FFD700; box-shadow: 0 0 15px rgba(249,218,87,0.8); transform: scale(1.05);
    }
    .option-item.selected .item-visual {
      border-color: #4B0082; box-shadow: 0 0 0 4px #4B0082;
    }
    .item-price { font-size: 14px; color: #4B0082; font-weight: bold; margin-top: 5px; }

    .background-white { background-color: #FFFFFF; border: 1px solid #ddd; }
    .background-beige { background-color: #F5F5DC; }
    .background-black { background-color: #000000; }
    .background-pink { background-color: #FFC0CB; }

    .lighting-white { 
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
      box-shadow: 0 0 20px rgba(255,255,255,0.8);
    }
    .lighting-yellow { 
      background: linear-gradient(135deg, #fff3cd, #ffd700);
      box-shadow: 0 0 20px rgba(255,215,0,0.6);
    }
    .lighting-beige { 
      background: linear-gradient(135deg, #f5e6d3, #d2b48c);
      box-shadow: 0 0 20px rgba(210,180,140,0.5);
    }

    footer { background: #f6eaa6; border-top: 1px solid #f1e9b6; margin-top: 50px; }
    .footer-inner {
      max-width: 1100px; margin: auto; padding: 18px 22px;
      display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
    }
    .copy { color: #374151; font-size: 16px; }
    .socials { display: flex; gap: 12px; align-items: center; }
    .socials a {
      width: 48px; height: 48px; border-radius: 50%; display: grid; place-items: center;
      background: linear-gradient(180deg,#fff8d6,#fffbe6); border: 1px solid #f1e9b6;
      box-shadow: 0 6px 14px rgba(245,158,11,.12); transition: all .15s;
    }
    .socials a:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,158,11,.18); background: #fff; }
    .socials img { width: 22px; height: 22px; object-fit: contain; }

    .lighting-effect {
      position: absolute; top: 0; left: 0; width: 100%; height: 100%;
      pointer-events: none; z-index: 50; opacity: 0; transition: opacity 0.5s ease;
    }
    .light-spot {
      position: absolute; border-radius: 50%; filter: blur(20px); opacity: 0.6;
    }
    .light-beam {
      position: absolute; background: linear-gradient(transparent, rgba(255,255,255,0.8), transparent);
      transform-origin: top; filter: blur(5px);
    }

    .notification {
      position: fixed; top: 20px; right: 20px; padding: 15px 20px; border-radius: 10px;
      color: white; font-weight: 600; box-shadow: 0 5px 15px rgba(0,0,0,0.2); z-index: 1000;
      max-width: 350px; display: flex; align-items: center; gap: 10px;
      transform: translateX(400px); transition: transform 0.3s ease;
    }
    .notification.show { transform: translateX(0); }
    .notification.success { background: linear-gradient(135deg, #4CAF50, #45a049); }
    .notification.warning { background: linear-gradient(135deg, #ff9800, #e68900); }

    .custom-modal {
      position: fixed; top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;
      z-index: 2000; opacity: 0; visibility: hidden; transition: all 0.3s ease;
    }
    .custom-modal.show { opacity: 1; visibility: visible; }
    .modal-content {
      background: white; border-radius: 16px; padding: 30px; max-width: 400px; width: 90%;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .modal-header { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
    .modal-icon {
      width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center;
      justify-content: center; font-size: 24px; background: linear-gradient(135deg, #ff9800, #e68900); color: white;
    }
    .modal-title { font-size: 20px; font-weight: bold; color: #4B0082; margin: 0; }
    .modal-message { margin-bottom: 25px; line-height: 1.6; color: #555; }
    .modal-buttons { display: flex; gap: 15px; justify-content: flex-end; }
    .modal-btn { padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .modal-btn.cancel { background: #f0f0f0; color: #666; }
    .modal-btn.confirm { background: #F9DA57; color: #333; }

    .hidden-form { display: none; }

    @media (max-width: 768px) {
      .customize-container { padding: 0 20px; margin-top: 20px; }
      .studio-canvas-container { height: 300px; }
      .book-design-button { width: 100%; text-align: center; }
      .option-item { width: 100px; }
      .item-visual { width: 100px; height: 100px; }
      .footer-inner { flex-direction: column; text-align: center; gap: 15px; }
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

  <main class="customize-container">
    <?php if (isset($save_error)): ?>
      <div class="notification warning show" style="position: relative; margin: 20px auto; max-width: 500px;">
        ⚠️ <?php echo htmlspecialchars($save_error); ?>
      </div>
    <?php endif; ?>

    <section class="studio-preview-board">
      <div class="canvas-controls">
        <button class="control-btn" id="resetBtn">Reset</button>
        <button class="control-btn" id="bringToFrontBtn">Bring to Front</button>
        <button class="control-btn" id="sendToBackBtn">Send to Back</button>
        <button class="control-btn" id="deleteBtn">Delete Selected</button>
      </div>
      <div class="studio-canvas-container">
        <div id="lightingEffect" class="lighting-effect"></div>
        <canvas id="fabricCanvas"></canvas>
      </div>
    </section>

    <button class="book-design-button" id="bookButton">Book Your Customized Studio</button>

    <div class="total-cost">
      <div class="total-cost-display" id="totalCost">Total: 50 SAR</div>
    </div>

    <section class="customization-options">
      <div class="category-section">
        <h3>1. Backgrounds</h3>
        <div class="options-carousel" id="backgroundsCarousel">
          <?php foreach($backgrounds as $bg): ?>
          <div class="option-item" data-element-id="<?= $bg['id'] ?>">
            <div class="item-visual background-<?= explode('-', $bg['id'])[1] ?>"></div>
            <p><?= $bg['name'] ?></p>
            <div class="item-price"><?= $bg['price'] ?> SAR</div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="category-section">
        <h3>2. Lighting</h3>
        <div class="options-carousel" id="lightingCarousel">
          <?php foreach($lightings as $light): ?>
          <div class="option-item" data-element-id="<?= $light['id'] ?>">
            <div class="item-visual lighting-<?= explode('-', $light['id'])[1] ?>"></div>
            <p><?= $light['name'] ?></p>
            <div class="item-price"><?= $light['price'] ?> SAR</div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="category-section">
        <h3>3. Decorations</h3>
        <div class="options-carousel" id="decorationsCarousel">
          <?php foreach($decorations as $decor): ?>
          <div class="option-item" onclick="addDecoration('<?= htmlspecialchars($decor['img_url']) ?>', '<?= htmlspecialchars($decor['name']) ?>', <?= $decor['price'] ?>)">
            <div class="item-visual" style="background-image:url('<?= htmlspecialchars($decor['img_url']) ?>')"></div>
            <p><?= htmlspecialchars($decor['name']) ?></p>
            <div class="item-price"><?= $decor['price'] ?> SAR</div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="ready-designs-link" style="text-align: center; margin-top: 30px;">
        <a href="Designs.php" style="color: #4B0082; font-weight: bold; text-decoration: underline;">Browse Ready-Made Designs</a>
      </div>
    </section>

    <form id="saveDesignForm" method="POST" class="hidden-form">
      <input type="hidden" name="save_design" value="1">
      <input type="hidden" name="design_name" id="designName">
      <input type="hidden" name="design_description" id="designDescription">
      <input type="hidden" name="design_image" id="designImage">
      <input type="hidden" name="design_data" id="designData">

      <input type="hidden" name="background" id="background">
      <input type="hidden" name="background_price" id="backgroundPrice">
      <input type="hidden" name="lighting" id="lighting">
      <input type="hidden" name="lighting_price" id="lightingPrice">
      <input type="hidden" name="decoration_price" id="decorationPrice">
      <input type="hidden" name="total_cost" id="totalCostValue">
    </form>
  </main>

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

  <div class="custom-modal" id="resetModal">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-icon">⚠️</div>
        <h3 class="modal-title">Reset Design</h3>
      </div>
      <div class="modal-message">
        Are you sure you want to reset the current design? All decorations will be deleted.
      </div>
      <div class="modal-buttons">
        <button type="button" class="modal-btn cancel" onclick="hideResetModal()">Cancel</button>
        <button type="button" class="modal-btn confirm" onclick="confirmReset()">Yes, Reset</button>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>

  <script>
    let canvas, selectedObject = null;
    const baseSessionPrice = 50;
    let totalCost = baseSessionPrice;
    let currentDesign = { background: 'white', lighting: 'white', decorations: [] };

    function showNotification(message, type = 'success') {
      const notification = document.createElement('div');
      notification.className = `notification ${type}`;
      notification.innerHTML = `<span>${type === 'warning' ? '⚠️' : '✅'} ${message}</span>`;
      document.body.appendChild(notification);
      setTimeout(() => notification.classList.add('show'), 100);
      setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => document.body.removeChild(notification), 300);
      }, 4000);
    }

    function showResetModal() { document.getElementById('resetModal').classList.add('show'); }
    function hideResetModal() { document.getElementById('resetModal').classList.remove('show'); }
    function confirmReset() { hideResetModal(); resetDesign(); }

    function updateCostDisplay() { document.getElementById('totalCost').textContent = `Total: ${totalCost} SAR`; }
    function calculateTotalCost() {
      totalCost = baseSessionPrice;
      const bgPrices = {'white': 0, 'beige': 10, 'black': 12, 'pink': 15};
      const lightPrices = {'white': 0, 'yellow': 15, 'beige': 10};
      totalCost += bgPrices[currentDesign.background] + lightPrices[currentDesign.lighting];
      currentDesign.decorations.forEach(decor => totalCost += decor.price);
      updateCostDisplay();
    }

    function getBackgroundPrice(bg) {
        const prices = {'white': 0, 'beige': 10, 'black': 12, 'pink': 15};
        return prices[bg] || 0;
    }

    function getLightingPrice(light) {
        const prices = {'white': 0, 'yellow': 15, 'beige': 10};
        return prices[light] || 0;
    }

    function getDecorationPrice() {
        return currentDesign.decorations.reduce((total, decor) => total + decor.price, 0);
    }

    function initCanvas() {
      const container = document.querySelector('.studio-canvas-container');
      canvas = new fabric.Canvas('fabricCanvas', {
        width: container.offsetWidth,
        height: container.offsetHeight,
        selection: true,
        preserveObjectStacking: true,
        backgroundColor: '#FFFFFF'
      });
      
      canvas.on('object:moving', syncDecorationsFromCanvas);
      canvas.on('object:modified', syncDecorationsFromCanvas);
      canvas.on('object:scaling', syncDecorationsFromCanvas);
      canvas.on('object:rotating', syncDecorationsFromCanvas);

      canvas.on('selection:created', (e) => selectedObject = e.selected[0]);
      canvas.on('selection:updated', (e) => selectedObject = e.selected[0]);
      canvas.on('selection:cleared', () => selectedObject = null);
    }

    function applyBackground(bgId) {
      const colors = {'bg-white': '#FFFFFF', 'bg-beige': '#F5F5DC', 'bg-black': '#000000', 'bg-pink': '#FFC0CB'};
      const names = {'bg-white': 'white', 'bg-beige': 'beige', 'bg-black': 'black', 'bg-pink': 'pink'};
      canvas.backgroundColor = colors[bgId];
      canvas.renderAll();
      currentDesign.background = names[bgId];
      calculateTotalCost();
    }

    function applyLighting(lightId) {
      const lightingEffect = document.getElementById('lightingEffect');
      lightingEffect.innerHTML = '';
      lightingEffect.style.opacity = '0';
      
      let lightColor = '';
      let lightSpots = [];
      
      switch(lightId) {
        case 'light-white': 
          lightColor = 'rgba(255, 255, 255, 0.7)';
          lightSpots = [
            { top: '10%', left: '20%', width: '200px', height: '200px' },
            { top: '15%', left: '70%', width: '150px', height: '150px' },
            { top: '60%', left: '30%', width: '180px', height: '180px' }
          ];
          break;
        case 'light-yellow': 
          lightColor = 'rgba(255, 255, 0, 0.6)';
          lightSpots = [
            { top: '5%', left: '40%', width: '250px', height: '250px' },
            { top: '50%', left: '10%', width: '120px', height: '120px' },
            { top: '40%', left: '80%', width: '160px', height: '160px' }
          ];
          break;
        case 'light-beige': 
          lightColor = 'rgba(245, 222, 179, 0.6)';
          lightSpots = [
            { top: '8%', left: '60%', width: '180px', height: '180px' },
            { top: '30%', left: '20%', width: '140px', height: '140px' },
            { top: '65%', left: '50%', width: '200px', height: '200px' }
          ];
          break;
      }
      
      lightSpots.forEach(spot => {
        const lightSpot = document.createElement('div');
        lightSpot.className = 'light-spot';
        lightSpot.style.background = `radial-gradient(circle, ${lightColor} 0%, transparent 70%)`;
        lightSpot.style.top = spot.top;
        lightSpot.style.left = spot.left;
        lightSpot.style.width = spot.width;
        lightSpot.style.height = spot.height;
        lightingEffect.appendChild(lightSpot);
      });
      
      for (let i = 0; i < 3; i++) {
        const lightBeam = document.createElement('div');
        lightBeam.className = 'light-beam';
        lightBeam.style.background = `linear-gradient(to bottom, ${lightColor}, transparent)`;
        lightBeam.style.width = '80px';
        lightBeam.style.height = '200px';
        lightBeam.style.top = '0';
        lightBeam.style.left = `${20 + (i * 30)}%`;
        lightBeam.style.transform = `perspective(500px) rotateX(45deg)`;
        lightingEffect.appendChild(lightBeam);
      }
      
      setTimeout(() => {
        lightingEffect.style.opacity = '1';
      }, 50);
      
      currentDesign.lighting = lightId.split('-')[1];
      calculateTotalCost();
    }

    function addDecoration(imgSrc, imgName, price) {
      fabric.Image.fromURL(imgSrc, (img) => {
        const scale = Math.min(120 / img.width, 120 / img.height);
        const decorationId = 'decor-' + Date.now();
        
        img.set({
          left: (canvas.width - (img.width * scale)) / 2,
          top: (canvas.height - (img.height * scale)) / 2,
          scaleX: scale, 
          scaleY: scale,
          cornerStyle: 'circle', 
          cornerColor: '#4B0082',
          name: imgName, 
          price: price,
          decorationId: decorationId,
          src: imgSrc
        });

        canvas.add(img);
        canvas.setActiveObject(img);
        selectedObject = img;
        
        currentDesign.decorations.push({
            src: imgSrc,
            name: imgName,
            id: decorationId,
            price: price,
            x: img.left,
            y: img.top,
            scale: img.scaleX,
            angle: img.angle || 0
        });
        
        canvas.renderAll();
        calculateTotalCost();
        showNotification(`${imgName} added to design`);
      });
    }

    function resetDesign() {
      canvas.clear();
      applyBackground('bg-white');
      applyLighting('light-white');
      currentDesign.decorations = [];
      selectedObject = null;
      totalCost = baseSessionPrice;
      updateCostDisplay();
      showNotification('Design reset successfully');
    }

    function deleteSelectedObject() {
      if (selectedObject) {
        const itemName = selectedObject.name || 'Item';
        const decorationId = selectedObject.decorationId;
        
        canvas.remove(selectedObject);
        
        if (decorationId) {
          currentDesign.decorations = currentDesign.decorations.filter(decor => 
            decor.id !== decorationId
          );
        }
        
        selectedObject = null;
        canvas.renderAll();
        calculateTotalCost();
        showNotification(`${itemName} has been deleted`);
      } else {
        showNotification('Please select an item to delete', 'warning');
      }
    }

    function saveDesignToDatabase() {
        const designImage = canvas.toDataURL({ format: 'png', quality: 0.8 });
        
        let designDescription = "Custom Design with " + 
                               currentDesign.background.charAt(0).toUpperCase() + currentDesign.background.slice(1) + 
                               " Background and " + 
                               currentDesign.lighting.charAt(0).toUpperCase() + currentDesign.lighting.slice(1) + 
                               " Lighting";
        
        if (currentDesign.decorations.length > 0) {
            const decorationNames = currentDesign.decorations.map(decor => decor.name);
            designDescription += " and " + decorationNames.join(", ");
        }
        
        document.getElementById('designName').value = 'Custom Design - ' + new Date().toLocaleDateString();
        document.getElementById('designDescription').value = designDescription;
        document.getElementById('designImage').value = designImage;
        document.getElementById('designData').value = JSON.stringify(currentDesign);

        
        document.getElementById('background').value = currentDesign.background;
        document.getElementById('backgroundPrice').value = getBackgroundPrice(currentDesign.background);
        document.getElementById('lighting').value = currentDesign.lighting;
        document.getElementById('lightingPrice').value = getLightingPrice(currentDesign.lighting);
        document.getElementById('decorationPrice').value = getDecorationPrice();
        document.getElementById('totalCostValue').value = totalCost;
        
        document.getElementById('saveDesignForm').submit();
    }

    function syncDecorationsFromCanvas() {
        currentDesign.decorations = canvas.getObjects().map(obj => ({
            id: obj.decorationId,
            name: obj.name,
            price: obj.price,
            src: obj.src,
            x: obj.left,
            y: obj.top,
            scale: obj.scaleX,
            angle: obj.angle || 0
        }));
    }

    document.addEventListener('DOMContentLoaded', () => {
      initCanvas();
      applyLighting('light-white');
      updateCostDisplay();

      document.querySelectorAll('[data-element-id^="bg-"]').forEach(option => {
        option.addEventListener('click', () => applyBackground(option.getAttribute('data-element-id')));
      });
      document.querySelectorAll('[data-element-id^="light-"]').forEach(option => {
        option.addEventListener('click', () => applyLighting(option.getAttribute('data-element-id')));
      });

      document.getElementById('resetBtn').addEventListener('click', showResetModal);
      document.getElementById('bringToFrontBtn').addEventListener('click', () => {
        if (selectedObject) { selectedObject.bringToFront(); canvas.renderAll(); }
      });
      document.getElementById('sendToBackBtn').addEventListener('click', () => {
        if (selectedObject) { selectedObject.sendToBack(); canvas.renderAll(); }
      });
      document.getElementById('deleteBtn').addEventListener('click', deleteSelectedObject);
      document.getElementById('bookButton').addEventListener('click', saveDesignToDatabase);

      document.addEventListener('keydown', (e) => {
        if ((e.key === 'Delete' || e.key === 'Backspace') && selectedObject) {
          deleteSelectedObject();
        }
        if (e.key === 'Escape') hideResetModal();
      });

      document.getElementById('resetModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) hideResetModal();
      });
    });
  </script>
</body>
</html>