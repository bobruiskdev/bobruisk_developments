<?php
session_start();

// Database connection settings
$db_host = "localhost";
$db_user = "pryhazha";
$db_pass = "your_db_password";
$db_name = "pryhazha";

// Create a database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    $sql = "SELECT id, username, password, user_role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $db_username, $db_password, $user_role);

    if ($stmt->num_rows == 1 && $stmt->fetch() && password_verify($input_password, $db_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $db_username;
        $_SESSION['user_role'] = $user_role;

        if ($user_role == 'author') {
            header("location: author.php");
        } elseif ($user_role == 'editor') {
            header("location: editor.html");
        } elseif ($user_role == 'reviewer') {
            header("location: reviewer.html");
        } else {
            echo "Unknown user role. Please contact the administrator.";
        }
    } else {
        echo "Login failed. Please check your username and password.";
    }

    $stmt->close();
}
?>
