<?php
header('Content-Type: application/json');
require __DIR__ . '/Credentials.php';
require __DIR__ . '/GetTargetUrl.php';

// A function that formats datetime strings for SQL insertion
function FormatDateTimeStr($Str){
	$OutStr = substr($Str,0,4)
		.'-'.substr($Str,4,2)
		.'-'.substr($Str,6,2)
		.'T'.substr($Str,9,2)
		.':'.substr($Str,11,2)
		.':'.substr($Str,13,2);
	return $OutStr;
}

// A function that computes the time interval in seconds between PHP DateTime objects
// ... this function returns the signed difference in seconds between inputs (A and B).
// ... The rusult is greater than zero when B > A.
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

// A function that makes stimuli assignments
function MakeAssignment() {
    $ImgNum = range(0,11);
    for ($ii=0; $ii<12; $ii++) {
	    $ImgNum[$ii] = sprintf("i%02d",$ImgNum[$ii]);
    }
    shuffle($ImgNum);
    $Assignment['tA'] = $ImgNum[ 0];
    $Assignment['tB'] = $ImgNum[ 1];
    $Assignment['tC'] = $ImgNum[ 2];
    $Assignment['tD'] = $ImgNum[ 3];
    $Assignment['tE'] = $ImgNum[ 4];
    $Assignment['tF'] = $ImgNum[ 5];
    $Assignment['aA'] = $ImgNum[ 6];
    $Assignment['aB'] = $ImgNum[ 7];
    $Assignment['aC'] = $ImgNum[ 8];
    $Assignment['aD'] = $ImgNum[ 9];
    $Assignment['aE'] = $ImgNum[10];
    $Assignment['aF'] = $ImgNum[11];
    $Assignment = json_encode($Assignment);
    return $Assignment;
}

// Preallocate the result (which will contain the direction):
$Result = array();

// Check and unpack inputs:
if (!isset($_POST['FunctionCall']) ) {
    $Result['Error'] = 'No function name!';
}
if (!isset($_POST['Args'])) {
    $Result['Error'] = 'No function arguments!';
}
$Inputs = $_POST['Args'];

// Connect to the database:
$Conn = new mysqli($Servername, $Username, $Password, $Dbname);
if($Conn->connect_error) {
	die("Connection failed: " . $Conn->connect_error);
}

// Test virginity, log via SQL, and set the result:
if (!isset($Result['Error'])) {
    switch ($_POST['FunctionCall']) {
        case 'LogLanding':
            
            // Set PoolId, SubjectId, and Now:
            $PoolId = $Inputs['PoolId'];
            $SubjectId = $Inputs['SubjectId'];
            $PoolId = mysqli_real_escape_string($Conn,$PoolId);
            $SubjectId = mysqli_real_escape_string($Conn,$SubjectId);
            $Now = new DateTimeImmutable("now", new DateTimeZone('Europe/London'));
            $DateTime_Landing = $Now->format('Y-m-d\TH:i:s');
            
            // Query the Register to see if there is a match:
            $Sql00 = "SELECT * FROM Register WHERE SubjectId = '$SubjectId'";
            $QueryRes00 = mysqli_query($Conn, $Sql00);
            if ($QueryRes00 === false) {
                $Conn->close();
                die("Query Sql00 failed to execute successfully!");
            } else {
                $Virgin = true;
                while($Row = mysqli_fetch_assoc($QueryRes00)) {
                    $Virgin = false;
                    $PoolId = $Row["PoolId"]; // Redefine as it may be null;
                    $State = $Row["State"];
                    $TaskPerm = $Row["TaskPerm"];
                }
            }
            
            // Log and direct depedning on current state:
            if ($Virgin) {
                
                // Select a task permutation
                $AllPerms = [84,98,140,161,266,273];
                $TaskPerm = $AllPerms[array_rand($AllPerms)];
                
                // Make an assignment
                $Assignment = MakeAssignment();
                
                // Cons
                $Sql01 = "INSERT INTO Register (PoolId, SubjectId, State, TaskPerm, Assignment, DateTime_Landing) 
				    VALUES ('$PoolId', '$SubjectId', 0, $TaskPerm, '$Assignment','$DateTime_Landing')";
				if($Conn->query($Sql01) === true) {
					$Result['TargetUrl'] = "./Consent.html?SubjectId=$SubjectId#";
				} else {
					$Conn->close();
					die('Query Sql01 failed to execute successfully!');
				}
                
            } else {
                // They have been here before!
                
                // Log the Relanding
                $Sql02 = "INSERT INTO Relandings (PoolId, SubjectId, State, DateTime_Reland) VALUES ('$PoolId', '$SubjectId', $State, '$DateTime_Landing')";
                if($Conn->query($Sql02) === false) {
                    $Conn->close();
					die('Query Sql02 failed to execute successfully!');
                }
                
                // Get the TargetUrl and return it
                $Url = GetTargetUrl($Conn, $SubjectId);
                $Result['TargetUrl'] = $Url;
            }
            break;
            
        case 'LogExclusion':
            
            // Set PoolId, SubjectId, and Now:
            $PoolId = $Inputs['PoolId'];
            $SubjectId = $Inputs['SubjectId'];
            $OS = $Inputs['OS'];
            $Browser = $Inputs['Browser'];
            
            $PoolId = mysqli_real_escape_string($Conn,$PoolId);
            $SubjectId = mysqli_real_escape_string($Conn,$SubjectId);
            $OS = mysqli_real_escape_string($Conn,$OS);
            $Browser = mysqli_real_escape_string($Conn,$Browser);
            
            if (!boolval($PoolId)) {
                $PoolId = 'null';
            }
            if (!boolval($SubjectId)) {
                $SubjectId = 'null';
            }
            
            $Now = new DateTimeImmutable("now", new DateTimeZone('Europe/London'));
            $DateTime_Exclude = $Now->format('Y-m-d\TH:i:s');
            
            $Sql = "INSERT INTO Exclusions (PoolId, SubjectId, OS, Browser, DateTime_Exclude) VALUES ('$PoolId', '$SubjectId', '$OS', '$Browser', '$DateTime_Exclude')";
			if ($Conn->query($Sql) === true) {
			    $Result['Success'] = true;
			} else {
			    die("Query Sql failed to execute successfully!\n" . $Conn -> error);
			}
            break;
            
        case 'LogInstruct':
            // Set SubjectId, and Now:
            $SubjectId = $Inputs['SubjectId'];
            $SubjectId = mysqli_real_escape_string($Conn,$SubjectId);
            $Now = new DateTimeImmutable("now", new DateTimeZone('Europe/London'));
            $DateTime_Instruct = $Now->format('Y-m-d\TH:i:s');
            
            // DateTime_Start
            $DateTime_Start = FormatDateTimeStr($Inputs['DateTime_Start']);
            $Start = new DateTimeImmutable($DateTime_Start, new DateTimeZone('Europe/London'));
            
            // Interval between Now and Start
            $Interval = GetTimeInterval($Start,$Now);
            
            // ClientTimeZone
            $ClientTimeZone = $Inputs['ClientTimeZone'];
            $ClientTimeZone = mysqli_real_escape_string($Conn,$ClientTimeZone);
            
            // TaskId
            $TaskId = $Inputs['TaskId'];
            $TaskId = mysqli_real_escape_string($Conn,$TaskId);
            
            // Test to see if enough time has passed (depending on the TaskId)
            $TestBool = false;
            switch ($TaskId) {
                case 'AItrain':
                    if ($Interval > 76) {
                        $TestBool = true;
                    }
                    break;
                case 'AIprobe':
                    if ($Interval > 51) {
                        $TestBool = true;
                    }
                    break;
                case 'RC1':
                    if ($Interval > 25) {
                        $TestBool = true;
                    }
                    break;
                case 'RC2':
                    if ($Interval > 0) {
                        $TestBool = true;
                    }
                    break;
                case 'TItrain':
                    if ($Interval > 79) {
                        $TestBool = true;
                    }
                    break;
                case 'TIprobe':
                    if ($Interval > 39) {
                        $TestBool = true;
                    }
                    break;
                default:
                    die('Bad TaskId!');
                    break;
            }
            
            if ($TestBool) {
                // They are good to continue...
                
                // Increment State and send them on there way
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
                
                // SQL to update the Register table
                if ($TaskId=='AItrain') {
                    $Sql01 = "UPDATE Register SET State = $State, DateTime_AIinstr = '$DateTime_Instruct' WHERE SubjectId ='$SubjectId'";
                    
                } else if ($TaskId=='RC1') {
                    $Sql01 = "UPDATE Register SET State = $State, DateTime_RCinstr = '$DateTime_Instruct' WHERE SubjectId ='$SubjectId'";
                    
                } else if ($TaskId=='TItrain') {
                    $Sql01 = "UPDATE Register SET State = $State, DateTime_TIinstr = '$DateTime_Instruct' WHERE SubjectId ='$SubjectId'";
                    
                } else {
                    $Sql01 = "UPDATE Register SET State = $State WHERE SubjectId ='$SubjectId'";
                    
                }
		        
			    // Run and set the result:
			    if ($Conn->query($Sql01)===true) {
			        $Url = GetTargetUrl($Conn, $SubjectId);
                    $Result['TargetUrl'] = $Url;
			    } else {
			        $Conn->close();
			        die('Query Sql01 failed to execute successfully;');
			    }
            } else {
                // If they are not good to continue (i.e., they jumpped the gun)...
                
                // Get their State...
                $Sql02 = "SELECT * FROM Register WHERE SubjectId = '$SubjectId'";
	            $QueryRes02 = mysqli_query($Conn, $Sql02);
	            if($QueryRes02 === false) {
                    $Conn->close();
                    die("Query Sql02 failed to execute successfully!");
                } else {
                    while($Row = mysqli_fetch_assoc($QueryRes02)) {
                        $State = $Row["State"];
                    }
                }
                
                // Record this naughtiness
                $Sql03 = "INSERT INTO InstructNaughtiness (SubjectId, State, TaskId, DateTime_Naughty) VALUES ('$SubjectId', $State, '$TaskId', '$DateTime_Instruct')";
                if ($Conn->query($Sql03)===true) {
			        $Result['TargetUrl'] = "./Instruct.html?SubjectId=$SubjectId&TaskId=$TaskId&Warn=true#";
			    } else {
			        $Conn->close();
			        die('Query Sql03 failed to execute successfully;');
			    }
                
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