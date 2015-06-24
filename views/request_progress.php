<?php 
require_once('views/template/header.php');
require_once('views/template/nav.php');

$MM = "Marketing Manager";
$CrM = "Creative Manager";
$CoM = "Coms Manager";

$user = $_SESSION['user_group'];
/*
if ($user == 'Admin') {
    $user = 'Product Area 1';
}*/
?>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h3>View request progress</h3>
                <hr />
                <p>These are all the requests current assigned to you.</p>
                <div id="infoMessage"></div>
                <!-- register form -->
                
                <div class="taskList panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <?php 
                    $tasks = $management->viewTasksByAssigned($user); 
                    $status = '';
                    $newStatus = 'init';
                    while($row = $tasks->fetch_assoc()) { 
                        $status = $row['status'];
                        
                        if($status != $newStatus) {
                            $newStatus = $status;
                            echo "<h3>$status</h3>";
                        } 
                        
                        $scope = $management->getScopeRecord($row['request_id']);
                        $log = $management->getAuditLog($row['request_id']);
                        $subtasks = $management->getDeliverables($row['request_id']);
                        
                        if($scope == null) {
                            if($status == 'Pending') {
                                $rq_status = 'pending';
                            }
                            elseif($status == 'Declined') {
                                $rq_status = 'Declined';
                            }
                            elseif($log->num_rows > 0) {
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
                              <?=$row['request_name']?> <span class="small"><?=transformDate($row['date_due']);?> </span>
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
                                </div>
                            </a>
                          </h4>
                        </div>
                        <div id="heading<?=$row['request_id']?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="panel<?=$row['request_id']?>" data-assigned="<?=$row['request_assigned']?>">
                          <div class="panel-footer">
                            <div class="btn-toolbar" role="toolbar">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-<?=$row['status']?> dropdown-toggle btn btn-xs" data-toggle="dropdown" aria-expanded="false" id="button<?=$row['request_id']?>">Status:  <?=$row['status']?>
                                  </button>
                                </div>
                              <!--<ul class="dropdown-menu" role="menu">-->
                                <?php
                                    displayActionsInLine('Comment', $row['request_id']);
                                ?>
                              <!--</ul>-->
                            </div>
                          </div>
                          <div class="panel-body">
                              
                            <?php /*<p><b>Time submitted:</b> <?=$row['date_created']?><br />
                               <b>Category:</b> <?=$row['request_category']?><br />
                               <b>Assigned to:</b> <?=$row['request_assigned']?><br />
                               <b>Due by:</b> <?=$row['date_due']?><br />
                              </p> */ ?>
                            <div class="row">
                                <div class="request-data col-xs-5">
                                    <div class="row">
                                        <div class="col-xs-6 bold">Request Date</div>
                                        <div class="col-xs-6"> <?=transformDate($row['date_created'])?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6 bold">Due</div>
                                        <div class="col-xs-6"> <?=transformDate($row['date_due'])?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6 bold">Category</div>
                                        <div class="col-xs-6"> <?=$row['request_category']?></div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                   <p><b>Description</b><br /><?=$row['description']?></p>
                                </div>
                            </div>  
                            
                            <?php if( $status == "scoped" || $status == "approved" || $status == "backlog"  || $status == "pending" || $status == "query" ) { 
                                if($scope == null) {
                                }
                                else {
                              ?>
                                <div class="scopeNumbers row">
                                    <div class="col-md-12"><h4>Scope</h4></div>
                                    
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="scope-data col-xs-5">
                                                <div class="row">
                                                    <div class="col-xs-6 bold">Scoped by</div>
                                                    <div class="col-xs-6"><?=cutEmail($scope['scoper']);?></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-6 bold">On</div>
                                                    <div class="col-xs-6">
                                                        <?=transformDate($scope['date_scoped']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 col-xs-5">
                                                <div class="row">
                                                    <?php   
                                                           echo '<div class="col-xs-12">';
                                                           displayScopeAmounts($scope);
                                                           echo '</div>';
                                                        ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                                <div class="subTasks detail">
                                    <?php displaySutbtasks($subtasks); ?>
                                </div>
                                <div class="auditLog detail">
                                    <?php displayAuditLog($log); ?>
                                </div>
                            <?php 
                              } ?>
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
            $(function () {
              $('[data-toggle="tooltip"]').tooltip()
            });
        });
    </script>
    <!-- // Ajax Script -->


<?php
require_once('views/template/footer.php'); ?>