<?php
    require_once 'navbar_investor.php';
	//this script is called by view_crm_list_script.php or view_customer_script.php, and displays CRM note information
    //this script is connected to customer by customer id and note id, which was passed as name-value pairs via GET
    
    //define variables and set to empty
    $customerID = $noteID = $userID = "";
    
    //check if id only contains numbers and length is between 1 and 11
    include ("./functions/functions.php"); //test_input function sanitizes input
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (preg_match("/^[0-9]{1,11}$/", base64_decode($_GET["customer_id"]))) {
            $customerID = test_input(base64_decode($_GET["customer_id"]));
        } 
        if (preg_match("/^[0-9]{1,11}$/", base64_decode($_GET["note_id"]))) {
            $noteID = test_input(base64_decode($_GET["note_id"]));
        }
    
		//database connection details kept in separate file
		include ("database.php");
		//construct select statement using PDO prepared statement
		//results from select statement used to populate form
		$statement = $connection->prepare(
			"SELECT * 
			FROM organization_notes 
			WHERE note_id = :noteid and customer_id = :customerid");
		$statement->bindParam(':customerid', $customerID);
		$statement->bindParam(':noteid', $noteID);
		$result = $statement->execute();
		if ($result) $row = $statement->fetchObject();
		
		//get customer name
		$statementCust = $connection->prepare(
			"SELECT customer_name 
			FROM customers
			WHERE customer_id = :customerid");
		$statementCust->bindParam(':customerid', $customerID);        
		$resultCust = $statementCust->execute();
		if ($resultCust) $rowCust = $statementCust->fetchObject();
		
		//get user_name (who wrote the note)
		$statementUser = $connection->prepare(
			"SELECT user_name 
			FROM users 
			WHERE user_id = :userid");
		$userID = $row->user_id;
		$statementUser->bindParam(':userid', $userID);
		$resultUser = $statementUser->execute();
		if ($resultUser) $rowUser = $statementUser->fetchObject();
		 
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
		<div id="content">
			<h2 style="text-align:center;">View CRM note</h2>
			<form class="form-horizontal" role="form">
				<div class="form-group">
					<label class="control-label col-md-4">Customer name:</label>
					<div class="col-md-4">
						<input class="form-control" value="<?php echo htmlentities($rowCust->customer_name);?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="note_id">Note ID:</label>
					<div class="col-md-2">
						<input class="form-control" value="<?php echo $row->note_id;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="note_title">Note title:</label>
					<div class="col-md-4">
						<input class="form-control" value="<?php echo htmlentities($row->note_title);?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="note_author">Note author:</label>
					<div class="col-md-4">
						<input class="form-control" value="<?php echo htmlentities($rowUser->user_name);?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="note_date">Note date:</label>
					<div class="col-md-3">
						<input class="form-control" value="<?php echo $row->note_date;?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="note_body">Note body:</label>
					<div class="col-md-4">
						<textarea class="form-control" name="note_body" id="note_body" rows="6" cols="50" readonly style="resize:vertical;"><?php echo $row->note_body;?></textarea>
					</div>
				</div>
				<!--php code to display the followup status-->
				<?php
					if ($row->followup == 1) {
						echo "<div class='form-group'>
								<label class='control-label col-md-4' for='followup'>Follow-up:</label>
								<div class='col-md-2'>
									<input class='form-control' value='Yes' type='text' readonly>
								</div>
							</div>
							<div class='form-group'>
								<div class='col-md-4'></div>
								<div class='col-md-7'>	
								<!--display a 'Done' button to update the followup status to 'No', button calls a function that passes the customer id and note id as name-value pairs via GET-->
									<button class='btn btn-success' onclick='loadDone(" . $noteID . ", " . $customerID . ");return false;'><span class='glyphicon glyphicon-check'></span> Done follow-up</button>
									&nbsp &nbsp
									<button class='btn btn-primary' onclick='loadView(" . $row->customer_id . ");return false;'><span class='glyphicon glyphicon-zoom-in'></span> View Customer</button>
									&nbsp &nbsp
									<button class='btn btn-success' onclick='loadAddCrm(" . $row->customer_id . ");return false;'><span class='glyphicon glyphicon-plus'></span> Add note</button>
								</div>
							</div>";	
					}
					else {
						echo "<div class='form-group'>
								<label class='control-label col-md-4' for='followup'>Follow-up:</label>
								<div class='col-md-2'>
									<input class='form-control' value='No' type='text' readonly>
								</div>
							</div>
							<div class='form-group'>
								<div class='col-md-4'></div>
								<div class='col-md-4'>	
									<button class='btn btn-primary' onclick='loadView(" . $row->customer_id . ");return false;'><span class='glyphicon glyphicon-zoom-in'></span> View Customer</button>
									&nbsp &nbsp
									<button class='btn btn-success' onclick='loadAddCrm(" . $row->customer_id . ");return false;'><span class='glyphicon glyphicon-plus'></span> Add note</button>
								</div>
							</div>";
					}
				?>
			</form>
		</div>
	</div>
		<script type="text/javascript">
			//ajax function for done button
			function loadDone(n_id, c_id) {
				//set up variables needed for GET
				$.ajax({
					//method
					type: "GET",
					//php script to use
					url: "edit_crm_followup_script.php",
					//passed parameters as name-value pairs
					data: {"note_id" : window.btoa(n_id),
						"customer_id" : window.btoa(c_id)},
					//display response message in div id=content
					success: function(response) {
						$('#content').html(response);
					}
				});
				//prevents page from reloading
				return false;
			}
			
			function loadAddCrm(id) {
				window.location = "add_crm_form.php?customer_id=" + window.btoa(id);
			}
			
			function loadView(id) {
				window.location = "view_customer_script.php?customer_id=" + window.btoa(id);
			}
		</script>
	</body>
</html>