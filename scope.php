<?php

/* This deals with the scoping of requests. Yo! */

require_once("core/config.php"); 
require_once("core/keys.php");
require_once("classes/Management.php");

// Gotta get the request class
$management = new Management();

// If scope record is ajaxed through do all this stuff
if(isset($_POST['scope'])) {
    // id index exists
    $scope = $_POST['scope'];
    
    $audit = array();
    $audit['rq_id'] = $_POST['request_id'];
    $audit['status'] = 'Awaiting Approval';
    $audit['creator'] = $_SESSION['user_email'];
    
    // Create scope record
    $management->createScopeRecord($_POST, $audit);
    
    // Delete previous deliverables
    $management->deleteDeliverables($_POST['request_id']);
    
    $audit['status'] = 'Previous delverables deleted';
    $management->createAuditRecord($audit);
    
    // Create deliverables
    $scope_id = $management->getScopeRecord($_POST['request_id']);
    $scope_id = $scope_id['scope_id'];
    
    $forms = array();
    
    foreach($_POST as $key => $value) {
      if(preg_match('@^st_@', $key)) {

        $exp = explode('-', $key);

        $formName = "form".$exp[1];
        ${$formName}[$exp[0]] = $value;
        $forms[$formName] = ${$formName}; 
      }
    }
    
    foreach($forms as $subtask) {
        $subtask['scope'] = 'create subtask';
        $subtask['scope_id'] = $scope_id;
        $subtask['request_id'] = $_POST['request_id'];
        $subtask['delete_prev'] = "true";
        
        // No name no subtask
        if($subtask['st_name'] != '') {
            $management->createDeliverable($subtask);
        }
    }
    
    $audit['status'] = 'New deliverables created';
    $management->createAuditRecord($audit);
    
    include("core/messages.php");
    
    echo "<a href='manage.php?status=query' class='btn btn-primary'>Back to requests</a>";
    
} elseif(isset($_GET['rq_id'])) {
    // id index exists
    $id = $_GET['rq_id'];
    
    include("views/scope.php");
    
} else {
    header("Location: manage.php?status=query");
    die();
}
