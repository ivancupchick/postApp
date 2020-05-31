<?php
 	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

	define('DB_HOST', 'localhost');
	define('DB_USER', 'root');
	define('DB_PASS', 'root');
	define('DB_NAME', 'mydb');

	function connect() {
	  $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	  if (mysqli_connect_errno($connect)) {
	    die("Failed to connect:" . mysqli_connect_error());
	  }

	  mysqli_set_charset($connect, "utf8");

	  return $connect;
	}

	$con = connect();
	// php -S 127.0.0.1:8080 -t ./backend
	// php -S 127.0.0.1:8080 -t ./
?>
