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
                    $image_path = '';
                    
                    // Handle image upload
                    if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] == 0) {
                        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                        $filename = $_FILES['project_image']['name'];
                        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
                        
                        if (in_array(strtolower($filetype), $allowed)) {
                            $new_filename = uniqid() . '.' . $filetype;
                            $upload_path = '../uploads/projects/' . $new_filename;
                            
                            if (move_uploaded_file($_FILES['project_image']['tmp_name'], $upload_path)) {
                                $image_path = 'uploads/projects/' . $new_filename;
                            } else {
                                $error = 'Failed to upload image';
                            }
                        } else {
                            $error = 'Only JPG, JPEG, PNG & GIF files are allowed';
                        }
                    }
                    
                    if (!$error) {
                        $stmt = $pdo->prepare("INSERT INTO projects (title, description, technologies, project_url, github_url, date_completed, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $_POST['title'],
                            $_POST['description'],
                            $_POST['technologies'],
                            $_POST['project_url'],
                            $_POST['github_url'],
                            $_POST['date_completed'],
                            $image_path
                        ]);
                        $message = 'Project added successfully!';
                    }
                } catch (PDOException $e) {
                    $error = 'Error adding project: ' . $e->getMessage();
                }
                break;
                
            case 'edit':
                try {
                    $image_path = $_POST['existing_image']; // Keep existing image by default
                    
                    // Handle new image upload
                    if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] == 0) {
                        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                        $filename = $_FILES['project_image']['name'];
                        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
                        
                        if (in_array(strtolower($filetype), $allowed)) {
                            $new_filename = uniqid() . '.' . $filetype;
                            $upload_path = '../uploads/projects/' . $new_filename;
                            
                            if (move_uploaded_file($_FILES['project_image']['tmp_name'], $upload_path)) {
                                // Delete old image if it exists
                                if ($image_path && file_exists('../' . $image_path)) {
                                    unlink('../' . $image_path);
                                }
                                $image_path = 'uploads/projects/' . $new_filename;
                            } else {
                                $error = 'Failed to upload new image';
                            }
                        } else {
                            $error = 'Only JPG, JPEG, PNG & GIF files are allowed';
                        }
                    }
                    
                    if (!$error) {
                        $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, technologies=?, project_url=?, github_url=?, date_completed=?, image=? WHERE id=?");
                        $stmt->execute([
                            $_POST['title'],
                            $_POST['description'],
                            $_POST['technologies'],
                            $_POST['project_url'],
                            $_POST['github_url'],
                            $_POST['date_completed'],
                            $image_path,
                            $_POST['id']
                        ]);
                        $message = 'Project updated successfully!';
                    }
                } catch (PDOException $e) {
                    $error = 'Error updating project: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                try {
                    // Get project info to delete image
                    $stmt = $pdo->prepare("SELECT image FROM projects WHERE id=?");
                    $stmt->execute([$_POST['id']]);
                    $project = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Delete project from database
                    $stmt = $pdo->prepare("DELETE FROM projects WHERE id=?");
                    $stmt->execute([$_POST['id']]);
                    
                    // Delete image file if it exists
                    if ($project['image'] && file_exists('../' . $project['image'])) {
                        unlink('../' . $project['image']);
                    }
                    
                    $message = 'Project deleted successfully!';
                } catch (PDOException $e) {
                    $error = 'Error deleting project: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all projects
$projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get project for editing if ID is provided
$edit_project = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_project = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - Admin</title>
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
        input, textarea { 
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        textarea { height: 120px; resize: vertical; }
        .projects-table { 
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .projects-table th,
        .projects-table td { 
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .projects-table th { 
            background: #f8f9fa;
            font-weight: 600;
        }
        .projects-table tr:hover { background: #f8f9fa; }
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
        .project-actions { display: flex; gap: 0.5rem; }
        .project-meta { font-size: 0.9rem; color: #666; }
        .project-image { 
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .current-image { 
            max-width: 200px;
            border-radius: 5px;
            margin-top: 0.5rem;
        }
        .image-upload-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
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
        <h1>Manage Projects</h1>
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
            <h2><?php echo $edit_project ? 'Edit Project' : 'Add New Project'; ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $edit_project ? 'edit' : 'add'; ?>">
                <?php if ($edit_project): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_project['id']; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo $edit_project['image']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="title">Project Title *</label>
                        <input type="text" id="title" name="title" required 
                               value="<?php echo $edit_project ? htmlspecialchars($edit_project['title']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="date_completed">Date Completed</label>
                        <input type="date" id="date_completed" name="date_completed" 
                               value="<?php echo $edit_project ? $edit_project['date_completed'] : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?php echo $edit_project ? htmlspecialchars($edit_project['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="technologies">Technologies Used (comma separated)</label>
                        <input type="text" id="technologies" name="technologies" 
                               value="<?php echo $edit_project ? htmlspecialchars($edit_project['technologies']) : ''; ?>"
                               placeholder="PHP, JavaScript, MySQL">
                    </div>
                    
                    <div class="form-group">
                        <label for="project_url">Project URL</label>
                        <input type="url" id="project_url" name="project_url" 
                               value="<?php echo $edit_project ? htmlspecialchars($edit_project['project_url']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="github_url">GitHub URL</label>
                    <input type="url" id="github_url" name="github_url" 
                           value="<?php echo $edit_project ? htmlspecialchars($edit_project['github_url']) : ''; ?>">
                </div>
                
                <div class="image-upload-section">
                    <h3>Project Image</h3>
                    <?php if ($edit_project && $edit_project['image']): ?>
                        <p>Current Image:</p>
                        <img src="../<?php echo htmlspecialchars($edit_project['image']); ?>" alt="Current project image" class="current-image">
                        <br><br>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="project_image"><?php echo $edit_project ? 'Upload New Image (optional)' : 'Upload Project Image'; ?></label>
                        <input type="file" id="project_image" name="project_image" accept="image/*">
                        <div class="help-text">Allowed formats: JPG, JPEG, PNG, GIF. Max file size: 5MB</div>
                    </div>
                </div>
                
                <button type="submit" class="btn">
                    <?php echo $edit_project ? 'Update Project' : 'Add Project'; ?>
                </button>
                
                <?php if ($edit_project): ?>
                    <a href="projects.php" class="btn btn-secondary">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="content-section">
            <h2>Existing Projects</h2>
            
            <?php if (empty($projects)): ?>
                <p>No projects found. Add your first project above!</p>
            <?php else: ?>
                <table class="projects-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Technologies</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td>
                                    <?php if ($project['image']): ?>
                                        <img src="../<?php echo htmlspecialchars($project['image']); ?>" alt="Project image" class="project-image">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #999;">No Image</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                                    <div class="project-meta">
                                        <?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?>
                                        <?php if (strlen($project['description']) > 100) echo '...'; ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($project['technologies']); ?></td>
                                <td><?php echo $project['date_completed'] ? date('M Y', strtotime($project['date_completed'])) : 'N/A'; ?></td>
                                <td>
                                    <div class="project-actions">
                                        <a href="projects.php?edit=<?php echo $project['id']; ?>" class="btn">Edit</a>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this project?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
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