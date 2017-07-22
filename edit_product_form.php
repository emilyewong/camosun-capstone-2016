<?php
    require_once 'navbar_investor.php';
	//this form is called by search_products_form.php or view_product_script.php, and displays product information that can be edited
    //this form is connected to product by sku, which was passed as a name-value pair via GET
    
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
	}
?>
	
	<link href="form.css" rel="stylesheet" type="text/css"/>
		<style type="text/css">
			#sku{background-color:rgba(0,0,0,0) !important; border:none !important;}
			#sku:focus:focus{outline-style:none !important;
					box-shadow:none !important;
					border-color:transparent !important;}
		</style>
    <div class="container">
        <div id="content">
			<h2 style="text-align:center;">Edit product information</h2>
			<form class="form-horizontal" role="form" id="edit_prod">
				<div class="form-group">
					<label class="control-label col-md-4" for="sku">SKU:</label>
					<div class="col-md-2">
						<!--sku is displayed but cannot be changed-->
						<input class="form-control" type="text" name="sku" id="sku" value="<?php echo $row->sku;?>" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="brand_id">Brand ID:</label>
					<div class="col-md-4">
						<select class="form-control" name="brand_id" id="brand_id" required>
							<option value="" selected="selected"></option>
							<!--php code for creating select list of all brands-->
							<?php
								//construct select statement using PDO prepared statement
								//get all brand names/ids
								$statementBrand = $connection->prepare(
									"SELECT name, brand_id 
									FROM brands");
								$resultBrand = $statementBrand->execute();
								if ($resultBrand) {
									while ($rowBrand = $statementBrand->fetchObject()) {
										if ($rowBrand->brand_id == $row->brand_id) {
											echo "<option selected='selected' value='" . $rowBrand->brand_id . "'>" . $rowBrand->brand_id . " - " . $rowBrand->name . "</option>";
										}
										else {
											echo "<option value='" . $rowBrand->brand_id . "'>" . $rowBrand->brand_id . " - " . $rowBrand->name . "</option>";
										}
									}
								}
								//close connection
								$connection = null;
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="size">Size:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="size" id="size" maxlength="7" pattern="[0-9]{1,3}[\.][0-9]{3}" value="<?php echo $row->size;?>" title="Must have 3 decimal places" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="is_packaged">Is packaged:</label>
					<div class="col-md-2">	
						<select class="form-control" name="is_packaged" id="is_packaged" required>
							<!--php code to select the package status-->
							<?php 
								if ($row->is_packaged == 1) {
									echo "<option value='0'>No</option>
											<option value='1' selected='selected'>Yes</option>";
								}
								else {
									echo "<option value='0' selected='selected'>No</option>
											<option value='1'>Yes</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="container_deposit">Container deposit:</label>
					<div class="col-md-2">	
						<input class="form-control" type="text" name="container_deposit" id="container_deposit" maxlength="16" pattern="[0-9]{1,13}[\.][0-9]{2}" value="<?php echo $row->container_deposit;?>" title="Must have 2 decimal places" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="duty_paid">Duty paid:</label>
					<div class="col-md-2">	
						<input class="form-control" type="text" name="duty_paid" id="duty_paid" maxlength="16" pattern="[0-9]{1,13}[\.][0-9]{2}" value="<?php echo $row->duty_paid;?>" title="Must have 2 decimal places" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="upc">UPC:</label>
					<div class="col-md-3">	
						<input class="form-control" type="text" name="upc" id="upc" maxlength="32" value="<?php echo $row->upc;?>"/>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Save changes</button>
						&nbsp &nbsp
						<button type="button" class="btn btn-warning" onclick="goBack();return false;"><span class="glyphicon glyphicon-ban-circle"></span> Cancel</button>
					</div>
				</div>
			</form>
		</div>
	</div>
		<script type="text/javascript">
			//ajax function that performs POST action on form
			$(function () { 
				//execute when submit button in form id=edit_prod is pressed
				$('#edit_prod').submit(function(e) {
					//prevents page from reloading
					e.preventDefault();
					//set up variables needed for POST 
					$.ajax({ 
						//method
						type: "POST",
						//php script to use
						url: "edit_product_script.php",
						//grab all form input and put into name-value pairs
						data: {	"sku" : window.btoa($('#sku').val()),
							"brand_id" : window.btoa($('#brand_id').val()),
							"size" : window.btoa($('#size').val()),
							"is_packaged" : window.btoa($('#is_packaged').val()),
							"container_deposit" : window.btoa($('#container_deposit').val()),
							"duty_paid" : window.btoa($('#duty_paid').val()),
							"upc" : window.btoa($('#upc').val())
						},
						//display response message in div id=content
						success: function(response) { 
							$('#content').html(response);
						}
					});
				});
			});
			
			function goBack() {
				window.history.back();
			}
		</script>
	</body>
</html>