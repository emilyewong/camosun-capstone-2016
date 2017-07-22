<?php
    require_once 'navbar.php';
	//this script is called by search_customers_form.php or customer_detail.php, and displays customer's information
    //this script is connected to customer by customer id, which was passed as a name-value pair via GET
	
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

		<style type="text/css">
			input,textarea{background-color:rgba(0,0,0,0) !important; border:none !important;}
			input:focus,textarea:focus{outline-style:none !important;
					box-shadow:none !important;
					border-color:transparent !important;}
		</style>
    
		<div class="container">
			<h2 style="text-align:center;">View customer information</h2>
			<form class="form-horizontal" role="form">
				<div class="form-group">
					<label class="control-label col-md-4" for="customer_id">Customer ID:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->customer_id;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="customer_name">Customer name:</label>
					<div class="col-md-4">
						<input class="form-control" value="<?php echo htmlentities($row->customer_name);?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="type">Type:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->type;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="address">Address:</label>
					<div class="col-md-4">
						<input class="form-control" value="<?php echo $row->address;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="city">City:</label>
					<div class="col-md-3">
						<input class="form-control" value="<?php echo $row->city;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="postal_code">Postal code:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->postal_code;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="region">Region:</label>
					<div class="col-md-3">
						<input class="form-control" value="<?php echo $row->region;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="phone">Phone:</label>
					<div class="col-md-3">
						<input class="form-control" value="<?php echo $row->phone;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="email">Email:</label>
					<div class="col-md-4">
						<input class="form-control" value="<?php echo $row->email;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="website">Website:</label>
					<div class="col-md-4">
						<input class="form-control" value="<?php echo $row->website;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="primary_contact">Primary contact:</label>
					<div class="col-md-4">
						<input class="form-control" value="<?php echo $row->primary_contact;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="pref_contact_method">Preferred contact method:</label>
					<div class="col-md-4">
						<input class="form-control" value="<?php echo $row->pref_contact_method;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="active">Active:</label>
					<div class="col-md-2">
						<!--php code to display the active status-->
						<?php if ($row->active == 1) echo "<input class='form-control' value='Yes' type='text' readonly>";
							else echo "<input class='form-control' value='No' type='text' readonly>";
						?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<!--php code to display the edit and add crm button-->
						<?php
							//each button calls a function that passes the customer id as a name-value pair via GET
							//investors cannot edit data or add crm notes
							if ($response['role'] != "Investor") {
								echo "<button class='btn btn-success' onclick='loadEdit($row->customer_id);return false;'><span class='glyphicon glyphicon-edit'></span> Edit</button>";
								echo "&nbsp &nbsp &nbsp";
								echo "<button class='btn btn-success' onclick='loadAddCrm($row->customer_id);return false;'><span class='glyphicon glyphicon-plus'></span> Add note</button>";
							}
						?>
					</div>
				</div>
			</form>
		</div>
			
		<div style="margin-right:auto;margin-left:auto; width:max-content;">
            <!--php code to display recent crm notes-->
			<?php
				//investors cannot view crm notes
				if ($response['role'] != "Investor") {
					echo "<br>";
					//show recent crm notes 
					echo "<label>Recent CRM notes</label><br>";
					//get five most recent crm notes using prepared statement
					$statementCrm = $connection->prepare(
						"SELECT note_id, note_title, note_date, followup 
						FROM organization_notes
						WHERE customer_id = :customerid
						ORDER BY note_date DESC
						LIMIT 5");
					$statementCrm->bindParam(':customerid', $customerID);
					$statementCrm->execute();
					
					//get number of rows returned
					$numRows = $statementCrm->rowCount();
		
					if ($numRows > 0) {
						echo "<table class='table table-striped'>";
						//columns for note date, title, followup, and view button
						echo "<tr><th>Note date</th>
							<th>Note title</th>
							<th>Follow-up</th>
							<th></th></tr>";
						
						//each row displays note info and a view button
						//button calls a function that passes the customer id and note id as name-value pairs via GET
						while ($rowCrm = $statementCrm->fetchObject()) {
							echo "<tr><td>" . $rowCrm->note_date . "</td>";
							echo "<td>" . $rowCrm->note_title . "</td>";
							if ($rowCrm->followup == 1) echo "<td>Yes</td>";
							else echo "<td>No</td>";
							echo "<td><button class='btn btn-primary' onclick='loadView(" . $rowCrm->note_id . ", " . $customerID . ");return false;'><span class='glyphicon glyphicon-zoom-in'></span> View</button></td></tr>";
						}
						echo "</table>";
					}
					else echo "No CRM notes found";
					
					//close connection
					$connection = null;
				}
			?>
        </div>
		<script type="text/javascript">
			//ajax functions for edit and view buttons
			function loadEdit(id) {
				//load page and pass btoa base64 encoded parameter
				window.location = "edit_customer_form.php?customer_id=" + window.btoa(id);
			}
			
			function loadView(n_id, c_id) {
				window.location = "view_crm_note_script.php?note_id=" + window.btoa(n_id) + "&customer_id=" + window.btoa(c_id);
			}
			
			function loadAddCrm(id) {
				window.location = "add_crm_form.php?customer_id=" + window.btoa(id);
			}
		</script>
	</body>
</html>