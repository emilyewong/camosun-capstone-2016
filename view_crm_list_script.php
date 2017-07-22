<?php require_once 'navbar_investor.php';?>
	<div class="container">
	<h2 style="text-align:center;">View CRM notes</h2>
		<?php
			//this script is called by search_customers_form.php or customer_detail.php, and displays list of all CRM notes for a customer
			//CRM notes list is connected to customer by customer id, which was passed as a name-value pair via GET
			
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
				//get customer name
				$statementName = $connection->prepare(
					"SELECT customer_name 
					FROM customers
					WHERE customer_id = :customerid");
				$statementName->bindParam(':customerid', $customerID); 
				$resultName = $statementName->execute();
				if ($resultName) $rowName = $statementName->fetchObject();
				
				//get note information
				$statement = $connection->prepare(
					"SELECT note_id, note_title, note_date, followup 
					FROM organization_notes 
					WHERE customer_id = :customerid
					ORDER BY note_date DESC");
				$statement->bindParam(':customerid', $customerID);
				
				$statement->execute();
				
				//button for adding a new note
				echo "<div><span style='float: left; font-size: 25px;'>". $rowName->customer_name ."</span>
				<span style='float: right;'><button class='btn btn-success' onclick='loadAddCrm(" . $customerID . ")'><span class='glyphicon glyphicon-plus'></span> Add note</button></span>
				</div>";
				
				//get number of rows returned
				$numRows = $statement->rowCount();
				
				if ($numRows > 0) {
					echo "<table class='table table-striped'>";
					//columns for note date, title, followup and view button
					echo "<tr><th>Note date</th>
						<th>Note title</th>
						<th>Follow-up</th>
						<th></th></tr>";
					
					//each row displays note info and a view button
					//button calls a function that passes the customer id and note id as name-value pairs via GET
					while ($row = $statement->fetchObject()) {
						echo "<tr><td>" . $row->note_date . "</td>";
						echo "<td>" . $row->note_title . "</td>";
						if ($row->followup == 1) echo "<td>Yes</td>";
						else echo "<td>No</td>";
						echo "<td><button class='btn btn-primary' onclick='loadView(" . $row->note_id . ", " . $customerID . ")'><span class='glyphicon glyphicon-zoom-in'></span> View</button></td></tr>";
					}
					echo "</table>";
				}
				else echo "<div style='margin-right:auto;margin-left:auto;width:max-content;'>No CRM notes found</div>";
				
				//close connection
				$connection = null;
			}
		?>
	</div>
	<script type="text/javascript">
		//javascript function for view button
		function loadView(n_id, c_id) {
			//load page and pass btoa base64 encoded parameter
			window.location = "view_crm_note_script.php?note_id=" + window.btoa(n_id) + "&customer_id=" + window.btoa(c_id);
		}
		
		function loadAddCrm(id) {
			window.location = "add_crm_form.php?customer_id=" + window.btoa(id);
		}
	</script>
	</body>
</html>