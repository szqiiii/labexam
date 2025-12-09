<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Lab Exam - Home</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(0,0,0,0.1);
        }
        
        .btn-secondary {
            background: #f1f1f1;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #e1e1e1;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            font-size: 14px;
        }
        
        .info-box h3 {
            color: #667eea;
            margin-bottom: 8px;
        }
        
        .status {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .status.online {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔬 Network Lab Exam</h1>
        <p class="subtitle">Local Server Application for Traffic Analysis</p>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="info-box">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
                <p>You are logged in and ready to use the chat system.</p>
            </div>
            <a href="chat.php" class="btn btn-primary">📨 Go to Chat</a>
            <a href="logout.php" class="btn btn-secondary">🚪 Logout</a>
        <?php else: ?>
            <div class="info-box">
                <h3>Network Analysis Tasks:</h3>
                <ul style="padding-left: 20px; margin-top: 8px;">
                    <li>Observe HTTP traffic in Wireshark</li>
                    <li>Identify protocols & ports used</li>
                    <li>Analyze plain text data transmission</li>
                    <li>Compare with HTTPS (Bonus)</li>
                </ul>
            </div>
            
            <a href="login.php" class="btn btn-primary">🔑 Login</a>
            <a href="register.php" class="btn btn-secondary">📝 Register</a>
        <?php endif; ?>
        
        <div class="status <?php echo isset($_SESSION['user_id']) ? 'online' : ''; ?>">
            Status: <?php echo isset($_SESSION['user_id']) ? 'Logged In' : 'Not Logged In'; ?>
        </div>
    </div>
</body>
</html>