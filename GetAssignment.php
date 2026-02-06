<?php
require __DIR__ . '/Credentials.php';
header('Content-Type: application/json');

// Preallocate the result (which will contain the direction):
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

// Get assignment
if(!isset($Result['Error'])) {
    switch($_POST['FunctionCall']) {
        case 'GetAssignment':
    		
    		// Run Sql to get records based on SubjectId:
    		$SubjectId = mysqli_real_escape_string($Conn,$SubjectId);
    		$Sql = "SELECT * FROM Register WHERE SubjectId = '$SubjectId'";
    		$QueryRes = mysqli_query($Conn, $Sql);
    		$FailedToFind = true;
    		if($QueryRes === FALSE) {
    			// If there is an SQL error:
    			$Conn->close();
    			die("Query Sql failed to execute successfully");
    		} else {
    			// If the query ran successfully...
    			while($Row = mysqli_fetch_assoc($QueryRes)) {
    				$Assignment = $Row["Assignment"];
    				$Assignment = json_decode($Assignment,true);
    				$FailedToFind = false;
    			}
    		}
    		// If no assignment could be found...
    		if ($FailedToFind) {
    		    $Assignment = array();
    		    $Assignment['tA'] = 'i00';
                $Assignment['tB'] = 'i01';
                $Assignment['tC'] = 'i02';
                $Assignment['tD'] = 'i03';
                $Assignment['tE'] = 'i04';
                $Assignment['tF'] = 'i05';
                $Assignment['aA'] = 'i06';
                $Assignment['aB'] = 'i07';
                $Assignment['aC'] = 'i08';
                $Assignment['aD'] = 'i09';
                $Assignment['aE'] = 'i10';
                $Assignment['aF'] = 'i11';
    		}
    		
    		$Result['Assignment'] = $Assignment;
            
            break;
        default:
            $Result['Error'] = 'Bad function call!';
            break;
    }
}

$Conn->close();
echo json_encode($Result);
?>