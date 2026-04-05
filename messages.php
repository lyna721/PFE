<?php include("config/db.php"); 
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$my_id = $_SESSION['user_id'];
$chat_with = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

// LOGIC: Save the message when the Send button is clicked
if(isset($_POST['send_msg']) && $chat_with > 0){
    $message = $conn->real_escape_string($_POST['message_text']);
    if(!empty($message)){
        $conn->query("INSERT INTO messages (sender_id, receiver_id, message) VALUES ('$my_id', '$chat_with', '$message')");
        // Mark as read immediately
        header("Location: messages.php?user_id=$chat_with");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main { margin-left: 280px; padding: 40px; }
        .chat-container { display: flex; height: 600px; background: white; border: 1px solid #eee; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .chat-sidebar { width: 30%; border-right: 1px solid #eee; overflow-y: auto; background: #fafafa; }
        .chat-window { width: 70%; display: flex; flex-direction: column; }
        .msg-history { flex: 1; padding: 25px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: #fff; }
        .msg-me { align-self: flex-end; background: #0095f6; color: white; padding: 10px 15px; border-radius: 18px 18px 0 18px; max-width: 75%; }
        .msg-them { align-self: flex-start; background: #f1f1f1; color: #333; padding: 10px 15px; border-radius: 18px 18px 18px 0; max-width: 75%; }
        .chat-sidebar a { display: block; padding: 15px; border-bottom: 1px solid #eee; text-decoration: none; color: #333; font-size: 14px; }
        .chat-sidebar a:hover { background: #f0f7ff; }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>Messages</h2>
        <a href="index.php">🏠 Home Feed</a>
    </div>

    <div class="main">
        <div class="chat-container">
            <div class="chat-sidebar">
                <?php
                $q = "SELECT DISTINCT IF(sender_id = $my_id, receiver_id, sender_id) AS contact_id FROM messages WHERE sender_id = $my_id OR receiver_id = $my_id";
                $res = $conn->query($q);
                if($res->num_rows > 0){
                    while($row = $res->fetch_assoc()){
                        $cid = $row['contact_id'];
                        $user = $conn->query("SELECT name FROM users WHERE id='$cid'")->fetch_assoc();
                        $bg = ($chat_with == $cid) ? "background: #e7f3ff;" : "";
                        echo "<a href='messages.php?user_id=$cid' style='$bg'>👤 ".$user['name']."</a>";
                    }
                } else {
                    echo "<p style='padding:20px; color:gray; font-size:12px; text-align:center;'>No conversations yet.</p>";
                }
                ?>
            </div>

            <div class="chat-window">
                <?php if($chat_with > 0): 
                    $other = $conn->query("SELECT name FROM users WHERE id='$chat_with'")->fetch_assoc();
                    $conn->query("UPDATE messages SET is_read=1 WHERE sender_id='$chat_with' AND receiver_id='$my_id'");
                ?>
                    <div style="padding:15px; border-bottom:1px solid #eee; background: white;">
                        <strong><?php echo $other['name']; ?></strong>
                    </div>

                    <div class="msg-history">
                        <?php
                        $msgs = $conn->query("SELECT * FROM messages WHERE (sender_id=$my_id AND receiver_id=$chat_with) OR (sender_id=$chat_with AND receiver_id=$my_id) ORDER BY created_at ASC");
                        while($m = $msgs->fetch_assoc()){
                            $class = ($m['sender_id'] == $my_id) ? "msg-me" : "msg-them";
                            echo "<div class='$class'>".$m['message']."</div>";
                        }
                        ?>
                    </div>

                    <form method="POST" style="padding:15px; border-top: 1px solid #eee; display: flex; gap: 10px;">
                        <input type="text" name="message_text" placeholder="Type a message..." required style="flex:1; padding:12px; border:1px solid #ddd; border-radius:8px;">
                        <button name="send_msg" class="btn" style="width:80px; margin:0;">Send</button>
                    </form>
                <?php else: ?>
                    <div style="display:flex; height:100%; align-items:center; justify-content:center; color:gray; text-align:center;">
                        <div>
                            <p style="font-size:24px;">💬</p>
                            <p>Select a user to chat or click<br>"Chat" on a product in the feed!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>