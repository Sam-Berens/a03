<?php
header('Content-Type: application/json');
$Result = array();
if (!isset($_POST['FunctionCall']) ) {
    $Result['Error'] = 'No function name!';
}
if (!isset($_POST['PID'])) {
    $Result['Error'] = 'No function arguments!';
}
if(!isset($Result['Error'])) {
    switch($_POST['FunctionCall']) {
        case 'GenSubjectId':
            $RawHash = md5($_POST['PID']);
            $Result['SubjectId'] = substr($RawHash, -8);
            break;
           
        default:
            $Result['Error'] = 'Bad function call!';
            break;
    }
}
echo json_encode($Result);
?>