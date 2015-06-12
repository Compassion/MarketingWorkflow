<?php
    function displayMenu($user_group, $backlogCount, $queryCount, $scopeCount, $approveCount, $pendingCount) {
        switch ($user_group) {
            case "Admin":
                echo "<a href='#' class='list-group-item disabled'>Admin Tasks</a>";
                echo "<a href='register.php' class='list-group-item'>Register new user</a>";
                echo "<a href='capacity.php' class='list-group-item'>View Capacity</a>";
                echo "<a href='manage.php?status=query' class='list-group-item'>View all queries</a>";
                echo "<a href='manage.php?status=pending' class='list-group-item'>View all pending</a>";
                echo "<a href='manage.php?status=declined' class='list-group-item'>View all declined</a>";
                echo "<a href='manage.php?status=scoped' class='list-group-item'>View all scoped</a>";
                echo "<a href='#' class='list-group-item' id='syncPanel'>Sync task database <button class='btn btn-primary pull-right btn-xs' id='syncBtn'><span class='glyphicon glyphicon-retweet'></span> </button></a>";
                echo "<a href='#' class='list-group-item disabled'>---</a>";
            case "Marketing Manager":
            case "Coms Manager":
            case "Creative Manager":
                echo "<a href='manage.php?status=backlog' class='list-group-item' >Backlog (Backlog) <span class='badge'>$backlogCount</span></a>";
                echo "<a href='approve.php?status=pending' class='list-group-item' >Unsure Q (Pending)  <span class='badge'>$pendingCount</span></a>";
            case "Product Area 1":
            case "Product Area 2":
            case "Product Area 3":
                echo "<a href='manage.php' class='list-group-item'>New Requests (Query Q, Query) <span class='badge'>$queryCount</span></a>";
            case "Workflow":
                echo "<a href='plan.php' class='list-group-item'>Workflow Planning (Approved)<span class='badge'>$approveCount</span></a>";
                echo "<a href='approve.php' class='list-group-item'>Requires Management Approval (Scoped)<span class='badge'>$scopeCount</span></a>";
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
    
    // Work days
    function calculateWorkDays($from, $to) {
        $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)
        $holidayDays = ['*-12-25', '*-01-01', '2013-12-23']; # variable and fixed holidays

        $from = new DateTime($from);
        $to = new DateTime($to);
        $to->modify('+1 day');
        $interval = new DateInterval('P1D');
        $periods = new DatePeriod($from, $interval, $to);

        $days = 0;
        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) continue;
            if (in_array($period->format('Y-m-d'), $holidayDays)) continue;
            if (in_array($period->format('*-m-d'), $holidayDays)) continue;
            $days++;
        }
        
        return $days;
    }

    function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }


    // Display Status Count
    function returnStatusCount($status, $user_group) {
        // Establish connection
        $db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $errors = array();
        // Check DB
        if (!$db_connection->set_charset("utf8")) {
            $errors[] = $db_connection->error;
        }
        
        // If no errors grab all the tasks with the right status
        if (!$db_connection->connect_errno) {
            
            if ( $user_group == 'Admin' ) {
                $sql = "SELECT COUNT(`status`) FROM requests WHERE `status` = '$status'";
            }
            elseif($user_group != 'none' ) {
               $sql = "SELECT COUNT(`status`) FROM requests WHERE `status` = '$status' AND `request_assigned` = '$user_group'"; 
            } 
            else {
                $sql = "SELECT COUNT(`status`) FROM requests WHERE `status` = '$status'"; 
            }
            
            
            $count = $db_connection->query($sql);
            
            $result = $count->fetch_assoc()['COUNT(`status`)'];
            
            if($result == 0) {
                $result = null;
            }
            
            return $result;
        }
    }


    function displayStatusButton($status, $who) {
        switch ($who) {
            case "Marketing Manager":
                $who_id = 'status_MM';
                break;
            case "Coms Manager":
                $who_id = 'status_CoM';
                break;
            case "Creative Manager":
                $who_id = 'status_CrM';
                break;
            default:
                $who_id = 'status_Unk';
                break;
        }
        switch ($status) {
            case "Awaiting Approval":
                $btn_icon = "glyphicon-star-empty";
                $btn_class = "btn-warning";
                break;
            
            case "Scope Approved":
                $btn_icon = "glyphicon-star";
                $btn_class = "btn-success";
                break;
            
            case "Scope Declined":
                $btn_icon = "glyphicon-remove";
                $btn_class = "btn-danger";
                break;
            default: 
                $btn_icon = "glyphicon-question-sign";
                $btn_class = "btn-info";
                break;
        }
        
        $button = '<button type="button" class="btn btn-default ' .$btn_class .' btn-xs disabled" id="'. $who_id.'"><span class="glyphicon '. $btn_icon.'"></span></button>';
        
        return $button;
        
    }
 


?>