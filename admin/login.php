<?php
require_once '../config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare("SELECT id, username, email, password, role FROM admins WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role'] = $admin['role'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            $error = 'Database error occurred';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Portfolio</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #006b3c, #ce1126);
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }
        .login-container { 
            background: white; 
            padding: 2rem; 
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); 
            width: 100%; 
            max-width: 400px;
        }
        .login-header { text-align: center; margin-bottom: 2rem; }
        .login-header h1 { color: #006b3c; margin-bottom: 0.5rem; }
        .login-header p { color: #666; }
        .form-group { margin-bottom: 1.5rem; }
        label { 
            display: block; 
            margin-bottom: 0.5rem; 
            color: #333; 
            font-weight: 500; 
        }
        input[type="text"], input[type="password"] {
            width: 100%; 
            padding: 0.75rem; 
            border: 1px solid #ddd; 
            border-radius: 5px;
            font-size: 1rem;
        }
        .btn { 
            width: 100%; 
            background: #006b3c; 
            color: white; 
            padding: 0.75rem; 
            border: none;
            border-radius: 5px; 
            font-size: 1rem; 
            cursor: pointer;
        }
        .btn:hover { background: #005530; }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 0.75rem; 
            border-radius: 5px;
            margin-bottom: 1rem; 
            border: 1px solid #f5c6cb;
        }
        .back-link { text-align: center; margin-top: 1rem; }
        .back-link a { color: #006b3c; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Login</h1>
            <p>Sign in to access the admin panel</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="back-link">
            <a href="../index.php">Back to Portfolio</a>
        </div>
    </div>
</body>
</html>