<?php
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
// Get search parameters
$title = isset($_GET['title']) ? $_GET['title'] : ''; // get title - default to empty string
$author = isset($_GET['author']) ? $_GET['author'] : ''; // get author - ^
$category = isset($_GET['category']) ? intval($_GET['category']) : null; // get category - default to null

// Pagination setup
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Retrieve 'page' parameter and convert it to an integer, default to page 1.
$limit = 5; // Maximum number of results to display per page.
$offset = ($page - 1) * $limit; // Calculate the starting point (offset) for SQL results based on the current page.

// Build SQL query
$sql = "SELECT books.isbn, books.booktitle, books.author, books.edition, books.year, books.reserved, category.categorydescription 
        FROM books
        LEFT JOIN category ON books.category = category.categoryid
        WHERE 1=1"; // this line is used for the dynamic AND conditions bellow.

// Variables to store query parameters and their types
$params = []; // Array to hold parameter valus
$param_types = ""; // specify i or s

// Add conditions dynamically
if (!empty($title)) { // if title not empty
    $sql .= " AND books.booktitle LIKE ?";
    $params[] = "%$title%"; // Add the 'title' value with wildcard for SQL LIKE.
    $param_types .= "s"; // append 's' (string) to the parameter type string.
}
if (!empty($author)) { // if author not empty
    $sql .= " AND books.author LIKE ?";
    $params[] = "%$author%"; // add the 'author' value with wilcard for SQL LIKE.
    $param_types .= "s"; 
}
if (!empty($category)) { // If category not empty
    $sql .= " AND books.category = ?"; // No wildcard needed as category exact
    $params[] = $category; // Add the category value.
    $param_types .= "i"; // Append 'i' (integer) to the parameter type string.
}

// Append pagination
$sql .= " LIMIT ? OFFSET ?"; // Append the LIMIT and OFFSET clauses for pagination.
$params[] = $limit; // Add the limit value.
$params[] = $offset; // Add the offset value.
$param_types .= "ii"; // Append 'ii' for limit and offset

// Prepare and execute the statement
$stmt = $conn->prepare($sql); // prepare the statement from constructed sql string
$stmt->bind_param($param_types, ...$params); // bind paramater types (i or s list), and dynamic list of paramaters needed.
$stmt->execute();
$result = $stmt->get_result();

// HTML
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luke's Library</title>
    <link rel="stylesheet" href="../css/navbar+footer.css">
    <link rel="stylesheet" href="../css/general.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="brand">Luke's Library</div>
        <div class="nav-links">
            <a href="reserved_list.php">Reserved</a>
            <a href="search_page.php">Search</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Main Content Placeholder -->
    <div class="center" style="min-height: 90vh; padding: 20px;">
        <h1 style="transform: scale(1.4);">Search Results</h1>

        <?php
        // Display search results
        if ($result->num_rows > 0) {
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

            while ($row = $result->fetch_assoc()) {
                $status = $row['reserved'] === 'Y' ? "Reserved" : "Available";
                $action = $row['reserved'] === 'N' ? "<a href='reserve_book.php?isbn={$row['isbn']}'>Reserve</a>" : "Unavailable"; // reserve function

                echo "<tr>
                        <td>{$row['isbn']}</td>
                        <td>{$row['booktitle']}</td>
                        <td>{$row['author']}</td>
                        <td>{$row['categorydescription']}</td>
                        <td>{$row['edition']}</td>
                        <td>{$row['year']}</td>
                        <td>$status</td>
                        <td>$action</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p style=\"transform: scale(1.4);\">No books found matching your search criteria.</p>";
        }

        // Pagination controls - section used to count how many books meet the criteria. same process as above for query.
        $sql_count = "SELECT COUNT(*) AS total FROM books 
                      LEFT JOIN category ON books.category = category.categoryid
                      WHERE 1=1";
        if (!empty($title)) {
            $sql_count .= " AND books.booktitle LIKE '%$title%'";
        }
        if (!empty($author)) {
            $sql_count .= " AND books.author LIKE '%$author%'";
        }
        if (!empty($category)) {
            $sql_count .= " AND books.category = $category";
        }
        $total_result = $conn->query($sql_count);
        $total_rows = $total_result->fetch_assoc()['total'];

        $total_pages = ceil($total_rows / $limit); // ceil used to round up
        echo "<div>";
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='search_function.php?title=$title&author=$author&category=$category&page=$i'>$i</a> "; // redo steps, but with page paramater being passed.
        }
        echo "</div>";

        // Close database connection
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
