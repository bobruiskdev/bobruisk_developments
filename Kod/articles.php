<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'database/db_config.php';

// Fetch published articles from the clanky table
$articles_stmt = $conn->prepare("SELECT id, text, review_mark FROM clanky WHERE status = 'published'");
$articles_stmt->execute();
$articles_result = $articles_stmt->get_result();
$articles = $articles_result->fetch_all(MYSQLI_ASSOC);
$articles_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clanky</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Publikované články</h2>

        <table class="table">
            <thead>
                <tr>
                <th>ID článku</th>
                <th>Text</th>
                <th>Hodnocení recenze</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?php echo $article["id"]; ?></td>
                        <td><?php echo $article["text"]; ?></td>
                        <td><?php echo $article["review_mark"]; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.html">Zpět na hlavní stránku</a>
    </div>

    <!-- Add Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
