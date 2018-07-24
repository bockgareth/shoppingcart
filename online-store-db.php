<?php
	$ErrorMsgs = [];
	$DBConnect = @new mysqli("localhost", "root","", "oshop");
	if ($DBConnect->connect_errno) {
        $ErrorMsgs[] = "Unable to connect to the database server." .
			" Error code " . $DBConnect->connect_errno . ": " . $DBConnect->connect_error;
    }
		
?>