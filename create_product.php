<?php include("config/db.php"); 
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') { header("Location: login.php"); exit(); }
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <style>
        .main { margin-left: 280px; padding: 40px; display: flex; gap: 30px; }
        #canvas-area { position: relative; width: 500px; height: 500px; background: #ffffff; border: 1px solid #ddd; }
        #blank-base { width: 100%; height: 100%; object-fit: contain; pointer-events: none; position: absolute; top:0; left:0; }

        /* Safe Zone */
        #design-container {
            position: absolute;
            top: 120px; left: 140px; width: 220px; height: 280px;
            z-index: 10; overflow: hidden;
            border: 1px dashed rgba(0,0,0,0.2);
            background: transparent;
        }
        
        #logo-preview { width: 120px; display: none; cursor: move; position: relative; background: transparent; }

        /* THE FIX: Remove white backgrounds and fix scaling */
        #inner-logo { 
            width: 100%; height: 100%;
            display: block;
            mix-blend-mode: multiply; 
            user-select: none;
        }

        /* Delete Button Style */
        #remove-logo {
            position: absolute; top: -10px; right: -10px;
            background: #ff4d4d; color: white; border-radius: 50%;
            width: 22px; height: 22px; text-align: center; line-height: 20px;
            cursor: pointer; font-weight: bold; font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 100;
        }

        .controls { width: 350px; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .ui-resizable-handle { background: #0095f6; width: 10px; height: 10px; border-radius: 50%; border: 2px solid white; }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>Seller Panel</h2>
        <a href="index.php">🏠 Home Feed</a>
        <a href="create_product.php" class="active">✨ Create New Design</a>
        <a href="dashboard.php">📦 My Orders</a>
        <hr style="border: 0.5px solid #444; margin: 15px 0;">
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="main">
        <div id="canvas-area">
            <img id="blank-base" src="assets/blanks/hoodieM2.png">
            <div id="design-container">
                <div id="logo-preview">
                    <span id="remove-logo" title="Remove Logo">×</span>
                    <img id="inner-logo" src="">
                </div>
            </div>
        </div>

        <div class="controls">
            <h3>Product Creator</h3>
            <label>1. Select Blank Type</label>
            <select id="blank-selector">
                <option value="hoodieM2.png" data-top="120" data-left="140" data-w="220" data-h="280">White Hoodie</option>
                <option value="shirtM.png" data-top="100" data-left="150" data-w="200" data-h="300">White T-Shirt</option>
                <option value="cup.png" data-top="150" data-left="180" data-w="120" data-h="150">Coffee Mug</option>
            </select>

            <label>2. Product Category</label>
            <select id="prod-category">
                <option value="Hoodies">Hoodies</option>
                <option value="T-Shirts">T-Shirts</option>
                <option value="Accessories">Accessories</option>
            </select>

            <label>3. Upload Logo</label>
            <input type="file" id="upload-logo" accept="image/*">

            <label>4. Details</label>
            <input type="text" id="prod-name" placeholder="Product Name">
            <input type="number" id="prod-price" value="25.00">

            <button id="save-btn" class="btn" style="width:100%; margin-top:20px;">Create & Publish</button>
            <p id="status-msg" style="margin-top:10px; font-size:13px; text-align:center;"></p>
        </div>
    </div>
</div>

<script>

$("#logo-preview").resizable({
    /* Removed aspect Ratio to fix zooming and allow 8 handles */
    handles: "n, e, s, w, ne, se, sw, nw", // ADD THIS FOR 8 HANDLES
    containment: "#design-container",
    stop: function(event, ui) {
        // When the user stops resizing, we update the image inside
        // to match the new container size.
        $("#inner-logo").css({
            "width": ui.size.width + "px",
            "height": ui.size.height + "px"
        });
    }
});

$(document).on("click", "#remove-logo", function() {
    $("#inner-logo").attr("src", "");
    $("#logo-preview").hide();
    $("#upload-logo").val("");
});

$("#blank-selector").change(function(){
    let sel = $(this).find('option:selected');
    $("#blank-base").attr("src", "assets/blanks/" + sel.val());
    $("#design-container").css({
        "top": sel.data("top") + "px", "left": sel.data("left") + "px",
        "width": sel.data("w") + "px", "height": sel.data("h") + "px"
    });
});

$("#upload-logo").change(function(e){
    var reader = new FileReader();
    reader.onload = function(event){
        $("#inner-logo").attr("src", event.target.result);
        $("#logo-preview").show();
    }
    reader.readAsDataURL(e.target.files[0]);
});

$("#save-btn").click(function(){
    let name = $("#prod-name").val();
    if(!name) { alert("Name your product!"); return; }
    
    // Hide the "X" and dashed border before taking the screenshot
    $("#remove-logo").hide();
    $("#design-container").css("border", "none");

    html2canvas(document.querySelector("#canvas-area")).then(canvas => {
        let imageData = canvas.toDataURL("image/png");
        $.post("save_design.php", {
            image: imageData,
            name: name,
            price: $("#prod-price").val(),
            category: $("#prod-category").val()
        }, function(response){
            if(response.trim() == "success") {
                window.location.href="index.php";
            } else {
                alert("Error: " + response);
                $("#remove-logo").show(); // Bring back controls if it fails
                $("#design-container").css("border", "1px dashed rgba(0,0,0,0.2)");
            }
        });
    });
});
</script>
</body>
</html>