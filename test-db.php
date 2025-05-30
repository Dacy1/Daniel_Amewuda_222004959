<?php
echo "Testing database connection...<br>";
require_once 'config.php';

try {
    $pdo = getConnection();
    echo "✅ Database connection successful!<br>";
    
    // Test if contact_messages table exists
    $result = $pdo->query("SHOW TABLES LIKE 'contact_messages'");
    if ($result->rowCount() > 0) {
        echo "✅ contact_messages table exists!<br>";
    } else {
        echo "❌ contact_messages table does NOT exist!<br>";
    }
    
    // Test inserting a test message
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Test User', 'test@example.com', 'Test Subject', 'Test message']);
    echo "✅ Test message inserted successfully!<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>