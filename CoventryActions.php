<?php
require __DIR__ . '/Credentials.php';
header('Content-Type: application/json');

$Result = array();

if(!isset($_POST['FunctionCall'])) {
	die('No function name!');
}
if(!isset($_POST['Args'])) {
	die('No function arguments!');
}

// Connect to the database:
$Conn = new mysqli($Servername, $Username, $Password, $Dbname);
if($Conn->connect_error) {
	die("Connection failed: " . $Conn->connect_error);
}

// Get the input variables:
$Input = $_POST['Args'];
$SubjectId = $Input['SubjectId'];
$SubjectId = mysqli_real_escape_string($Conn,$SubjectId);
$Now = new DateTimeImmutable("now", new DateTimeZone('Europe/London'));
$DateTime_Now = $Now->format('Y-m-d\TH:i:s');

switch($_POST['FunctionCall']) {
	case 'GetReason':
	    
	    $Sql00 = "SELECT * FROM Register WHERE SubjectId = '$SubjectId'";
	    $QueryRes00 = mysqli_query($Conn, $Sql00);
	    if($QueryRes00 === false) {
            $Conn->close();
            die("Query Sql00 failed to execute successfully!");
        } else {
            while($Row = mysqli_fetch_assoc($QueryRes00)) {
                $State = $Row["State"];
            }
        }
        
        switch ($State) {
            case -1:
                $Result['HTML'] = "<h1><b>Oops...</b></h1>
                    <p>Sorry, it appears that we could not get a good microphone recording off you.</p>
                    <p>This means that we will not be able to use your data &#128546;.</p>
                    <p>Your participation has been discontinued. Please return your Prolific submission.</p>";
                break;
            case -2:
                $Result['HTML'] = "<h1><b>Oops...</b></h1>
                    <p>Sorry, it appears that we could not get a good microphone recording off you and/or your speakers do not work properly.</p>
                    <p>This means that we will not be able to use your data &#128546;.</p>
                    <p>Your participation has been discontinued. Please return your Prolific submission.</p>";
                break;
            case -3:
                $Result['HTML'] = "<h1><b>Oops...</b></h1>
                    <p>Sorry, it appears that you clicked away from the experiment too many times.</p>
                    <p>This means that we will not be able to use your data &#128546;.</p>
                    <p>Your participation has been discontinued. Please return your Prolific submission.</p>";
                break;
            case -4:
                $Result['HTML'] = "<h1><b>Oops...</b></h1>
                    <p>Sorry, it appears that you clicked away from the experiment for too long.</p>
                    <p>This means that we will not be able to use your data &#128546;.</p>
                    <p>Your participation has been discontinued. Please return your Prolific submission.</p>";
                break;
            case -99:
                $Result['HTML'] = "<h1><b>Oops...</b></h1>
                    <p>Sorry, it appears that you have been trying to cheat us!</p>
                    <p>We don't take kindly to this sort of behaviour &#128546;.</p>
                    <p>Your participation has been discontinued. Please return your Prolific submission.</p>";
                break;
            default:
                $Conn->close();
		        die('State does not match!');
		        break;
        }
        
		break;
		
	default:
		// Kill it if the function call is bad:
		$Conn->close();
		die('Bad function call.');
		break;
}

$Conn->close();
echo json_encode($Result);

?>