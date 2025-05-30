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
                    $stmt = $pdo->prepare("INSERT INTO education (degree, institution, graduation_date, description) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['degree'],
                        $_POST['institution'],
                        $_POST['graduation_date'] ?: null,
                        $_POST['description']
                    ]);
                    $message = 'Education record added successfully!';
                } catch (PDOException $e) {
                    $error = 'Error adding education: ' . $e->getMessage();
                }
                break;
                
            case 'edit':
                try {
                    $stmt = $pdo->prepare("UPDATE education SET degree=?, institution=?, graduation_date=?, description=? WHERE id=?");
                    $stmt->execute([
                        $_POST['degree'],
                        $_POST['institution'],
                        $_POST['graduation_date'] ?: null,
                        $_POST['description'],
                        $_POST['id']
                    ]);
                    $message = 'Education record updated successfully!';
                } catch (PDOException $e) {
                    $error = 'Error updating education: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                try {
                    $stmt = $pdo->prepare("DELETE FROM education WHERE id=?");
                    $stmt->execute([$_POST['id']]);
                    $message = 'Education record deleted successfully!';
                } catch (PDOException $e) {
                    $error = 'Error deleting education: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all education records
$education_records = $pdo->query("SELECT * FROM education ORDER BY graduation_date DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get education for editing if ID is provided
$edit_education = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM education WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_education = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Education - Admin</title>
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
        textarea { height: 100px; resize: vertical; }
        .education-table { 
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .education-table th,
        .education-table td { 
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .education-table th { 
            background: #f8f9fa;
            font-weight: 600;
        }
        .education-table tr:hover { background: #f8f9fa; }
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
        .education-actions { display: flex; gap: 0.5rem; }
        .education-meta { font-size: 0.9rem; color: #666; }
        .degree-icon {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: #006b3c;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            margin-right: 10px;
            font-size: 14px;
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
        <h1>Manage Education</h1>
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
            <h2><?php echo $edit_education ? 'Edit Education' : 'Add New Education'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $edit_education ? 'edit' : 'add'; ?>">
                <?php if ($edit_education): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_education['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="degree">Degree/Qualification *</label>
                        <input type="text" id="degree" name="degree" required 
                               value="<?php echo $edit_education ? htmlspecialchars($edit_education['degree']) : ''; ?>"
                               placeholder="e.g., Master of Arts in Development Studies">
                    </div>
                    
                    <div class="form-group">
                        <label for="institution">Institution *</label>
                        <input type="text" id="institution" name="institution" required 
                               value="<?php echo $edit_education ? htmlspecialchars($edit_education['institution']) : ''; ?>"
                               placeholder="e.g., University of Ghana">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="graduation_date">Graduation Date</label>
                    <input type="date" id="graduation_date" name="graduation_date" 
                           value="<?php echo $edit_education ? $edit_education['graduation_date'] : ''; ?>">
                    <div class="help-text">Leave blank if still in progress or not applicable</div>
                </div>
                
                <div class="form-group full-width">
                    <label for="description">Additional Details</label>
                    <textarea id="description" name="description" 
                              placeholder="e.g., Specialization, honors, relevant coursework, thesis topic..."><?php echo $edit_education ? htmlspecialchars($edit_education['description']) : ''; ?></textarea>
                </div>
                
                <button type="submit" class="btn">
                    <?php echo $edit_education ? 'Update Education' : 'Add Education'; ?>
                </button>
                
                <?php if ($edit_education): ?>
                    <a href="education.php" class="btn btn-secondary">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="content-section">
            <h2>Education & Training</h2>
            
            <?php if (empty($education_records)): ?>
                <p>No education records found. Add your first qualification above!</p>
            <?php else: ?>
                <table class="education-table">
                    <thead>
                        <tr>
                            <th>Qualification</th>
                            <th>Institution</th>
                            <th>Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($education_records as $edu): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        <span class="degree-icon">🎓</span>
                                        <div>
                                            <strong><?php echo htmlspecialchars($edu['degree']); ?></strong>
                                            <?php if ($edu['description']): ?>
                                                <div class="education-meta">
                                                    <?php echo htmlspecialchars(substr($edu['description'], 0, 100)); ?>
                                                    <?php if (strlen($edu['description']) > 100) echo '...'; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($edu['institution']); ?></td>
                                <td>
                                    <?php if ($edu['graduation_date']): ?>
                                        <?php echo date('M Y', strtotime($edu['graduation_date'])); ?>
                                    <?php else: ?>
                                        <span style="color: #666;">In Progress</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="education-actions">
                                        <a href="education.php?edit=<?php echo $edu['id']; ?>" class="btn">Edit</a>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this education record?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $edu['id']; ?>">
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