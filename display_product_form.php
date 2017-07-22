<?php
require_once './core/init.php';

//to prevent access to this page via url
	if(!isset($_GET['token'])) header('Location: index.php');
?>
    <!--this form is called by add_product_script.php and is displayed after an unsuccessful insert of new product-->
    <link href="form.css" rel="stylesheet" type="text/css"/>
	<div class="container">
		<div id="content">
			<h2 style="text-align:center;">Add new product</h2>
			<!--onsubmit event executes ajax function addProduct() and return false prevents page reload-->
			<form class="form-horizontal" role="form" id="add_prod" onsubmit="addProduct();return false;">
				<div class="form-group">
					<label class="control-label col-md-4" for="sku">SKU:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="sku" id="sku" maxlength="11" pattern="[0-9]{1,11}" title="Must be numbers only" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="brand_id">Brand ID:</label>
					<div class="col-md-4">	
						<select class="form-control" name="brand_id" id="brand_id" required>
							<option value="" selected="selected"></option>
							<!--php code for creating select list of all brands-->
							<?php
								//database connection details kept in separate file
								include ("database.php");
								//construct select statement using PDO prepared statement
								//get all brand names/ids
								$statement = $connection->prepare(
									"SELECT name, brand_id 
									FROM brands");
								$result = $statement->execute();
								if ($result) {
									while ($row = $statement->fetchObject()) {
										echo "<option value='" . $row->brand_id . "'>" . $row->brand_id . " - " . $row->name . "</option>";
									}
								}
								//close connection
								$connection = null;
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="size">Size:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="size" id="size" maxlength="7" pattern="[0-9]{1,3}[\.][0-9]{3}" title="Must have 3 decimal places" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="is_packaged">Is packaged:</label>
					<div class="col-md-2">	
						<select class="form-control" name="is_packaged" id="is_packaged" required>
							<option value="" selected="selected"></option>
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="container_deposit">Container deposit:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="container_deposit" id="container_deposit" maxlength="16" pattern="[0-9]{1,13}[\.][0-9]{2}" title="Must have 2 decimal places" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="duty_paid">Duty paid:</label>
					<div class="col-md-2">					
						<input class="form-control" type="text" name="duty_paid" id="duty_paid" maxlength="16" pattern="[0-9]{1,13}[\.][0-9]{2}" title="Must have 2 decimal places" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="upc">UPC:</label>
					<div class="col-md-3">
						<input class="form-control" type="text" name="upc" id="upc" maxlength="32"/>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add product</button>
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
			function addProduct() {
				//set up variables needed for POST
				$.ajax({
					//method
					type: "POST",
					//php script to use
					url: "add_product_script.php",
					//grab all form input and put into name-value pairs
					data: {	"sku" : window.btoa($('#sku').val()),
							"brand_id" : window.btoa($('#brand_id').val()),
							"size" : window.btoa($('#size').val()),
							"is_packaged" : window.btoa($('#is_packaged').val()),
							"container_deposit" : window.btoa($('#container_deposit').val()),
							"duty_paid" : window.btoa($('#duty_paid').val()),
							"upc" : window.btoa($('#upc').val())
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