<?php

/* This deals with the scoping of requests. Yo! */

require_once("core/config.php"); 
require_once("core/keys.php");
require_once("classes/Management.php");


// Gotta get the request class
$management = new Management();
/*
echo "POST";
var_dump($_POST);
echo "<hr />";

echo "Team name is ".$_POST['name'] . "<br />";
echo "Team capacity per day is ".$_POST['day'] . "<br />";
echo "Team members are ";

foreach($_POST['members'] as $member=>$info) {
    $info['name'] = $member;
    $info['team'] = $_POST['name'];
    echo $member ." "; 
    var_dump($info);
}
echo "<br /><br />";

foreach($_POST['members'] as $member=>$info) {
    echo $member ." works " .$info['hours'] ." hours per week. <br />"; 
    echo "They work ";
    
    foreach($info['days'] as $day) {
        if($day != 'sat') {
            echo $day .", ";
        }
    }
    echo " each week. <br />";
} */

$management->updateCapacity($_POST);
/*
$audit = array();
$audit['rq_id'] = $_POST['request_id'];
$audit['status'] = 'Awaiting Approval';
$audit['creator'] = $_SESSION['user_email'];

$create_audit = $management->createAuditRecord($_POST);
*/
//$scope_id = $management->getScopeRecord($_POST['request_id']);
//$scope_id = $scope_id['scope_id'];
/*
var_dump($scope_id);

$forms = array();

foreach($_POST as $key => $value) {
  if(preg_match('@^st_@', $key)) {
    $books[$key] = $value;
      
    //explode('-', $key);
    //  var_dump($key);
    
    $exp = explode('-', $key);
      
    $formName = "form".$exp[1];
    ${$formName}[$exp[0]] = $value;
    $forms[$formName] = ${$formName}; 
  }
}

//var_dump($books);
var_dump($forms);

foreach($forms as $subtask) {
    $subtask['scope'] = 'create subtask';
    $subtask['scope_id'] = $scope_id;
    $subtask['request_id'] = $_POST['request_id'];
    var_dump($subtask);
    
    $management->createDeliverable($subtask);
}
*/
// show potential errors / feedback (from registration object)
$alertTop_Danger = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
$alertTop_Success = '<div class="alert alert-success alert-dismissible fade in" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
$alertEnd = '</div>';

if (isset($management)) {
    if ($management->errors) {
        foreach ($management->errors as $error) {
            echo $alertTop_Danger;
            echo $error;
            echo $alertEnd;
        }
    }
    if ($management->messages) {
        foreach ($management->messages as $message) {
            echo $alertTop_Success;
            echo $message;
            echo $alertEnd;
        }
    }
} 