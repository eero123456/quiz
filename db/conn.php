<?php

$db;

function connectDB()
{
	global $db;

	$DB_servername = getenv("DB_ADDRESS");
	$DB_port = getenv("DB_PORT");
	$DB_username = getenv("DB_USERNAME");
	$DB_password = getenv("DB_PASSWORD");
	$DB_tietokanta = getenv("DB_DATABASE");

	try {
		$db = new mysqli("$DB_servername:$DB_port", $DB_username, $DB_password, $DB_tietokanta);
	} catch (Exception $error) {
		die("Virhe yhteydessä: " . $error->getMessage());
	}
}

connectDB();


//	Apufunktio turvallisille kyselyille ilman parametreja
function getAll($sql)
{
	global $db;

	$result = $db->query($sql);

	if (!$result) {
		return [];
	}

	$data = $result->fetch_all(MYSQLI_ASSOC);
	return $data;
}



function getUser($sql)
{
	global $db;

	$result = $db->query($sql);

	if (!$result) {
		return [];
	}

	$data = $result->fetch_all(MYSQLI_ASSOC);
	return $data;
}