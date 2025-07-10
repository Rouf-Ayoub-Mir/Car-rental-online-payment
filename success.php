<?php
session_start();
include('includes/config.php');

$email = $_SESSION['login'];
$sql = "SELECT * FROM tblbooking WHERE userEmail = :email ORDER BY BookingNumber DESC LIMIT 1";
$query = $dbh->prepare($sql);
$query->bindParam(':email', $email, PDO::PARAM_STR);
$query->execute();
$booking = $query->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    echo "<h3>No recent booking found.</h3>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #e1f5fe);
            margin: 0;
            padding: 20px;
            animation: fadeIn 1s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #2E86C1;
            font-size: 32px;
        }
        .datetime {
            font-size: 16px;
            color: #555;
            margin-top: 5px;
        }
        .invoice {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1.2s ease-in;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2E86C1;
        }
        .details p {
            margin: 5px 0;
        }
        .status {
            padding: 10px;
            background: #ffeeba;
            color: #856404;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            margin-top: 20px;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .invoice, .invoice * {
                visibility: visible;
            }
            .invoice {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
        .buttons {
            text-align: center;
            margin-top: 30px;
        }
        .buttons button {
            padding: 10px 20px;
            margin: 10px;
            background-color: #2E86C1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .buttons button:hover {
            background-color: #1B4F72;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Success! Your Booking is in process.</h1>
    <div class="datetime" id="datetime"></div>
</div>

<div class="invoice" id="invoice">
    <h2>Booking Invoice</h2>
    <div class="details">
        <p><strong>Booking Number:</strong> <?php echo htmlentities($booking['BookingNumber']); ?></p>
        <p><strong>Name:</strong> <?php echo htmlentities($booking['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlentities($booking['userEmail']); ?></p>
        <p><strong>Vehicle ID:</strong> <?php echo htmlentities($booking['VehicleId']); ?></p>
        <p><strong>From:</strong> <?php echo htmlentities($booking['FromDate']); ?></p>
        <p><strong>To:</strong> <?php echo htmlentities($booking['ToDate']); ?></p>
        <p><strong>No. of Days:</strong> <?php echo htmlentities($booking['no_of_days']); ?></p>
        <p><strong>Total Paid:</strong> ₹<?php echo htmlentities($booking['total_amount']); ?></p>
        <p><strong>Residence:</strong> <?php echo htmlentities($booking['residence']); ?></p>
    </div>
    <div class="status">
        <strong>Status:</strong> Your booking is currently <b>In Process</b>.<br>
        We are verifying your documents. You'll be notified upon approval or rejection.
    </div>
</div>

<div class="buttons">
    <button onclick="window.print()">🖨️ Print Invoice</button>
    <button onclick="downloadPDF()">⬇️ Download as PDF</button>
</div>

<div style="text-align: center; margin-top: 20px;">
    <p>You will be redirected to the <a href="index.php">home page</a> in <span id="countdown">30</span> seconds...</p>
    <p>If not redirected, <a href="index.php">click here</a>.</p>
</div>

<!-- html2pdf.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function downloadPDF() {
        const invoice = document.getElementById('invoice');
        const opt = {
            margin: 0.5,
            filename: 'booking-invoice.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().from(invoice).set(opt).save();
    }

    // Countdown redirect
    let seconds = 30;
    const countdownEl = document.getElementById('countdown');
    const interval = setInterval(() => {
        seconds--;
        countdownEl.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = 'index.php';
        }
    }, 1000);

    // Live Date & Time
    function updateDateTime() {
        const now = new Date();
        const formatted = now.toLocaleString();
        document.getElementById('datetime').textContent = formatted;
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();
</script>

</body>
</html>
