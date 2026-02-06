<?php
require __DIR__ . '/Credentials.php';
header('Content-Type: application/json');

// A function that computes the time interval in seconds between PHP DateTime objects
function GetTimeInterval($A, $B) {
    $Yr = date_diff($A, $B)->y;
    $Mo = date_diff($A, $B)->m;
	$Dy = date_diff($A, $B)->d;
    $Hr = date_diff($A, $B)->h;
    $Mi = date_diff($A, $B)->i;
    $Sc = date_diff($A, $B)->s;
    $Interval = ($Yr*365.25*24*60*60) + ($Mo*30.4375*24*60*60) + ($Dy*24*60*60) + ($Hr*60*60) + ($Mi*60) + $Sc;
    if ($A > $B) {
        $Interval = -1 * $Interval;
    } else {
        $Interval =  1 * $Interval ;
    }
    return $Interval;
}

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
	case 'LogUnfocus':
	    
        $Location = $Input['Location'];
        $Location = mysqli_real_escape_string($Conn,$Location);
	    
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
        
        $Sql01 = "INSERT INTO Unfocuses (SubjectId, State, Location, DateTime_Unfocus) VALUES ('$SubjectId', $State, '$Location', '$DateTime_Write')";
	    if (($Conn->query($Sql01))===false) {
	        $Conn->close();
	        die('Query Sql01 failed to execute successfully;');
	    }
	    
	    $Sql02 = "SELECT * FROM Unfocuses WHERE SubjectId = '$SubjectId'";
	    $QueryRes02 = mysqli_query($Conn, $Sql02);
        $Result['Count'] = $QueryRes02->num_rows;
        $Result['Notice'] = "###";
        if ($Result['Count'] == 1) {
            $Result['Notice'] = "Please stay focused on this tab for the duration of the experiment.\nThis is your 1st warning. Repeatedly clicking away will result in your participation being discontinued.";
        } elseif ($Result['Count'] == 2) {
            $Result['Notice'] = "Please stay focused on this tab for the duration of the experiment.\nThis is your 2nd warning. Repeatedly clicking away will result in your participation being discontinued.";
        } elseif ($Result['Count'] == 3) {
            $Result['Notice'] = "Please stay focused on this tab for the duration of the experiment.\nThis is your 3rd and FINAL warning. Clicking away once more will result in your participation being discontinued.";
        } else {
            $Result['Notice'] = 'You blew it!';
            $Sql03 = "UPDATE Register SET State = -3 WHERE SubjectId ='$SubjectId'";
            if ($Conn->query($Sql03)===false) {
		        $Conn->close();
		        die('Query Sql03 failed to execute successfully;');
		    }
        }
        
		break;
		
	case 'LogRefocus':
	    
	    $Sql04 = "SELECT * FROM Unfocuses WHERE SubjectId = '$SubjectId'";
	    $QueryRes04 = mysqli_query($Conn, $Sql04);
	    if($QueryRes04 === false) {
            $Conn->close();
            die("Query Sql04 failed to execute successfully!");
        } else {
            $ii = 0;
            while($Row = mysqli_fetch_assoc($QueryRes04)) {
                $ii = $ii + 1;
                $DTU = new DateTimeImmutable(str_replace(' ','T',$Row["DateTime_Unfocus"]), new DateTimeZone('Europe/London'));
                if ($ii==1) {
                    $DateTime_Unfocus = $DTU;
                } else {
                    if ($DTU > $DateTime_Unfocus) {
                        $DateTime_Unfocus = $DTU;
                    }
                }
            }
        }
        // Now we have the latest DateTime_Unfocus set...
        $Bool = abs(GetTimeInterval($DateTime_Unfocus,$Now)) > (7*60);
        $Result['Bool'] = $Bool;
        if ($Bool) {
            $Sql05 = "UPDATE Register SET State = -4 WHERE SubjectId ='$SubjectId'";
            if ($Conn->query($Sql05)===false) {
		        $Conn->close();
		        die('Query Sql05 failed to execute successfully;');
		    }
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