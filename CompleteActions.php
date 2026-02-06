<?php
require __DIR__ . '/Credentials.php';
header('Content-Type: application/json');

// Preallocate the result:
$Result = array();

// Check and unpack inputs:
if (!isset($_POST['FunctionCall']) ) {
    $Result['Error'] = 'No function name!';
}
if (!isset($_POST['SubjectId'])) {
    $Result['Error'] = 'No function arguments!';
} else {
    $SubjectId = $_POST['SubjectId'];
}

// Connect to the database:
$Conn = new mysqli($Servername, $Username, $Password, $Dbname);
if($Conn->connect_error) {
	die("Connection failed: " . $Conn->connect_error);
}

// Sanitize SubjectId
$SubjectId = mysqli_real_escape_string($Conn,$SubjectId);

// Set DateTime_Write
$Now = new DateTimeImmutable("now", new DateTimeZone('Europe/London'));
$DateTime_Write = $Now->format('Y-m-d\TH:i:s');

// Get assignment
if(!isset($Result['Error'])) {
    switch($_POST['FunctionCall']) {
        
        case 'GetFeedback':
    		$Sql = "SELECT * FROM Feedback WHERE SubjectId = '$SubjectId'";
    		$QueryRes = mysqli_query($Conn, $Sql);
    		$FeedbackFound = false;
    		$Feedback = null;
    		if($QueryRes === FALSE) {
    			// If there is an SQL error:
    			$Conn->close();
    			die("Query Sql failed to execute successfully");
    		} else {
    			// If the query ran successfully...
    			while($Row = mysqli_fetch_assoc($QueryRes)) {
    				$FeedbackFound = true;
    				$Feedback = $Row["Feedback"];
    			}
    		}
    		$Result['FeedbackFound'] = $FeedbackFound;
    		$Result['Feedback'] = $Feedback;
            break;
            
        case 'WriteFeedback':
            $Feedback = $_POST['Feedback'];
            $Feedback = mysqli_real_escape_string($Conn,$Feedback);
    		$Sql = "CALL RecordFeedback('$SubjectId','$Feedback','$DateTime_Write')";
            // Run and set the result:
		    if ($Conn->query($Sql)===true) {
			    $Result['Success'] = true;
		    } else {
			    $Conn->close();
			    die('Query Sql failed to execute successfully;');
		    }
            break;
        
        case 'GetCompletionLink':
    		$Sql1 = "SELECT * FROM Register WHERE SubjectId = '$SubjectId'";
    		$QueryRes = mysqli_query($Conn, $Sql1);
    		$FoundSubject = false;
    		if($QueryRes === FALSE) {
    			// If there is an SQL error:
    			$Conn->close();
    			die("Query Sql1 failed to execute successfully");
    		} else {
    			// If the query ran successfully...
    			while($Row = mysqli_fetch_assoc($QueryRes)) {
    				$FoundSubject = true;
    				$State = $Row["State"];
    			}
    		}
    		$Result['FoundSubject'] = $FoundSubject;
    		$Result['Completed'] = false;
    		$Result['CompletionLink'] = '<a id="CompletionLink" href="./Error.html?SubjectId='.$SubjectId.'#" target="_blank">###</a>';
    		if ($FoundSubject) {
    		    if ($State == 15) {
    		        $Result['Completed'] = true;
    		        $Result['CompletionLink'] = '<a id="CompletionLink" href="https://www.sussex.ac.uk/research/centres/sussex-neuroscience/" target="_blank">https://www.sussex.ac.uk/research/centres/sussex-neuroscience/</a>';
    		    }
    		}
    		
    		// Log that they have landed on the Complete.html page:
    		$Sql2 = "UPDATE Register SET DateTime_Complete = '$DateTime_Write' WHERE SubjectId ='$SubjectId'";
    		if ($Conn->query($Sql2)===false) {
    		    $Conn->close();
			    die('Query Sql2 failed to execute successfully;');
    		}
            break;
            
        default:
            $Result['Error'] = 'Bad function call!';
            break;
    }
}

$Conn->close();
echo json_encode($Result);
?>