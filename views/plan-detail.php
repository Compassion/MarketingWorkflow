<?php 
    require_once('views/template/header.php');
    require_once('core/functions.php');

    $tasks = $management->getTaskById($_GET['pl_id']); 
    $scope = $management->getScopeRecord($_GET['pl_id']); 
    $capacity = $management->returnCapacity();
?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h3>Planning <small><a href="index.php" class="pull-right btn btn-default">Menu</a></small></h3>
                <hr />
                
                <div class="form-inline">
                    <div class="form-group">
                        <label for="sDate">Start date</label>
                        <input id="sDate" class="form-control" type="date" name="sDate" autocomplete="off" value="<?=$tasks['date_created']?>" />
                    </div>
                    <div class="form-group">
                        <label for="eDate">Date required</label>
                        <input id="eDate" class="form-control" type="date" name="eDate" autocomplete="off"  value="<?=$tasks['date_due'] ?>" />
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">Go</button>
                    </div>
                </div>
                
                <br />

                <h4><?php echo $tasks['request_name']?> 
                    <br /><small><?php echo $tasks['request_type']?> - <?php echo $tasks['request_category']?>
                    <br />Submitted by  <?php echo $tasks['request_maker']?> on <?php echo $tasks['date_created']?>, Due: <?php echo $tasks['date_due']?></small></h4>
             
                <br />
                
                <div class="scopeNumbers row">
                    <?php displayScopeAmounts($scope); ?>
                </div>
                <br />
                
                <!-- Progress Bars -->
                <h4>Workload</h4>
                <?php displayWorkloadBars($scope); ?>
                <!-- // Progress Bars -->
                
                <?php 
                    //var_dump($tasks);
                    //var_dump($scope);
                    ?>
                
                <div id="hc"></div>
                <br />
                <form class="text-center">
                    <button class="btn btn-lg btn-success"><span class="glyphicon glyphicon-ok"></span> Send to Asana</button>
                </form>
                <br />
                <form class="text-center">
                    <button class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-list-alt"></span> Send to Backlog</button>
                </form>
                
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
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/highstock.js"></script>
    <script type="text/javascript" src="js/moment.js"></script>
    <script type="text/javascript" src="js/work.js"></script>
    <script type="text/javascript">
        var wl = new work();
        
        var cap = <?php echo json_encode($capacity); ?>;
        
        wl.build(taskList, cap);
        wl.getProposal();
        wl.buildPropLines();
        
    </script>


<?php
require_once('views/template/footer.php'); ?>