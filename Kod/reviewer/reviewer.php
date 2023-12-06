<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../database/db_config.php';

// Check if the user is logged in and is a reviewer
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "reviewer") {
    header("Location: login.php");
    exit();
}

// Fetch edited articles from the clanky table with status 'edited'
$articles_stmt = $conn->prepare("SELECT id, text, review_mark FROM clanky WHERE status = 'edited'");
$articles_stmt->execute();
$articles_result = $articles_stmt->get_result();
$articles = $articles_result->fetch_all(MYSQLI_ASSOC);
$articles_stmt->close();

// Handle form submission for publishing or declining an article
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["article_id"]) && isset($_POST["action"])) {
        $article_id = $_POST["article_id"];
        $action = $_POST["action"];

        if ($action === "publish") {
            // Update the article status to 'published'
            $update_stmt = $conn->prepare("UPDATE clanky SET status = 'published' WHERE id = ?");
            $update_stmt->bind_param("i", $article_id);
            $update_stmt->execute();
            $update_stmt->close();
        } elseif ($action === "decline") {
            // Update the article status to 'declined'
            $update_stmt = $conn->prepare("UPDATE clanky SET status = 'declined' WHERE id = ?");
            $update_stmt->bind_param("i", $article_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewer Page</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>

        <h3>Review Edited Articles:</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Article ID</th>
                    <th>Text</th>
                    <th>Review Mark</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?php echo $article["id"]; ?></td>
                        <td><?php echo $article["text"]; ?></td>
                        <td><?php echo $article["review_mark"]; ?></td>
                        <td>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <input type="hidden" name="article_id" value="<?php echo $article["id"]; ?>">
                                <button type="submit" class="btn btn-success" name="action" value="publish">Publish</button>
                                <button type="submit" class="btn btn-danger" name="action" value="decline">Decline</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="../authenticate/logout.php" class="btn btn-primary mt-3">Logout</a>
    </div>

    <!-- Add Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
