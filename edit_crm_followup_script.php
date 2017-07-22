<?php
    //this script is called by view_crm_note_script.php if the note has a follow-up of "Yes", and updates the follow-up to "No"
    //this script is connected to customer by customer id and note id, which was passed as name-value pairs via GET
    
    //define variable and set to empty
    $customerID = $noteID = "";
    
    //check if id only contains numbers and length is correct
    include ("./functions/functions.php"); //test_input function sanitizes input
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (preg_match("/^[0-9]{1,11}$/", base64_decode($_GET["customer_id"]))) {
            $customerID = test_input(base64_decode($_GET["customer_id"]));
        }
		
		if (preg_match("/^[0-9]{1,11}$/", base64_decode($_GET["note_id"]))) {
            $noteID = test_input(base64_decode($_GET["note_id"]));
        }
    
		//database connection details kept in separate file
		include ("database.php");
		//construct update statement using PDO prepared statement
		$statement = $connection->prepare(
			"UPDATE organization_notes
			SET followup = 0
			WHERE note_id = :noteid");
		$statement->bindParam(':noteid', $noteID);
		
		$statement->execute();
		
		//get number of rows affected
		$numRows = $statement->rowCount();
		
		//echo back success/failure message
		if ($numRows > 0) {
			echo "<div class='col-xs-3'></div><div class='col-xs-6 text-center alert alert-success'><span class='glyphicon glyphicon-ok-circle'></span> CRM note updated</div>";
		}
		else {
			echo "<div class='col-xs-3'></div><div class='col-xs-6 text-center alert alert-danger'><span class='glyphicon glyphicon-remove-circle'></span> Unable to edit CRM note</div>";
		}
		
		//execute javascript function to display the updated note
		echo "<div class='col-xs-12' id='display_info'></div>
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
		
		//close connection
		$connection = null;
	}
?>