<?php
require_once 'navbar_investor.php';
?>
	<!--this report is called by the "Customer follow-up" sub-menu item and displays list of customers/notes with follow-up of "Yes", plus view button-->
	<div class="container">
        <h2 style="text-align:center;">Customers Requiring Follow-up</h2>
		<?php		
			//database connection details kept in separate file
			include ("database.php");
			//construct select statement using PDO prepared statement
			//get note data and customer name
			$statement = $connection->prepare(
				"SELECT customer_name, note_id, note_title, note_date, o.customer_id 
				FROM customers c, organization_notes o
				WHERE c.customer_id = o.customer_id
				AND followup = 1
				ORDER BY note_date DESC");
			$statement->execute();
			
			//get number of rows returned
			$numRows = $statement->rowCount();
	
			if ($numRows > 0) {
				echo "<table class='table table-striped'>";
				echo "<tr><th>Customer name</th>
					<th>Note title</th>
					<th>Note date</th>
					<th></th></tr>";
				
				//each row displays customer name, note details, and a view button
				//button calls a function that passes the note id and customer id as name-value pairs via GET
				while ($row = $statement->fetchObject()) {				
					echo "<tr><td>" . $row->customer_name . "</td>";
					echo "<td>" . $row->note_title . "</td>";
					echo "<td>" . $row->note_date . "</td>";
					echo "<td><button class='btn btn-primary' onclick='loadView(" . $row->note_id . ", " . $row->customer_id . ")'><span class='glyphicon glyphicon-zoom-in'></span> View</button></td></tr>";
				}
				echo "</table>";
			}
			else echo "<div style='margin-right:auto;margin-left:auto;width:max-content;'>No customers found</div>";
				
			//close connection
			$connection = null;
		?>
	</div>
	<script type="text/javascript">
		//ajax function for view button
		function loadView(n_id, c_id) {
			window.location = "view_crm_note_script.php?note_id=" + window.btoa(n_id) + "&customer_id=" + window.btoa(c_id);
		}
	</script>
	</body>
</html>