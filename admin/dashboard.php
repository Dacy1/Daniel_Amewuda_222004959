<?php
require_once '../config.php';
requireLogin();

$pdo = getConnection();

// Get dashboard statistics
$stats = [];
$stats['projects'] = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$stats['messages'] = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
$stats['skills'] = $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();
$stats['admins'] = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();

// Get recent messages
$recent_messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio</title>
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
        .user-info { display: flex; align-items: center; gap: 1rem; }
        .logout-btn { 
            background: #ce1126;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 5px;
        }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        .stats-grid { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card { 
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number { 
            font-size: 2.5rem;
            font-weight: bold;
            color: #006b3c;
            margin-bottom: 0.5rem;
        }
        .stat-label { 
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .nav-menu {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .nav-menu h2 { margin-bottom: 1rem; color: #006b3c; }
        .nav-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .nav-link { 
            background: #006b3c;
            color: white;
            padding: 1rem;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .nav-link:hover { background: #005530; }
        .recent-section { 
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section-header { 
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        .message-item { 
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        .message-item:last-child { border-bottom: none; }
        .message-info h4 { color: #333; margin-bottom: 0.25rem; }
        .message-info p { color: #666; font-size: 0.9rem; }
        .no-messages { text-align: center; color: #666; padding: 2rem; }
        .badge { 
            background: #fcd116;
            color: #333;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <?php if (isSuperAdmin()): ?>
                <span class="badge">Super Admin</span>
            <?php endif; ?>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['projects']; ?></div>
                <div class="stat-label">Total Projects</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['messages']; ?></div>
                <div class="stat-label">Unread Messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['skills']; ?></div>
                <div class="stat-label">Skills Listed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['admins']; ?></div>
                <div class="stat-label">Admin Users</div>
            </div>
        </div>
        
        <div class="nav-menu">
            <h2>Admin Functions</h2>
          <div class="nav-links">
    <a href="projects.php" class="nav-link">Manage Projects</a>
    <a href="about.php" class="nav-link">Manage About</a>
    <a href="experience.php" class="nav-link">Manage Experience</a>
    <a href="education.php" class="nav-link">Manage Education</a>
    <a href="skills.php" class="nav-link">Manage Skills</a>
    <a href="messages.php" class="nav-link">Contact Messages</a>
    <a href="blog.php" class="nav-link">Blog Posts</a>
    <?php if (isSuperAdmin()): ?>
    <a href="admins.php" class="nav-link">Manage Admins</a>
    <?php endif; ?>
</div>
        </div>
        
        <div class="recent-section">
            <div class="section-header">
                <h2>Recent Contact Messages</h2>
            </div>
            
            <?php if (empty($recent_messages)): ?>
                <div class="no-messages">
                    <p>No messages yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($recent_messages as $message): ?>
                    <div class="message-item">
                        <div class="message-info">
                            <h4><?php echo htmlspecialchars($message['name']); ?></h4>
                            <p><?php echo htmlspecialchars($message['email']); ?></p>
                            <p><?php echo htmlspecialchars(substr($message['message'], 0, 100)); ?>...</p>
                            <small><?php echo date('M j, Y', strtotime($message['created_at'])); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>