<?php
// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$dbname = "librarydb";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form input values
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $first_name = $_POST['first_name'];
    $surname = $_POST['surname'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];
    $city = $_POST['city'];
    $telephone = $_POST['telephone'];
    $mobile = $_POST['mobile'];

    // Check password length
    if (strlen($pass) != 6) {
        // Password len not equal 6, redirect with a query parameter
        header("Location: ../html/registration.html?password=true");
        exit;
    }

    // Check mobile number length
    if (!ctype_digit($mobile) || strlen($mobile) != 10) {
        // Mobile len not equal 10, redirect with a query parameter
        header("Location: ../html/registration.html?mobile=true");
        exit;
    }

    // Establish a connection to the database
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?"); // prepare connection - this way used to stop sql injection
    $stmt->bind_param("s", $user); // bind ? to inputed user - s refers to string data type
    $stmt->execute(); // execute the sql query
    $result = $stmt->get_result();

    if ($result->num_rows > 0) { // if a result came back using your inputed username
        // Username already exists, redirect with a query parameter
        header("Location: ../html/registration.html?duplicate=true");
        $stmt->close();
        $conn->close();
        exit;
    }

    // Prepare an SQL statement to insert a new user
    $stmt = $conn->prepare("INSERT INTO users (username, password, firstname, surname, addressline1, addressline2, city, telephone, mobile) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"); // prepare connection - this way used to stop sql injection
    $stmt->bind_param("sssssssii", $user, $pass, $first_name, $surname, $address1, $address2, $city, $telephone, $mobile); // the s refer to string the i refers to integer for the numbers

    // Execute the statement
    if ($stmt->execute()) {
        // Registration successful, redirect to login page
        header("Location: ../index.html");
        // Close the statement and connection
        $stmt->close();
        $conn->close();
        exit;
    } else {
        // Error handling
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
