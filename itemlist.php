<?php
include "../inc/dbinfo.inc";
define('TABLE_NAME', 'webpage');


/* Connect to MySQL and select the database. */
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

$database = mysqli_select_db($connection, DB_DATABASE);

verifyTable($connection); // Ensure that the Employees table exists.


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
	removeWebPage($connection, $webpage_id);
}

$query = "SELECT * FROM ".TABLE_NAME.";";

$result = mysqli_query($connection, $query); 

$i = 0;
while($query_data = mysqli_fetch_row($result)) {
	echo "<a href='$query_data[2]' target='content' class='list-group-item list-group-item-primary-action' onclick='onclick_list_item(this, $i, $query_data[0], `$query_data[1]`, `$query_data[2]`)'>$query_data[1]</a>";
	$i++;
}


mysqli_free_result($result);
mysqli_close($connection);


/* DB CRUD */

/* Add an employee to the table. */
function addWebPage($connection, $title, $url) {
	$t = mysqli_real_escape_string($connection, $title);
	$u = mysqli_real_escape_string($connection, $url);

	$query = "INSERT INTO `".TABLE_NAME."` (`title`, `url`) VALUES ('$t', '$u');";

	if(!mysqli_query($connection, $query)) echo("<p>Error adding data.</p>");
}

function editWebPage($connection, $id, $title, $url) {
	$t = mysqli_real_escape_string($connection, $title);
	$u = mysqli_real_escape_string($connection, $url);

	$query = "UPDATE `".TABLE_NAME."` SET `title`='$t', `url`='$u' WHERE `id` = '$id';";

	if(!mysqli_query($connection, $query)) echo("<p>Error adding data.</p>");
}

function removeWebPage($connection, $id) {
	$query = "DELETE FROM `".TABLE_NAME."` WHERE `id` = '$id';";

	if(!mysqli_query($connection, $query)) echo("<p>Error deleting data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function verifyTable($connection) {
	if(!tableExists($connection)) 
	{
		$query = "CREATE TABLE `".TABLE_NAME."` (
			`id` int(11) not null auto_increment,
			`title` varchar(255) not null,
			`url` varchar(511) not null,
			primary key (`id`));";

		if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
	}
}

/* Check for the existence of a table. */
function tableExists($connection) {
	$t = mysqli_real_escape_string($connection, TABLE_NAME);
	$d = mysqli_real_escape_string($connection, DB_DATABASE);

	$checktable = mysqli_query($connection, 
		"SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

	if(mysqli_num_rows($checktable) > 0) return true;

	return false;
}
?>