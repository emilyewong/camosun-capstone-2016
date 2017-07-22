<?php
    require_once 'navbar.php';
	//this script is called by search_products_form.php and displays product's information
    //this script is connected to product by sku, which was passed as a name-value pair via GET
    
    //define variable and set to empty
    $sku = "";
    
    //check if sku only contains numbers and length is between 1 and 11
    include ("./functions/functions.php"); //test_input function sanitizes input
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (preg_match("/^[0-9]{1,11}$/", base64_decode($_GET["sku"]))) {
            $sku = test_input(base64_decode($_GET["sku"]));
        }  
    
		//database connection details kept in separate file
		include ("database.php");
		//construct select statement using PDO prepared statement
		//results from select statement used to populate form
		$statement = $connection->prepare(
			"SELECT * 
			FROM products 
			WHERE sku = :sku");
		$statement->bindParam(':sku', $sku);
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
			<h2 style="text-align:center;">View product information</h2>
			<form class="form-horizontal" role="form">
				<div class="form-group">
					<label class="control-label col-md-4" for="sku">SKU:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->sku;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="brand_id">Brand ID:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->brand_id;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="size">Size:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->size;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="is_packaged">Is packaged:</label>
					<div class="col-md-2">
					<!--php code to display the package status-->
					<?php if ($row->is_packaged == 1) echo "<input class='form-control' value='Yes' type='text' readonly>";
						else echo "<input class='form-control' value='No' type='text' readonly>";
					?>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="container_deposit">Container deposit:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->container_deposit;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="duty_paid">Duty paid:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->duty_paid;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="upc">UPC:</label>
					<div class="col-md-3">
						<input class="form-control" value="<?php echo $row->upc;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<!--button to edit form calls a function that passes the sku as a name-value pair via GET-->
						<?php
							//investors cannot edit data
							if ($response['role'] != "Investor") {
								echo "<button class='btn btn-success' onclick='loadEdit($row->sku);return false;'><span class='glyphicon glyphicon-edit'></span> Edit</button>";
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
				window.location = "edit_product_form.php?sku=" + window.btoa(id);
			}
		</script>
	</body>
</html>