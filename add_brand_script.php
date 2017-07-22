<?php
    //this script is called by add_brand_form.php
    //this script processes input from "Add new brand" form using POST and inserts into database
    
    //define variables and set to default NULL
    $name = $stakeNo = $ibu = $color = $bold = $style = $alcPerVol = $costPerLiter = $allergens = $brewmasterNotes = $description = NULL;
    
    //assign user input to variables
    //check that required data was entered and length is within limits
    include ("./functions/functions.php"); //test_input function sanitizes input
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty(base64_decode($_POST["name"])) && strlen(base64_decode($_POST["name"])) <= 100) {
            $name = test_input(base64_decode($_POST["name"]));
        } 
		
		if (!empty(base64_decode($_POST["stake_no"])) && preg_match("/^[0-9]{0,11}$/", base64_decode($_POST["stake_no"]))) {
            $stakeNo = test_input(base64_decode($_POST["stake_no"]));
        }
        
        if (!empty(base64_decode($_POST["ibu"])) && preg_match("/^[0-9]{0,11}$/", base64_decode($_POST["ibu"]))) {
            $ibu = test_input(base64_decode($_POST["ibu"]));
        }
        
        if (!empty(base64_decode($_POST["color"])) && preg_match("/^[0-5]{0,1}$/", base64_decode($_POST["color"]))) {
            $color = test_input(base64_decode($_POST["color"]));
        }
        
        if (!empty(base64_decode($_POST["bold"])) && preg_match("/^[0-5]{0,1}$/", base64_decode($_POST["bold"]))) {
           $bold = test_input(base64_decode($_POST["bold"]));
        }
        
        if (!empty(base64_decode($_POST["style"])) && strlen(base64_decode($_POST["style"])) <= 30) {
            $style = test_input(base64_decode($_POST["style"]));
        }
        
        if (!empty(base64_decode($_POST["alc_per_vol"])) && preg_match("/^[0-9]{1,3}[\.][0-9]{3}$/", base64_decode($_POST["alc_per_vol"]))) {
            $alcPerVol = test_input(base64_decode($_POST["alc_per_vol"]));
        }
		
		if (!empty(base64_decode($_POST["cost_per_liter"])) && preg_match("/^[0-9]{1,3}[\.][0-9]{2}$/", base64_decode($_POST["cost_per_liter"]))) {
            $costPerLiter = test_input(base64_decode($_POST["cost_per_liter"]));
        }
        
        if (!empty(base64_decode($_POST["allergens"])) && strlen(base64_decode($_POST["allergens"])) <= 50) {
            $allergens = test_input(base64_decode($_POST["allergens"]));
        }
        
        if (!empty(base64_decode($_POST["brewmaster_notes"])) && strlen(base64_decode($_POST["brewmaster_notes"])) <= 1000) {
            $brewmasterNotes = test_input(base64_decode($_POST["brewmaster_notes"]));
        }
        
        if (!empty(base64_decode($_POST["description"])) && strlen(base64_decode($_POST["description"])) <= 1000) {
            $description = test_input(base64_decode($_POST["description"]));
        }
    
		//database connection details kept in separate file
		include ("database.php");
		//construct insert statement using PDO prepared statement
		$statement = $connection->prepare(
			"INSERT INTO brands
				(name,
				stake_no,
				ibu,
				color,
				bold,
				style,
				alc_per_vol,
				cost_per_liter,
				allergens,
				brewmaster_notes,
				description) 
			VALUES (:name,
				:stakeno,
				:ibu,
				:color,
				:bold,
				:style,
				:alcpervol,
				:costperliter,
				:allergens,
				:brewmasternotes,
				:description)");
		$statement->bindParam(':name', $name);
		$statement->bindParam(':stakeno', $stakeNo);
		$statement->bindParam(':ibu', $ibu);
		$statement->bindParam(':color', $color);
		$statement->bindParam(':bold', $bold);
		$statement->bindParam(':style', $style);
		$statement->bindParam(':alcpervol', $alcPerVol);
		$statement->bindParam(':costperliter', $costPerLiter);
		$statement->bindParam(':allergens', $allergens);
		$statement->bindParam(':brewmasternotes', $brewmasterNotes);
		$statement->bindParam(':description', $description);
		
		$statement->execute();
		
		//get auto-incremented brand id
		$brandID = $connection->lastInsertId();
		
		//get number of rows affected
		$numRows = $statement->rowCount();
		
		//if successful echo back success message and execute javascript function to display the new brand info
		if ($numRows > 0) {
			echo "<div class='col-xs-3'></div><div class='col-xs-6 text-center alert alert-success'><span class='glyphicon glyphicon-ok-circle'></span> New brand added</div>
			<div class='col-xs-12' id='display_info'></div>
			<script>
				$(document).ready(function() {
					$.ajax({
						//method
						type: 'GET',
						//php script to use
						url: 'display_brand_info.php',
						//pass id as name-value pair
						//pass a token to prevent access via url 
						data: {'token' : 1,
							'brand_id': window.btoa(". $brandID .")},
						//display response message in div id=display_info
						success: function(response) { 
							$('#display_info').html(response);
						}
					});
					//prevents page from reloading
					return false;
				});
			</script>";
		}
		//if unsuccessful echo back failure message and execute javascript function to re-display add brand form and keep input in fields
		else {
			echo "<div class='col-xs-3'></div><div class='col-xs-6 text-center alert alert-danger'><span class='glyphicon glyphicon-remove-circle'></span> Unable to add new brand</div>
			<div class='col-xs-12' id='display_info'></div>
			<script>
				$(document).ready(function() {
					$.ajax({
						//method
						type: 'GET',
						//php script to use
						url: 'display_brand_form.php',
						//pass a token to prevent access via url
						data: {'token' : 1},
						//display response message in div id=display_info and populate fields
						success: function(response) { 
							$('#display_info').html(response);
							$('#name').val('". $name ."');
							$('#stake_no').val('". $stakeNo ."');
							$('#ibu').val('". $ibu ."');
							$('#color').val('". $color ."');
							$('#bold').val('". $bold ."');
							$('#style').val('". $style ."');
							$('#alc_per_vol').val('". $alcPerVol ."');
							$('#cost_per_liter').val('". $costPerLiter ."');
							$('#allergens').val('". $allergens ."');
							$('#brewmaster_notes').val('". $brewmasterNotes ."');
							$('#description').val('". $description ."');
						}
					});
					//prevents page from reloading
					return false;
				});
			</script>";
		}
		//close connection
		$connection = null;
	}
?>