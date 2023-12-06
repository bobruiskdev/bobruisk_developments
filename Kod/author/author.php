<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../database/db_config.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "author") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create_article"])) {
        $name = $_POST["name"];
        $genre = $_POST["genre"];
        $review_mark = $_POST["review_mark"];
        $text = $_POST["text"];

        $stmt = $conn->prepare("INSERT INTO clanky (name, genre, review_mark, id_author, text) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $name, $genre, $review_mark, $user_id, $text);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST["delete_article"])) {
        $article_id = $_POST["delete_article"];

        $stmt = $conn->prepare("DELETE FROM clanky WHERE id = ? AND id_author = ?");
        $stmt->bind_param("ii", $article_id, $user_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST["archive_article"])) {
        $article_id = $_POST["archive_article"];

        // Fetch the article details
        $stmtArticle = $conn->prepare("SELECT name, genre, review_mark, text FROM clanky WHERE id = ? AND id_author = ?");
        $stmtArticle->bind_param("ii", $article_id, $user_id);
        $stmtArticle->execute();
        $resultArticle = $stmtArticle->get_result();
        $articleDetails = $resultArticle->fetch_assoc();
        $stmtArticle->close();

        if ($articleDetails) {
            // Insert into archive
            $stmtArchive = $conn->prepare("INSERT INTO archive (name, genre, review_mark, id_author, text) VALUES (?, ?, ?, ?, ?)");
            $stmtArchive->bind_param("ssiss", $articleDetails["name"], $articleDetails["genre"], $articleDetails["review_mark"], $user_id, $articleDetails["text"]);
            $stmtArchive->execute();
            $stmtArchive->close();

            // Delete from clanky
            $stmtDelete = $conn->prepare("DELETE FROM clanky WHERE id = ? AND id_author = ?");
            $stmtDelete->bind_param("ii", $article_id, $user_id);
            $stmtDelete->execute();
            $stmtDelete->close();
        }
    }
}

// Fetch articles created by the author
$stmt = $conn->prepare("SELECT id, name, genre, review_mark, id_author, text FROM clanky WHERE id_author = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$articles = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Page</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            flex: 1 1 auto; /* Adjust the flex property */
            overflow-y: auto;
        }


        h2, h3 {
            color: #333;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 300px;
            text-align: center;
        }

        input, textarea {
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

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        a {
            color: #4caf50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Vítejte, <?php echo $_SESSION["username"]; ?>!</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h3>Vytvořit článek </h3>
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <label for="genre">Genre:</label>
        <input type="text" name="genre" required>
        <label for="review_mark">Review Mark:</label>
        <input type="number" name="review_mark" required>
        <label for="text">Text:</label>
        <textarea name="text" rows="4" required></textarea>
        <input type="submit" name="create_article" value="Create Article">
    </form>

    <h3>Your Articles</h3>
    <ul>
        <?php foreach ($articles as $article): ?>
            <li>
                <strong><?php echo $article["name"]; ?></strong> - <?php echo $article["genre"]; ?> -
                Review Mark: <?php echo $article["review_mark"]; ?><br>
                <div><?php echo nl2br($article["text"]); ?></div>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="delete_article" value="<?php echo $article["id"]; ?>">
                    <input type="submit" value="Delete Article">
                </form>
                <!-- Add an "Archive" button for each article -->
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="archive_article" value="<?php echo $article["id"]; ?>">
                    <input type="submit" value="Archive Article">
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="../authenticate/logout.php">Logout</a>
</body>
</html>
