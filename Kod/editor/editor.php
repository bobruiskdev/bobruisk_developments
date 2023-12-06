<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../database/db_config.php';

// Check if the user is logged in and is an editor
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "editor") {
    header("Location: login.php");
    exit();
}

// Fetch articles from the clanky table
$articles_stmt = $conn->prepare("SELECT id, name, review_mark, text, status FROM clanky");
$articles_stmt->execute();
$articles_result = $articles_stmt->get_result();
$articles = $articles_result->fetch_all(MYSQLI_ASSOC);
$articles_stmt->close();

// Initialize variables for selected article details
$selected_article_name = "";
$selected_article_text = "";

// Handle form submission for choosing an article
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["article_id"])) {
    $article_id = $_POST["article_id"];

    // Fetch the selected article details
    $selected_article_stmt = $conn->prepare("SELECT name, text FROM clanky WHERE id = ?");
    $selected_article_stmt->bind_param("i", $article_id);
    $selected_article_stmt->execute();
    $selected_article_result = $selected_article_stmt->get_result();
    $selected_article_row = $selected_article_result->fetch_assoc();

    // Check if the query was successful and the row exists
    if ($selected_article_row) {
        $selected_article_name = $selected_article_row["name"];
        $selected_article_text = $selected_article_row["text"];
    }

    $selected_article_stmt->close();
}

// Handle form submission for editing an article
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_article"])) {
    $article_id = $_POST["article_id"];
    $new_text = $_POST["new_text"];
    $new_review_mark = $_POST["new_review_mark"];
    $new_status = $_POST["new_status"];

    // Update the article information in the clanky table
    $update_stmt = $conn->prepare("UPDATE clanky SET text = ?, review_mark = ?, status = ? WHERE id = ?");
    $update_stmt->bind_param("sssi", $new_text, $new_review_mark, $new_status, $article_id);
    $update_stmt->execute();
    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Page</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>

        <h3>Choose an Article to Edit:</h3>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="article">Select Article:</label>
                <select class="form-control" id="article" name="article_id" required>
                    <?php foreach ($articles as $article): ?>
                        <option value="<?php echo $article["id"]; ?>"><?php echo $article["name"]; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Fetch Article Details</button>
        </form>

        <?php if (!empty($selected_article_name)): ?>
            <h3>Original Text of Article: <?php echo $selected_article_name; ?></h3>
            <div><?php echo nl2br($selected_article_text); ?></div>

            <h3>Edit Article: <?php echo $selected_article_name; ?></h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="article_id" value="<?php echo $_POST["article_id"]; ?>">
                <div class="form-group">
                    <label for="new_text">New Text:</label>
                    <textarea class="form-control" id="new_text" name="new_text" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label for="new_review_mark">New Review Mark:</label>
                    <input type="text" class="form-control" id="new_review_mark" name="new_review_mark" required>
                </div>
                <div class="form-group">
                    <label for="new_status">New Status:</label>
                    <select class="form-control" id="new_status" name="new_status" required>
                        <option value="pending">Pending</option>
                        <option value="edited">Edited</option>
                        <option value="published">Published</option>
                        <option value="declined">Declined</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success" name="edit_article">Edit Article</button>
            </form>
        <?php endif; ?>

        <a href="../authenticate/logout.php" class="btn btn-danger mt-3">Logout</a>
    </div>

    <!-- Add Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
