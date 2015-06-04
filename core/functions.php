<?php
    function displayMenu($user_group) {
        
        switch ($user_group) {
            case "Admin":
                echo "<a href='#' class='list-group-item disabled'>Admin Tasks</a>";
                echo "<a href='capacity.php' class='list-group-item'>View Capacity</a>";
            case "Marketing Manager":
            case "Coms Manager":
            case "Creative Manager":
                //echo "<a href='manage.php' class='list-group-item' >Manage Requests </a>";
            case "Product Area 1":
            case "Product Area 2":
            case "Product Area 3":
                echo "<a href='manage.php' class='list-group-item'>Manage and Scope</a>";
            case "Workflow":
                echo "<a href='plan.php' class='list-group-item'>Workflow Planning </a>";
            case "Requester":
                echo "<a href='request.php' class='list-group-item'>Make Request</a>";
                break;
        }
        
    }
    function displayActions($user_group, $rq_id) {
        
        switch ($user_group) {
            case "Admin":
                echo "<a href='#' class='list-group-item disabled'>Admin Tasks</a>";
            case "Marketing Manager":
            case "Coms Manager":
            case "Creative Manager":
                echo "<a href='manage.php' class='list-group-item' >Manage Requests </a>";
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
    
    // Used for displaying the panel of scoped values
    function displayScopeAmounts($scope) {
        $noDisplay = array('scope_id','request_id', 'date_scoped', 'project_assigned', 'scoper');

        foreach($scope as $key => $val) { 
            if(!in_array($key, $noDisplay, true) ) {

                $lowerKey = substr($key, 6);
                $cutKey = ucfirst(substr($key, 6));

                echo "<div class='scoped'><div class='title'>";
                echo $cutKey;
                echo "</div><div class='number' name='" . $lowerKey ."' value='" . $val . "'>";
                echo $val;
                echo "</div></div>";
            }
        }
    }
    // Used for displaying the panel of the load bars
    function displayWorkloadBars($scope) {
        $noDisplay = array('scope_id','request_id', 'date_scoped', 'project_assigned', 'scoper');

        foreach($scope as $key => $val) { 
            if(!in_array($key, $noDisplay, true) ) {

                $lowerKey = substr($key, 6);
                $cutKey = ucfirst(substr($key, 6));

                echo '<div class="row"><div class="col-sm-2">' . $cutKey .'</div><div class="col-sm-10"><div class="progress">';
                echo '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;" id="bar-' . $lowerKey .'">0%</div>';
                echo '<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-width: 0em;" id="bar-' . $lowerKey . '-prop"></div></div></div></div>';
            }
        }
    }
?>