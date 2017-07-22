<?php require_once 'navbar.php'; ?>
    <!--this form is called by the "View customer" sub-menu item and is for searching a customer by name or city-->
		<div class="container" style="margin-right:auto;margin-left:auto;width:max-content;">
			<h2 style="text-align:center;">Search for customer</h2>
			<form class="form-inline" role="form" id="search_customers" method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<div class="form-group">
					<label class="control-label" for="search_type">Search by:</label>
					<select class="form-control" name="search_type" id="search_type">
						<option value="search_name" selected="selected">Customer name</option>
						<option value="search_cuid">Customer ID</option>
						<option value="search_city">City</option>
					</select>
					<input class="form-control" type="text" name="search_input" id="search_input" maxlength="50"/>
				</div>
				<div class="form-group">	
					<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search</button>
				</div>			
			</form>
		</div>
		<br>
		<!--div for displaying search results-->
		<div class="container" id="customer_results">
		<?php
		//this script displays list of matching customers by name or city
		if (isset($_GET["search_type"])) {	
			//define variables and set to empty
			$searchType = $searchInput = "";
			
			//if customer name or city was provided, check if length within limit and sanitize it
			include ("./functions/functions.php"); //test_input function sanitizes input
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				if (!empty($_GET["search_type"]) && strlen($_GET["search_type"]) == 11) {
					$searchType = test_input($_GET["search_type"]);
				}
				
				if (!empty($_GET["search_input"]) && strlen($_GET["search_input"]) <= 50) {
					$searchInput = test_input($_GET["search_input"]);
				}
				
				//database connection details kept in separate file
				include ("database.php");
				//construct select statement using PDO prepared statement
				//find all matches using LIKE (any names containing this word)
				//if searching by customer name and there is input
				if ($searchType == "search_name" && $searchInput != "") {
					$statement = $connection->prepare(
						"SELECT customer_id, customer_name, city, type 
						FROM customers 
						WHERE customer_name LIKE concat('%', :customername, '%')
						ORDER BY customer_name");
					$statement->bindParam(':customername', $searchInput);
				}
				//if searching by city and there is input
				elseif ($searchType == "search_city" && $searchInput != "") {
					$statement = $connection->prepare(
						"SELECT customer_id, customer_name, city, type 
						FROM customers 
						WHERE city LIKE concat('%', :city, '%')
						ORDER BY customer_name");
					$statement->bindParam(':city', $searchInput);
				}
				//if searching by customer ID and there is input
				elseif ($searchType == "search_cuid" && $searchInput != "") {
					$statement = $connection->prepare(
						"SELECT customer_id, customer_name, city, type 
						FROM customers 
						WHERE customer_id LIKE :customerid
						ORDER BY customer_name");
					$statement->bindParam(':customerid', $searchInput);
				}
				//if searching by city or customer and no search input, only return customers in the order_mv
				else {
					$statement = $connection->prepare(
						"SELECT customer_id, customer_name, city, type 
						FROM customers 
						ORDER BY customer_name");
				}
				$statement->execute();
    
				//get number of rows returned
				$numRows = $statement->rowCount();
					
				if ($numRows > 0) {
					echo "<table class='table table-striped'>";
					//columns for customer name, type, city, and view button
					echo "<tr><th>Customer name</th>
							<th>Type</th>
							<th>City</th>
							<th></th>";
					//columns for edit, view notes, and add note
					//investors cannot edit data and cannot view or add notes
					if ($response['role'] != "Investor"){
						echo "<th></th>
							<th></th>
							<th></th>";
					}
					//order history button column
					echo "<th></th>
						</tr>";
						
					//each row displays customer name, city, and five buttons: view, edit, view notes, add note, view order history
					//each button calls a function that passes the customer id as a name-value pair via GET
					while ($row = $statement->fetchObject()) {
						echo "<tr><td>" . $row->customer_name . "</td>";
						echo "<td>" . $row->type . "</td>";
						echo "<td>" . $row->city . "</td>";
						echo "<td align='right'><button class='btn btn-primary' onclick='loadView(" . $row->customer_id . ")'><span class='glyphicon glyphicon-zoom-in'></span> View</button></td>";
						//investors cannot edit data and cannot view or add notes
						if ($response['role'] != "Investor"){
							echo "<td align='right'><button class='btn btn-success' onclick='loadEdit(" . $row->customer_id . ")'><span class='glyphicon glyphicon-edit'></span> Edit</button></td>";
							echo "<td align='right'><button class='btn btn-primary' onclick='loadViewCrm(" . $row->customer_id . ")'><span class='glyphicon glyphicon-zoom-in'></span> View notes</button></td>";
							echo "<td align='right'><button class='btn btn-success' onclick='loadAddCrm(" . $row->customer_id . ")'><span class='glyphicon glyphicon-plus'></span> Add note</button></td>";
						}
						echo "<td align='right'><button class='btn btn-primary' onclick='loadViewOrders(" . $row->customer_id . ")'><span class='glyphicon glyphicon-zoom-in'></span> Order history</button></td>";
					}
					echo "</tr></table>";
				}
				else echo "<div style='margin-right:auto;margin-left:auto;width:max-content;'>No customers found</div>";
				
				//small javascript code to keep the selected search type selected and the input in field box
				echo "<script>
					$(document).ready(function() {
						$('#search_type').val('". $searchType ."');
						$('#search_input').val('". $searchInput ."');
					});
				</script>";
				
				//close connection
				$connection = null;
			}
		}
		?>
		</div>
		<script type="text/javascript">
			//javascript functions for view, edit, view notes, and add note buttons
			function loadView(id) {
				//load page and pass btoa base64 encoded parameter
				window.location = "view_customer_script.php?customer_id=" + window.btoa(id);
			}
			
			function loadEdit(id) {
				window.location = "edit_customer_form.php?customer_id=" + window.btoa(id);
			}
			
			function loadViewCrm(id) {
				window.location = "view_crm_list_script.php?customer_id=" + window.btoa(id);
			}
			
			function loadAddCrm(id) {
				window.location = "add_crm_form.php?customer_id=" + window.btoa(id);
			}
			
			function loadViewOrders(id) {
				window.location = "customer_detail.php?storeid=" + id;
			}
		</script>
	</body>
</html>