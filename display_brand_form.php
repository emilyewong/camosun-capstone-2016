<?php
require_once './core/init.php';

//to prevent access to this page via url
	if(!isset($_GET['token'])) header('Location: index.php');
?>

    <!--this form is called by add_brand_script.php and is displayed after an unsuccessful insert of a new brand-->
    <link href="form.css" rel="stylesheet" type="text/css"/>
	<div class="container">
		<div id="content">
			<h2 style="text-align:center;">Add new brand</h2>
			<!--onsubmit event executes ajax function addBrand() and return false prevents page reload-->
			<form class="form-horizontal" role="form" id="add_brand" onsubmit="addBrand();return false;">
				<div class="form-group">
					<label class="control-label col-md-4" for="name">Brand name:</label>
					<div class="col-md-4">
						<input class="form-control" type="text" name="name" id="name" maxlength="100" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="stake_no">Stake number:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="stake_no" id="stake_no" maxlength="11" pattern="[0-9]{0,11}" title="Must be numbers only"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="ibu">IBU:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="ibu" id="ibu" maxlength="11" pattern="[0-9]{0,11}" title="Must be numbers only"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="color">Color:</label>
					<div class="col-md-1">	
						<select class="form-control" name="color" id="color">
							<option value="" selected="selected"></option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="bold">Bold:</label>
					<div class="col-md-1">	
						<select class="form-control" name="bold" id="bold">
							<option value="" selected="selected"></option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="style">Style:</label>
					<div class="col-md-3">
						<input class="form-control" type="text" name="style" id="style" maxlength="30"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="alc_per_vol">Alcohol per volume:</label>
					<div class="col-md-2">
						<input class="form-control" type="text" name="alc_per_vol" id="alc_per_vol" maxlength="7" pattern="[0-9]{1,3}[\.][0-9]{3}" title="Must have 3 decimal places"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="cost_per_liter">Cost per liter:</label>
					<div class="col-md-2">	
						<input class="form-control" type="text" name="cost_per_liter" id="cost_per_liter" maxlength="6" pattern="[0-9]{1,3}[\.][0-9]{2}" title="Must have 2 decimal places"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="allergens">Allergens:</label>
					<div class="col-md-4">
						<input class="form-control" type="text" name="allergens" id="allergens" maxlength="50"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="brewmaster_notes">Brewmaster notes:</label>
					<div class="col-md-4">
						<textarea class="form-control" name="brewmaster_notes" id="brewmaster_notes" maxlength="1000" rows="6" cols="50" style="resize:vertical;"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4" for="description">Description:</label>
					<div class="col-md-4">	
						<textarea class="form-control" name="description" id="description" maxlength="1000" rows="6" cols="50" style="resize:vertical;"></textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add brand</button>
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
			function addBrand() {
				//set up variables needed for POST
				$.ajax({
					//method
					type: "POST",
					//php script to use
					url: "add_brand_script.php",
					//grab all form input and put into name-value pairs
					data: {
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
				//prevents page from reloading
				return false;
			}
			
			function goBack() {
				window.location = "index.php";
			}
		</script>
	</body>
</html>