<?php
require __DIR__ . '/Credentials.php';
header('Content-Type: application/json');

// Connect to the database:
$Conn = new mysqli($Servername, $Username, $Password, $Dbname);
if($Conn->connect_error) {
	die("Connection failed: " . $Conn->connect_error);
}

$Sql = "CALL GetAudioLog()";
$QueryRes = mysqli_query($Conn,$Sql);
$iRow = -1;
$Result = array();
while($Row = mysqli_fetch_assoc($QueryRes)) {
	$iRow = $iRow + 1;
	foreach($Row as $cId => $Val) {
		$Result[$iRow][$cId] = $Val;
	}
}

$Conn->close();
echo json_encode($Result);
?>