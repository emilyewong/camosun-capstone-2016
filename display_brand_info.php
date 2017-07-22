<?php
require_once './core/init.php';

//to prevent access to this page via url
	if(!isset($_GET['token'])) header('Location: index.php');
	
	//this script is called by add_brand_script.php or edit_brand_script.php and displays brand's information after insert/update
    //this script is connected to brand by brand id, which was passed as a name-value pair via GET
	
    //define variable and set to empty
    $brandID = "";
    
	//check if id only contains numbers and length is between 1 and 11
    include ("./functions/functions.php"); //test_input function sanitizes input
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
		if (preg_match("/^[0-9]{1,11}$/", base64_decode($_GET["brand_id"]))) {
			$brandID = test_input(base64_decode($_GET["brand_id"]));
		}
	
		//database connection details kept in separate file
		include ("database.php");
		//construct select statement using PDO prepared statement
		//results from select statement used to populate form
		$statement = $connection->prepare(
			"SELECT * 
			FROM brands 
			WHERE brand_id = :brandid");
		$statement->bindParam(':brandid', $brandID);
		$result = $statement->execute();
		
		if ($result) $row = $statement->fetchObject();
		
		//close connection
		$connection = null;
	}
?>

		<style type="text/css">
			input,textarea{background-color:rgba(0,0,0,0) !important; border:none !important;}
			input:focus,textarea:focus{outline-style:none !important;
					box-shadow:none !important;
					border-color:transparent !important;}
		</style>
   
		<div class="container">
			<h2 style="text-align:center;">View brand information</h2>
			<form class="form-horizontal" role="form">
				<div class="form-group">
					<label class="control-label col-md-4" for="brand_id">Brand ID:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->brand_id;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="name">Brand name:</label>
					<div class="col-md-4">
						<input class="form-control" value="<?php echo htmlentities($row->name);?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="stake_no">Stake number:</label>
					<div class="col-md-2 output">
						<input class="form-control" value="<?php echo $row->stake_no;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="ibu">IBU:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->ibu;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="color">Color:</label>
					<div class="col-md-1">
						<input class="form-control" value="<?php echo $row->color;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4"for="bold">Bold:</label>
					<div class="col-md-1">
						<input class="form-control" value="<?php echo $row->bold;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="style">Style:</label>
					<div class="col-md-3">
						<input class="form-control" value="<?php echo $row->style;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="alc_per_vol">Alcohol per volume:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->alc_per_vol;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="cost_per_liter">Cost per liter:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->cost_per_liter;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="allergens">Allergens:</label>
					<div class="col-md-4">
						<input class="form-control" value="<?php echo $row->allergens;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="brewmaster_notes">Brewmaster notes:</label>
					<div class="col-md-4">
						<textarea class="form-control" name="brewmaster_notes" id="brewmaster_notes" maxlength="255" rows="6" cols="50" readonly style="resize:vertical;"><?php echo $row->brewmaster_notes;?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="description">Description:</label>
					<div class="col-md-4">
						<textarea class="form-control" name="description" id="description" maxlength="255" rows="6" cols="50" readonly style="resize:vertical;"><?php echo $row->description;?></textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-4"></div>
					<div class="col-md-4">
				<!--button to edit form calls a function that passes the brand id as a name-value pair via GET-->
				<?php
					//investors cannot edit data
					if ($response['role'] != "Investor") {
						echo "<button class='btn btn-success' onclick='loadEdit($row->brand_id);return false;'><span class='glyphicon glyphicon-edit'></span> Edit</button>";
					}
				?>
					</div>
				</div>
			</form>
		</div>
		<script type="text/javascript">
			//javascript function for edit button
			function loadEdit(id) {
				//load page and pass btoa base64 encoded parameter
				window.location = "edit_brand_form.php?brand_id=" + window.btoa(id);
			}
		</script>
	</body>
</html>