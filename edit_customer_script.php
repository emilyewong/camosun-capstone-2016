<?php
    //this script is called by edit_customer_form.php
    //this script processes input from "Edit customer information" form using POST and updates database
    
    //define variables and set to default NULL
    $customerID = $customerName = $city = $type = $address = $postalCode = $region = $phone = $email = $website = $primaryContact = $prefContactMethod = $active = NULL;
    
    //assign user input to variables
    //check that required data was entered and length is within limits
    include ("./functions/functions.php"); //test_input function sanitizes input
    if ($_SERVER["REQUEST_METHOD"] == "POST") {		
		if (!empty(base64_decode($_POST["customer_id"])) && preg_match("/^[0-9]{1,11}$/", base64_decode($_POST["customer_id"]))) {
            $customerID = test_input(base64_decode($_POST["customer_id"]));
        } 
        
        if (!empty(base64_decode($_POST["customer_name"])) && strlen(base64_decode($_POST["customer_name"])) <= 50) {
            $customerName = test_input(base64_decode($_POST["customer_name"]));
        } 
        
        if (!empty(base64_decode($_POST["type"])) && strlen(base64_decode($_POST["type"])) == 3) {
            $type = test_input(base64_decode($_POST["type"]));
        }
        
        if (!empty(base64_decode($_POST["address"])) && strlen(base64_decode($_POST["address"])) <= 100) {
            $address = test_input(base64_decode($_POST["address"]));
        }
        
        if (!empty(base64_decode($_POST["city"])) && strlen(base64_decode($_POST["city"])) <= 100) {
           $city = test_input(base64_decode($_POST["city"]));
        }
        
        if (!empty(base64_decode($_POST["postal_code"])) && preg_match("/^[A-Z]\d[A-Z]\s?\d[A-Z]\d$/", base64_decode($_POST["postal_code"]))) {
            $postalCode = test_input(base64_decode($_POST["postal_code"]));
        }
        
        if (!empty(base64_decode($_POST["region"])) && strlen(base64_decode($_POST["region"])) <= 25) {
            $region = test_input(base64_decode($_POST["region"]));
        }
        
        if (!empty(base64_decode($_POST["phone"])) && strlen(base64_decode($_POST["phone"])) <= 20) {
            $phone = test_input(base64_decode($_POST["phone"]));
        }
        
        if (!empty(base64_decode($_POST["email"])) && preg_match("/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/", base64_decode($_POST["email"]))) {
            $email = test_input(base64_decode($_POST["email"]));
        }
        
        if (!empty(base64_decode($_POST["website"])) && strlen(base64_decode($_POST["website"])) <= 100) {
            $website = test_input(base64_decode($_POST["website"]));
        }
    
        if (!empty(base64_decode($_POST["primary_contact"])) && strlen(base64_decode($_POST["primary_contact"])) <= 100) {
            $primaryContact = test_input(base64_decode($_POST["primary_contact"]));
        }
        
        if (!empty(base64_decode($_POST["pref_contact_method"])) && strlen(base64_decode($_POST["pref_contact_method"])) <= 50) {
            $prefContactMethod = test_input(base64_decode($_POST["pref_contact_method"]));
        }
        
		//can only be 1 or 0
        if (base64_decode($_POST["active"]) == 1 || base64_decode($_POST["active"]) == 0) {
            $active = test_input(base64_decode($_POST["active"]));
        }
    
		//database connection details kept in separate file
		include ("database.php");
		//construct update statement using PDO prepared statement
		$statement = $connection->prepare(
			"UPDATE customers
			SET customer_name = :customername,
				type = :type,
				address = :address,
				city = :city,
				region = :region,
				postal_code = :postalcode,
				phone = :phone,
				email = :email,
				website = :website,
				primary_contact = :primarycontact,
				pref_contact_method = :prefcontactmethod,
				active = :active
			WHERE customer_id = :customerid");
		$statement->bindParam(':customerid', $customerID);
		$statement->bindParam(':customername', $customerName);
		$statement->bindParam(':type', $type);
		$statement->bindParam(':address', $address);
		$statement->bindParam(':city', $city);
		$statement->bindParam(':region', $region);
		$statement->bindParam(':postalcode', $postalCode);
		$statement->bindParam(':phone', $phone);
		$statement->bindParam(':email', $email);
		$statement->bindParam(':website', $website);
		$statement->bindParam(':primarycontact', $primaryContact);
		$statement->bindParam(':prefcontactmethod', $prefContactMethod);
		$statement->bindParam(':active', $active);
		
		$result = $statement->execute();
		
		//echo back success/failure message
		if ($result) echo "<div class='col-xs-3'></div><div class='col-xs-6 text-center alert alert-success'><span class='glyphicon glyphicon-ok-circle'></span> Customer data updated</div>";
		else echo "<div class='col-xs-3'></div><div class='col-xs-6 text-center alert alert-danger'><span class='glyphicon glyphicon-remove-circle'></span> Unable to edit customer data</div>";
		
		//execute javascript function to display the updated customer info
		echo "<div class='col-xs-12' id='display_info'></div>
		<script>
			$(document).ready(function() {
				$.ajax({
					//method
					type: 'GET',
					//php script to use
					url: 'display_customer_info.php',
					//pass id as name-value pair
					//pass a token to prevent access via url
					data: {'token' : 1,
						'customer_id': window.btoa(". $customerID .")},
					//display response message in div id=display_info
					success: function(response) { 
						$('#display_info').html(response);
					}
				});
				//prevents page from reloading
				return false;
			});
		</script>";
		
		//close connection
		$connection = null;
	}
?>