<?php

/* This deals with the management of requests. Yo! */

require_once("core/config.php"); 
require_once("core/keys.php");
require_once("classes/Management.php");


// Gotta get the request class
$management = new Management();

if (isset($_GET['ajax'])) {
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
} else {
    include("views/approve.php");
}
// Show the approval view
