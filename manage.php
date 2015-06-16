<?php

/* This deals with the management of requests. Yo! */

require_once("core/config.php"); 
require_once("core/keys.php");
require_once("classes/Management.php");


// Gotta get the request class
$management = new Management();

// Show the plan view
if(isset($_GET['status'])) {
    // status index exists
    $status = $_GET['status'];
    include("views/specific_status.php");
    
} elseif (isset($_GET['ajax'])) {
    include("core/messages.php");
    
} elseif(isset($_POST['audit'])) {
    // If an audit is posted create an audit log!
    
    if($_POST['audit'] == 'reassign') {
        $_POST['comment'] = 'Reassigned from ' . $_POST['currently_assigned'];
        
        $management->updateRequestAssigned($_POST['rq_id'], $_POST['reassign_to']);
    }
    
    $management->createAuditRecord($_POST);
    include("core/messages.php");
} else {
    include("views/manage.php");
}
// Show the request view
