<!--
// Ensure the user is logged in with a valid session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Define the author's team (you can retrieve this from the logged-in user's session)
    $authorTeam = $_SESSION['authorTeam']; // Change 'authorTeam' to your actual session variable name

    $servername = "localhost"; // Your database server name
    $username = "pryhazha"; // Your database username
    $password = "Tis*7291911"; // Your database password
    $database = "pryhazha"; // Your database name

    // Create a connection to the database
    $conn = new mysqli($servername, $username, $password, $database);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute a SQL query to retrieve articles by the author's team
    $sql = "SELECT id, genre, name, file_link, state, date_added, review_mark FROM your_table_name WHERE authorTeam = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $authorTeam); // Assuming authorTeam is a string
    $stmt->execute();

    $result = $stmt->get_result();

    $articles = array();
    while ($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }

    // Return the articles as JSON
    echo json_encode($articles);

    // Close the database connection
    $conn->close();
} else {
    // If the user is not logged in, return an empty array or an error message
    echo json_encode([]);
}

?>
-->