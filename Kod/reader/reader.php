<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../database/db_config.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "reader") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch articles from the archive
$archive_stmt = $conn->prepare("SELECT id, name, genre, review_mark, text FROM archive");
$archive_stmt->execute();
$archive_result = $archive_stmt->get_result();
$archive_articles = $archive_result->fetch_all(MYSQLI_ASSOC);
$archive_stmt->close();

// Fetch favorite articles for the reader
$favorite_stmt = $conn->prepare("SELECT archive.id, name, genre, review_mark, text FROM favorites JOIN archive ON favorites.article_id = archive.id WHERE user_id = ?");
$favorite_stmt->bind_param("i", $user_id);
$favorite_stmt->execute();
$favorite_result = $favorite_stmt->get_result();
$favorite_articles = $favorite_result->fetch_all(MYSQLI_ASSOC);
$favorite_stmt->close();

// Handle adding/removing articles from favorites
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_to_favorites"])) {
        $add_to_favorites_id = $_POST["add_to_favorites"];

        // Check if the article is not already in favorites
        $check_favorite_stmt = $conn->prepare("SELECT * FROM favorites WHERE user_id = ? AND article_id = ?");
        $check_favorite_stmt->bind_param("ii", $user_id, $add_to_favorites_id);
        $check_favorite_stmt->execute();
        $check_favorite_result = $check_favorite_stmt->get_result();

        if ($check_favorite_result->num_rows == 0) {
            // Article is not in favorites, add it
            $add_to_favorites_stmt = $conn->prepare("INSERT INTO favorites (user_id, article_id) VALUES (?, ?)");
            $add_to_favorites_stmt->bind_param("ii", $user_id, $add_to_favorites_id);
            $add_to_favorites_stmt->execute();
            $add_to_favorites_stmt->close();
        }

        $check_favorite_stmt->close();
    } elseif (isset($_POST["remove_from_favorites"])) {
        $remove_from_favorites_id = $_POST["remove_from_favorites"];

        $remove_from_favorites_stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND article_id = ?");
        $remove_from_favorites_stmt->bind_param("ii", $user_id, $remove_from_favorites_id);
        $remove_from_favorites_stmt->execute();
        $remove_from_favorites_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reader Page</title>
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
        }

        h2, h3 {
            color: #333;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 20px;
        }

        form {
            display: inline;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
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
    <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>

    <!-- Display Archive Articles -->
    <h3>Archive Articles</h3>
    <ul id="archive-articles">
        <?php foreach ($archive_articles as $article): ?>
            <li>
                <strong><?php echo $article["name"]; ?></strong> - <?php echo $article["genre"]; ?> -
                Review Mark: <?php echo $article["review_mark"]; ?><br>
                <?php echo $article["text"]; ?><br>
                <form class="add-to-favorites-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="add_to_favorites" value="<?php echo $article["id"]; ?>">
                    <input type="submit" value="Add to Favorites">
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Display Favorite Articles -->
    <h3>Your Favorite Articles</h3>
    <ul id="favorite-articles">
        <?php foreach ($favorite_articles as $article): ?>
            <li>
                <strong><?php echo $article["name"]; ?></strong> - <?php echo $article["genre"]; ?> -
                Review Mark: <?php echo $article["review_mark"]; ?><br>
                <?php echo $article["text"]; ?><br>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="remove_from_favorites" value="<?php echo $article["id"]; ?>">
                    <input type="submit" value="Remove from Favorites">
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="../authenticate/logout.php">Logout</a>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
          $(document).ready(function () {
        // Add to Favorites asynchronously
        $(".add-to-favorites-form").submit(function (event) {
            event.preventDefault();
            var form = $(this);

            $.ajax({
                type: form.attr("method"),
                url: form.attr("action"),
                data: form.serialize(),
                success: function () {
                    // Redirect to a new page or refresh the content using another AJAX request
                    location.href = 'reader.php';
                }
            });
        });
    });
    </script>
</body>
</html>