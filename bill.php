<?php 
session_start();

// Ensure user is logged in
if (!isset($_SESSION["username"])) {
    echo "<script>alert('Please log in first!'); window.location='login.php';</script>";
    exit();
}

// Database connection
//$conn = new mysqli("sql307.infinityfree.com", "if0_38640462", "y444lyPudGY8gov", "if0_38640462_user1");
$conn = new mysqli("localhost","root", "", "user1");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION["username"];

// Fetch order details
$sql = "SELECT * FROM `order1` WHERE uname='$username'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill - Ice Cream Parlor</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }
        .bill-container {
            width: 80%;
            margin: 50px auto;
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        .bill-container h1 {
            color: #ff6600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #ffcc00;
            color: black;
        }
        .btn {
            background: #ff6600;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #ff3300;
        }
    </style>
</head>
<body>

    <div class="bill-container">
        <h1>Your Bill 🍦</h1>
        
        <table>
            <tr>
                <th>Flavor</th>
                <th>Quantity</th>
                <th>Price (₹)</th>
            </tr>

            <?php 
            $total = 0;
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['flavor']}</td>
                        <td>{$row['quantity']}</td>
                        <td>₹{$row['price']}</td>
                    </tr>";
                    $total += $row['price'];
                }
            } else {
                echo "<tr><td colspan='3'>No orders found!</td></tr>";
            }
            ?>

        </table>

        <h2>Total Amount: ₹<?php echo $total; ?></h2>
        <button class="btn" onclick="window.print()">Print Bill</button>
       
        <form action="download_bill.php" method="POST">
    <input type="hidden" name="send_invoice" value="1">
    <button class="btn" type="submit">📧 Send Invoice </button>
</form>

    </div>

</body>
</html>
