<?php

/* This deals with the scoping of requests. Yo! */

require_once("core/config.php"); 
require_once("core/keys.php");
require_once("classes/Management.php");


if(isset($_GET['rq_id'])) {
    // id index exists
    $id = $_GET['rq_id'];
} else {
    header("Location: manage.php");
    die();
}


// Gotta get the request class
$management = new Management();


if(isset($_POST['scope'])) {
    // id index exists
    $scope = $_POST['scope'];
    include("views/scope-recieved.php");
} else {
    include("views/scope.php");
}
// Show the request view
