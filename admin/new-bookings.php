<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{

 ?>

<!doctype html>
<html lang="en" class="no-js">



<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">
	
	<title>Motohub | New Bookings   </title>

	<!-- Font awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="css/fileinput.min.css">
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<!-- Admin Stye -->
	<link rel="stylesheet" href="css/style.css">
  <style>
		.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>

</head>

<body>
	<?php include('includes/header.php');?>

	<div class="ts-main-content">
		<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">

						<h2 class="page-title">New Bookings</h2>

						<!-- Zero Configuration Table -->
						<div class="panel panel-default">
							<div class="panel-heading">Bookings Info</div>
							<div class="panel-body">

								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th>#</th>
      <th>Name</th>
      <th>Booking No.</th>
      <th>Vehicle</th>
      <th>From Date</th>
      <th>To Date</th>
      <th>Posting date</th>
      <th>Document Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th>#</th>
      <th>Name</th>
      <th>Booking No.</th>
      <th>Vehicle</th>
      <th>From Date</th>
      <th>To Date</th>
      <th>Posting date</th>
      <th>Document Status</th>
      <th>Action</th>
    </tr>
  </tfoot>
  <tbody>

<?php 
$status=0;
$sql = "SELECT tblusers.FullName,
               tblbrands.BrandName,
               tblvehicles.VehiclesTitle,
               tblbooking.FromDate,
               tblbooking.ToDate,
               tblbooking.message,
               tblbooking.VehicleId as vid,
               tblbooking.Status,
               tblbooking.PostingDate,
               tblbooking.id,
               tblbooking.BookingNumber,
               tblbooking.document_status,
               tblbooking.dl_front,
               tblbooking.dl_back,
               tblbooking.aadhaar_front,
               tblbooking.aadhaar_back
        FROM tblbooking 
        JOIN tblvehicles ON tblvehicles.id = tblbooking.VehicleId 
        JOIN tblusers ON tblusers.EmailId = tblbooking.userEmail 
        JOIN tblbrands ON tblvehicles.VehiclesBrand = tblbrands.id 
        WHERE tblbooking.Status = :status";

$query = $dbh -> prepare($sql);
$query -> bindParam(':status',$status, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{				?>	
<tr>
  <td><?php echo htmlentities($cnt);?></td>
  <td><?php echo htmlentities($result->FullName);?></td>
  <td><?php echo htmlentities($result->BookingNumber);?></td>
  <td><a href="edit-vehicle.php?id=<?php echo htmlentities($result->vid);?>"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></a></td>
  <td><?php echo htmlentities($result->FromDate);?></td>
  <td><?php echo htmlentities($result->ToDate);?></td>
  <td><?php echo htmlentities($result->PostingDate);?></td>
  <td>
  <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#documentModal<?php echo $cnt; ?>">View Docs</button>

  <!-- Modal -->
  <div class="modal fade" id="documentModal<?php echo $cnt; ?>" tabindex="-1" role="dialog" aria-labelledby="docLabel<?php echo $cnt; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="docLabel<?php echo $cnt; ?>">Uploaded Documents</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body text-center">
          <img src="<?php echo htmlentities('http://localhost/motohubltd/motohub/' . $result->dl_front); ?>" class="img-responsive" style="max-width: 100%; max-height: 300px;"><br>
          <img src="<?php echo htmlentities('http://localhost/motohubltd/motohub/' . $result->dl_back); ?>" class="img-responsive" style="max-width: 100%; max-height: 300px;"><br>
          <img src="<?php echo htmlentities('http://localhost/motohubltd/motohub/' . $result->aadhaar_front); ?>" class="img-responsive" style="max-width: 100%; max-height: 300px;"><br>
          <img src="<?php echo htmlentities('http://localhost/motohubltd/motohub/' . $result->aadhaar_back); ?>" class="img-responsive" style="max-width: 100%; max-height: 300px;">
        </div>
      </div>
    </div>
  </div>
</td>

  <td><a href="bookig-details.php?bid=<?php echo htmlentities($result->id);?>"> View</a></td>
</tr>
<?php $cnt=$cnt+1; }} ?>

  </tbody>
</table>


							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
</body>
</html>
<?php } ?>
