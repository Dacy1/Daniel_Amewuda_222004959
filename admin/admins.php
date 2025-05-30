<?php
require_once '../config.php';
requireLogin();
requireSuperAdmin(); // Only super admin can manage admins

$pdo = getConnection();
$message = '';
$error = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    // Check if username or email already exists
                    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
                    $stmt->execute([$_POST['username'], $_POST['email']]);
                    if ($stmt->fetch()) {
                        $error = 'Username or email already exists!';
                    } else {
                        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO admins (username, email, password, role) VALUES (?, ?, ?, ?)");
                        $stmt->execute([
                            $_POST['username'],
                            $_POST['email'],
                            $hashed_password,
                            $_POST['role']
                        ]);
                        $message = 'Admin added successfully!';
                    }
                } catch (PDOException $e) {
                    $error = 'Error adding admin: ' . $e->getMessage();
                }
                break;
                
            case 'edit':
                try {
                    if (!empty($_POST['password'])) {
                        // Update with new password
                        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE admins SET username=?, email=?, password=?, role=? WHERE id=?");
                        $stmt->execute([
                            $_POST['username'],
                            $_POST['email'],
                            $hashed_password,
                            $_POST['role'],
                            $_POST['id']
                        ]);
                    } else {
                        // Update without changing password
                        $stmt = $pdo->prepare("UPDATE admins SET username=?, email=?, role=? WHERE id=?");
                        $stmt->execute([
                            $_POST['username'],
                            $_POST['email'],
                            $_POST['role'],
                            $_POST['id']
                        ]);
                    }
                    $message = 'Admin updated successfully!';
                } catch (PDOException $e) {
                    $error = 'Error updating admin: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                try {
                    // Prevent deleting self
                    if ($_POST['id'] == $_SESSION['admin_id']) {
                        $error = 'You cannot delete your own account!';
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM admins WHERE id=?");
                        $stmt->execute([$_POST['id']]);
                        $message = 'Admin deleted successfully!';
                    }
                } catch (PDOException $e) {
                    $error = 'Error deleting admin: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all admins
$admins = $pdo->query("SELECT * FROM admins ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get admin for editing if ID is provided
$edit_admin = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_admin = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - Admin Panel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            line-height: 1.6;
        }
        .header { 
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { color: #006b3c; }
        .btn { 
            background: #006b3c;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 0.5rem;
        }
        .btn:hover { background: #005530; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-secondary { background: #6c757d; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        .content-section { 
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .form-grid { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-group { margin-bottom: 1rem; }
        label { 
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        input, select { 
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        .admins-table { 
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .admins-table th,
        .admins-table td { 
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .admins-table th { 
            background: #f8f9fa;
            font-weight: 600;
        }
        .admins-table tr:hover { background: #f8f9fa; }
        .message { 
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        .success { 
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error { 
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .role-badge { 
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .role-super { 
            background: #fcd116;
            color: #333;
        }
        .role-admin { 
            background: #e9ecef;
            color: #495057;
        }
        .admin-actions { display: flex; gap: 0.5rem; }
        .current-user { background: #e3f2fd; }
        .warning-text { 
            color: #856404;
            background: #fff3cd;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage Admins</h1>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
    
    <div class="container">
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="content-section">
            <h2><?php echo $edit_admin ? 'Edit Admin' : 'Add New Admin'; ?></h2>
            
            <div class="warning-text">
                <strong>Important:</strong> Only Super Admins can manage admin accounts. Regular admins have limited access to portfolio management features.
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $edit_admin ? 'edit' : 'add'; ?>">
                <?php if ($edit_admin): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_admin['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo $edit_admin ? htmlspecialchars($edit_admin['username']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo $edit_admin ? htmlspecialchars($edit_admin['email']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="password">Password <?php echo $edit_admin ? '(leave blank to keep current)' : '*'; ?></label>
                        <input type="password" id="password" name="password" 
                               <?php echo $edit_admin ? '' : 'required'; ?>>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role *</label>
                        <select id="role" name="role" required>
                            <option value="admin" <?php echo ($edit_admin && $edit_admin['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="super_admin" <?php echo ($edit_admin && $edit_admin['role'] === 'super_admin') ? 'selected' : ''; ?>>Super Admin</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn">
                    <?php echo $edit_admin ? 'Update Admin' : 'Add Admin'; ?>
                </button>
                
                <?php if ($edit_admin): ?>
                    <a href="admins.php" class="btn btn-secondary">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="content-section">
            <h2>Existing Admins</h2>
            
            <table class="admins-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr <?php echo $admin['id'] == $_SESSION['admin_id'] ? 'class="current-user"' : ''; ?>>
                            <td>
                                <?php echo htmlspecialchars($admin['username']); ?>
                                <?php if ($admin['id'] == $_SESSION['admin_id']): ?>
                                    <small>(You)</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td>
                                <span class="role-badge <?php echo $admin['role'] === 'super_admin' ? 'role-super' : 'role-admin'; ?>">
                                    <?php echo $admin['role'] === 'super_admin' ? 'Super Admin' : 'Admin'; ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($admin['created_at'])); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="admins.php?edit=<?php echo $admin['id']; ?>" class="btn">Edit</a>
                                    
                                    <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>