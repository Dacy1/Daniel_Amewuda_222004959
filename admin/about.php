<?php
require_once '../config.php';
requireLogin();

$pdo = getConnection();
$message = '';
$error = '';

// Handle form submission
if ($_POST) {
    try {
        // Check if about_info exists
        $existing = $pdo->query("SELECT COUNT(*) FROM about_info")->fetchColumn();
        
        if ($existing > 0) {
            // Update existing record
            $stmt = $pdo->prepare("UPDATE about_info SET name=?, title=?, description=?, email=?, phone=?, address=?, linkedin=?, twitter=?, github=? WHERE id=1");
        } else {
            // Insert new record
            $stmt = $pdo->prepare("INSERT INTO about_info (name, title, description, email, phone, address, linkedin, twitter, github) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        }
        
        $stmt->execute([
            $_POST['name'],
            $_POST['title'],
            $_POST['description'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['linkedin'],
            $_POST['twitter'],
            $_POST['github']
        ]);
        
        $message = 'About information updated successfully!';
    } catch (PDOException $e) {
        $error = 'Error updating about information: ' . $e->getMessage();
    }
}

// Get current about info
$about = $pdo->query("SELECT * FROM about_info LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$about) {
    // Initialize with default values if no record exists
    $about = [
        'name' => '',
        'title' => '',
        'description' => '', 
        'email' => '',
        'phone' => '',
        'address' => '',
        'linkedin' => '',
        'twitter' => '',
        'github' => ''
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About Section - Admin</title>
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
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover { background: #005530; }
        .btn-secondary { 
            background: #6c757d;
            margin-left: 1rem;
        }
        .btn-secondary:hover { background: #5a6268; }
        .container { max-width: 1000px; margin: 2rem auto; padding: 0 2rem; }
        .content-section { 
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-grid { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .form-group { margin-bottom: 1.5rem; }
        .form-group.full-width { grid-column: 1 / -1; }
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
            font-family: inherit;
        }
        input:focus, textarea:focus { 
            outline: none;
            border-color: #006b3c;
        }
        textarea { 
            height: 120px;
            resize: vertical;
        }
        .description-textarea { height: 200px; }
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
        .section-title { 
            color: #006b3c;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #fcd116;
        }
        .help-text { 
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.25rem;
        }
        .social-links-section { 
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1.5rem;
        }
        .social-links-section h3 { 
            color: #006b3c;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage About Section</h1>
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
            <h2 class="section-title">Personal Information</h2>
            
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo htmlspecialchars($about['name']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="title">Professional Title *</label>
                        <input type="text" id="title" name="title" required 
                               value="<?php echo htmlspecialchars($about['title']); ?>"
                               placeholder="e.g., Development Specialist | Researcher">
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="description">About Description *</label>
                    <textarea id="description" name="description" class="description-textarea" required><?php echo htmlspecialchars($about['description']); ?></textarea>
                    <div class="help-text">Write a compelling description about yourself, your expertise, and your passion.</div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($about['email']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($about['phone']); ?>"
                               placeholder="+233 240858453">
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($about['address']); ?></textarea>
                    <div class="help-text">Your location or business address</div>
                </div>
                
                <div class="social-links-section">
                    <h3>Social Media Links</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="linkedin">LinkedIn Profile</label>
                            <input type="url" id="linkedin" name="linkedin" 
                                   value="<?php echo htmlspecialchars($about['linkedin']); ?>"
                                   placeholder="https://linkedin.com/in/yourprofile">
                        </div>
                        
                        <div class="form-group">
                            <label for="twitter">Twitter Profile</label>
                            <input type="url" id="twitter" name="twitter" 
                                   value="<?php echo htmlspecialchars($about['twitter']); ?>"
                                   placeholder="https://twitter.com/yourusername">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="github">GitHub Profile</label>
                        <input type="url" id="github" name="github" 
                               value="<?php echo htmlspecialchars($about['github']); ?>"
                               placeholder="https://github.com/yourusername">
                    </div>
                </div>
                
                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn">Update About Information</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>