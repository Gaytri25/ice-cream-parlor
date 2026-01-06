<?php
$conn = new mysqli("sql307.infinityfree.com", "if0_38640462", "y444lyPudGY8gov", "if0_38640462_user1");

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirmpassword"]);

    // Password Match Check
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match!'); window.location='register.html';</script>";
        exit();
    }

    // Directly storing plain text password (NOT SECURE)
    $stmt = $conn->prepare("INSERT INTO reg (fullname, username, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $username, $email, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration Successful! You can now log in.'); window.location='login.html';</script>";
    } else {
        echo "<script>alert('Registration Failed! Try again.'); window.location='register.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
