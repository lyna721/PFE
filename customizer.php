<?php include("config/db.php"); 
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$type = $_GET['type'] ?? 'shirt'; 
$base_img = "images/plain-" . $type . ".png"; 
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .custom-area { position: relative; width: 350px; margin: 20px auto; border: 2px solid #eee; }
        .base-item { width: 100%; display: block; }
        #logo-preview { 
            position: absolute; top: 30%; left: 30%; width: 100px; 
            cursor: move; border: 1px dashed transparent; 
        }
        #logo-preview:hover { border-color: #007bff; }
    </style>
</head>
<body>
<div class="container">
    <div class="main" style="text-align:center;">
        <h2>Customize Your <?php echo ucfirst($type); ?></h2>
        
        <div class="custom-area">
            <img src="<?php echo $base_img; ?>" class="base-item">
            <img id="logo-preview" src="" style="display:none;">
        </div>

        <div class="form-box">
            <label>Upload your design (PNG recommended):</label><br><br>
            <input type="file" id="upload-design" accept="image/*" onchange="previewDesign(event)">
            <p><small>Drag the logo to position it!</small></p>
            <hr>
            <form action="order.php" method="GET">
                <input type="hidden" name="type" value="<?php echo $type; ?>">
                <button class="btn">Confirm Design & Order</button>
            </form>
        </div>
    </div>
</div>

<script>
function previewDesign(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('logo-preview');
        output.src = reader.result;
        output.style.display = "block";
    }
    reader.readAsDataURL(event.target.files[0]);
}

// Simple drag logic
const logo = document.getElementById('logo-preview');
let isDragging = false;
logo.onmousedown = () => isDragging = true;
window.onmouseup = () => isDragging = false;
window.onmousemove = (e) => {
    if(isDragging) {
        logo.style.left = (e.pageX - logo.offsetWidth) + 'px';
        logo.style.top = (e.pageY - logo.offsetHeight) + 'px';
    }
}
</script>
</body>
</html>