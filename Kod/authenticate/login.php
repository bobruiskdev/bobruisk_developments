<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Perform database lookup (use prepared statements for security)

    // Sample connection code
    $servername = "localhost";
    $username_db = "pryhazha";
    $password_db = "Tis*7291911";
    $dbname = "pryhazha";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, username, password, role FROM uzivatele WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];

        switch ($user["role"]) {
            case "author":
                header("Location: ../author/author.php");
                break;
            case "editor":
                header("Location: ../editor/editor.php");
                break;
            case "reader":
                header("Location: ../reader/reader.php");
                break;
            case "reviewer":
                header("Location: ../reviewer/reviewer.php");
                break;
            default:
                // Handle other roles or errors
                break;
        }
        exit();
    } else {
        $error_message = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        p {
            color: red;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h2>Přihlášení</h2>
        <?php if (isset($error_message)) { echo "<p>$error_message</p>"; } ?>
        <input type="text" name="username" placeholder="Uživatelské jméno" required>
        <input type="password" name="password" placeholder="Heslo" required>
        <input type="submit" value="Přihlásit se">
        <p style="text-align: center;  margin-right:10px">
        <a href="../index.html">Zpět na hlavní stránku</a>
    </p>
    </form>
    
</body>
</html>
