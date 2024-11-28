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
if (!isset($_SESSION['username'])) {
    die("You must be logged in to reserve a book.");
}

// Get the current username
$username = $_SESSION['username'];

// Get ISBN from the request
if (!isset($_GET['isbn'])) {
    die("No book specified for reservation.");
}
$isbn = $_GET['isbn'];

// Prepare to update the `books` table to mark the book as reserved
$stmt1 = $conn->prepare("UPDATE books SET reserved = 'Y' WHERE isbn = ?");
$stmt1->bind_param("s", $isbn);
$stmt1->execute();

// Check if the book was successfully updated
$reservation_message = "";
if ($stmt1->affected_rows > 0) {
    // Prepare to insert into the 'reservations' table
    $stmt2 = $conn->prepare("INSERT INTO reservations (isbn, username, reserveddate) VALUES (?, ?, ?)");
    $reserveddate = date("Y-m-d"); // Get the current date
    $stmt2->bind_param("sss", $isbn, $username, $reserveddate);
    $stmt2->execute();

    if ($stmt2->affected_rows > 0) {
        $reservation_message = "Book reserved successfully!";
    } else {
        $reservation_message = "Failed to record the reservation.";
    }

    $stmt2->close();
} else {
    $reservation_message = "Failed to reserve the book. It might already be reserved.";
}

$stmt1->close();
$conn->close();
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

    <!-- Main Content -->
    <div class="center" style="min-height: 90vh; padding: 20px;">
        <h1 style="transform: scale(1.4);">Reservation Status</h1>
        <p style="transform: scale(1.4);"><?php echo $reservation_message; ?></p>
        <p style="transform: scale(1.4);">You will be redirected to the search page shortly.</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2024 Luke's Library. For WebDev2.
    </div>

</body>
<script type="text/javascript">
        // Redirect after 5 seconds
        setTimeout(function() {
            window.location.href = "search_page.php";
        }, 3000);
</script>
</html>
