<?php
// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$dbname = "librarydb";

session_start(); // Used for tracking username variable accross pages

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from POST request
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Establish a connection to the database
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check if the connection was successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare a SQL statement to check if the username exists
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?"); // prepare connection - this way used to stop sql injection
    $stmt->bind_param("s", $user); // bind ? to inputed user - s refers to string data type
    $stmt->execute(); // execute the sql query
    $result = $stmt->get_result();

    // Check if the username exists
    if ($result->num_rows == 1) { // if there is a result from the above username query
        // Username found, fetch the stored password for comparison
        $row = $result->fetch_assoc(); // fetch user table row
        $stored_password = $row['password'];

        // Check if the provided password matches the stored password
        if ($stored_password === $pass) { // password match
            $_SESSION['username'] = $user; // store username in session for use accross pages
            header("Location: reserved_list.php"); // Redirect to the reserved books page
            // Close statement and connection
            $stmt->close();
            $conn->close();
            exit;
        } else {
            // Password is incorrect
            header("Location: ../index.html?wrong=true"); // Redirect to index with a paramter
            // Close statement and connection
            $stmt->close();
            $conn->close();
            exit;
        }
    } else {
        // Username not found
        header("Location: ../index.html?wrong=true"); // Redirect to index with a paramter
        // Close statement and connection
        $stmt->close();
        $conn->close();
        exit;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>