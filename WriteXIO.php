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
	case 'WriteAItrainIO':
	    
	    // Parse inputes
		$ClientTimeZone = $Input['ClientTimeZone'];
        $ClientTimeZone = mysqli_real_escape_string($Conn,$ClientTimeZone);
		$AItrainIO = $Input["AItrainIO"];
		$AItrainIO = mysqli_real_escape_string($Conn,$AItrainIO);
		
		// Get the current state number from the register table and add one to it
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
        $State = $State + 1;
		
		// SQL to save AItrainIO
        $Sql01 = "CALL RecordAItrainIO('$SubjectId','$DateTime_Write','$ClientTimeZone','$AItrainIO')";
		
		// SQL to update the Register table	
		$Sql02 = "UPDATE Register SET State = $State, DateTime_AItrain = '$DateTime_Write'
						WHERE SubjectId ='$SubjectId'";
			
		// Run and set the result:
		if(($Conn->query($Sql01)===true)&&($Conn->query($Sql02)===true)) {
		    $Url = GetTargetUrl($Conn, $SubjectId);
			$Result['TargetUrl'] = $Url;
		} else {
			$Conn->close();
			die('Query Sql01 and/or Sql02 failed to execute successfully;');
		}
		break;
		
	case 'WriteAIprobeIO':
	    
	    // Parse inputes
		$ClientTimeZone = $Input['ClientTimeZone'];
        $ClientTimeZone = mysqli_real_escape_string($Conn,$ClientTimeZone);
		$AIprobeIO = $Input["AIprobeIO"];
		$AIprobeIO = mysqli_real_escape_string($Conn,$AIprobeIO);
		
		// Get the current state number from the register table and add one to it
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
        $State = $State + 1;
		
		// SQL to save AIprobeIO
        $Sql01 = "CALL RecordAIprobeIO('$SubjectId','$DateTime_Write','$ClientTimeZone','$AIprobeIO')";
		
		// SQL to update the Register table	
		$Sql02 = "UPDATE Register SET State = $State, DateTime_AIprobe = '$DateTime_Write'
						WHERE SubjectId ='$SubjectId'";
			
		// Run and set the result:
		if(($Conn->query($Sql01)===true)&&($Conn->query($Sql02)===true)) {
			$Url = GetTargetUrl($Conn, $SubjectId);
			$Result['TargetUrl'] = $Url;
		} else {
			$Conn->close();
			die('Query Sql01 and/or Sql02 failed to execute successfully;');
		}
		break;
		
	case 'WriteRCtaskIO':
	    
	    // Parse inputes
		$ClientTimeZone = $Input['ClientTimeZone'];
        $ClientTimeZone = mysqli_real_escape_string($Conn,$ClientTimeZone);
		$StoryId = $Input['StoryId'];
        $StoryId = mysqli_real_escape_string($Conn,$StoryId);
		$RCtaskIO = $Input["RCtaskIO"];
		$RCtaskIO = mysqli_real_escape_string($Conn,$RCtaskIO);
		
		// Get the current state number from the register table and add one to it
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
        $State = $State + 1;
		
		
		if ($StoryId=='Amy') {
			// SQL to save RCtaskIO
			$Sql01 = "CALL RecordRCamyliIO('$SubjectId','$DateTime_Write','$ClientTimeZone','$RCtaskIO')";
			
			// SQL to update the Register table	
			$Sql02 = "UPDATE Register SET State = $State, DateTime_RCamyli = '$DateTime_Write'
							WHERE SubjectId ='$SubjectId'";
		} else {
			// SQL to save RCtaskIO
			$Sql01 = "CALL RecordRCgeorgIO('$SubjectId','$DateTime_Write','$ClientTimeZone','$RCtaskIO')";
			
			// SQL to update the Register table	
			$Sql02 = "UPDATE Register SET State = $State, DateTime_RCgeorg = '$DateTime_Write'
							WHERE SubjectId ='$SubjectId'";
		}
			
		// Run and set the result:
		if(($Conn->query($Sql01)===true)&&($Conn->query($Sql02)===true)) {
			$Url = GetTargetUrl($Conn, $SubjectId);
			$Result['TargetUrl'] = $Url;
		} else {
			$Conn->close();
			die('Query Sql01 and/or Sql02 failed to execute successfully;');
		}
		break;
		
	case 'WriteTItrainIO':
	    
	    // Parse inputes
		$ClientTimeZone = $Input['ClientTimeZone'];
        $ClientTimeZone = mysqli_real_escape_string($Conn,$ClientTimeZone);
		$TItrainIO = $Input["TItrainIO"];
		$TItrainIO = mysqli_real_escape_string($Conn,$TItrainIO);
		
		// Get the current state number from the register table and add one to it
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
        $State = $State + 1;
		
		// SQL to save TItrainIO
        $Sql01 = "CALL RecordTItrainIO('$SubjectId','$DateTime_Write','$ClientTimeZone','$TItrainIO')";
		
		// SQL to update the Register table	
		$Sql02 = "UPDATE Register SET State = $State, DateTime_TItrain = '$DateTime_Write'
						WHERE SubjectId ='$SubjectId'";
			
		// Run and set the result:
		if(($Conn->query($Sql01)===true)&&($Conn->query($Sql02)===true)) {
			$Url = GetTargetUrl($Conn, $SubjectId);
			$Result['TargetUrl'] = $Url;
		} else {
			$Conn->close();
			die('Query Sql01 and/or Sql02 failed to execute successfully;');
		}
		break;
		
	case 'WriteTIprobeIO':
	    
	    // Parse inputes
		$ClientTimeZone = $Input['ClientTimeZone'];
        $ClientTimeZone = mysqli_real_escape_string($Conn,$ClientTimeZone);
		$TIprobeIO = $Input["TIprobeIO"];
		$TIprobeIO = mysqli_real_escape_string($Conn,$TIprobeIO);
		
		// Get the current state number from the register table and add one to it
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
        $State = $State + 1;
		
		// SQL to save TIprobeIO
        $Sql01 = "CALL RecordTIprobeIO('$SubjectId','$DateTime_Write','$ClientTimeZone','$TIprobeIO')";
		
		// SQL to update the Register table	
		$Sql02 = "UPDATE Register SET State = $State, DateTime_TIprobe = '$DateTime_Write'
						WHERE SubjectId ='$SubjectId'";
			
		// Run and set the result:
		if(($Conn->query($Sql01)===true)&&($Conn->query($Sql02)===true)) {
			$Url = GetTargetUrl($Conn, $SubjectId);
			$Result['TargetUrl'] = $Url;
		} else {
			$Conn->close();
			die('Query Sql01 and/or Sql02 failed to execute successfully;');
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