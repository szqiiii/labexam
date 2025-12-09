<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$message = '';

// Handle sending messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message_text = trim($_POST['message']);
    
    if (empty($message_text)) {
        $error = 'Message cannot be empty';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $message_text]);
            $message = 'Message sent successfully!';
        } catch(PDOException $e) {
            $error = 'Error sending message: ' . $e->getMessage();
        }
    }
}

// Fetch all messages with usernames
try {
    $stmt = $pdo->prepare("
        SELECT m.message, m.created_at, u.username 
        FROM messages m 
        JOIN users u ON m.user_id = u.id 
        ORDER BY m.created_at DESC
        LIMIT 50
    ");
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = 'Error loading messages: ' . $e->getMessage();
    $messages = [];
}

// Handle delete all messages
if (isset($_POST['delete_all'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM messages");
        $stmt->execute();
        header('Location: chat.php');
        exit();
    } catch(PDOException $e) {
        $error = 'Error clearing messages: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Network Lab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .header {
            background: white;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        h1 {
            color: #333;
            font-size: 24px;
        }
        
        .user-info {
            color: #666;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-logout {
            background: #dc3545;
            color: white;
        }
        
        .btn-logout:hover {
            background: #c82333;
        }
        
        .btn-home {
            background: #6c757d;
            color: white;
            margin-right: 10px;
        }
        
        .btn-home:hover {
            background: #5a6268;
        }
        
        .btn-clear {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-clear:hover {
            background: #e0a800;
        }
        
        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .chat-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            height: 600px;
            display: flex;
            flex-direction: column;
        }
        
        .messages-container {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8f9fa;
        }
        
        .message {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-left: 4px solid #667eea;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .username {
            font-weight: 600;
            color: #667eea;
        }
        
        .timestamp {
            font-size: 12px;
            color: #999;
        }
        
        .message-content {
            color: #333;
            line-height: 1.5;
        }
        
        .input-container {
            padding: 20px;
            background: white;
            border-top: 1px solid #e1e1e1;
        }
        
        .form-group {
            display: flex;
            gap: 10px;
        }
        
        input[type="text"] {
            flex: 1;
            padding: 15px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-send {
            padding: 15px 30px;
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(0,0,0,0.1);
        }
        
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .info-box {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2196F3;
        }
        
        .info-box h3 {
            color: #2196F3;
            margin-bottom: 8px;
        }
        
        .info-box p {
            font-size: 14px;
            color: #666;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div>
                <h1>📨 Network Lab Chat System</h1>
                <div class="user-info">Logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></div>
            </div>
            <div>
                <a href="index.php" class="btn btn-home">🏠 Home</a>
                <a href="logout.php" class="btn btn-logout">🚪 Logout</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="info-box">
            <h3>🔍 Wireshark Analysis Point</h3>
            <p>All messages are transmitted over HTTP in plain text. Use Wireshark to:</p>
            <ul style="margin-left: 20px; margin-top: 8px;">
                <li>Observe POST requests when sending messages</li>
                <li>Analyze the HTTP headers and body content</li>
                <li>Identify the protocol (HTTP) and port (80)</li>
                <li>Compare with HTTPS traffic (bonus implementation)</li>
            </ul>
        </div>
        
        <div class="chat-container">
            <div class="messages-container" id="messagesContainer">
                <?php if(empty($messages)): ?>
                    <div class="message" style="text-align: center; color: #999;">
                        No messages yet. Start the conversation!
                    </div>
                <?php else: ?>
                    <?php foreach($messages as $msg): ?>
                        <div class="message">
                            <div class="message-header">
                                <span class="username"><?php echo htmlspecialchars($msg['username']); ?></span>
                                <span class="timestamp"><?php echo date('M j, H:i', strtotime($msg['created_at'])); ?></span>
                            </div>
                            <div class="message-content">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="input-container">
                <form method="POST" action="" id="messageForm">
                    <div class="form-group">
                        <input type="text" name="message" placeholder="Type your message here..." autocomplete="off" required>
                        <button type="submit" class="btn-send">Send</button>
                    </div>
                </form>
                
                <div class="actions">
                    <form method="POST" action="" onsubmit="return confirm('Clear all messages?')">
                        <button type="submit" name="delete_all" class="btn btn-clear">🗑️ Clear All Messages</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-scroll to bottom of messages
        function scrollToBottom() {
            const container = document.getElementById('messagesContainer');
            container.scrollTop = container.scrollHeight;
        }
        
        // Scroll on page load
        window.addEventListener('load', scrollToBottom);
        
        // Auto-refresh messages every 5 seconds
        setInterval(() => {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newMessages = doc.querySelector('.messages-container').innerHTML;
                    document.querySelector('.messages-container').innerHTML = newMessages;
                    scrollToBottom();
                });
        }, 5000);
        
        // Form submission with AJAX for better UX
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                document.querySelector('.messages-container').innerHTML = doc.querySelector('.messages-container').innerHTML;
                document.querySelector('.input-container input[name="message"]').value = '';
                scrollToBottom();
            });
        });
    </script>
</body>
</html>