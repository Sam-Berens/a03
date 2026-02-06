<?php
header('Content-Type: application/json');
require __DIR__ . '/Credentials.php';
require __DIR__ . '/GetTargetUrl.php';

$Result = array();
if (!isset($_POST['FunctionCall'])) {
	die('No function name!');
}
if (!isset($_POST['Args'])) {
	die('No function arguments!');
}

// Connect to the database:
$Conn = new mysqli($Servername, $Username, $Password, $Dbname);
if($Conn->connect_error) {
	die("Connection failed: " . $Conn->connect_error);
}

// Get the input variables
$Input = $_POST['Args'];


// Set Now
$Now = new DateTimeImmutable("now", new DateTimeZone('Europe/London'));

switch($_POST['FunctionCall']) {
	case 'Register':
		
		// Define SubjectId
		$SubjectId = $Input["SubjectId"];
		$SubjectId = mysqli_real_escape_string($Conn,$SubjectId);
		
		// Define Gender
		$Gender = $Input["Gender"];
		$Gender = mysqli_real_escape_string($Conn,$Gender);
		
		// Define L1
		$L1 = $Input["L1"];
		$L1 = mysqli_real_escape_string($Conn,$L1);
		
		// Define Handedness:
		$Handedness = $Input["Handedness"];
		$Handedness = mysqli_real_escape_string($Conn,$Handedness);
		
		// Define DateTime_Register
		$DateTime_Register = $Now->format('Y-m-d\TH:i:s');
		
		// Set Sql to enter the data into Register
		$Sql = "UPDATE Register SET 
		    State = 2,
	    	Gender = '$Gender', 
	    	L1 = '$L1',
	    	Handedness = '$Handedness', 
	    	DateTime_Register = '$DateTime_Register' 
	    	WHERE SubjectId = '$SubjectId'";
		
		// Run Sql:
		if ($Conn->query($Sql) === true) {
		    $Url = GetTargetUrl($Conn, $SubjectId);
			$Result['TargetUrl'] = $Url;
		} else {
			$Conn->close();
			die('Query $Sql failed to execute successfully;');
		}
		break;
		
	default:
		$Conn->close();
		die('Bad function call.');
		break;
}
$Conn->close();
echo json_encode($Result);
?>