<?php
    function displayMenu($user_group) {
        
        switch ($user_group) {
            case "Admin":
                echo "<a href='#' class='list-group-item disabled'>Admin Tasks</a>";
            case "Marketing Manager":
            case "Coms Manager":
            case "Creative Manager":
                echo "<a href='#' class='list-group-item disabled' >Manage Requests </a>";
            case "Product Area 1":
            case "Product Area 2":
            case "Product Area 3":
                echo "<a href='#' class='list-group-item disabled'>Manage Product Area</a>";
            case "Workflow":
                echo "<a href='#' class='list-group-item disabled'>Task Scoping</a>";
                echo "<a href='#' class='list-group-item disabled'>Workflow Planning </a>";
            case "Requester":
                echo "<a href='request.php' class='list-group-item'>Make Request</a>";
                break;
        }
        
    }
?>