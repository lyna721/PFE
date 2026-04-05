<?php include("config/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>CustomShop | Home</title>
    <style>
        .main { margin-left: 280px; padding: 40px; min-height: 100vh; background: #fdfdfd; }
        .search-container { margin-bottom: 20px; display: flex; gap: 10px; padding: 10px; background: white; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .search-container input { flex: 1; padding: 12px; border: 1px solid #eee; border-radius: 8px; outline: none; }
        
        /* Category Tabs Styles */
        .category-tabs { display: flex; gap: 10px; margin-bottom: 30px; overflow-x: auto; padding-bottom: 5px; }
        .tab-link { 
            padding: 8px 18px; background: #eee; border-radius: 20px; 
            text-decoration: none; color: #555; font-size: 14px; transition: 0.3s;
            white-space: nowrap;
        }
        .tab-link:hover, .tab-link.active { background: #0095f6; color: white; }
        
        .badge { background: #ff4d4d; color: white; padding: 2px 7px; border-radius: 50%; font-size: 11px; margin-left: 5px; }
        .btn-chat { background: #444; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; font-size: 13px; }
        .price-tag { font-weight: bold; color: #2ecc71; margin: 5px 0; display: block; }
        .cat-tag { font-size: 10px; background: #f0f0f0; padding: 2px 6px; border-radius: 4px; color: #888; text-transform: uppercase; }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>CustomShop</h2>
        <?php if(isset($_SESSION['user_id'])){ 
            $uid = $_SESSION['user_id'];
            $notif_res = $conn->query("SELECT COUNT(*) as t FROM notifications WHERE user_id='$uid' AND is_read=0")->fetch_assoc();
            $msg_res = $conn->query("SELECT COUNT(*) as t FROM messages WHERE receiver_id='$uid' AND is_read=0")->fetch_assoc();
        ?>
            <div style="text-align:center; padding:15px;">
                <img src="<?php echo $_SESSION['pic']; ?>" width="70" style="border-radius:50%; border: 2px solid #0095f6;">
                <p><strong><?php echo $_SESSION['name']; ?></strong></p>
                <a href="profile.php" style="font-size:11px; color:#0095f6;">Edit Profile</a>
            </div>
            <hr>
            <a href="index.php">🏠 Home Feed</a>
            <a href="messages.php">✉️ Messages <?php if($msg_res['t']>0) echo "<span class='badge'>".$msg_res['t']."</span>"; ?></a>
            <a href="notifications.php">🔔 Alerts <?php if($notif_res['t']>0) echo "<span class='badge'>".$notif_res['t']."</span>"; ?></a>
            
            <?php if($_SESSION['role'] == "seller"){ ?>
                <a href="create_product.php">✨ Create New Design</a>
                <a href="dashboard.php">📊 My Shop Orders</a>
            <?php } else { ?>
                <a href="nails.php">💅 My Nails</a>
                <a href="measurements.php">📏 My Sizes</a>
            <?php } ?>
            <a href="logout.php">🚪 Logout</a>
        <?php } else { ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php } ?>
    </div>

    <div class="main">
        <div class="search-container">
            <form action="index.php" method="GET" style="display:flex; width:100%; gap:10px;">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo $_GET['search'] ?? ''; ?>">
                <button type="submit" class="btn" style="width:100px; margin:0;">Search</button>
            </form>
        </div>

        <div class="category-tabs">
            <?php $active_cat = $_GET['cat'] ?? ''; ?>
            <a href="index.php" class="tab-link <?php echo $active_cat == '' ? 'active' : ''; ?>">All</a>
            <a href="index.php?cat=Hoodies" class="tab-link <?php echo $active_cat == 'Hoodies' ? 'active' : ''; ?>">Hoodies</a>
            <a href="index.php?cat=T-Shirts" class="tab-link <?php echo $active_cat == 'T-Shirts' ? 'active' : ''; ?>">T-Shirts</a>
            <a href="index.php?cat=Nails" class="tab-link <?php echo $active_cat == 'Nails' ? 'active' : ''; ?>">Nails</a>
            <a href="index.php?cat=Accessories" class="tab-link <?php echo $active_cat == 'Accessories' ? 'active' : ''; ?>">Accessories</a>
        </div>

        <h2 class="title"><?php echo $active_cat ?: 'Product Feed'; ?></h2>
        <div class="feed">
            <?php
            $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
            $cat = isset($_GET['cat']) ? $conn->real_escape_string($_GET['cat']) : '';
            
            $sql = "SELECT * FROM products WHERE status='Active'";
            if($search) $sql .= " AND (name LIKE '%$search%' OR category LIKE '%$search%')";
            if($cat) $sql .= " AND category = '$cat'";
            
            $res = $conn->query($sql . " ORDER BY id DESC");

            if($res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    $pid = $row['id'];
                    $seller_id = $row['user_id'];
                    $likes = $conn->query("SELECT COUNT(*) as t FROM likes WHERE product_id='$pid'")->fetch_assoc();
            ?>
                <div class="card">
                    <img src="<?php echo $row['image']; ?>" class="product-img">
                    <div class="card-content">
                        <span class="cat-tag"><?php echo $row['category']; ?></span>
                        <h3><?php echo $row['name']; ?></h3>
                        <span class="price-tag">$<?php echo number_format($row['price'], 2); ?></span>
                        <p>❤️ <span id="like-count-<?php echo $pid; ?>"><?php echo $likes['t']; ?></span> likes</p>
                        
                        <div style="display:flex; gap:8px; margin-top:10px; align-items:center;">
                            <button class="btn-like" onclick="doLike(<?php echo $pid; ?>)">Like</button>
                            
                            <?php if(isset($_SESSION['user_id'])){ ?>
                                <a href="messages.php?user_id=<?php echo $seller_id; ?>" class="btn-chat">💬 Chat</a>
                                <a href="order.php?id=<?php echo $pid; ?>" class="btn-order">Order</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } 
            } else { echo "<p style='color:gray; padding:20px;'>No designs found here yet.</p>"; } ?>
        </div>
    </div>
</div>

<script>
function doLike(pid) {
    $.post("like.php", { id: pid }, function(data) {
        if(data == "login_required") { window.location.href = "login.php"; }
        else {
            let countSpan = $("#like-count-" + pid);
            let currentCount = parseInt(countSpan.text());
            countSpan.text(data == "liked" ? currentCount + 1 : currentCount - 1);
        }
    });
}
</script>
</body>
</html>