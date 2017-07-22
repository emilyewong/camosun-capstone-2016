<?php
    //this script is called by add_product_form.php
    //this script processes input from "Add new product" form using POST and inserts into database
    
    //define variables and set to default NULL
    $sku = $brandID = $size = $isPackaged = $containerDeposit = $dutyPaid = $upc = NULL;
    
    //assign user input to variables
    //check that required data was entered and length is within limits
    include ("./functions/functions.php"); //test_input function sanitizes input
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //check if sku only contains numbers and length is between 1 and 11
        if (!empty(base64_decode($_POST["sku"])) && preg_match("/^[0-9]{1,11}$/", base64_decode($_POST["sku"]))) {
            $sku = test_input(base64_decode($_POST["sku"]));
        } 
        
        if (!empty(base64_decode($_POST["brand_id"])) && preg_match("/^[0-9]{1,11}$/", base64_decode($_POST["brand_id"]))) {
            $brandID = test_input(base64_decode($_POST["brand_id"]));
        } 
        
        if (!empty(base64_decode($_POST["size"])) && preg_match("/^[0-9]{1,3}[\.][0-9]{3}$/", base64_decode($_POST["size"]))) {
            $size = test_input(base64_decode($_POST["size"]));
        }
        
        //can only be 1 or 0
        if (base64_decode($_POST["is_packaged"]) == 1 || base64_decode($_POST["is_packaged"]) == 0) {
            $isPackaged = test_input(base64_decode($_POST["is_packaged"]));
        }
        
        if (!empty(base64_decode($_POST["container_deposit"])) && preg_match("/^[0-9]{1,13}[\.][0-9]{2}$/", base64_decode($_POST["container_deposit"]))) {
           $containerDeposit = test_input(base64_decode($_POST["container_deposit"]));
        }
        
        if (!empty(base64_decode($_POST["duty_paid"])) && preg_match("/^[0-9]{1,13}[\.][0-9]{2}$/", base64_decode($_POST["duty_paid"]))) {
            $dutyPaid = test_input(base64_decode($_POST["duty_paid"]));
        }
        
        if (!empty(base64_decode($_POST["upc"])) && strlen(base64_decode($_POST["upc"])) <= 32) {
            $upc = test_input(base64_decode($_POST["upc"]));
        }
    
		//database connection details kept in separate file
		include ("database.php");
		try {	
			//catch error messages
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//construct insert statement using PDO prepared statement
			$statement = $connection->prepare(
				"INSERT INTO products
					(sku,
					brand_id,
					size,
					is_packaged,
					container_deposit,
					duty_paid,
					upc) 
				VALUES (:sku,
					:brandid,
					:size,
					:ispackaged,
					:containerdeposit,
					:dutypaid,
					:upc)");
			$statement->bindParam(':sku', $sku);
			$statement->bindParam(':brandid', $brandID);
			$statement->bindParam(':size', $size);
			$statement->bindParam(':ispackaged', $isPackaged);
			$statement->bindParam(':containerdeposit', $containerDeposit);
			$statement->bindParam(':dutypaid', $dutyPaid);
			$statement->bindParam(':upc', $upc);
			
			$statement->execute();
			
			//if successful echo back success message and execute javascript function to display the new product info
			echo "<div class='col-xs-3'></div><div class='col-xs-6 text-center alert alert-success'><span class='glyphicon glyphicon-ok-circle'></span> New product added</div>
				<div class='col-xs-12' id='display_info'></div>
				<script>
					$(document).ready(function() {
						$.ajax({
							//method
							type: 'GET',
							//php script to use
							url: 'display_product_info.php',
							//pass sku as name-value pair
							//pass a token to prevent access via url
							data: {'token' : 1,
								'sku': window.btoa(". $sku .")},
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
		catch (PDOException $e) {
			//if unsuccessful echo back failure message and execute javascript function to re-display add product form and keep input in fields
			echo "<div class='col-xs-3'></div><div class='col-xs-6 text-center alert alert-danger'><span class='glyphicon glyphicon-remove-circle'></span> Unable to add new product."; 
			
			//if there is a primary key conflict
			if($e->errorInfo[0] == '23000' && $e->errorInfo[1] =='1062') {
				echo "<br>The SKU is already in use.";
			}
			echo "</div>
				<div class='col-xs-12' id='display_info'></div>
				<script>
					$(document).ready(function() {
						$.ajax({
							//method
							type: 'GET',
							//php script to use
							url: 'display_product_form.php',
							//pass a token to prevent access via url
							data: {'token' : 1},
							//display response message in div id=display_info and populate fields
							success: function(response) { 
								$('#display_info').html(response);
								$('#sku').val('". $sku ."');
								$('#brand_id').val('". $brandID ."');
								$('#size').val('". $size ."');
								$('#is_packaged').val('". $isPackaged ."');
								$('#container_deposit').val('". $containerDeposit ."');
								$('#duty_paid').val('". $dutyPaid ."');
								$('#upc').val('". $upc ."');
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