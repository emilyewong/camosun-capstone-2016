<?php    
	require_once './core/init.php'; //to access user data
	
	//this script is called by add_crm_form.php
    //this script processes input from "Add new CRM note" form using POST and inserts into database
	
    //define variables and set to default NULL
    $customerID = $noteTitle = $noteDate = $noteBody = $userID = $followup = NULL;
	$userSession = "";
    
    //assign user input to variables
    //check that required data was entered and length is within limits
    include ("./functions/functions.php"); //test_input function sanitizes input
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty(base64_decode($_POST["customer_id"])) && preg_match("/^[0-9]{1,11}$/", base64_decode($_POST["customer_id"]))) {
            $customerID = test_input(base64_decode($_POST["customer_id"]));
        } 
        
        if (!empty(base64_decode($_POST["note_title"])) && strlen(base64_decode($_POST["note_title"])) <= 64) {
            $noteTitle = test_input(base64_decode($_POST["note_title"]));
        } 
        
        if (!empty(base64_decode($_POST["note_date"]))) {
            $noteDate = test_input(base64_decode($_POST["note_date"]));
        }
        
        if (!empty(base64_decode($_POST["note_body"])) && strlen(base64_decode($_POST["note_body"])) <= 1000) {
            $noteBody = test_input(base64_decode($_POST["note_body"]));
        }
        
        //can only be 1 or 0
        if (base64_decode($_POST["followup"]) == 1 || base64_decode($_POST["followup"]) == 0) {
            $followup = test_input(base64_decode($_POST["followup"]));
        }
    
		//get session id to get user id from sessions table
		//$userSession = Session::get($GLOBALS['config']['session']['session_name']);
		//$userSession = strval($userSession);
		
		//database connection details kept in separate file
		include ("database.php");
		//construct select statement using PDO prepared statement
		//get user_id (user who is adding note)
		//$statementUser = $connection->prepare(
		//	"SELECT user_id 
		//	FROM sessions 
		//	WHERE session_id = :sessionid"); 
		//$statementUser->bindParam(':sessionid', $userSession);
		//$resultUser = $statementUser->execute();
		//if ($resultUser) {
		//	$rowUser = $statementUser->fetchObject();
			$userID = 1;//$rowUser->user_id;
		//}
		
		//construct insert statement using PDO prepared statement
		$statement = $connection->prepare(
			"INSERT INTO organization_notes
				(customer_id,
				user_id,
				note_title,
				note_date,
				note_body,
				followup) 
			VALUES (:customerid,
				:userid,
				:notetitle,
				:notedate,
				:notebody,
				:followup)");
		$statement->bindParam(':customerid', $customerID);
		$statement->bindParam(':userid', $userID);
		$statement->bindParam(':notetitle', $noteTitle);
		$statement->bindParam(':notedate', $noteDate);
		$statement->bindParam(':notebody', $noteBody);
		$statement->bindParam(':followup', $followup);
		
		$statement->execute();
		
		//get auto-incremented note id
		$noteID = $connection->lastInsertId();
		
		//get number of rows affected
		$numRows = $statement->rowCount();
		
		//if successful echo back success message and execute javascript function to display the new note
		if ($numRows > 0) {
			echo "<div class='col-xs-3'></div><div class='col-xs-6 text-center alert alert-success'><span class='glyphicon glyphicon-ok-circle'></span> New note added</div>
			<div class='col-xs-12' id='display_info'></div>
			<script>
				$(document).ready(function() {
					$.ajax({
						//method
						type: 'GET',
						//php script to use
						url: 'display_crm_note.php',
						//pass ids as name-value pairs
						//pass a token to prevent access via url
						data: {'token' : 1,
							'note_id': window.btoa(". $noteID ."),
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
		}
		//if unsuccessful echo back failure message and execute javascript function to re-display add crm form and keep input in fields
		else { 
			echo "<div class='col-xs-3'></div><div class='col-xs-6 text-center alert alert-danger'><span class='glyphicon glyphicon-remove-circle'></span> Unable to add new note</div>
			<div class='col-xs-12' id='display_info'></div>
			<script>
				$(document).ready(function() {
					$.ajax({
						//method
						type: 'GET',
						//php script to use
						url: 'display_crm_form.php',
						//pass id as name-value pair
						//pass a token to prevent access via url
						data: {'token' : 1,
							'customer_id' : window.btoa(". $customerID .")},
						//display response message in div id=display_info and populate fields
						success: function(response) { 
							$('#display_info').html(response);
							$('#customer_id').val('". $customerID ."');
							$('#note_title').val('". $noteTitle ."');
							$('#note_date').val('". $noteDate ."');
							$('#note_body').val('". $noteBody ."');
							$('#followup').val('". $followup ."');
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