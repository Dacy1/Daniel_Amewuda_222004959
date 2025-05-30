<?php
require_once '../config.php';
requireLogin();

$pdo = getConnection();
$message = '';
$error = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    // Create slug from title
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title'])));
                    
                    $stmt = $pdo->prepare("INSERT INTO blog_posts (title, slug, content, excerpt, status, author_id) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['title'],
                        $slug,
                        $_POST['content'],
                        $_POST['excerpt'],
                        $_POST['status'],
                        $_SESSION['admin_id']
                    ]);
                    $message = 'Blog post added successfully!';
                } catch (PDOException $e) {
                    $error = 'Error adding blog post: ' . $e->getMessage();
                }
                break;
                
            case 'edit':
                try {
                    // Create slug from title
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title'])));
                    
                    $stmt = $pdo->prepare("UPDATE blog_posts SET title=?, slug=?, content=?, excerpt=?, status=? WHERE id=?");
                    $stmt->execute([
                        $_POST['title'],
                        $slug,
                        $_POST['content'],
                        $_POST['excerpt'],
                        $_POST['status'],
                        $_POST['id']
                    ]);
                    $message = 'Blog post updated successfully!';
                } catch (PDOException $e) {
                    $error = 'Error updating blog post: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                try {
                    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id=?");
                    $stmt->execute([$_POST['id']]);
                    $message = 'Blog post deleted successfully!';
                } catch (PDOException $e) {
                    $error = 'Error deleting blog post: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all blog posts with author info
$blog_posts = $pdo->query("
    SELECT bp.*, a.username as author_name 
    FROM blog_posts bp 
    LEFT JOIN admins a ON bp.author_id = a.id 
    ORDER BY bp.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Get blog post for editing if ID is provided
$edit_post = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_post = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blog Posts - Admin</title>
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
        .form-group.full-width { grid-column: 1 / -1; }
        label { 
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        input, textarea, select { 
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        textarea { resize: vertical; }
        .content-textarea { height: 300px; }
        .excerpt-textarea { height: 100px; }
        .posts-table { 
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .posts-table th,
        .posts-table td { 
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .posts-table th { 
            background: #f8f9fa;
            font-weight: 600;
        }
        .posts-table tr:hover { background: #f8f9fa; }
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
        .post-actions { display: flex; gap: 0.5rem; }
        .post-meta { font-size: 0.9rem; color: #666; }
        .status-badge { 
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-published { 
            background: #d4edda;
            color: #155724;
        }
        .status-draft { 
            background: #fff3cd;
            color: #856404;
        }
        .help-text { 
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage Blog Posts</h1>
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
            <h2><?php echo $edit_post ? 'Edit Blog Post' : 'Create New Blog Post'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $edit_post ? 'edit' : 'add'; ?>">
                <?php if ($edit_post): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_post['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="title">Post Title *</label>
                        <input type="text" id="title" name="title" required 
                               value="<?php echo $edit_post ? htmlspecialchars($edit_post['title']) : ''; ?>"
                               placeholder="Enter an engaging blog post title">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <option value="draft" <?php echo ($edit_post && $edit_post['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo ($edit_post && $edit_post['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="excerpt">Excerpt</label>
                    <textarea id="excerpt" name="excerpt" class="excerpt-textarea" 
                              placeholder="Write a brief summary of your blog post..."><?php echo $edit_post ? htmlspecialchars($edit_post['excerpt']) : ''; ?></textarea>
                    <div class="help-text">A short description that will appear in blog listings and previews</div>
                </div>
                
                <div class="form-group full-width">
                    <label for="content">Content *</label>
                    <textarea id="content" name="content" class="content-textarea" required 
                              placeholder="Write your blog post content here..."><?php echo $edit_post ? htmlspecialchars($edit_post['content']) : ''; ?></textarea>
                    <div class="help-text">Full content of your blog post. You can use line breaks for paragraphs.</div>
                </div>
                
                <button type="submit" class="btn">
                    <?php echo $edit_post ? 'Update Post' : 'Create Post'; ?>
                </button>
                
                <?php if ($edit_post): ?>
                    <a href="blog.php" class="btn btn-secondary">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="content-section">
            <h2>All Blog Posts</h2>
            
            <?php if (empty($blog_posts)): ?>
                <p>No blog posts found. Create your first blog post above!</p>
            <?php else: ?>
                <table class="posts-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Author</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($blog_posts as $post): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                                    <?php if ($post['excerpt']): ?>
                                        <div class="post-meta">
                                            <?php echo htmlspecialchars(substr($post['excerpt'], 0, 100)); ?>
                                            <?php if (strlen($post['excerpt']) > 100) echo '...'; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $post['status']; ?>">
                                        <?php echo ucfirst($post['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($post['author_name']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                                <td>
                                    <div class="post-actions">
                                        <a href="blog.php?edit=<?php echo $post['id']; ?>" class="btn">Edit</a>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this blog post?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>