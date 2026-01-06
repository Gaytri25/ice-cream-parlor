<?php
session_start();



error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ CORS Headers (Fixes CORB)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");



//$fpdf_path = __DIR__ . '/fpdf186/fpdf.php'; // check path

$fpdf_path ='fpdf186/fpdf.php';

//if (file_exists($fpdf_path)) 
//{
 //   echo "FPDF file exists at: " . $fpdf_path;
//} else
//	{
   // echo " FPDF file NOT found. Check the folder structure!";
//}

// check file exist or not in folder ice cream parlour  for hosting purpose on web
//for server
//if (!class_exists('FPDF')) {
 //   require_once '/home/vol9_8/infinityfree.com/if0_38640462/htdocs/fpdf186/fpdf.php';
//}
//$conn = new mysqli("sql307.infinityfree.com", "if0_38640462", "y444lyPudGY8gov", "if0_38640462_user1");
require($fpdf_path);


//  Database Connection
$conn = new mysqli("localhost","root", "", "user1");
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Check User Login
if (!isset($_SESSION["username"])) {
    echo "<script>alert('Please log in first!'); window.location='login.php';</script>";
    exit();
}

$username = $_SESSION["username"];
$date = date("d-m-Y");

//  Fetch User Email
$emailQuery = "SELECT email FROM reg WHERE username=?";
$stmtEmail = $conn->prepare($emailQuery);
$stmtEmail->bind_param("s", $username);
$stmtEmail->execute();
$resultEmail = $stmtEmail->get_result();
$userEmail = ($row = $resultEmail->fetch_assoc()) ? $row['email'] : '';

if (!$userEmail) {
    echo "<script>alert('User email not found!'); window.location='bill.php';</script>";
    exit();
}

//  Fetch Order Details
$query = "SELECT flavor, quantity, price FROM order1 WHERE uname=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;

//  Generate PDF Invoice
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, "Ice Cream Parlor - Tax Invoice", 1, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, "Customer: $username", 0, 0);
$pdf->Cell(95, 6, "Date: $date", 0, 1);
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(70, 8, "Flavor", 1, 0, 'C');
$pdf->Cell(30, 8, "Qty", 1, 0, 'C');
$pdf->Cell(40, 8, "Unit Price (₹)", 1, 0, 'C');
$pdf->Cell(40, 8, "Total Price (₹)", 1, 1, 'C');
$pdf->SetFont('Arial', '', 10);

while ($row = $result->fetch_assoc()) {
    $flavor = $row['flavor'];
    $quantity = $row['quantity'];
    $price = $row['price'];
    $total = $quantity * $price;
    $total_price += $total;

    $pdf->Cell(70, 8, $flavor, 1, 0, 'C');
    $pdf->Cell(30, 8, $quantity, 1, 0, 'C');
    $pdf->Cell(40, 8, "₹" . number_format($price, 2), 1, 0, 'C');
    $pdf->Cell(40, 8, "₹" . number_format($total, 2), 1, 1, 'C');
}

//  Calculate GST & Grand Total
$gst = $total_price * 0.18;
$grand_total = $total_price + $gst;

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(140, 8, "Subtotal", 1, 0, 'R');
$pdf->Cell(40, 8, "₹" . number_format($total_price, 2), 1, 1, 'C');

$pdf->Cell(140, 8, "GST (18%)", 1, 0, 'R');
$pdf->Cell(40, 8, "₹" . number_format($gst, 2), 1, 1, 'C');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(140, 10, "Grand Total", 1, 0, 'R');
$pdf->Cell(40, 10, "₹" . number_format($grand_total, 2), 1, 1, 'C');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(190, 6, "Thank you for choosing Ice Cream Parlor!", 0, 1, 'C');
$pdf->Cell(190, 6, "Visit Again! Stay Cool!", 0, 1, 'C');

//  Ensure Bills Directory Exists
$bill_dir = "bills/";
if (!file_exists($bill_dir)) {
    mkdir($bill_dir, 0777, true);
}

$pdfPath = $bill_dir . "Ice_Cream_Bill_" . time() . ".pdf";
$pdf->Output($pdfPath, "F");

// Send Email Using PHPMailer
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$mail = new PHPMailer(true); // Ensure PHPMailer is initialized properly


try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true;
    $mail->Username = 'icecreamparadice07@gmail.com'; // Replace with your email
    $mail->Password = 'hofj bmdw hzkl tiyz'; // Use App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('icecreamparadice07@gmail.com', 'Ice Cream Parlor');
    $mail->addAddress($userEmail, $username);

    $mail->isHTML(true);
    $mail->Subject = "Your Ice Cream Invoice";
    $mail->Body = "
        <h3>Hello, $username! 🍦</h3>
        <p>Thank you for ordering from Ice Cream Parlor. Please find your invoice attached.</p>
        <p><b>Invoice Date:</b> " . date("d-m-Y") . "</p>
        <p>We appreciate your business!</p>
    ";

    // Attach PDF
    if (file_exists($pdfPath)) {
        $mail->addAttachment($pdfPath);
    } else {
        echo "<script>alert('PDF file not found!'); window.location='bill.php';</script>";
        exit();
    }

    if ($mail->send()) {
        echo "<script>alert('Invoice sent successfully to $userEmail! Thank You :) Visit Again'); window.location='ty.html';</script>";
    } else {
        echo "<script>alert('Failed to send email!'); window.location='bill.php';</script>";
    }
} catch (Exception $e) {
    echo "<script>alert('Mailer Error: " . $mail->ErrorInfo . "'); window.location='bill.php';</script>";
}

$stmt->close();
$conn->close();
?>
