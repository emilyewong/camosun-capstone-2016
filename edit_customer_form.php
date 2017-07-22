<?php
    require_once 'navbar_investor.php';
	//this form is called by search_customers_form.php or view_customer_script.php, and displays customer information that can be edited
    //this form is connected to customer by customer id, which was passed as a name-value pair via GET
    
    //define variable and set to empty
    $customerID = "";
    
    //check if id only contains numbers and length is between 1 and 11
    include ("./functions/functions.php"); //test_input function sanitizes input
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (preg_match("/^[0-9]{1,11}$/", base64_decode($_GET["customer_id"]))) {
            $customerID = test_input(base64_decode($_GET["customer_id"]));
        }  
    
		//database connection details kept in separate file
		include ("database.php");
		//construct select statement using PDO prepared statement
		//results from select statement used to populate form
		$statement = $connection->prepare(
			"SELECT * 
			FROM customers 
			WHERE customer_id = :customerid");
		$statement->bindParam(':customerid', $customerID);
		$result = $statement->execute();
		if ($result) $row = $statement->fetchObject();
	}
?>
	
	<link href="form.css" rel="stylesheet" type="text/css"/>
		<style type="text/css">
			#customer_id{background-color:rgba(0,0,0,0) !important; border:none !important;}
			#customer_id:focus{outline-style:none !important;
					box-shadow:none !important;
					border-color:transparent !important;}
		</style>
	
	<div class="container">
		<div id="content">
			<h2 style="text-align:center;">Edit customer information</h2>
			<form class="form-horizontal" role="form" id="edit_cust">
				<div class="form-group">
					<label class="control-label col-md-4" for="customer_id">Customer ID:</label>
					<div class="col-md-2">
						<!--customer id is displayed but cannot be changed-->
						<input class="form-control" type="text" name="customer_id" id="customer_id" value="<?php echo $row->customer_id;?>" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="customer_name">Customer name:</label>
					<div class="col-md-4">
						<!--need to use htmlentities() because customer names with quotes in it break the html-->
						<input class="form-control" type="text" name="customer_name" id="customer_name" maxlength="50" value="<?php echo htmlentities($row->customer_name);?>" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="type">Type:</label>
					<div class="col-md-2">
						<select class="form-control" name="type" id="type">
							<!--php code to select the type from array of types-->
							<?php
								$types = array("", "COU", "DFS", "GLS", "GRC", "LIC", "LRS", "MOS", "RAS", "SOL", "TWS", "VQA", "WAS", "WIN");
								$arraylength = count($types);
								for ($i = 0; $i < $arraylength; $i++) {
									if ($types[$i] == $row->type) echo "<option value='" . $types[$i] . "' selected='selected'>" . $types[$i] . "</option>";
									else echo "<option value='" . $types[$i] . "'>" . $types[$i] . "</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="address">Address:</label>
					<div class="col-md-4">
						<input class="form-control" type="text" name="address" id="address" maxlength="100" value="<?php echo $row->address;?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="city">City:</label>
					<div class="col-md-3">	
						<input class="form-control" type="text" name="city" id="city" maxlength="100" value="<?php echo $row->city;?>" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="postal_code">Postal code:</label>
					<div class="col-md-2">	
						<input class="form-control" type="text" name="postal_code" id="postal_code" maxlength="7" pattern="[A-Z]\d[A-Z]\s?\d[A-Z]\d" title="Must be valid postal code format" value="<?php echo $row->postal_code;?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="region">Region:</label>
					<div class="col-md-3">	
						<select class="form-control" name="region" id="region">
							<option value="" selected="selected"></option>
							<!--php code for creating select list of all regions-->
							<?php
								//construct select statement using PDO prepared statement
								//get all regions
								$statementRegion = $connection->prepare(
									"SELECT region_name 
									FROM regions");
								$resultRegion = $statementRegion->execute();
								if ($resultRegion) {
									while ($rowRegion = $statementRegion->fetchObject()) {
										if ($rowRegion->region_name == $row->region) {
											echo "<option selected='selected' value='" . $rowRegion->region_name . "'>" . $rowRegion->region_name . "</option>";
										}
										else {
											echo "<option value='" . $rowRegion->region_name . "'>" . $rowRegion->region_name . "</option>";
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
					<label class="control-label col-md-4" for="phone">Phone:</label>
					<div class="col-md-3">	
						<input class="form-control" type="text" name="phone" id="phone" maxlength="20" value="<?php echo $row->phone;?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="email">Email:</label>
					<div class="col-md-4">	
						<input class="form-control" type="text" name="email" id="email" maxlength="100" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" title="Must be valid email format" value="<?php echo $row->email;?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="website">Website:</label>
					<div class="col-md-4">	
						<input class="form-control" type="text" name="website" id="website" maxlength="100" value="<?php echo $row->website;?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="primary_contact">Primary contact:</label>
					<div class="col-md-4">	
						<input class="form-control" type="text" name="primary_contact" id="primary_contact" maxlength="100" value="<?php echo $row->primary_contact;?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="pref_contact_method">Preferred contact method:</label>
					<div class="col-md-4">	
						<input class="form-control" type="text" name="pref_contact_method" id="pref_contact_method" maxlength="50" value="<?php echo $row->pref_contact_method;?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="active">Active:</label>
					<div class="col-md-2">
						<select class="form-control" name="active" id="active">
						<!--php code to select the active status-->
						<?php 
							if ($row->active == 1) {
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
				//execute when submit button in form id=edit_cust is pressed
				$('#edit_cust').submit(function(e) {
					//prevents page from reloading
					e.preventDefault();
					//set up variables needed for POST 
					$.ajax({ 
						//method
						type: "POST",
						//php script to use
						url: "edit_customer_script.php",
						//grab all form input and put into name-value pairs
						data: {"customer_id" : window.btoa($('#customer_id').val()),
							"customer_name" : window.btoa($('#customer_name').val()),
							"city" : window.btoa($('#city').val()),
							"type" : window.btoa($('#type').val()),
							"address" : window.btoa($('#address').val()),
							"postal_code" : window.btoa($('#postal_code').val()),
							"region" : window.btoa($('#region').val()),
							"phone" : window.btoa($('#phone').val()),
							"email" : window.btoa($('#email').val()),
							"website" : window.btoa($('#website').val()),
							"active" : window.btoa($('#active').val()),
							"primary_contact" : window.btoa($('#primary_contact').val()),
							"pref_contact_method" : window.btoa($('#pref_contact_method').val())
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