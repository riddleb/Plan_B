<?php
/* ---------------------------------------------------------------------------
 * filename    : fr_persons.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program displays a list of volunteers (table: fr_persons)
 * ---------------------------------------------------------------------------
 */
session_start();
if(!isset($_SESSION["fr_person_id"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');     // go to login page
	exit;
}
$sessionid = $_SESSION['fr_person_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
	<link rel="icon" href="cardinal_logo.png" type="image/png" />
</head>

<body style="background-color: lightblue !important";>
    <div class="container">
		<?php 
			//gets logo
			require 'functions.php';
			functions::logoDisplay2();
		?>
		<div class="row">
			<h3>Customer</h3>
		</div>
		<div class="row">
			<p>
				<?php if($_SESSION['fr_person_title']=='Administrator')
					echo '<a href="fr_per_create.php" class="btn btn-primary">Add Customer</a>';
				?>
				<a href="logout.php" class="btn btn-warning">Logout</a> &nbsp;&nbsp;&nbsp;
				<a href="fr_events.php" class="btn btn-primary">Events</a> &nbsp;
				<a href="fr_assignments.php" class="btn btn-primary">All Assignments</a>&nbsp;
				<a href="fr_assignments.php?id=<?php echo $sessionid; ?>" class="btn btn-primary">My Assignments</a>&nbsp;
			</p>
				
			<table class="table table-striped table-bordered" style="background-color: lightgrey !important">
				<thead>
					<tr>
					    <th>Profile Picture</th>
						<th>Name</th>
						<th>Email</th>
						<th>Mobile</th>
						<th>Address</th>
						<th>City</th>
						<th>State</th>
						<th>Zip Code</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						include 'database.php';
						$pdo = Database::connect();
						$sql = 'SELECT `fr_persons`.*, COUNT(`fr_assignments`.assign_per_id) AS countAssigns FROM `fr_persons` LEFT OUTER JOIN `fr_assignments` ON (`fr_persons`.id=`fr_assignments`.assign_per_id) GROUP BY `fr_persons`.id ORDER BY `fr_persons`.lname ASC, `fr_persons`.fname ASC';
						//$sql = 'SELECT * FROM fr_persons ORDER BY `fr_persons`.lname ASC, `fr_persons`.fname ASC';
						foreach ($pdo->query($sql) as $row) {
							echo '<tr>';
	
						echo '<td> <img src="data:image/jpeg;base64,'.base64_encode($row['filecontent'] ).'" height="200" width="200" class="img-thumnail" /> </td>';
								echo '<td>'. trim($row['lname']) . ', ' . trim($row['fname']) . ' (' . substr($row['title'], 0, 1) . ') - '.$row['countAssigns']. ' events</td>';
							echo '<td>'. $row['email'] . '</td>';
							echo '<td>'. $row['mobile'] . '</td>';
							echo '<td>'. $row['address'] . '</td>';
							echo '<td>'. $row['city'] . '</td>';
							echo '<td>'. $row['state'] . '</td>';
							echo '<td>'. $row['zip'] . '</td>';
							echo '<td width=250>';
							# always allow read
							echo '<a class="btn" href="fr_per_read.php?id='.$row['id'].'">Details</a>&nbsp;';
							# person can update own record
							if ($_SESSION['fr_person_title']=='Administrator'
								|| $_SESSION['fr_person_id']==$row['id'])
								echo '<a class="btn btn-success" href="fr_per_update.php?id='.$row['id'].'">Update</a>&nbsp;';
							# only admins can delete
							if ($_SESSION['fr_person_title']=='Administrator' 
								&& $row['countAssigns']==0)
								echo '<a class="btn btn-danger" href="fr_per_delete.php?id='.$row['id'].'">Delete</a>';
							if($_SESSION["fr_person_id"] == $row['id']) 
								echo " &nbsp;&nbsp;Me";
							echo '</td>';
							echo '</tr>';
						}
						Database::disconnect();
					?>
				</tbody>
			</table>
			
    	</div>
    </div> <!-- /container -->
  </body>
</html>