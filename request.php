<?php

/* Making them requests Yo! */

require_once("core/config.php"); 
//require_once("classes/Request.php");
require_once("classes/Management.php");


// Gotta get the request class
$request = new Management();

// Show the request view
include("views/request.php");