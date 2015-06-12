<?php 
require_once('views/template/header.php');

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
?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                
                <h3>Scope request<small><a href="index.php" class="pull-right btn btn-default">Menu</a></small></h3>
                <hr />
                   
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
                <!-- // display errors -->
            
                <h4><?php echo $task['request_name']?> 
                    <br /><small><?php echo $task['request_type']?> - <?php echo $task['request_category']?>
                    <br />Submitted by  <?php echo $task['request_maker']?> on <?php echo $task['date_created']?>, Due: <?php echo $task['date_due']?></small></h4>
                
                <br />
                <div class="description">
                    <p><b>Description</b><br />
                        <?php echo $task['description']?></p>
                </div>
				<br />
				<h4>Desired deliverables</h4>
				<form>
					<div class="row">
						<div class="form-group col-sm-9">
							<label for="">Task</label>
							<input class="form-control" />
						</div>
						<div class="form-group col-sm-3">
							<label for="">Due</label>
							<input class="form-control" type="date" />
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-9">
							<label for="">Task</label>
							<input class="form-control" />
						</div>
						<div class="form-group col-sm-3">
							<label for="">Due</label>
							<input class="form-control" type="date" />
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-9">
							<label for="">Task</label>
							<input class="form-control" />
						</div>
						<div class="form-group col-sm-3">
							<label for="">Due</label>
							<input class="form-control" type="date" />
						</div>
					</div>
					
				</form>
                
                <!-- register form -->
                <br />
                 <?php var_dump($_POST); ?>
                
                <h4>Estimated resources required</h4>
                <form method="post" post="scope.php" name="scopingForm" id="scopingForm" class="">
                    <input type="hidden" name="scoper" value="<?php echo $_SESSION["user_email"] ?>" />
                    <input type="hidden" name="date_scoped" value="<?php echo date('Y-m-d'); ?>" />
                    <input type="hidden" name="request_id" value="<?php echo $id; ?>" />
                    
                    <div class="form-group col-sm-4">
                        <label for="scope_prod">Product</label>
                        <input id="scope_prod" class="login_input form-control" type="number" name="scope_prod" required />
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="scope_coms">Coms</label>
                        <input id="scope_coms" class="login_input form-control" type="number" name="scope_coms" required />
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="scope_dig">Digital</label>
                        <input id="scope_dig" class="login_input form-control" type="number" name="scope_dig" required />
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="scope_des">Design</label>
                        <input id="scope_des" class="login_input form-control" type="number" name="scope_des" required />
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="scope_vid">Video</label>
                        <input id="scope_vid" class="login_input form-control" type="number" name="scope_vid" required />
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="scope_ext">External</label>
                        <input id="scope_ext" class="login_input form-control" type="number" name="scope_ext" required />
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="project_assign">Assign to which project?</label>
                        <select name="project_assign" class="form-control">
                        <option>-- Select project --</option>
                            <?php var_dump($management->getAsanaProjects()->data); 
                                $projs = $management->getAsanaProjects()->data;
                                foreach ($projs as $val ) { ?>
                                <option value="<?php echo $val->id; ?>"><?php echo $val->name; ?></option>
                            <?php } ?>
                        </select> 
                    </div>
                    <div class="form-group col-sm-12">
                        <input type="submit" class="btn btn-primary" name="scope" value="Scope" />
                    </div>
                </form>
            </div>
        </div>
    </div>
    
     
    <script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>


<?php
require_once('views/template/footer.php'); ?>