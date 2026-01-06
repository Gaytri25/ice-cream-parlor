
error_reporting(E_ALL);
ini_set('display_errors', 1);

<?php
session_start();
$conn = new mysqli("localhost","root", "", "user1");
////$conn = new mysqli("sql307.infinityfree.com", "if0_38640462", "y444lyPudGY8gov", "if0_38640462_user1");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}







if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Fetch user data from reg table
    $sql = "SELECT username, password FROM reg WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Compare entered password with stored password (No hashing used)
        if ($password === $row["password"]) {
            // Start session and redirect to order1.html
            $_SESSION["username"] = $username;
            header("Location: order1.html");
            exit();
        } else {
            echo "<script>alert('Incorrect Password!'); window.location='login.html';</script>";
        }
    } else {
        echo "<script>alert('Username not found!'); window.location='login.html';</script>";
    }

    mysqli_close($conn);
}
?>
