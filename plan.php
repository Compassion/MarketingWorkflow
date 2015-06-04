<?php

/* This deals with the management of requests. Yo! */

require_once("core/config.php"); 
require_once("core/keys.php");
require_once("classes/Management.php");


// Gotta get the request class
$management = new Management();

// Show the plan view
if(isset($_GET['pl_id'])) {
    // id index exists
    $scope = $_GET['pl_id'];
    include("views/plan-detail.php");
} else {
    include("views/plan.php");
}