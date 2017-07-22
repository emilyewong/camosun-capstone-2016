<?php
    require_once 'navbar_investor.php';
	//this form is called by view_brands_list_script.php or view_brand_script.php, and displays brand information that can be edited
    //this form is connected to brand by brand id, which was passed as a name-value pair via GET
    
    //define variable and set to empty
    $brandID = "";
    
	include ("./functions/functions.php"); //test_input function sanitizes input
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
		//check if id only contains numbers and length is between 1 and 11
		if (preg_match("/^[0-9]{1,11}$/", base64_decode($_GET["brand_id"]))) {
			$brandID = test_input(base64_decode($_GET["brand_id"]));
		}
    
		//database connection details kept in separate file
		include ("database.php");
		//construct select statement using PDO prepared statement
		//results from select statement used to populate form
		$statement = $connection->prepare(
			"SELECT * 
			FROM brands 
			WHERE brand_id = :brandid");
		$statement->bindParam(':brandid', $brandID);
		$result = $statement->execute();
		if ($result) $row = $statement->fetchObject();
		
		//close connection
		$connection = null;
	}
?>
	<link href="form.css" rel="stylesheet" type="text/css"/>
		<style type="text/css">
			#brand_id{background-color:rgba(0,0,0,0) !important; border:none !important;}
			#brand_id:focus{outline-style:none !important;
					box-shadow:none !important;
					border-color:transparent !important;}
		</style>
	<div class="container">
        <div id="content">
			<h2 style="text-align:center;">Edit brand information</h2>
			<form class="form-horizontal" role="form" id="edit_brand">
				<div class="form-group">
					<label class="control-label col-md-4" for="brand_id">Brand ID:</label>
					<div class="col-md-4">
						<!--brand id is displayed but cannot be changed-->
						<input class="form-control" type="text" name="brand_id" id="brand_id" value="<?php echo $row->brand_id;?>" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="name">Brand name:</label>
					<div class="col-md-4">
						<!--need to use htmlentities() because names with quotes in it break the html-->
						<input class="form-control" type="text" name="name" id="name" maxlength="100" value="<?php echo htmlentities($row->name);?>" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="stake_no">Stake number:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="stake_no" id="stake_no" maxlength="11" pattern="[0-9]{0,11}" value="<?php echo $row->stake_no;?>" title="Must be numbers only"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="ibu">IBU:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="ibu" id="ibu" maxlength="11" pattern="[0-9]{0,11}" value="<?php echo $row->ibu;?>" title="Must be numbers only"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="color">Color:</label>
					<div class="col-md-1">
						<!--php code to select the color status-->
						<select class="form-control" name="color" id="color">
							<?php
								$color = array("", "1", "2", "3", "4", "5");
								$colorArrayLength = count($color);
								for ($i = 0; $i < $colorArrayLength; $i++) {
									if ($color[$i] == $row->color) echo "<option value='" . $color[$i] . "' selected='selected'>" . $color[$i] . "</option>";
									else echo "<option value='" . $color[$i] . "'>" . $color[$i] . "</option>";
								}
							?>
						</select> 
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="bold">Bold:</label>
					<div class="col-md-1">
						<!--php code to select the bold status-->
						<select class="form-control" name="bold" id="bold">
							<?php
								$bold = array("", "1", "2", "3", "4", "5");
								$boldArrayLength = count($bold);
								for ($i = 0; $i < $boldArrayLength; $i++) {
									if ($bold[$i] == $row->bold) echo "<option value='" . $bold[$i] . "' selected='selected'>" . $bold[$i] . "</option>";
									else echo "<option value='" . $bold[$i] . "'>" . $bold[$i] . "</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="style">Style:</label>
					<div class="col-md-3">
						<input class="form-control" type="text" name="style" id="style" maxlength="30" value="<?php echo $row->style;?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="alc_per_vol">Alcohol per volume:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="alc_per_vol" id="alc_per_vol" maxlength="7" pattern="[0-9]{1,3}[\.][0-9]{3}" value="<?php echo $row->alc_per_vol;?>" title="Must have 3 decimal places"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="cost_per_liter">Cost per liter:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="cost_per_liter" id="cost_per_liter" maxlength="6" pattern="[0-9]{1,3}[\.][0-9]{2}" value="<?php echo $row->cost_per_liter;?>" title="Must have 2 decimal places"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="allergens">Allergens:</label>
					<div class="col-md-4">
						<input class="form-control" type="text" name="allergens" id="allergens" maxlength="50" value="<?php echo $row->allergens;?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="brewmaster_notes">Brewmaster notes:</label>
					<div class="col-md-4">
						<textarea class="form-control" name="brewmaster_notes" id="brewmaster_notes" maxlength="1000" rows="6" cols="50" style="resize:vertical;"><?php echo $row->brewmaster_notes;?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="description">Description:</label>
					<div class="col-md-4">
						<textarea class="form-control" name="description" id="description" maxlength="1000" rows="6" cols="50" style="resize:vertical;"><?php echo $row->description;?></textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Save changes</button>
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
				//execute when submit button in form id=edit_brand is pressed
				$('#edit_brand').submit(function(e) {
					//prevents page from reloading
					e.preventDefault();
					//set up variables needed for POST 
					$.ajax({ 
						//method
						type: "POST",
						//php script to use
						url: "edit_brand_script.php",
						//grab all form input and put into name-value pairs
						data: {
							"brand_id" : window.btoa($('#brand_id').val()),
							"name" : window.btoa($('#name').val()),
							"stake_no" : window.btoa($('#stake_no').val()),
							"ibu" : window.btoa($('#ibu').val()),
							"color" : window.btoa($('#color').val()),
							"bold" : window.btoa($('#bold').val()),
							"style" : window.btoa($('#style').val()),
							"alc_per_vol" : window.btoa($('#alc_per_vol').val()),
							"cost_per_liter" : window.btoa($('#cost_per_liter').val()),
							"allergens" : window.btoa($('#allergens').val()),
							"brewmaster_notes" : window.btoa($('#brewmaster_notes').val()),
							"description" : window.btoa($('#description').val())
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