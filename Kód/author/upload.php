<?php


// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    $servername = "your_server_name";
    $username = "your_username";
    $password = "your_password";
    $database = "your_database_name";

    // Create a new database connection
    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the form data
    $articleName = $_POST['articleName'];
    $authorTeam = $_POST['authorTeam'];
    $topic = $_POST['topic'];
    $pdfFile = $_FILES['pdfFile']; // Assuming this is the file input field

    // Additional data to insert
    $author_id = $_SESSION['user_id']; // The logged-in user's ID

    // Insert the article into the database
    $sql = "INSERT INTO articles (author_id, genre, name, file_link, state, date_added, review_mark) VALUES (?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    
    // Determine the state and review_mark values here (as per your logic)
    $state = "Pending"; // Example state
    $review_mark = 0;  // Example review mark

    // Bind parameters and execute the query
    $stmt->bind_param("issssi", $author_id, $topic, $articleName, $pdfFile, $state, $review_mark);

    // Execute the query
    if ($stmt->execute()) {
        echo "Article submitted successfully.";
    } else {
        echo "Error submitting article.";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>
