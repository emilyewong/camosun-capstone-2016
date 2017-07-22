<?php
    require_once 'navbar_investor.php';
	//this form is called by search_customers_form.php and is for adding a new CRM note
    //crm note is connected to customer by customer id, which was passed as a name-value pair via GET
	
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
		//get customer name to populate form
		$statement = $connection->prepare(
			"SELECT customer_name 
			FROM customers
			WHERE customer_id = :customerid");
		$statement->bindParam(':customerid', $customerID); 
		$result = $statement->execute();
		if ($result) $row = $statement->fetchObject();
		
		//close connection
		$connection = null;
	}
?>
	<link href="form.css" rel="stylesheet" type="text/css"/>
		<style type="text/css">
			#customer_name{background-color:rgba(0,0,0,0) !important; border:none !important;}
			#customer_name:focus{outline-style:none !important;
					box-shadow:none !important;
					border-color:transparent !important;}
		</style>
	<div class="container">
		<div id="content">
			<h2 style="text-align:center;">Add new CRM note</h2>
			<form class="form-horizontal" role="form" id="add_crm">
				<!--customer id is hidden and passed during POST-->
				<input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customerID;?>"/>
				<div class="form-group">
					<!--customer name is displayed and cannot be changed (not passed during POST)-->
					<label class="control-label col-md-4" for="customer_name">Customer name:</label>
					<div class="col-md-4">
						<input id="customer_name" class="form-control" value="<?php echo htmlentities($row->customer_name);?>" type="text" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="note_title">Note title:</label>
					<div class="col-md-4">
						<input class="form-control" type="text" name="note_title" id="note_title" maxlength="64" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="note_date">Note date:</label>
					<div class="col-md-3">
						<input class="form-control" type="datetime-local" name="note_date" id="note_date" value="<?php echo date("Y-m-d\TH:i:s");?>" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="note_body">Note body:</label>
					<div class="col-md-4">
						<textarea class="form-control" name="note_body" id="note_body" maxlength="1000" rows="6" cols="50" style="resize:vertical;" required></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="followup">Follow-up:</label>
					<div class="col-md-2">	
						<select class="form-control" name="followup" id="followup">
							<option value="0" selected="selected">No</option>
							<option value="1">Yes</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add note</button>
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
				//execute when submit button in form id=add_crm is pressed
				$('#add_crm').submit(function(e) {
					//prevents page from reloading
					e.preventDefault();
					//set up variables needed for POST 
					$.ajax({ 
						//method
						type: "POST",
						//php script to use
						url: "add_crm_script.php",
						//grab all form input and put into name-value pairs
						data: {
							"customer_id" : window.btoa($('#customer_id').val()),
							"note_title" : window.btoa($('#note_title').val()),
							"note_date" : window.btoa($('#note_date').val()),
							"note_body" : window.btoa($('#note_body').val()),
							"followup" : window.btoa($('#followup').val())
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