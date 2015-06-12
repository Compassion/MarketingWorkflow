<?php 
require_once('views/template/header.php');

$MM = "Marketing Manager";
$CrM = "Creative Manager";
$CoM = "Coms Manager";
$status = "Scoped";

?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h3>Approve scoped requests <small><a href="index.php" class="pull-right btn btn-default">Menu</a></small></h3>
                <hr />
                
                <br />
                <div id="infoMessage"></div>
                <!-- register form -->
                
                <div class="taskList panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <?php 
                    $tasks = $management->viewTasks("Scoped"); 

                    while($row = $tasks->fetch_assoc()) { 
                        $scope = $management->getScopeRecord($row['request_id']);
                        
                        $MM_status = $management->returnAuditStatus($row['request_id'], $MM);
                        $CrM_status = $management->returnAuditStatus($row['request_id'], $CrM);
                        $CoM_status = $management->returnAuditStatus($row['request_id'], $CoM);
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
                                    echo displayStatusButton($CoM_status, $CoM); 
                                    echo displayStatusButton($CrM_status, $CrM); 
                                    echo displayStatusButton($MM_status, $MM); 
                                    ?>
                                <!-- // STARS -->
                                    
                                  <button type="button" class="btn btn-<?=$row['status']?> dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false" id="button<?=$row['request_id']?>">
                                    <?=$row['status']?> <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu" role="menu">
                                    <?php
                                        displayActions($row['status'], $row['request_id']);
                                    ?>
                                  </ul>
                                </div>
                                <!-- // Approval counts -->
                          </h4>
                        </div>
                        <div id="heading<?=$row['request_id']?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="panel<?=$row['request_id']?>">
                          <div class="panel-body">  
                            <div class="scopeNumbers row">
                                <?php displayScopeAmounts($scope); ?>
                            </div>
                              <br />
                            <p><b>Time submitted:</b> <?=$row['date_created']?><br />
                               <b>Category:</b> <?=$row['request_category']?><br />
                               <b>Scoped by:</b> <?=$scope['scoper']?><br />
                               <b>Due by:</b> <?=$row['date_due']?><br />
                              </p>
                            <p><?=$row['description']?></p>
                            
                              
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
    
    
     
    <script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
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