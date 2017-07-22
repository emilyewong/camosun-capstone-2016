<?php
require_once 'navbar_investor.php';
?>
    <!--this form is called by the "Add customer" sub-menu item and is for adding a new customer
        the form performs input checking by specifying pattern, length, required, etc.-->
    <link href="form.css" rel="stylesheet" type="text/css"/>
	<div class="container">
		<div id="content">
			<h2 style="text-align:center;">Add new customer</h2>
			<!--onsubmit event executes ajax function addCustomer() and return false prevents page reload-->
			<form class="form-horizontal" role="form" id="add_cust" onsubmit="addCustomer();return false;">
				<div class="form-group">
					<label class="control-label col-md-4" for="customer_id">Customer ID:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="customer_id" id="customer_id" maxlength="11" pattern="[0-9]{1,11}" title="Must be numbers only" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="customer_name">Customer Name:</label>
					<div class="col-md-4">
						<input class="form-control" type="text" name="customer_name" id="customer_name" maxlength="50" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="type">Type:</label>
					<div class="col-md-2">
						<select class="form-control" name="type" id="type">
							<!--php code to generate select list from array of types-->
							<?php
								$types = array("", "COU", "DFS", "GLS", "GRC", "LIC", "LRS", "MOS", "RAS", "SOL", "TWS", "VQA", "WAS", "WIN");
								$arraylength = count($types);
								for ($i = 0; $i < $arraylength; $i++) {
									if ($types[$i] == "") echo "<option value='" . $types[$i] . "' selected='selected'>" . $types[$i] . "</option>";
									else echo "<option value='" . $types[$i] . "'>" . $types[$i] . "</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="address">Address:</label>
					<div class="col-md-4">
						<input class="form-control" type="text" name="address" id="address" maxlength="100"/>
					</div>
				</div>
				
				<div class="form-group">
					<label class="control-label col-md-4" for="city">City:</label>
					<div class="col-md-3">
						<input class="form-control" type="text" name="city" id="city" maxlength="100" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="postal_code">Postal Code:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="postal_code" id="postal_code" maxlength="7" pattern="[A-Z]\d[A-Z]\s?\d[A-Z]\d" title="Must be valid postal code format"/>
					</div>
				</div>
				
				<div class="form-group">
					<label class="control-label col-md-4" for="region">Region:</label>
					<div class="col-md-3">
						<select class="form-control" name="region" id="region">
							<option value="" selected="selected"></option>
							<!--php code for creating select list of all regions-->
							<?php
								//database connection details kept in separate file
								include ("database.php");
								//construct select statement using PDO prepared statement
								//get all regions
								$statement = $connection->prepare(
									"SELECT region_name
									FROM regions");
								$result = $statement->execute();
								if ($result) {
									while ($row = $statement->fetchObject()) {
										echo "<option value='" . $row->region_name . "'>" . $row->region_name . "</option>";
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
						<input class="form-control" type="text" name="phone" id="phone" maxlength="20"/>
					</div>
				</div>
				
				<div class="form-group">
					<label class="control-label col-md-4" for="email">Email:</label>
					<div class="col-md-4">
						<input class="form-control" type="text" name="email" id="email" maxlength="100" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" title="Must be valid email format"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="website">Website:</label>
					<div class="col-md-4">
						<input class="form-control" type="text" name="website" id="website" maxlength="100"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="primary_contact">Primary Contact:</label>
					<div class="col-md-4">
						<input class="form-control" type="text" name="primary_contact" id="primary_contact" maxlength="100"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="pref_contact_method">Preferred Contact Method:</label>
					<div class="col-md-4">
						<input class="form-control" type="text" name="pref_contact_method" id="pref_contact_method" maxlength="50"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="active">Active:</label>
					<div class="col-md-2">
						<select class="form-control" name="active" id="active">
							<option value="0">No</option>
							<option value="1" selected="selected">Yes</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add customer</button>
						&nbsp &nbsp
						<button type="button" class="btn btn-warning" onclick="goBack();return false;"><span class="glyphicon glyphicon-ban-circle"></span> Cancel</button>
					</div>
				</div>
			</form>
		</div>
	</div>
		<script type="text/javascript">
			//ajax function that performs POST action on form
			//function is declared in the <form> tag as onsubmit
			function addCustomer() {
				//set up variables needed for POST
				$.ajax({
					//method
					type: "POST",
					//php script to use
					url: "add_customer_script.php",
					//grab all form input and put into name-value pairs
					data: {	"customer_id" : window.btoa($('#customer_id').val()),
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
				//prevents page from reloading
				return false;
			}
			
			function goBack() {
				window.location = "index.php";
			}
		</script>
	</body>
</html>