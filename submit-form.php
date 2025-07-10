<?php
require 'vendor/autoload.php'; // Stripe SDK
\Stripe\Stripe::setApiKey('');

// Database connection
include('includes/config.php');

// Get form inputs
$name       = $_POST['name'] ?? '';
$email      = $_POST['email'] ?? '';
$residence  = $_POST['residence'] ?? '';
$fromdate   = $_POST['fromdate'] ?? '';
$todate     = $_POST['todate'] ?? '';
$vehicle_id = $_POST['vehicle_id'] ?? '';
$vehicle    = $_POST['vehicle_name'] ?? '';
$total_price = (float) ($_POST['total_price'] ?? 0);
$days       = $_POST['days'] ?? 1;

// Upload directory
$upload_dir = 'uploads/documents/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Helper function to save file
function saveFile($file, $prefix) {
    global $upload_dir;
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . time() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $upload_dir . $filename);
        return $upload_dir . $filename;
    }
    return '';
}

// Save all uploaded files
$dl_front      = saveFile($_FILES['dl_front'], 'dl_front');
$dl_back       = saveFile($_FILES['dl_back'], 'dl_back');
$aadhaar_front = saveFile($_FILES['aadhaar_front'], 'aadhaar_front');
$aadhaar_back  = saveFile($_FILES['aadhaar_back'], 'aadhaar_back');

// Insert into tblbooking (adjust your DB schema if needed)
$sql = "INSERT INTO tblbooking (userEmail, vehicleId, fromDate, toDate, message, status, dl_front, dl_back, aadhaar_front, aadhaar_back, document_status, name, residence, total_amount, no_of_days)
        VALUES (:email, :vehicle_id, :fromdate, :todate, '', 0, :dl_front, :dl_back, :aadhaar_front, :aadhaar_back, 'Pending', :name, :residence, :total_price, :days)";

$query = $dbh->prepare($sql);
$query->bindParam(':email', $email);
$query->bindParam(':vehicle_id', $vehicle_id);
$query->bindParam(':fromdate', $fromdate);
$query->bindParam(':todate', $todate);
$query->bindParam(':dl_front', $dl_front);
$query->bindParam(':dl_back', $dl_back);
$query->bindParam(':aadhaar_front', $aadhaar_front);
$query->bindParam(':aadhaar_back', $aadhaar_back);
$query->bindParam(':name', $name);
$query->bindParam(':residence', $residence);
$query->bindParam(':total_price', $total_price);
$query->bindParam(':days', $days);
$query->execute();

// Stripe Checkout
$checkout_session = \Stripe\Checkout\Session::create([
    'line_items' => [[
        'price_data' => [
            'currency' => 'inr',
            'product_data' => [
                'name' => "Booking: $vehicle",
            ],
            'unit_amount' => (int)($total_price * 100),
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'http://localhost/motohubltd/motohub/success.php',
    'cancel_url' => 'https://yourdomain.com/cancel.php',
]);

// Redirect to Stripe Checkout
header("Location: " . $checkout_session->url);
exit;
?>
