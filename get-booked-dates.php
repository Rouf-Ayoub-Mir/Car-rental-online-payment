<?php
include('includes/config.php');

$vehicle_id = $_GET['vehicle_id'] ?? null;
if (!$vehicle_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT FromDate, ToDate FROM tblbooking 
        WHERE VehicleId = :vehicle_id 
        AND Status != 'Cancelled'";
$query = $dbh->prepare($sql);
$query->bindParam(':vehicle_id', $vehicle_id, PDO::PARAM_INT);
$query->execute();

$bookedRanges = [];
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $from = new DateTime($row['FromDate']);
    $to = new DateTime($row['ToDate']);
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($from, $interval, $to->modify('+1 day'));

    foreach ($period as $date) {
        $bookedRanges[] = $date->format('Y-m-d');
    }
}

echo json_encode(array_unique($bookedRanges));
