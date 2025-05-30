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
                    $stmt = $pdo->prepare("INSERT INTO experience (position, company, location, start_date, end_date, is_current, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['position'],
                        $_POST['company'],
                        $_POST['location'],
                        $_POST['start_date'],
                        $_POST['end_date'] ?: null,
                        isset($_POST['is_current']) ? 1 : 0,
                        $_POST['description']
                    ]);
                    $message = 'Experience added successfully!';
                } catch (PDOException $e) {
                    $error = 'Error adding experience: ' . $e->getMessage();
                }
                break;
                
            case 'edit':
                try {
                    $stmt = $pdo->prepare("UPDATE experience SET position=?, company=?, location=?, start_date=?, end_date=?, is_current=?, description=? WHERE id=?");
                    $stmt->execute([
                        $_POST['position'],
                        $_POST['company'],
                        $_POST['location'],
                        $_POST['start_date'],
                        $_POST['end_date'] ?: null,
                        isset($_POST['is_current']) ? 1 : 0,
                        $_POST['description'],
                        $_POST['id']
                    ]);
                    $message = 'Experience updated successfully!';
                } catch (PDOException $e) {
                    $error = 'Error updating experience: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                try {
                    $stmt = $pdo->prepare("DELETE FROM experience WHERE id=?");
                    $stmt->execute([$_POST['id']]);
                    $message = 'Experience deleted successfully!';
                } catch (PDOException $e) {
                    $error = 'Error deleting experience: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all experience
$experiences = $pdo->query("SELECT * FROM experience ORDER BY start_date DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get experience for editing if ID is provided
$edit_experience = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM experience WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_experience = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Experience - Admin</title>
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
        textarea { height: 120px; resize: vertical; }
        .experience-table { 
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .experience-table th,
        .experience-table td { 
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .experience-table th { 
            background: #f8f9fa;
            font-weight: 600;
        }
        .experience-table tr:hover { background: #f8f9fa; }
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
        .experience-actions { display: flex; gap: 0.5rem; }
        .experience-meta { font-size: 0.9rem; color: #666; }
        .current-badge {
            background: #28a745;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage Experience</h1>
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
            <h2><?php echo $edit_experience ? 'Edit Experience' : 'Add New Experience'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $edit_experience ? 'edit' : 'add'; ?>">
                <?php if ($edit_experience): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_experience['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="position">Position/Job Title *</label>
                        <input type="text" id="position" name="position" required 
                               value="<?php echo $edit_experience ? htmlspecialchars($edit_experience['position']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="company">Company/Organization *</label>
                        <input type="text" id="company" name="company" required 
                               value="<?php echo $edit_experience ? htmlspecialchars($edit_experience['company']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" 
                           value="<?php echo $edit_experience ? htmlspecialchars($edit_experience['location']) : ''; ?>"
                           placeholder="e.g., Accra, Ghana">
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="start_date">Start Date *</label>
                        <input type="date" id="start_date" name="start_date" required 
                               value="<?php echo $edit_experience ? $edit_experience['start_date'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" 
                               value="<?php echo $edit_experience ? $edit_experience['end_date'] : ''; ?>"
                               <?php echo ($edit_experience && $edit_experience['is_current']) ? 'disabled' : ''; ?>>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_current" name="is_current" 
                               <?php echo ($edit_experience && $edit_experience['is_current']) ? 'checked' : ''; ?>
                               onchange="toggleEndDate(this)">
                        <label for="is_current">This is my current position</label>
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="description">Job Description</label>
                    <textarea id="description" name="description" 
                              placeholder="Describe your key responsibilities, achievements, and notable projects..."><?php echo $edit_experience ? htmlspecialchars($edit_experience['description']) : ''; ?></textarea>
                </div>
                
                <button type="submit" class="btn">
                    <?php echo $edit_experience ? 'Update Experience' : 'Add Experience'; ?>
                </button>
                
                <?php if ($edit_experience): ?>
                    <a href="experience.php" class="btn btn-secondary">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="content-section">
            <h2>Work Experience</h2>
            
            <?php if (empty($experiences)): ?>
                <p>No work experience found. Add your first experience above!</p>
            <?php else: ?>
                <table class="experience-table">
                    <thead>
                        <tr>
                            <th>Position & Company</th>
                            <th>Duration</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($experiences as $exp): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($exp['position']); ?></strong>
                                    <div class="experience-meta">
                                        <?php echo htmlspecialchars($exp['company']); ?>
                                        <?php if ($exp['is_current']): ?>
                                            <span class="current-badge">Current</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($exp['description']): ?>
                                        <div class="experience-meta">
                                            <?php echo htmlspecialchars(substr($exp['description'], 0, 100)); ?>
                                            <?php if (strlen($exp['description']) > 100) echo '...'; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo date('M Y', strtotime($exp['start_date'])); ?> - 
                                    <?php echo $exp['is_current'] ? 'Present' : date('M Y', strtotime($exp['end_date'])); ?>
                                </td>
                                <td><?php echo htmlspecialchars($exp['location']); ?></td>
                                <td>
                                    <div class="experience-actions">
                                        <a href="experience.php?edit=<?php echo $exp['id']; ?>" class="btn">Edit</a>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this experience?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $exp['id']; ?>">
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

    <script>
        function toggleEndDate(checkbox) {
            const endDateInput = document.getElementById('end_date');
            if (checkbox.checked) {
                endDateInput.disabled = true;
                endDateInput.value = '';
            } else {
                endDateInput.disabled = false;
            }
        }
    </script>
</body>
</html>