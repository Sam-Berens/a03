<?php
header('Content-Type: application/json');
require __DIR__ . '/Credentials.php';
require __DIR__ . '/GetTargetUrl.php';

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
$DateTime_Write = $Now->format('Y-m-d\TH:i:s');

switch($_POST['FunctionCall']) {
	case 'LogSpeakerTest':
	    
	    // Get an additional inputs
	    $TrialCount = $Input['TrialCount'];
	    $CR1 = $Input['CR1'];
	    $CR2 = $Input['CR2'];
	    $AR1 = $Input['AR1'];
	    $AR2 = $Input['AR2'];
        
        // Old JS functionality
        //CR1 = i1.toString(2).padStart(3,'0').split('').map(function(ii){return parseInt(ii);}).reduce(function(ii,jj){return ii+jj;},0);
        //CR2 = i2.toString(2).padStart(3,'0').split('').map(function(ii){return parseInt(ii);}).reduce(function(ii,jj){return ii+jj;},0);
        
        // Check whether this was a valid submission:
        $CR1 = array_sum(array_map('intval',str_split(sprintf( "%03d",decbin($CR1)))));
        $CR2 = array_sum(array_map('intval',str_split(sprintf( "%03d",decbin($CR2)))));
        $TestBool = ($CR1 == $AR1) && ($CR2 == $AR2);
	    
	    // Get the current state for this partcipant
        $Sql00 = "SELECT * FROM Register WHERE SubjectId = '$SubjectId'";
        $QueryRes00 = mysqli_query($Conn, $Sql00);
        $State = null;
        if($QueryRes00 === false) {
            $Conn->close();
            die("Query Sql00 failed to execute successfully!");
        } else {
            while($Row = mysqli_fetch_assoc($QueryRes00)) {
                $State = $Row["State"];
            }
        }
        
        // If they have been naughty
        if ($State != 103) {
            $Sql01 = "UPDATE Register SET State = -99, DateTime_MicTest = '$DateTime_Write' WHERE SubjectId ='$SubjectId'";
            if (($Conn->query($Sql01))===false) {
                die('Query Sql01 failed to execute successfully;');
            }
            $Success = true;
        } else if ($TestBool) {
            
            $Success = true;
            
            // Update the Register table...
            $Sql02 = "UPDATE Register SET State = 3, DateTime_MicTest = '$DateTime_Write' WHERE SubjectId ='$SubjectId'";
            if (($Conn->query($Sql02))===false) {
                die('Query Sql02 failed to execute successfully;');
            }
        } else {
            // Set the Success variable:
            $Success = false;
        }
        
        // If this submission was not valid, and they have had 3 previous unsuccessfully attempts ...
        // ... pretend that this (4th failed) attempt was successful (i.e., return Success=true), ...
        // ... but update the State variable to -2 so that they cannot progress (hahaha).
        if ((!$Success) && ($TrialCount > 3)) {
            // Update the Register table...
            $Sql03 = "UPDATE Register SET State = -2, DateTime_MicTest = '$DateTime_Write' WHERE SubjectId ='$SubjectId'";
            if (($Conn->query($Sql03))===false) {
                die('Query Sql02 failed to execute successfully;');
            }
            
            // Set the Success variable:
            $Success = true;
        }
		
		// Set the response from the PHP server:
		$Url = GetTargetUrl($Conn, $SubjectId);
		$Result['Success'] = $Success;
		$Result['TargetUrl'] = $Url;
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