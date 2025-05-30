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
                    $stmt = $pdo->prepare("INSERT INTO skills (skill_name, category) VALUES (?, ?)");
                    $stmt->execute([
                        $_POST['skill_name'],
                        $_POST['category']
                    ]);
                    $message = 'Skill added successfully!';
                } catch (PDOException $e) {
                    $error = 'Error adding skill: ' . $e->getMessage();
                }
                break;
                
            case 'edit':
                try {
                    $stmt = $pdo->prepare("UPDATE skills SET skill_name=?, category=? WHERE id=?");
                    $stmt->execute([
                        $_POST['skill_name'],
                        $_POST['category'],
                        $_POST['id']
                    ]);
                    $message = 'Skill updated successfully!';
                } catch (PDOException $e) {
                    $error = 'Error updating skill: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                try {
                    $stmt = $pdo->prepare("DELETE FROM skills WHERE id=?");
                    $stmt->execute([$_POST['id']]);
                    $message = 'Skill deleted successfully!';
                } catch (PDOException $e) {
                    $error = 'Error deleting skill: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all skills
$skills = $pdo->query("SELECT * FROM skills ORDER BY category, skill_name")->fetchAll(PDO::FETCH_ASSOC);

// Get skill for editing if ID is provided
$edit_skill = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_skill = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills - Admin</title>
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
        .skills-table { 
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .skills-table th,
        .skills-table td { 
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .skills-table th { 
            background: #f8f9fa;
            font-weight: 600;
        }
        .skills-table tr:hover { background: #f8f9fa; }
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
        .skill-actions { display: flex; gap: 0.5rem; }
        .category-badge {
            background: #e9ecef;
            color: #495057;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage Skills</h1>
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
            <h2><?php echo $edit_skill ? 'Edit Skill' : 'Add New Skill'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $edit_skill ? 'edit' : 'add'; ?>">
                <?php if ($edit_skill): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_skill['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="skill_name">Skill Name *</label>
                        <input type="text" id="skill_name" name="skill_name" required 
                               value="<?php echo $edit_skill ? htmlspecialchars($edit_skill['skill_name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category">
                            <option value="Technical" <?php echo ($edit_skill && $edit_skill['category'] === 'Technical') ? 'selected' : ''; ?>>Technical</option>
                            <option value="Management" <?php echo ($edit_skill && $edit_skill['category'] === 'Management') ? 'selected' : ''; ?>>Management</option>
                            <option value="Research" <?php echo ($edit_skill && $edit_skill['category'] === 'Research') ? 'selected' : ''; ?>>Research</option>
                            <option value="Communication" <?php echo ($edit_skill && $edit_skill['category'] === 'Communication') ? 'selected' : ''; ?>>Communication</option>
                            <option value="Operations" <?php echo ($edit_skill && $edit_skill['category'] === 'Operations') ? 'selected' : ''; ?>>Operations</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn">
                    <?php echo $edit_skill ? 'Update Skill' : 'Add Skill'; ?>
                </button>
                
                <?php if ($edit_skill): ?>
                    <a href="skills.php" class="btn btn-secondary">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="content-section">
            <h2>All Skills</h2>
            
            <?php if (empty($skills)): ?>
                <p>No skills found. Add your first skill above!</p>
            <?php else: ?>
                <table class="skills-table">
                    <thead>
                        <tr>
                            <th>Skill Name</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($skills as $skill): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($skill['skill_name']); ?></td>
                                <td>
                                    <span class="category-badge">
                                        <?php echo htmlspecialchars($skill['category']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="skill-actions">
                                        <a href="skills.php?edit=<?php echo $skill['id']; ?>" class="btn">Edit</a>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this skill?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $skill['id']; ?>">
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