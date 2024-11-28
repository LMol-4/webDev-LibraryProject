<?php
// Start session
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "librarydb");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("You must be logged in to unreserve a book.");
}

// Get the current username from the session
$username = $_SESSION['username'];

// Get ISBN from the request
if (!isset($_GET['isbn'])) {
    die("No book specified for unreservation.");
}
$isbn = $_GET['isbn'];

// First, unreserve the book by updating the books table
$stmt1 = $conn->prepare("UPDATE books SET reserved = 'N' WHERE isbn = ?");
$stmt1->bind_param("s", $isbn);
$stmt1->execute();

// Check if the update was successful
if ($stmt1->affected_rows > 0) {
    // Then, remove the reservation from the reservations table
    $stmt2 = $conn->prepare("DELETE FROM reservations WHERE isbn = ? AND username = ?");
    $stmt2->bind_param("ss", $isbn, $username);
    $stmt2->execute();

    if ($stmt2->affected_rows > 0) {
        // If successful, commit the transaction
        echo "Book successfully unreserved!";
    } else {
        // If no rows affected, something went wrong
        echo "Failed to remove reservation.";
    }

    $stmt2->close();
} else {
    // If no rows affected in books table, something went wrong
    echo "Failed to unreserve the book. It might not be reserved.";
}

header("Location: reserved_list.php");

$stmt1->close();
$conn->close();
exit;



?>
