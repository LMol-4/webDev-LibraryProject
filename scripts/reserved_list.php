<?php
// Start session
session_start();

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$dbname = "librarydb";

// Database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['username'])) { // session variable used for grabbing reserved books with your username in the table.
    die("You must be logged in to view your reservations.");
}

// Get the current username from the session
$username = $_SESSION['username']; // session variable used for grabbing reserved books with your username in the table.

// SQL query to fetch reserved books for the current user
$sql = "SELECT b.isbn, b.booktitle, b.author, b.edition, b.year, b.reserved, c.categorydescription 
        FROM books b
        JOIN reservations r ON b.isbn = r.isbn
        LEFT JOIN category c ON b.category = c.categoryid
        WHERE r.username = ?";

// Prepare and execute the statement
$stmt = $conn->prepare($sql); // prepare connection - this way used to stop sql injection
$stmt->bind_param("s", $username); // bind string "s" username to the ? in the above query.
$stmt->execute(); // execute query
$result = $stmt->get_result();

// HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserved Books - Luke's Library</title>
    <link rel="stylesheet" href="../css/navbar+footer.css">
    <link rel="stylesheet" href="../css/general.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="brand">Luke's Library</div>
        <div class="nav-links">
            <a href="#">Reserved</a>
            <a href="search_page.php">Search</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="center", style="min-height: 90vh; padding: 20px;">
        <h1 style="transform: scale(1.4);">Your Reserved Books</h1>
        
        <?php
        // Check if the user has any reserved books
        if ($result->num_rows > 0) { // if rows come back from above query
            echo "<table class=\"styled-table\">
                    <tr>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Edition</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>";

            // Display reserved books
            while ($row = $result->fetch_assoc()) {
                $status = $row['reserved'] === 'Y' ? "Reserved" : "Available"; // Check if reserved of the current row is Y, if so set $status Reserved, otherwise Available
                echo "<tr>
                        <td>{$row['isbn']}</td>
                        <td>{$row['booktitle']}</td>
                        <td>{$row['author']}</td>
                        <td>{$row['categorydescription']}</td>
                        <td>{$row['edition']}</td>
                        <td>{$row['year']}</td>
                        <td>$status</td>
                        <td><a href='unreserve.php?isbn={$row['isbn']}'>Unreserve</a></td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p style=\"transform: scale(1.4);\">You have no reserved books.</p>";
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
        ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2024 Luke's Library. For WebDev2.
    </div>

</body>
</html>
