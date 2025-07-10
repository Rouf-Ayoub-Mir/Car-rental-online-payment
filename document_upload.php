<?php
// Connect to DB
include('includes/config.php');

$vhid = isset($_GET['vhid']) ? intval($_GET['vhid']) : 0;
$fromdate = $_GET['fromdate'];
$todate = $_GET['todate'];

// Check for overlapping bookings
$sql = "SELECT * FROM tblbooking 
        WHERE VehicleId = :vhid 
        AND Status NOT IN ('Cancelled', 'Rejected') 
        AND (
            (:fromdate BETWEEN FromDate AND ToDate) OR
            (:todate BETWEEN FromDate AND ToDate) OR
            (FromDate BETWEEN :fromdate AND :todate)
        )";
$query = $dbh->prepare($sql);
$query->bindParam(':vhid', $vhid);
$query->bindParam(':fromdate', $fromdate);
$query->bindParam(':todate', $todate);
$query->execute();

if ($query->rowCount() > 0) {
    echo "<script>alert('This vehicle is already booked for the selected dates. Please choose different dates.'); window.location.href='vehical-details.php?vhid=" . $vhid . "';</script>";
    exit;
}

// Initialize variables
$vehicleName = 'Invalid vehicle';
$pricePerDay = $_GET['price'] ?? 0.00;  // ✅ Pull price from URL
$totalPrice = 0.00;
$days = 1;

$fromdate = $_GET['fromdate'] ?? '';
$todate = $_GET['todate'] ?? '';
$vehicle_id = $_GET['vehicle_id'] ?? '';

// Fetch vehicle name only
if ($vehicle_id) {
    $sql = "SELECT VehiclesTitle FROM tblvehicles WHERE id = :vehicle_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':vehicle_id', $vehicle_id, PDO::PARAM_INT);
    $query->execute();

    if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $vehicleName = $row['VehiclesTitle'];
    }
}
$sql = "SELECT 1 FROM tblbooking 
        WHERE VehicleId = :vhid 
        AND Status NOT IN ('Cancelled', 'Rejected') 
        AND (
            (:fromdate BETWEEN FromDate AND ToDate) OR
            (:todate BETWEEN FromDate AND ToDate) OR
            (FromDate BETWEEN :fromdate AND :todate)
        )";
$query = $dbh->prepare($sql);
$query->bindParam(':vhid', $vhid);
$query->bindParam(':fromdate', $fromdate);
$query->bindParam(':todate', $todate);
$query->execute();

if ($query->rowCount() > 0) {
    echo "<script>alert('This vehicle is already booked for the selected dates. Please choose different dates.'); window.location.href='vehical-details.php?vhid=" . $vhid . "';</script>";
    exit;
}

// Calculate number of days and total
if ($fromdate && $todate) {
    $start = new DateTime($fromdate);
    $end = new DateTime($todate);
    $interval = $start->diff($end);
    $days = max(1, $interval->days); 
    $totalPrice = $days * $pricePerDay;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Motohub - Upload Documents</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

* {
  box-sizing: border-box;
  font-family: 'Inter', sans-serif;
}

body {
  background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  color: #fff;
}

.upload-container {
  backdrop-filter: blur(20px);
  background: rgba(255, 255, 255, 0.05);
  border-radius: 20px;
  padding: 35px 30px;
  width: 100%;
  max-width: 520px;
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.upload-container h2 {
  font-size: 26px;
  font-weight: 600;
  margin-bottom: 25px;
  text-align: center;
  background: linear-gradient(90deg, #00c6ff, #0072ff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #ddd;
}

input[type="file"],
input[type="text"],
input[type="email"],
input[type="number"] {

  width: 100%;
  padding: 12px;
  background-color: rgba(255, 255, 255, 0.1);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  transition: all 0.3s ease;
}

input[type="file"]:hover,
input[type="text"]:hover {
  background-color: rgba(255, 255, 255, 0.15);
}

input[type="submit"] {
  width: 100%;
  padding: 14px;
  font-weight: 600;
  background: linear-gradient(135deg, #00c6ff, #0072ff);
  border: none;
  border-radius: 12px;
  color: white;
  font-size: 16px;
  margin-top: 10px;
  cursor: pointer;
  transition: background 0.3s ease;
}

input[type="submit"]:hover {
  background: linear-gradient(135deg, #0072ff, #00c6ff);
}

.note {
  font-size: 13px;
  text-align: center;
  color: #bbb;
  margin-top: 10px;
}

input::file-selector-button {
  padding: 10px 16px;
  margin-right: 12px;
  border: none;
  border-radius: 8px;
  background: #1e88e5;
  color: white;
  cursor: pointer;
  transition: background 0.3s ease;
}

input::file-selector-button:hover {
  background: #1565c0;
}

@media (max-width: 600px) {
  .upload-container {
    padding: 25px 20px;
  }
}

  </style>
  
</head>
<body>
  <div class="form-container">
    <h2>Upload Your Documents</h2>

    <p>
      <strong>Vehicle:</strong> <?= htmlspecialchars($vehicleName) ?><br>
      <strong>Price per Day:</strong> ₹<?= number_format($pricePerDay, 2) ?><br>
      <strong>Days:</strong> <?= $days ?><br>
      <strong>Total Price:</strong> ₹<?= number_format($totalPrice, 2) ?>
    </p>
<form action="submit-form.php" method="POST" enctype="multipart/form-data">
  <label for="name">Full Name</label>
  <input type="text" id="name" name="name" required />

  <label for="email">Email Address</label>
  <input type="email" id="email" name="email" required />

  <label for="residence">Residence</label>
  <input type="text" id="residence" name="residence" required />

  <label for="dl_front">Driving License (Front)</label>
  <input type="file" id="dl_front" name="dl_front" accept="image/*,application/pdf" required />

  <label for="dl_back">Driving License (Back)</label>
  <input type="file" id="dl_back" name="dl_back" accept="image/*,application/pdf" required />

  <label for="aadhaar_front">Aadhaar Card (Front)</label>
  <input type="file" id="aadhaar_front" name="aadhaar_front" accept="image/*,application/pdf" required />

  <label for="aadhaar_back">Aadhaar Card (Back)</label>
  <input type="file" id="aadhaar_back" name="aadhaar_back" accept="image/*,application/pdf" required />

  <!-- Hidden Fields -->
  <input type="hidden" name="vehicle_id" value="<?= $vehicle_id ?>">
  <input type="hidden" name="vehicle_name" value="<?= htmlspecialchars($vehicleName) ?>">
  <input type="hidden" name="fromdate" value="<?= htmlspecialchars($fromdate) ?>">
  <input type="hidden" name="todate" value="<?= htmlspecialchars($todate) ?>">
  <input type="hidden" name="days" value="<?= $days ?>">
  <input type="hidden" name="total_price" value="<?= $totalPrice ?>">

  <input type="submit" value="Proceed to Payment" />
</form>

    
  </div>
</body>
</html>