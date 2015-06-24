<?php
    function displayMenu($user_group, $backlogCount, $queryCount, $scopeCount, $approveCount, $pendingCount) {
        if($queryCount == null) {
            $qCd = 'disabled'; 
        } else { $qCd = ''; }        
        if($scopeCount == null) {
            $sCd = 'disabled';
        } else { $sCd = ''; }    
        if($approveCount == null) {
            $aCd = 'disabled';
        } else { $aCd = ''; }
        if($pendingCount == null) {
            $pCd = 'disabled';
        } else { $pCd = ''; }
        if($backlogCount == null) {
            $bCd = 'disabled';
        } else { $bCd = ''; }
        
        $register = "<a href='register.php' class='list-group-item'><span class='glyphicon glyphicon-user'></span> Register New User</a>";
        
        $pending = "<a href='manage.php?status=pending' class='list-group-item $pCd' ><span class='glyphicon glyphicon-flag'></span>  Escalated  <span class='badge'>$pendingCount</span></a>";
        
        $scope = "<a href='manage.php?status=scoped' class='list-group-item $sCd'><span class='glyphicon glyphicon-star-empty'></span> Approval Required<span class='badge'>$scopeCount</span></a>";
        
        $backlog = "<a href='manage.php?status=backlog' class='list-group-item $bCd' ><span class='glyphicon glyphicon-list-alt'></span> Backlog<span class='badge'>$backlogCount</span></a>";
        
        $capacity = "<a href='capacity.php' class='list-group-item'><span class='glyphicon glyphicon-stats'></span> Capacity</a>";
        
        $syncDb = "<a href='#' class='list-group-item' id='syncPanel'><span class='glyphicon glyphicon-retweet'></span> Sync Asana <button class='btn btn-primary pull-right btn-xs' id='syncBtn'><span class='glyphicon glyphicon-retweet'></span> </button></a>";
        
        $request = "<a href='request.php' class='list-group-item'><span class='glyphicon glyphicon-certificate'></span> New Request</a>";
        
        $plan = "<a href='manage.php?status=approved' class='list-group-item $aCd'><span class='glyphicon glyphicon glyphicon-calendar'></span> Workflow <span class='badge'>$approveCount</span></a>";
        
        $query = "<a href='manage.php?status=query' class='list-group-item $qCd'><span class='glyphicon glyphicon-inbox'></span> Requests <span class='badge'>$queryCount</span></a>";
        
        $progress = "<a href='progress.php' class='list-group-item'><span class='glyphicon glyphicon-th-list'></span> My Requests</a>";
        
        $reset_link = '<div class="list-group-item clear"><span class="glyphicon glyphicon-inbox"></span> Reset Password <div class="input-group input-group-sm pull-right col-xs-6">
        <input type="text" class="form-control" placeholder="Username" id="usernameInput">
            <span class="input-group-btn">
            <button class="btn btn-default" type="button" id="linkGen">Go!</button>
        </span>
    </div> <br /><span id="pw_here"></span></div>';
   
        switch ($user_group) {
            case "Admin":
                //echo "<a href='#' class='list-group-item disabled'>Admin Tasks</a>";      
                echo $request;
            
                echo $progress;
                echo "<h3>Queues</h3>";
                echo $query . $pending . $scope . $plan . $backlog;
                
                
                echo "<h3>Statuses</h3>";
                echo "<a href='manage.php?status=query' class='list-group-item'><span class='glyphicon glyphicon-inbox'></span> View all queries</a>";
                echo "<a href='manage.php?status=pending' class='list-group-item'><span class='glyphicon glyphicon-inbox'></span> View all pending</a>";
                echo "<a href='manage.php?status=declined' class='list-group-item'><span class='glyphicon glyphicon-inbox'></span> View all declined</a>";
                echo "<a href='manage.php?status=scoped' class='list-group-item'><span class='glyphicon glyphicon-inbox'></span> View all scoped</a>";
            
            
                echo "<h3>Admin</h3>";
                echo $register;
                echo $capacity;
                echo $syncDb;
                echo $reset_link;
                break;
            
            case "Marketing Manager":
            case "Coms Manager":
            case "Creative Manager":
                //echo "<a href='#' class='list-group-item disabled'>-- Managers --</a>";
                echo $query;
                echo $pending;
                echo $scope;
                echo $capacity;
                echo $backlog;
                echo $request;
                break;
            
            case "Workflow":
                //echo "<a href='#' class='list-group-item disabled'>-- Workflow --</a>";
                echo $plan;
                echo $capacity;
                echo $syncDb;
                echo $request;
                break;
            
            default:
                //break;
            // Product Managers
            case "Product Area 1":
            case "Product Area 2":
            case "Product Area 3":
                //echo "<a href='#' class='list-group-item disabled'>-- PMs --</a>";
                echo $query;
                echo $progress;
                echo $request;
                break;
            
            case "Requester":
                //echo "<a href='#' class='list-group-item disabled'>-- Requester --</a>";
                echo $request;
                break;
        }
        
    }

    function displayNavMenu($user_group, $backlogCount, $queryCount, $scopeCount, $approveCount, $pendingCount) {
        if($queryCount == null) {
            $qCd = 'disabled'; 
        } else { $qCd = ''; }        
        if($scopeCount == null) {
            $sCd = 'disabled';
        } else { $sCd = ''; }    
        if($approveCount == null) {
            $aCd = 'disabled';
        } else { $aCd = ''; }
        if($pendingCount == null) {
            $pCd = 'disabled';
        } else { $pCd = ''; }
        if($backlogCount == null) {
            $bCd = 'disabled';
        } else { $bCd = ''; }
        
        $register = "<li><a href='register.php' class=''><span class='glyphicon glyphicon-user'></span> Register New User</a></li>";
        
        $pending = "<li><a href='manage.php?status=pending' class=' $pCd' ><span class='glyphicon glyphicon-flag'></span>  Escalated  <span class='badge'>$pendingCount</span></a></li>";
        
        $scope = "<li><a href='manage.php?status=scoped' class=' $sCd'><span class='glyphicon glyphicon-star-empty'></span> Approval Req <span class='badge'>$scopeCount</span></a></li>";
        
        $backlog = "<li><a href='manage.php?status=backlog' class=' $bCd' ><span class='glyphicon glyphicon-list-alt'></span> Backlog <span class='badge'>$backlogCount</span></a></li>";
        
        $capacity = "<li><a href='capacity.php' class=''><span class='glyphicon glyphicon-stats'></span> Capacity</a></li>";
        
        $syncDb = "<li><a href='#' class='' id='syncPanel'><span class='glyphicon glyphicon-retweet'></span> Sync Asana <button class='btn btn-primary pull-right btn-xs' id='syncBtn'><span class='glyphicon glyphicon-retweet'></span> </button></a></li>";
        
        $request = "<li><a href='request.php' class=''><span class='glyphicon glyphicon-certificate'></span> New </a></li>";
        
        $plan = "<li><a href='manage.php?status=approved' class=' $aCd'><span class='glyphicon glyphicon glyphicon-calendar'></span> Workflow <span class='badge'>$approveCount</span></a></li>";
        
        $query = "<li><a href='manage.php?status=query' class=' $qCd'><span class='glyphicon glyphicon-inbox'></span> Requests <span class='badge'>$queryCount</span></a></li>";
        
        $progress = "<li><a href='progress.php' class=''><span class='glyphicon glyphicon-th-list'></span> My Requests</a></li>";
        
        $diver = "<li>|</li>";
       
        switch ($user_group) {
            case "Admin":
                //echo "<a href='#' class='list-group-item disabled'>Admin Tasks</a>";      
                echo $request;
            
                echo $progress;
                echo $query . $pending . $scope . $plan . $backlog;
                break;
            
            case "Marketing Manager":
            case "Coms Manager":
            case "Creative Manager":
                //echo "<a href='#' class='list-group-item disabled'>-- Managers --</a>";
                echo $request;
            
                echo $query;
                echo $pending;
                echo $scope;
                echo $capacity;
                echo $backlog;
                break;
            
            case "Workflow":
                //echo "<a href='#' class='list-group-item disabled'>-- Workflow --</a>";
                echo $request;
                echo $plan;
                echo $capacity;
                break;
            
            default:
                //break;
            // Product Managers
            case "Product Area 1":
            case "Product Area 2":
            case "Product Area 3":
                //echo "<a href='#' class='list-group-item disabled'>-- PMs --</a>";
                echo $request;
                echo $query;
                echo $progress;
                break;
            
            case "Requester":
                //echo "<a href='#' class='list-group-item disabled'>-- Requester --</a>";
                echo $request;
                break;
        }
        
    }

    function displayActions($status, $id) {
        // actions
        
        $scope = '<li><a href="scope.php?rq_id='.$id.'" class="scope" id="scope'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-arrow-right"></span> Scope</a></li>';
        
        $rescope = '<li><a href="#" class="rescope" id="rescope'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-repeat"></span> Request Re-scope</a></li>';
        
        $escalate = '<li><a href="scope.php?rq_id='.$id.'" class="pending" id="pending'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-flag"></span> Escalate</a></li>';
        
        $approve = '<li><a href="#" class="approve" id="approve'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-ok"></span> Approve</a></li>';
        
        $pre_approve = '<li><a href="#" class="preapprove" id="preapprove'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-ok"></span> Pre-approve for Scoping</a></li>';
        
        $plan = '<li><a href="plan.php?pl_id='.$id.'" class="plan" id="plan'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon glyphicon-calendar"></span> Plan</a></li>';
        $divider = '<li class="divider"></li>';
        
        $backlog = '<li><a href="#" class="backlog" id="backlog'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-list-alt"></span> Send to Backlog</a></li>';
        
        $decline = '<li><a href="#" class="decline" id="decline'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-remove"></span> Decline request</a></li>';
        
        $chili_decline = '<li><a href="#" class="chilidecline" id="chilidecline'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-log-out"></span> Use Chili</a></li>';
        
        $delete = '<li><a href="#" class="delete" id="delete'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-trash"></span> Remove from Backlog</a></li>';
        
        $move_to_plan = '<li><a href="#" class="re_approve" id="re_approve'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-calendar"></span> Move to plan</a></li>';
        
        $comment = '<li><a href="#" class="comment" id="comment'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-comment"></span> Leave Comment</a></li>';
        
        $reassign = '<li><a href="#" class="reassign" id="reassign'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-transfer"></span> Reassign task</a></li>';
        
        switch ($status) {
            case "Query":
                echo $scope . $escalate  .$reassign . $divider .$comment . $divider . $chili_decline;
                break;
            
            case "Pending":
                echo $pre_approve .$reassign .$divider .$comment .$divider .$decline;
                break;
            
            case "Scoped":
                echo $approve .$rescope  .$divider .$comment .$divider .$decline;
                break;
            
            case "Approved":
                echo $plan . $comment .$divider . $backlog;
                break;
            
            case "Backlog":
                echo $move_to_plan . $rescope  .$divider . $comment . $divider . $delete;
                break;
            case "Comment":
                echo $comment;
                break;
        }
        
    }

    function displayActionsInLine($status, $id) {
        // actions
        
        $scope = '<a href="scope.php?rq_id='.$id.'" class="scope btn btn-primary  btn-xs" id="scope'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-arrow-right"></span> Scope</a>';
        
        $rescope = '<a href="#" class="rescope btn btn-warning  btn-xs" id="rescope'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-repeat"></span> Rescope</a>';
        
        $escalate = '<a href="scope.php?rq_id='.$id.'" class="pending btn btn-warning  btn-xs" id="pending'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-flag"></span> Escalate</a>';
        
        $approve = '<a href="#" class="approve btn btn-success btn-xs" id="approve'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-ok"></span> Approve</a>';
        
        $pre_approve = '<a href="#" class="preapprove btn btn-success  btn-xs" id="preapprove'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-ok"></span> Pre-approve for Scoping</a>';
        
        $plan = '<a href="plan.php?pl_id='.$id.'" class="plan btn btn-primary  btn-xs" id="plan'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon glyphicon-calendar"></span> Plan</a>';
        $divider = '';
        
        $backlog = '<a href="#" class="backlog btn btn-danger  btn-xs" id="backlog'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-list-alt"></span> Send to Backlog</a>';
        
        $decline = '<a href="#" class="decline btn btn-danger  btn-xs" id="decline'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-remove"></span> Decline</a>';
        
        $chili_decline = '<a href="#" class="chilidecline btn btn-danger  btn-xs" id="chilidecline'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-log-out"></span> Use Chili</a>';
        
        $delete = '<a href="#" class="delete btn btn-danger  btn-xs" id="delete'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-trash"></span> Remove from Backlog</a>';
        
        $move_to_plan = '<a href="#" class="re_approve btn btn-info  btn-xs" id="re_approve'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-calendar"></span> Move to plan</a>';
        
        $comment = '<a href="#" class="comment btn btn-primary  btn-xs" id="comment'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-comment"></span> Leave Comment</a>';
        
        $reassign = '<a href="#" class="reassign btn btn-info  btn-xs" id="reassign'.$id.'" data-val="'.$id.'"><span class="glyphicon glyphicon-transfer"></span> Reassign task</a>';
        
        $groupStart = '<div class="btn-group" role="group">';
        $groupStartRight = '<div class="btn-group pull-right" role="group">';
        $groupEnd =  '</div>';
        
        switch ($status) {
            case "Query":
            
                echo $groupStart;
                echo $scope;
                echo $groupEnd;
                echo $groupStart;
                echo $escalate .$reassign . $chili_decline;
            
                echo $groupEnd;
                echo $groupStartRight;
                    echo $comment;
                echo $groupEnd;
                break;
            
            case "Pending":
                echo $groupStart;
                echo $pre_approve;
                echo $groupEnd;
                echo $groupStart;
                echo $reassign .$decline;
                echo $groupEnd;
                echo $groupStartRight;
                    echo $comment;
                echo $groupEnd;
                break;
            
            case "Scoped":
                echo $groupStart;
                    echo $approve .$decline;
                    echo $rescope;
                echo $groupEnd;
                echo $groupStartRight;
                    echo $comment;
                echo $groupEnd;
            
                break;
            
            case "Approved":
                echo $groupStart;
                echo $plan;
                echo $groupEnd;
            
                echo $groupStart;
                echo $backlog;
                echo $groupEnd;
            
                echo $groupStartRight;
                    echo $comment;
                echo $groupEnd;
                break;
            
            case "Backlog":
                echo $groupStart;
                echo $move_to_plan . $rescope;
                echo $groupEnd;
            
                echo $groupStart;
                echo $delete;
                echo $groupEnd;
            
                echo $groupStartRight;
                    echo $comment;
                echo $groupEnd;
                break;
            
            case "Comment":
                echo $groupStartRight;
                    echo $comment;
                echo $groupEnd;
                break;
        }
        
    }

    // Used for displaying the panel of scoped values
    function displayScopeAmountSquares($scope) {
        if($scope == null) {
            return false;
        }
        
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
    // Used for displaying the panel of scoped values
    function displayScopeAmounts($scope) {
        if($scope == null) {
            return false;
        }
        
        $noDisplay = array('scope_id','request_id', 'date_scoped', 'project_assigned', 'scoper');

        foreach($scope as $key => $val) { 
            if(!in_array($key, $noDisplay, true) ) {

                $lowerKey = substr($key, 6);
                $cutKey = ucfirst(substr($key, 6));
                
                echo "<div class='row'><div class='col-xs-6 bold'>";
                echo $cutKey;
                echo "</div><div class='col-xs-6'><span class='number' name='" . $lowerKey ."' value='" . $val . "'>";
                echo $val;
                echo "</span> <small>hours</small></div></div>";
            }
        }
    }

    // Cut an email to a username
    function cutEmail($email) {
            $person = explode('@', $email);
            $person = $person[0];
        
        return $person;
    }
    function transformDate($date) {
        $newDate = date("D j F\, Y", strtotime($date)); 
        return $newDate;
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
        $title = "";
        $disabled = 'disabled';
        
        switch ($who) {
            case "Marketing Manager":
                $who_id = 'status_MM';
                $title = 'Marketing Manager';
                $disabled = '';
                break;
            case "Coms Manager":
                $who_id = 'status_CoM';
                $title = 'Comms Manager';
                $disabled = '';
                break;
            case "Creative Manager":
                $who_id = 'status_CrM';
                $title = 'Creative Manager';
                $disabled = '';
                break;
            default:
                $who_id = 'status_Unk';
                break;
        }
        switch ($status) {
            case "Awaiting Approval":
                $btn_icon = "glyphicon-minus";
                $btn_class = "btn-default";
                $text = "";
                break;
            
            case "Scope Approved":
                $btn_icon = "glyphicon-ok";
                $btn_class = "btn-success";
                $text = "";
                break;
            
            case "Scope Declined":
                $btn_icon = "glyphicon-remove";
                $btn_class = "btn-danger";
                $text = "";
                break;
            
            case "New Request":
                $btn_icon = "glyphicon-certificate";
                $btn_class = "btn-warning";
                $text = "New Request";
                break;
            
            case "Pre Approved":
                $btn_icon = "glyphicon-flag";
                $btn_class = "btn-success";
                $text = "Pre-approved";
                break;
            
            case "pending":
                $btn_icon = "glyphicon-flag";
                $btn_class = "btn-info";
                $text = "Escalated";
                break;
            
            case "Re Scope":
                $btn_icon = "glyphicon-repeat";
                $btn_class = "btn-info";
                $text = "Re-scope";
                break;
            
            case "Complete":
                $btn_icon = "glyphicon-ok";
                $btn_class = "btn-primary";
                $text = "Complete";
                break;
            
            case "Backlog":
                $btn_icon = "glyphicon-list-alt";
                $btn_class = "btn-danger";
                $text = "Backlog";
                break;
            
            case "Declined":
                $btn_icon = "glyphicon-remove";
                $btn_class = "btn-danger";
                $text = "Declined";
                break;
            
            case "Approvals":
                $btn_icon = "glyphicon-pause";
                $btn_class = "btn-default";
                $text = "Awaiting Approvals";
                break;
            
            default: 
                $btn_icon = "glyphicon-question-sign";
                $btn_class = "btn-info";
                $text = "";
                break;
        }
        
        $button = '<button type="button" class="btn btn-default '.$btn_class.' btn-xs '.$disabled.'" id="'. $who_id.'" data-toggle="tooltip" data-placement="top" title="' . $title .'" ><span class="glyphicon '. $btn_icon.'"></span> '.$text.'</button>';
        
        return $button;
        
    }

    function displayAuditLog($audit_log) {
        if($audit_log->num_rows == null) {
            return false;
        }
        echo '<h4>History</h4>';
        echo '<table class="capacity table table-hover">
                    <thead>
                        <tr>
                            <th>Person</th>
                            <th>Status</th>
                            <th>Comment</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>';
                        
        while($row = $audit_log->fetch_assoc()) {
            $person = explode('@', $row['audit_person']);
            $person = $person[0];
            
            
            $date = explode("-", $row['audit_date']);
            $date = $date[2] . "/" . $date[1];
            
            $comment = explode(" ", $row['audit_status']);
            
            if( isset($comment[1]) ) {
                if ($comment[1] == 'comment') {
                    $tr = "<tr class='info'>";
                } 
                elseif($comment[0] == 'Scope') {
                    if($comment[1] == 'Approved') {
                        $tr = "<tr class='success'>";
                    } elseif ($comment[1] == 'Declined') {
                        $tr = "<tr class='danger'>";
                    }
                } 
                else {
                    $tr = "<tr>";
                }
            } else {
                $tr = "<tr>";
            }

            echo $tr;
            echo "<td> " .$person ."</td>";
            echo "<td> " .$row['audit_status'] ."</td>";
            echo "<td> " .$row['audit_comment'] ."</td>";
            echo "<td> " .$date ."</td>";
            echo "</tr>";
        }
        echo '</tbody></table>';
    }

    function displaySutbtasks($subtasks) {
        if($subtasks->num_rows == null) {
            return false;
        }
        echo '<h4>Subtasks</h4>';
        echo '<table class="capacity table table-hover">
                    <thead>
                        <tr>
                            <th class="left-padding">Name</th>
                            <th>Description</th>
                            <th>Due</th>
                        </tr>
                    </thead>
                    <tbody>';
        while($row = $subtasks->fetch_assoc()) {
            $date = explode("-", $row['st_date_required']);
            $date = $date[2] . "/" . $date[1];
            
            echo "<tr>";
            echo "<td class='left-padding'> " .$row['st_name'] ."</td>";
            echo "<td> " .$row['st_comment'] ."</td>";
            echo "<td> " .$date ."</td>";
            echo "</tr>";
        }
        echo '</tbody></table>';
    }
?>