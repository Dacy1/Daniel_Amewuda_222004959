<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Contact handler started...<br>";

require_once 'config.php';
echo "Config loaded...<br>";

// Set JSON header
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

echo "POST request received...<br>";

// Get POST data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

echo "Form data received: Name=$name, Email=$email<br>";

// Validate input
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit();
}

echo "Validation passed...<br>";

try {
    $pdo = getConnection();
    echo "Database connected...<br>";
    
    // Insert message into database
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$name, $email, $subject, $message]);
    
    echo "Query executed...<br>";
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Thank you for your message! I will get back to you soon.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save message']);
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo "General error: " . $e->getMessage() . "<br>";
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>