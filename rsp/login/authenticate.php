<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Securely connect to your database
   try { $db = new PDO("mysql:host=localhost;dbname=pryhazha", "pryhazha", "Tis*7291911");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit();
}

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $db->prepare("SELECT id, username, user_role, password FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['password'])) {
        echo "Authentication successful!";

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_role'] = $row['user_role'];

        if ($row['user_role'] === 'author') {
            header("Location: author.php");
        } elseif ($row['user_role'] === 'Admin') {
            header("Location: admin_dashboard.php");
        } else {
            // Handle other user roles or redirect to a default page
        }
    } else {
         echo "Authentication failed!";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
