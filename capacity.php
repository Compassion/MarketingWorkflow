<?php

/* This deals with the management of capacity. Yo! */

require_once("core/config.php"); 
require_once("core/keys.php");
require_once("classes/Management.php");


// Gotta get the request class
$management = new Management();

// Show the request view
include("views/capacity.php");