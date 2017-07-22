<?php require_once './navbar.php'; ?>
    <!--this form is called by the "View product" sub-menu item and is for searching a product by brand name-->
		<div class="container" style="margin-right:auto;margin-left:auto;width:max-content;">
			<h2 style="text-align:center;">Search for product</h2>
			<form class="form-inline" role="form" id="search_products" method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<div class="form-group">
					<label class="control-label" for="brand_id">Brand name:</label>
					<select class="form-control" name="brand_id" id="brand_id" required>
						<option value="" selected="selected"></option>
						<!--php code for creating select list of all brands-->
						<?php
							//database connection details kept in separate file
							include ("database.php");
							//construct select statement using PDO prepared statement
							$statementBrand = $connection->prepare(
								"SELECT name, brand_id 
								FROM brands
								ORDER BY name");
							$resultBrand = $statementBrand->execute();
						   
							if ($resultBrand) {
								while ($rowBrand = $statementBrand->fetchObject()) {
									echo "<option value='" . $rowBrand->brand_id . "'>" . $rowBrand->name . "</option>";
								}
							}
						?>
					</select>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search</button>
				</div>
			</form>
		</div>
		<br>
		<!--div for displaying search results-->
		<div class="container" id="product_results">
		<?php
			//this script displays list of products by brand name
		if (isset($_GET["brand_id"])) {
			//define variable and set to empty
			$brandID = "";
			
			include ("./functions/functions.php"); //test_input function sanitizes input
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				//check if id only contains numbers and length is between 1 and 11
				if (preg_match("/^[0-9]{1,11}$/", $_GET["brand_id"])) {
					$brandID = test_input($_GET["brand_id"]);
				} 
						
			//construct select statement using PDO prepared statement
			//get product info
			$statement = $connection->prepare(
				"SELECT name, sku, size 
				FROM products p, brands b
				WHERE p.brand_id = b.brand_id
				AND p.brand_id = :brandid
				ORDER BY size");
			$statement->bindParam(':brandid', $brandID);
			
			$statement->execute();
			
			//get number of rows returned
			$numRows = $statement->rowCount();
			
			if ($numRows > 0) {
				echo "<table class='table table-striped'>";
				//columns for Name, SKU, Size, and View button
				echo "<tr><th>Brand name</th>
					<th>SKU</th>
					<th>Size</th>
					<th></th>";
				//column for edit button
				//investors cannot edit data 
				if ($response['role'] != "Investor"){
					echo "<th></th>";
				}
				echo "</tr>";
				
				//each row displays sku, size, and two buttons: view, edit
				//each button calls a function that passes the sku as a name-value pair via GET
				while ($row = $statement->fetchObject()) {
					echo "<tr><td>" . $row->name . "</td>";
					echo "<td>" . $row->sku . "</td>";
					echo "<td>" . $row->size . "</td>";
					echo "<td><button class='btn btn-success' onclick='loadView(" . $row->sku . ")'><span class='glyphicon glyphicon-zoom-in'></span> View</button></td>";
					//investors cannot edit data
					if ($response['role'] != "Investor") {
						echo "<td><button class='btn btn-warning' onclick='loadEdit(" . $row->sku . ")'><span class='glyphicon glyphicon-edit'></span> Edit</button></td>";
					}
				}
				echo "</tr></table>";
			}
			else echo "<div style='margin-right:auto;margin-left:auto;width:max-content;'>No products found</div>";
			
			//small javascript code to keep the selected brand id selected
			echo "<script>
					$(document).ready(function() {
						$('#brand_id').val('". $brandID ."');
					});
				</script>";
			
			//close connection
			$connection = null;
			}
		}
		?>
		</div>
		<script type="text/javascript">
			//javascript functions for view and edit buttons
			function loadView(sku) {
				//load page and pass btoa base64 encoded parameter
				window.location = "view_product_script.php?sku=" + window.btoa(sku);
			}
			
			function loadEdit(sku) {
				window.location = "edit_product_form.php?sku=" + window.btoa(sku);
			}
		</script>
	</body>
</html>