<?php 
	header('Content-Type: text/html; charset=utf-8');

	$servername = "localhost";
	$username = "root";
	$password = "";

	$resp = "";
	try {
	    $conn = new PDO("mysql:host=$servername;dbname=joinasbr", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
	    // set the PDO error mode to exception
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	   	$resp = "Connected successfully";
	    }
	catch(PDOException $e)
	    {
	    	$resp = "Connection failed: " . $e->getMessage();
    }
?>