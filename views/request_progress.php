<?php 
require_once('views/template/header.php');

$MM = "Marketing Manager";
$CrM = "Creative Manager";
$CoM = "Coms Manager";

$user = $_SESSION['user_group'];

if ($user == 'Admin') {
    $user = 'Product Area 1';
}
?>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h3>View request progress <small><a href="index.php" class="pull-right btn btn-default">Menu</a></small></h3>
                <hr />
                <p>These are all the requests current assigned to you.</p>
                <br />
                <div id="infoMessage"></div>
                <!-- register form -->
                
                <div class="taskList panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <?php 
                    $tasks = $management->viewTasksByAssigned($user); 

                    while($row = $tasks->fetch_assoc()) { 
                        $status = $row['status'];
                        
                        $scope = $management->getScopeRecord($row['request_id']);
                        $log = $management->getAuditLog($row['request_id']);
                        $subtasks = $management->getDeliverables($row['request_id']);
                        
                        if($scope == null) {
                            if($log->num_rows > 0) {
                                $rq_status = "Pre Approved";
                            }
                            else {
                                $rq_status = "New Request";
                            }
                        } else {
                            $MM_status = $management->returnAuditStatus($row['request_id'], $MM);
                            $CrM_status = $management->returnAuditStatus($row['request_id'], $CrM);
                            $CoM_status = $management->returnAuditStatus($row['request_id'], $CoM);
                        }
                        ?>

                    <div class="panel panel-default" id="pendingPanel<?=$row['request_id']?>">
                        <div class="panel-heading" role="tab" id="panel<?=$row['request_id']?>">
                          <h4 class="panel-title clearfix">
                            <a data-toggle="collapse" data-parent="#accordion" href="#heading<?=$row['request_id']?>" aria-expanded="false" aria-controls="heading<?=$row['request_id']?>">
                              <?=$row['request_name']?> <span class="small"><?=$row['date_due']?> </span>
                            </a>
                              <!-- Single button -->
                                <div class="btn-group pull-right">
                                <!-- STARS -->
                                <?php 
                                    if($scope == null) 
                                    {
                                        echo displayStatusButton($rq_status, null);
                                    }
                                    else {
                                        if($status == "query") {
                                            echo displayStatusButton('Re Scope', null);
                                        } else {
                                            echo displayStatusButton($CoM_status, $CoM); 
                                            echo displayStatusButton($CrM_status, $CrM); 
                                            echo displayStatusButton($MM_status, $MM); 
                                        }
                                    }
                                    
                                    ?>
                                <!-- // STARS -->
                                  <button type="button" class="btn btn-<?=$row['status']?> dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false" id="button<?=$row['request_id']?>">
                                    <?=$row['status']?> <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu" role="menu">
                                    <?php
                                        displayActions('Comment', $row['request_id']);
                                    ?>
                                  </ul>
                                </div>
                          </h4>
                        </div>
                        <div id="heading<?=$row['request_id']?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="panel<?=$row['request_id']?>">
                          <div class="panel-body">
                            <p><b>Date submitted:</b> <?=$row['date_created']?><br />
                               <b>Category:</b> <?=$row['request_category']?><br />
                              </p>
                            <p><?=$row['description']?></p>
                            
                            <?php if( 1 == 1 ) { ?>
                                <div class="scopeNumbers detail">
                                    <h5>Scoping Information</h5>
                                    
                                    <?php  displayScopeAmounts($scope); ?>
                                    <p class="clear">
                                        <br />
                                       <b>Scoped by:</b> <?=$scope['scoper']?><br />
                                       <b>On:</b> <?=$scope['date_scoped']?><br />
                                    </p>
                                </div>
                                <div class="subTasks detail">
                                    <h5>Subtasks</h5>
                                    <?php displaySutbtasks($subtasks); ?>
                                </div>
                                <div class="auditLog detail">
                                    <h5>History</h5>
                                    <?php displayAuditLog($log); ?>
                                </div>
                            <?php } ?>
                          </div>
                        </div>
                    </div>
                
                 <?php } ?>
                </div>
                
                <p class="text-center">
                <?php
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
                ?></p>

            </div>
        </div>
    </div>
    
    <?php $status = 'Progress';
          include('views/template/modal_comment.php'); ?>
     
    <script src="js/jquery-2.1.4.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <!-- Ajax Script -->
    <script type="text/javascript" src="js/actions.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
           addAjax('<?=$_SESSION['user_group']?>', '<?=$_SESSION['user_email']?>'); 
        });
    </script>
    <!-- // Ajax Script -->


<?php
require_once('views/template/footer.php'); ?>