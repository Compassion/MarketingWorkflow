<?php 
require_once('views/template/header.php');
require_once('views/template/nav.php');

//var_dump($_SESSION);
//var_dump($_GET);
/*
if(isset($_GET['rq_id'])) {
    // id index exists
    $id = $_GET['rq_id'];
} else {
    header("Location: manage.php");
    die();
}
*/
$task = $management->getTaskById($id); 
$scope = $management->getScopeRecord($id);
$log = $management->getAuditLog($id);
$subtasks = $management->getDeliverables($id);

$ph = array();
if($scope == null) {
    $new_rq = true;
    
    $ph['product'] = 0;
    $ph['coms'] = 0;
    $ph['digital'] = 0;
    $ph['design'] = 0;
    $ph['video'] = 0;
    $ph['external'] = 0;
    
} else {
    $new_rq = false;
    
    $ph['product'] = $scope['scope_product'];
    $ph['coms'] = $scope['scope_coms'];
    $ph['digital'] = $scope['scope_digital'];
    $ph['design'] = $scope['scope_design'];
    $ph['video'] = $scope['scope_video'];
    $ph['external'] =$scope['scope_external'];
}
?>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                
                <h3>Scope request</h3>
                <hr />
                <!-- // display errors -->
                <br />
                <div class="well">
                    <h4><?php echo $task['request_name']?> 
                        <span class="label label-primary pull-right"><?="Due " .transformDate($task['date_due'])?></span>
                        <br /><small><?=$task['request_type']?> - <?=$task['request_category']?>
                        <br />Submitted by  <?=$task['request_maker']?> on <?=$task['date_created']?>, Due: <?=$task['date_due']?>
                        <?php if(!$new_rq) { ?>
                            <br /> Originally scoped by <?=$scope['scoper']; ?>, on <?=$scope['date_scoped']; ?>
                        <?php } ?>
                        </small></h4>

                    <br />
                    <div class="description">
                        <p><b>Description</b><br />
                            <?php echo $task['description']?></p>
                    </div>
                </div>
                <span id="infoMessage"></span>
                <!-- display errors -->
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
                
                <!-- Scope form -->
                <br />
                <div id="formsToHide">
                    <h4>Estimated resources required</h4>
                    <form method="post" post="scope.php" name="scopingForm" id="scopingForm" class="row">
                        <input type="hidden" name="scoper" value="<?php echo $_SESSION["user_email"] ?>" />
                        <input type="hidden" name="date_scoped" value="<?php echo date('Y-m-d'); ?>" />
                        <input type="hidden" name="request_id" value="<?php echo $id; ?>" />
                        <input type="hidden" name="scope" value="scoped" />

                        <div class="form-group col-sm-4">
                            <label for="scope_prod">Product</label>
                            <input id="scope_prod" class="login_input form-control" type="number" name="scope_prod" placeholder="<?=$ph['product']; ?>" required />
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="scope_coms">Comms</label>
                            <input id="scope_coms" class="login_input form-control" type="number" name="scope_coms" placeholder="<?=$ph['product']; ?>"  required />
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="scope_dig">Digital</label>
                            <input id="scope_dig" class="login_input form-control" type="number" name="scope_dig" placeholder="<?=$ph['digital']; ?>"  required />
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="scope_des">Design</label>
                            <input id="scope_des" class="login_input form-control" type="number" name="scope_des" placeholder="<?=$ph['design']; ?>"  required />
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="scope_vid">Video</label>
                            <input id="scope_vid" class="login_input form-control" type="number" name="scope_vid" placeholder="<?=$ph['video']; ?>"  required />
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="scope_ext">External</label>
                            <input id="scope_ext" class="login_input form-control" type="number" name="scope_ext" placeholder="<?=$ph['external']; ?>"  required />
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="project_assign">Assign to which project?</label>
                            <select name="project_assign" class="form-control">
                            <option>-- Select project --</option>
                                <?php
                                    $projs = $management->getAsanaProjects()->data;
                                    foreach ($projs as $val ) { ?>
                                    <option value="<?php echo $val->id; ?>"><?php echo $val->name; ?></option>
                                <?php } ?>
                            </select> 
                        </div>
                    </form>
                    <br />
                    <h4>Desired deliverables</h4>
                    <div class="" id="deliverableFormContainer">
                        <?php displayEditableSutbtasks($subtasks); ?>
                        <form method="post" post="scope.php" name="deliverableForm-0" id="deliverableForm-0" class="row deliverableForm">
                            <div class="form-group col-sm-8">
                                <label for="st_name-0">Deliverable</label>
                                <input class="form-control" placeholder="Name" name="st_name-0" id="st_name-0" />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="st_due-0">Due</label>
                                <input class="form-control" type="date" name="st_due-0" id="st_due-0" />
                            </div>
                            <div class="form-group col-sm-8">
                                <textarea class="form-control" rows="1" placeholder="Comment" name="st_comment-0" id="st_comment-0"></textarea>
                            </div>
                        </form>
                    </div>

                    <div class="col-sm-12">
                        <button class="btn btn-xs btn-info pull-right addDel"><span class="glyphicon glyphicon-plus-sign"></span> Add another</button>
                    </div>
                    <div class="col-sm-12 text-center">
                        <input type="submit" class="btn btn-primary btn-lg clearfix" name="scope" value="Scope" id="submitScope" data-loading-text="Attempting magic..." />
                    </div>
                </div>
            </div>
        </div>
    </div>
    
     
    <script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="js/scope.js" type="text/javascript"></script>


<?php
require_once('views/template/footer.php'); ?>