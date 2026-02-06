<?php

// A function that returns the TargetUrl given only a SubjectId ...
// ... and an active SQL connection;
function GetTargetUrl($Dbc, $SId) {
    
    // Get the State and the TaskPerm
    $Sql = "SELECT * FROM Register WHERE SubjectId = '$SId'";
    $QueryRes = mysqli_query($Dbc, $Sql);
    if($QueryRes === false) {
        die("Sql failed to execute successfully!");
    } else {
        while($Row = mysqli_fetch_assoc($QueryRes)) {
            $State = $Row["State"];
            $TaskPerm = $Row["TaskPerm"];
        }
    }
    
    // Main if statement...
    if ($State == 0) {
		// Landed before
		return "./Consent.html?SubjectId=$SId#";
		
	} else if ($State == 1) {
		// Consent given
		return "./Register.html?SubjectId=$SId#";
		
	} else if ($State == 2) {
		// Already registered
		return "./MicTest.html?SubjectId=$SId#";
		
	} else if ($State < 0) {
		// Kicked off
		return "./Coventry.html?SubjectId=$SId#";
		
	} else if (($State > 2) && ($State < 15)) {
		// If they have done the mic test and have not yet finished all tasks ...
		// Convert task perm into the binary represention
		$TaskPerm = str_pad(decbin($TaskPerm),9,'0',STR_PAD_LEFT);
		
		// Get positions for each of the tasks
		$AIpos = substr($TaskPerm,0,3);
		$RCpos = substr($TaskPerm,3,3);
		$TIpos = substr($TaskPerm,6,3);
		
		// Convert the State into a task and stage indices
		$TaskIndex = floor(($State-3)/4);
		$StageIndex = ($State-3) % 4;
		
		// Test for AI task
		if (intval(substr($AIpos,$TaskIndex,1))) {
			if ($StageIndex == 0) {
				return "./Instruct.html?SubjectId=$SId&TaskId=AItrain#";
			} else if ($StageIndex == 1) {
				return "./AItrain.html?SubjectId=$SId#";
			} else if ($StageIndex == 2) {
				return "./Instruct.html?SubjectId=$SId&TaskId=AIprobe#";
			} else {
				return "./AIprobe.html?SubjectId=$SId#";
			}
			
		// Test for RC task
		} else if (intval(substr($RCpos,$TaskIndex,1))) {
			if ($StageIndex == 0) {
				return "./Instruct.html?SubjectId=$SId&TaskId=RC1#";
			} else if ($StageIndex == 2) {
				return "./Instruct.html?SubjectId=$SId&TaskId=RC2#";
			} else {
				if ((intval($SId,16)%2) xor ($StageIndex-1)) {
					return "./RCtask.html?SubjectId=$SId&StoryId=George#";
				} else {
					return "./RCtask.html?SubjectId=$SId&StoryId=Amy#";
				}
			}
			
		// Test for TI task
		} else {
			if ($StageIndex == 0) {
				return "./Instruct.html?SubjectId=$SId&TaskId=TItrain#";
			} else if ($StageIndex == 1) {
				return "./TItrain.html?SubjectId=$SId#";
			} else if ($StageIndex == 2) {
				return "./Instruct.html?SubjectId=$SId&TaskId=TIprobe#";
			} else {
				return "./TIprobe.html?SubjectId=$SId#";
			}
		}
		
	} else {
		// Finished all tasks (State=15)
		return "./Complete.html?SubjectId=$SId#";
	}
}

?>