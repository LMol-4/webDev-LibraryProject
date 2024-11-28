<?php
// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$dbname = "librarydb";

// start the session
session_start();

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("You must be logged in to view your reservations.");
}

// Fetch all categories
$categories = [];
$sql = "SELECT categoryid, categorydescription FROM category";
if ($result = $conn->query($sql)) {
    // Loop through categories and store them in an array
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
} else {
    $categories = []; // No categories available
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books - Luke's Library</title>
    <link rel="stylesheet" href="../css/navbar+footer.css">
    <link rel="stylesheet" href="../css/general.css">
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="brand">Luke's Library</div>
        <div class="nav-links">
            <a href="reserved_list.php">Reserved</a>
            <a href="#">Search</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="center" style="min-height: 90vh; padding: 20px;">
        <h1 style="transform: scale(1.4);">Search for a Book</h1>
        <form style="transform: scale(1.5);" method="GET" action="search_function.php">
            <label for="title">Book Title:</label><br>
            <input type="text" name="title" id="title" placeholder="Enter title" /><br><br>

            <label for="author">Author:</label><br>
            <input type="text" name="author" id="author" placeholder="Enter author" /><br><br>

            <label for="category">Category:</label><br>
            <select name="category" id="category">
                <option value="">-- Select Category --</option>
                <?php
                // Check if there are categories and display them
                if (empty($categories)) {
                    echo "<option value=''>No categories available</option>";
                } else {
                    foreach ($categories as $category) {
                        echo "<option value='{$category['categoryid']}'>{$category['categorydescription']}</option>";
                    }
                }
                ?>
            </select><br><br>
            <div class="container">
            <button class="button-style" type="submit">Search</button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2024 Luke's Library. For WebDev2.
    </div>
</body>
</html>
