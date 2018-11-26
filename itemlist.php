<?php
include "../inc/dbinfo.inc";
$tablename = "webpage";


/* Connect to MySQL and select the database. */
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

$database = mysqli_select_db($connection, DB_DATABASE);

VerifyEmployeesTable($connection, DB_DATABASE); // Ensure that the Employees table exists.


/* Routing */
/* If input fields are populated, add a row to the Employees table. */
$webpage_id = htmlentities($_POST['id']);
$webpage_title = htmlentities($_POST['title']);
$webpage_url = htmlentities($_POST['url']);

if (strlen($webpage_title) || strlen($webpage_url)) {
	if (strlen($webpage_id)) {
		editWebPage($connection, $webpage_id, $webpage_title, $webpage_url);
	}
	else {
		addWebPage($connection, $webpage_title, $webpage_url);
	}
}
else if (strlen($webpage_id)) {
	removeWebPage($webpage_id);
}

$query = "SELECT * FROM '$tablename';";

$result = mysqli_query($connection, $query); 

$i = 0;
while($query_data = mysqli_fetch_row($result)) {
	if ($i == 0) {
		echo "<a href={$query_data[2]} target="content" class="list-group-item list-group-item-primary-action active" onclick="onclick_list_item(this, {$i}, {$query_data[0]})">{$query_data[1]}</a>";
	} else {
		echo "<a href={$query_data[2]} target="content" class="list-group-item list-group-item-primary-action" onclick="onclick_list_item(this, {$i}, {$query_data[0]})">{$query_data[1]}</a>";
	}
	$i = $i + 1;
}


mysqli_free_result($result);
mysqli_close($connection);


/* DB CRUD */

/* Add an employee to the table. */
function addWebPage($connection, $title, $url) {
	$t = mysqli_real_escape_string($connection, $title);
	$u = mysqli_real_escape_string($connection, $url);

	$query = "INSERT INTO '$tablename' (`title`, `url`) VALUES ('$t', '$u');";

	if(!mysqli_query($connection, $query)) echo("<p>Error adding data.</p>");
}

function editWebPage($connection, $id, $title, $url) {
	$t = mysqli_real_escape_string($connection, $title);
	$u = mysqli_real_escape_string($connection, $url);

	$query = "UPDATE '$tablename' SET `title`='$t', `url`='$u' WHERE id = '$id';";

	if(!mysqli_query($connection, $query)) echo("<p>Error adding data.</p>");
}

function removeWebPage($connection, $id) {
	$query = "DELETE FROM '$tablename' WHERE id = '$id';";

	if(!mysqli_query($connection, $query)) echo("<p>Error deleting data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyEmployeesTable($connection) {
	if(!TableExists($tablename, $connection, DB_DATABASE)) 
	{
		$query = "CREATE TABLE '$tablename' (
			`title` varchar(45) DEFAULT NULL,
			`url` varchar(90) DEFAULT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `ID_UNIQUE` (`id`)
		);";

		if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
	}
}

/* Check for the existence of a table. */
function TableExists($connection) {
	$t = mysqli_real_escape_string($connection, $tablename);
	$d = mysqli_real_escape_string($connection, DB_DATABASE);

	$checktable = mysqli_query($connection, 
		"SELECT '$tablename' FROM information_schema.TABLES WHERE '$tablename' = '$t' AND TABLE_SCHEMA = '$d'");

	if(mysqli_num_rows($checktable) > 0) return true;

	return false;
}
?>