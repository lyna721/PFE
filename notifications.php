<?php include("config/db.php"); 
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$my_id = $_SESSION['user_id'];
$chat_with = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

// --- MESSAGE SENDING LOGIC ---
if(isset($_POST['send_msg']) && $chat_with > 0){
    $message = $conn->real_escape_string($_POST['message_text']);
    if(!empty($message)){
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ('$my_id', '$chat_with', '$message')";
        if($conn->query($sql)){
            // Success! Redirect to refresh the list
            header("Location: messages.php?user_id=$chat_with");
            exit();
        } else {
            // THIS WILL TELL YOU IF THE TABLE IS MISSING
            $error_msg = "Database Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main { margin-left: 280px; padding: 40px; }
        .chat-container { display: flex; height: 550px; background: white; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; }
        .chat-sidebar { width: 30%; border-right: 1px solid #eee; overflow-y: auto; background: #fafafa; }
        .chat-window { width: 70%; display: flex; flex-direction: column; }
        .msg-history { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; }
        .msg-me { align-self: flex-end; background: #0095f6; color: white; padding: 10px 15px; border-radius: 15px 15px 0 15px; }
        .msg-them { align-self: flex-start; background: #eee; color: #333; padding: 10px 15px; border-radius: 15px 15px 15px 0; }
        .chat-sidebar a { display: block; padding: 15px; border-bottom: 1px solid #eee; text-decoration: none; color: #333; }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>Messages</h2>
        <a href="index.php">← Home Feed</a>
    </div>

    <div class="main">
        <?php if(isset($error_msg)) echo "<p style='color:red; background:#ffdada; padding:10px;'>$error_msg</p>"; ?>

        <div class="chat-container">
            <div class="chat-sidebar">
                <?php
                $q = "SELECT DISTINCT IF(sender_id = $my_id, receiver_id, sender_id) AS contact_id FROM messages WHERE sender_id = $my_id OR receiver_id = $my_id";
                $res = $conn->query($q);
                if($res && $res->num_rows > 0){
                    while($row = $res->fetch_assoc()){
                        $cid = $row['contact_id'];
                        $user = $conn->query("SELECT name FROM users WHERE id='$cid'")->fetch_assoc();
                        $bg = ($chat_with == $cid) ? "background: #e7f3ff;" : "";
                        echo "<a href='messages.php?user_id=$cid' style='$bg'>👤 ".$user['name']."</a>";
                    }
                } else {
                    echo "<p style='padding:20px; color:gray; font-size:12px;'>No chats yet.</p>";
                }
                ?>
            </div>

            <div class="chat-window">
                <?php if($chat_with > 0): 
                    $other = $conn->query("SELECT name FROM users WHERE id='$chat_with'")->fetch_assoc();
                ?>
                    <div style="padding:15px; border-bottom:1px solid #eee; background: #fff;">
                        <strong>Chatting with <?php echo $other['name']; ?></strong>
                    </div>

                    <div class="msg-history">
                        <?php
                        $msgs = $conn->query("SELECT * FROM messages WHERE (sender_id=$my_id AND receiver_id=$chat_with) OR (sender_id=$chat_with AND receiver_id=$my_id) ORDER BY created_at ASC");
                        if($msgs){
                            while($m = $msgs->fetch_assoc()){
                                $class = ($m['sender_id'] == $my_id) ? "msg-me" : "msg-them";
                                echo "<div class='$class'>".$m['message']."</div>";
                            }
                        }
                        ?>
                    </div>

                    <form method="POST" style="padding:15px; border-top: 1px solid #eee; display: flex; gap: 10px;">
                        <input type="text" name="message_text" placeholder="Type message..." required style="flex:1; padding:10px; border-radius:5px; border:1px solid #ddd;">
                        <button name="send_msg" class="btn" style="width:80px; margin:0;">Send</button>
                    </form>
                <?php else: ?>
                    <div style="display:flex; height:100%; align-items:center; justify-content:center; color:gray; text-align:center;">
                        <p>Select a contact on the left<br>or click "Chat" on a product!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>