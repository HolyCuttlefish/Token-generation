<?php

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: *');
	header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
	header('Content-Type: application/json; charset=UTF-8');

	require 'authenticationToken.php';

	include_once '../../RUDN_Gallery/RUDN_Book_Gallery/php/function/workingWithDB.php';
	include '../../RUDN_Gallery/RUDN_Book_Gallery/php/function/checkMethod.php';

	checkOptions($_SERVER['REQUEST_METHOD']);

	$args = null;

	$db = null;
	$stmt = null;

	$idAccount = null;

	$token = null;

	$args = json_decode(file_get_contents("php://input"), true);

	if(json_last_error() != JSON_ERROR_NONE)
	{
		echo json_encode(array('Error' => 'Invalid JSON data'));
		http_response_code(400);

		die;
	}

	if((!isset($args['login'])) && (!isset($args['password'])))
	{
		echo json_encode(array('Error' => 'Not data for input account'));
		http_response_code(400);

		die;
	}

	//var_dump($args);

	$db = connectDB();

	if(!$db)
	{
		echo json_encode(array('Error' => mysqli_connect_error()));
		http_response_code();

		die;
	}

	$stmt = mysqli_stmt_init($db);
	mysqli_stmt_prepare($stmt, "select id from Account where login = ? and password = ?");
	mysqli_stmt_bind_param($stmt, 'ss', $args['login'], $args['password']);
	
	try
	{
		mysqli_stmt_execute($stmt);
	} catch(Exception $e)
	{
		closeDB();
		
		echo json_encode(array('Error' => $e->getMessage()));
		http_response_code(500);

		die;
	}

	mysqli_stmt_bind_result($stmt, $idAccount);
	mysqli_stmt_fetch($stmt);

	if(is_null($idAccount))
	{
		closeDB();

		echo json_encode(array('Error' => 'Not data for input account'));
		http_response_code(401);

		die;
	} else
	{
		$token = generateToken($idAccount);
		echo json_encode(array('token' => $token));
	}

	closeDB();
?>
