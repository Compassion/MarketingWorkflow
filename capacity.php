<?php

/* This deals with the management of capacity. Yo! */

require_once("core/config.php"); 
require_once("core/keys.php");
require_once("classes/Management.php");


// Gotta get the request class
$management = new Management();

// Get workload
$load = $management->buildWorkArray();
$team = $management->getCapacityTeam('cap_product');

if(isset($_GET['team'])) {
    $team = $management->getCapacityTeam($_GET['team']);  
    displayCapacityMembers($team); 
} 
elseif(isset($_POST['day'])) {
    $management->updateCapacity($_POST);
    include("core/messages.php");
}
else { 
    // Show the request view
    include("views/capacity.php");
}
