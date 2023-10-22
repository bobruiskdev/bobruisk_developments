<?php
session_start();

echo "User ID: " . $_SESSION['user_id'] . "<br>";
echo "User Role: " . $_SESSION['user_role'] . "<br>";

if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'author') {
    // Author is authenticated; you can include the Author's Dashboard HTML here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author's Dashboard</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Author's Dashboard</h1>
        <div class="row mt-4">
            <div class="col-md-6">
                <a href="new_article.html" class="btn btn-primary btn-block">Create New Article</a>
            </div>
            <div class="col-md-6">
                <a href="view_articles.php" class="btn btn-success btn-block">View My Articles</a>
            </div>
            <div id="articleTableContainer"></div>
        </div>
    </div>
    <!-- Add Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
} else {
    header("Location: login.php");
    exit();
}
?>
