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
if (strlen($SubjectId)==0) {
	$SubjectId = substr(md5(rand()),-8);
}
$TrialId = $Input['TrialId'];
$TrialId = mysqli_real_escape_string($Conn,$TrialId);
$AudioDuration = $Input['AudioDuration'];
$AudioDuration = mysqli_real_escape_string($Conn,$AudioDuration);
$AudioDuration = $AudioDuration / 1000; // Convert to seconds;
$AudioData = $Input['AudioData'];
$DataHash = md5($AudioData);
$Now = new DateTimeImmutable("now", new DateTimeZone('Europe/London'));
$DateTime_Write = $Now->format('Y-m-d\TH:i:s');

switch($_POST['FunctionCall']) {
	case 'WriteAudioData':
	    
	    // Create a FileId by concatenating the SubjectId and TrialId:
	    $FileId = $SubjectId.'_'.$TrialId;
	    
	    // Write the audio data to a file:
	    file_put_contents('./AudioData/'.$FileId.'.dat', $AudioData);
	    $FileSize = filesize('./AudioData/'.$FileId.'.dat');
		
		// Update the database:
        $Sql01 = "CALL RecordAudioLog('$FileId','$SubjectId','$TrialId',$AudioDuration,$FileSize,'$DataHash','$DateTime_Write')";
        if (($Conn->query($Sql01))===false) {
            die('Query Sql01 failed to execute successfully;');
        }
		
		// Set the response from the PHP server:
		$Result['FileId'] = $FileId;
		break;
		
	case 'MicTest':
	    
	    // Get an additional input ('TrialCount'):
	    $TrialCount = $Input['TrialCount'];
	    
	    
	    // Create a FileId by concatenating the SubjectId and TrialId:
	    $FileId = $SubjectId.'_'.$TrialId;
	    
	    // Write the audio data to a file:
	    file_put_contents('./AudioData/'.$FileId.'.dat', $AudioData);
	    $FileSize = filesize('./AudioData/'.$FileId.'.dat');
		
		// Update the database:
        $Sql01 = "CALL RecordAudioLog('$FileId','$SubjectId','$TrialId',$AudioDuration,$FileSize,'$DataHash','$DateTime_Write')";
        if (($Conn->query($Sql01))===false) {
            die('Query Sql01 failed to execute successfully;');
        }
        
        // Check whether this was a valid submission:
        $TestBool = ($AudioDuration < 4);
        if ($TestBool) {
            $Success = false;
        } else {
            // Update the Register table...
            $Sql02 = "UPDATE Register SET State = 103, DateTime_MicTest = '$DateTime_Write' WHERE SubjectId ='$SubjectId'";
            if (($Conn->query($Sql02))===false) {
                die('Query Sql02 failed to execute successfully;');
            }
            
            // Set the Success variable:
            $Success = true;
        }
        
        // If this submission was not valid, and they have had 3 previous unsuccessfully attempts ...
        // ... pretend that this (4th failed) attempt was successful (i.e., return Success=true), ...
        // ... but update the State variable to -1 so that they cannot progress (hahaha).
        if ((!$Success) && ($TrialCount > 3)) {
            // Update the Register table...
            $Sql03 = "UPDATE Register SET State = -1, DateTime_MicTest = '$DateTime_Write' WHERE SubjectId ='$SubjectId'";
            if (($Conn->query($Sql03))===false) {
                die('Query Sql02 failed to execute successfully;');
            }
            
            // Set the Success variable:
            $Success = true;
        }
		
		// Set the response from the PHP server:
		$Result['Success'] = $Success;
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