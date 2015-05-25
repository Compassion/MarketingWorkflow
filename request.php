<?php

/* Making them requests Yo! */

require_once("core/config.php"); 
require_once("classes/Request.php");


// Gotta get the request class
$request = new Request();

// Show the request view
include("views/request.php");