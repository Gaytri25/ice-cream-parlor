<?php 
session_start();

// Ensure user is logged in
if (!isset($_SESSION["username"])) {
    echo "<script>alert('Please log in first!'); window.location='login.php';</script>";
    exit();
}

// Database connection
$conn = new mysqli("localhost","root", "", "user1");
//$conn = new mysqli("sql307.infinityfree.com", "if0_38640462", "y444lyPudGY8gov", "if0_38640462_user1");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted and flavors are selected
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['flavors'])) {
    $username = $_SESSION["username"];
    $flavors = $_POST['flavors'];
    $quantities = $_POST['qty'];

    // Price list for each flavor
    $prices = [
        "Vanilla" => 50, "Chocolate" => 60, "Strawberry" => 55, "Mango" => 65, "Butterscotch" => 70,
        "Pistachio" => 80, "Blueberry" => 75, "Coffee" => 85, "Caramel" => 90, "Mint" => 70,
        "Hazelnut" => 95, "Coconut" => 60, "Black Currant" => 80, "Almond" => 100, "Raspberry" => 85,
        "Cherry" => 90, "Peach" => 95, "Lemon" => 60, "Gulab Jamun" => 110, "Kesar Pista" => 120,
        "Mawa Malai" => 125, "Cookies & Cream" => 85, "Tiramisu" => 100, "Brownie Fudge" => 110, "Nutella Swirl" => 130
    ];

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO `order1` (uname, flavor, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $username, $flavor, $quantity, $price);

    // Loop through selected flavors and insert into database
    foreach ($flavors as $flavor) {
        $quantity = intval($quantities[$flavor]); // Ensure quantity is an integer
        $price = $prices[$flavor] * $quantity; // Calculate total price for the flavor
        
        if (!$stmt->execute()) {
            echo "Error inserting order: " . $stmt->error;
            exit();
        }
    }

    $stmt->close();
    $conn->close();

    // Redirect after successful order
    echo "<script>alert('Order placed successfully!'); window.location='bill.php';</script>";
} else {
    echo "<script>alert('No ice cream selected!'); window.location='order1.html';</script>";
}
?>
