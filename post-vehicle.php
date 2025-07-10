<?php
session_start();
include('includes/config.php');

// Redirect if not logged in
if(strlen($_SESSION['login'])==0){
    header('location:index.php');
    exit;
}

if(isset($_POST['submit'])) {
    $user_id = $_SESSION['id']; // Assuming user ID is stored in session
    $title = $_POST['title'];
    $brand = $_POST['brand'];
    $overview = $_POST['overview'];
    $price = $_POST['price'];
    $fuel = $_POST['fuel'];
    $model = $_POST['modelyear'];
    $seats = $_POST['seating'];
    
    $image = $_FILES['image']['name'];
    $tmp_image = $_FILES['image']['tmp_name'];
    $folder = "img/vehicleimages/" . $image;

    move_uploaded_file($tmp_image, $folder);

    $sql = "INSERT INTO tbluservehicles(user_id, VehiclesTitle, VehiclesBrand, VehicleOverview, PricePerDay, FuelType, ModelYear, SeatingCapacity, VehicleImage1)
            VALUES(:user_id, :title, :brand, :overview, :price, :fuel, :model, :seats, :image)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':title', $title);
    $query->bindParam(':brand', $brand);
    $query->bindParam(':overview', $overview);
    $query->bindParam(':price', $price);
    $query->bindParam(':fuel', $fuel);
    $query->bindParam(':model', $model);
    $query->bindParam(':seats', $seats);
    $query->bindParam(':image', $image);
    $query->execute();

    $msg = "Vehicle posted successfully and is pending admin approval.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Post Your Vehicle | Motohub</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="container" style="padding: 40px;">
    <h2>Post Your Vehicle</h2>
    <?php if(isset($msg)){ echo "<div style='color:green;'>$msg</div>"; } ?>

    <form method="post" enctype="multipart/form-data">
        <label>Vehicle Title:</label>
        <input type="text" name="title" required><br><br>

        <label>Brand:</label>
        <input type="text" name="brand" required><br><br>

        <label>Overview:</label>
        <textarea name="overview" required></textarea><br><br>

        <label>Price Per Day (INR):</label>
        <input type="number" name="price" required><br><br>

        <label>Fuel Type:</label>
        <select name="fuel" required>
            <option>Petrol</option>
            <option>Diesel</option>
            <option>Electric</option>
        </select><br><br>

        <label>Model Year:</label>
        <input type="text" name="modelyear" required><br><br>

        <label>Seating Capacity:</label>
        <input type="number" name="seating" required><br><br>

        <label>Upload Vehicle Image:</label>
        <input type="file" name="image" accept="image/*" required><br><br>

        <button type="submit" name="submit">Post Vehicle</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
