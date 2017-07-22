<?php
require_once 'navbar.php';
?>

	<!--this script is called by the "View brands" sub-menu item and displays list of all brands, with view and edit buttons-->
    <div class="container">
		<h2 style="text-align:center;">View brands</h2>
		<?php
			//database connection details kept in separate file
			include ("database.php");
			//construct select statement using PDO prepared statement
			$statement = $connection->prepare(
				"SELECT name, brand_id, stake_no 
				FROM brands
				ORDER BY name");
			
			$result = $statement->execute();
		
			if ($result) {
				echo "<table class='table table-striped'>";
				//columns for brand id, brand name, stake no., and view button
				echo "<tr><th>Brand ID</th>
					<th>Stake number</th>
					<th>Brand name</th>
					<th></th>";
				//column for edit button
				//investors cannot edit data
				if ($response['role'] != "Investor") {
					echo "<th></th>";
				}
				echo "</tr>";
				
				//each row displays brand name and two buttons: view and edit
				//each button calls a function that passes the brand id as a name-value pair via GET
				while ($row = $statement->fetchObject()) {
					echo "<tr><td>" . $row->brand_id . "</td>";
					echo "<td>" . $row->stake_no . "</td>";
					echo "<td>" . $row->name . "</td>";
					echo "<td><button class='btn btn-primary' onclick='loadView(" . $row->brand_id . ")'><span class='glyphicon glyphicon-zoom-in'></span> View</button></td>";
					//investors cannot edit data
					if ($response['role'] != "Investor") {
						echo "<td><button class='btn btn-success' onclick='loadEdit(" . $row->brand_id . ")'><span class='glyphicon glyphicon-edit'></span> Edit</button></td>";
					}
				}
				echo "</tr></table>";
			}
			else echo "<div style='margin-right:auto;margin-left:auto;width:max-content;'>No brands available</div>";
				
			//close connection
			$connection = null;
		?>
	</div>
	<script type="text/javascript">
		//javascript functions for view and edit buttons
		function loadView(id) {
			//load page and pass btoa base64 encoded parameter
			window.location = "view_brand_script.php?brand_id=" + window.btoa(id);
		}
		
		function loadEdit(id) {
			window.location = "edit_brand_form.php?brand_id=" + window.btoa(id);
		}
	</script>
	</body>
</html>