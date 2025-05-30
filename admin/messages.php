<?php
require_once '../config.php';
requireLogin();

$pdo = getConnection();
$message = '';
$error = '';

// Handle actions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'mark_read':
                try {
                    $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $message = 'Message marked as read!';
                } catch (PDOException $e) {
                    $error = 'Error updating message: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                try {
                    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $message = 'Message deleted successfully!';
                } catch (PDOException $e) {
                    $error = 'Error deleting message: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all messages
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get counts
$total_count = count($messages);
$unread_count = count(array_filter($messages, function($msg) { return !$msg['is_read']; }));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin</title>
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
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
        }
        .btn:hover { background: #005530; }
        .btn-secondary { background: #6c757d; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        .content-section { 
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stats-row { 
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .stat-item { text-align: center; }
        .stat-number { 
            font-size: 1.5rem;
            font-weight: bold;
            color: #006b3c;
        }
        .stat-label { 
            font-size: 0.9rem;
            color: #666;
        }
        .message-item { 
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 1rem;
            overflow: hidden;
        }
        .message-item.unread { 
            border-left: 4px solid #fcd116;
            background: #fffbf0;
        }
        .message-header { 
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .message-info h4 { 
            color: #333;
            margin-bottom: 0.25rem;
        }
        .message-meta { 
            font-size: 0.9rem;
            color: #666;
        }
        .message-actions { 
            display: flex;
            gap: 0.5rem;
        }
        .message-content { padding: 1.5rem; }
        .message-subject { 
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .message-text { 
            color: #666;
            line-height: 1.6;
        }
        .message { 
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
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
        .no-messages { 
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        .badge { 
            background: #fcd116;
            color: #333;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Contact Messages</h1>
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
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_count; ?></div>
                    <div class="stat-label">Total Messages</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $unread_count; ?></div>
                    <div class="stat-label">Unread</div>
                </div>
            </div>
            
            <?php if (empty($messages)): ?>
                <div class="no-messages">
                    <h3>No messages found</h3>
                    <p>No contact messages have been received yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message-item <?php echo !$msg['is_read'] ? 'unread' : ''; ?>">
                        <div class="message-header">
                            <div class="message-info">
                                <h4><?php echo htmlspecialchars($msg['name']); ?></h4>
                                <div class="message-meta">
                                    <strong>Email:</strong> <?php echo htmlspecialchars($msg['email']); ?> | 
                                    <strong>Date:</strong> <?php echo date('M j, Y g:i A', strtotime($msg['created_at'])); ?>
                                    <?php if (!$msg['is_read']): ?>
                                        <span class="badge">New</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="message-actions">
                                <?php if (!$msg['is_read']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="mark_read">
                                        <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
                                        <button type="submit" class="btn">Mark Read</button>
                                    </form>
                                <?php endif; ?>
                                
                                <form method="POST" style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this message?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="message-content">
                            <?php if ($msg['subject']): ?>
                                <div class="message-subject">
                                    <strong>Subject:</strong> <?php echo htmlspecialchars($msg['subject']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="message-text">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>