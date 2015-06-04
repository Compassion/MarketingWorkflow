<?php 
require_once('views/template/header.php');
?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h3>Manage requests <small><a href="index.php" class="pull-right btn btn-default">Menu</a></small></h3>
                <hr />
                
                <br />
                <div id="infoMessage"></div>
                <!-- register form -->
                
                <div class="taskList">
                <?php 
                    $tasks = $management->viewTasks('Query'); 
                    var_dump($_SESSION);
                    //var_dump($tasks->fetch_assoc());

                    while($row = $tasks->fetch_assoc()) { ?>

                    <div class="panel panel-default" id="pendingPanel<?=$row['request_id']?>">
                        <div class="panel-heading" role="tab" id="panel<?=$row['request_id']?>">
                          <h4 class="panel-title clearfix">
                            <a data-toggle="collapse" data-parent="#accordion" href="#heading<?=$row['request_id']?>" aria-expanded="false" aria-controls="heading<?=$row['request_id']?>">
                              <?=$row['request_name']?> <span class="small"><?=$row['date_due']?> </span>
                            </a>
                              <!-- Single button -->
                                <div class="btn-group pull-right">
                                  <button type="button" class="btn btn-<?=$row['status']?> dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false" id="button<?=$row['request_id']?>">
                                    <?=$row['status']?> <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu" role="menu">
                                    <li><a href="scope.php?rq_id=<?=$row['request_id']?>" class="approve" id="approve<?=$row['request_id']?>" data-val="<?=$row['request_id']?>"><span class="glyphicon glyphicon-arrow-right"></span> Scope</a></li>
                                    <li><a href="#" class="decline" id="decline<?=$row['request_id']?>" data-val="<?=$row['request_id']?>"><span class="glyphicon glyphicon-remove"></span> Decline request</a></li>

                                    <li class="divider"></li>
                                    <li><a href="#" class="pending" id="pending<?=$row['request_id']?>" data-val="<?=$row['request_id']?>"><span class="glyphicon glyphicon-hourglass"></span> Wait</a></li>
                                  </ul>
                                </div>
                          </h4>
                        </div>
                        <div id="heading<?=$row['request_id']?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="panel<?=$row['request_id']?>">
                          <div class="panel-body">
                            <p><b>Time submitted:</b> <?=$row['date_created']?><br />
                               <b>Category:</b> <?=$row['request_category']?><br />
                               <b>Assigned to:</b> <?=$row['request_assigned']?><br />
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


<?php
require_once('views/template/footer.php'); ?>