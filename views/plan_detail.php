<?php 
    require_once('views/template/header.php');
    require_once('core/functions.php');
    require_once('views/template/nav.php');

    $tasks = $management->getTaskById($_GET['pl_id']); 
    $scope = $management->getScopeRecord($_GET['pl_id']); 
    $audit_log = $management->getAuditLog($_GET['pl_id']); 
    $capacity = $management->returnCapacity();
    $load = $management->buildWorkArray();

    $management->onLoadBuildWorkDataBase(); 

?>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h3>Planning</h3>
                <hr />
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
                        <button class="btn btn-primary" id="go-prop">Go</button>
                    </div>
                </div>
                
                <br />

                <h4><?php echo $tasks['request_name']?> 
                    <br /><small><?php echo $tasks['request_type']?> - <?php echo $tasks['request_category']?>
                    <br />Submitted by  <?php echo $tasks['request_maker']?> on <?php echo $tasks['date_created']?>, Due: <?php echo $tasks['date_due']?></small></h4>
                <p> <?php echo $tasks['description']; ?></p>
                <br />
                
                <div class="scopeNumbers row">
                    <?php displayScopeAmountSquares($scope); ?>
                </div>
                <br />
                
                <!-- Progress Bars -->
                <h4>Workload</h4>
                <?php displayWorkloadBars($scope); ?>
                <!-- // Progress Bars -->
                
                <div id="hc"></div>
                <br />
                <form class="text-center" method="post" action="plan.php" name="requestform" id="requestForm">
                    <input type="hidden" name="submit_to_asana" value="<?=$_GET['pl_id']?>" />
                    <input type="hidden" name="scope_id" value="<?=$scope['scope_id']?>" />
                    <input type="hidden" name="plan_start_date" value="<?=$tasks['date_created']?>" id="plan_start_date" />
                    <input type="hidden" name="plan_end_date" value="<?=$tasks['date_due'] ?>" id="plan_end_date" />
                    
                    <button class="btn btn-lg btn-success" data-loading-text="Magic is Happening..." id="sendToAsana"><span class="glyphicon glyphicon-ok"></span> Send to Asana</button>
                </form>
                <br />
                <form class="text-center" method="post" action="plan.php" name="backlogform" id="backlogForm">
                    <input type="hidden" name="submit_to_backlog" value="<?=$_GET['pl_id']?>" />
                    <button class="btn btn-lg btn-warning"  data-loading-text="Into the Backlog!" id="sendToBacklog"><span class="glyphicon glyphicon-list-alt"></span> Send to Backlog</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h3>Audit log</h3>
                <?php displayAuditLog($audit_log); ?>
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
        var load = <?php echo json_encode($load); ?>;
        wl.build(load, cap);
        wl.getProposal();
        wl.buildPropLines();
        wl.buildHighStock('hc');
        
        $("#go-prop").click( function() { 
            wl.build(load, cap);
            wl.getProposal();
            wl.buildPropLines();
        });
        $("#sendToAsana").click( function() { 
            var $btn = $(this).button('loading');
        });
        $('#sDate').change( function() { 
            var sDate = $(this).val(); 
            $('#plan_start_date').val(sDate);
        });
        $('#eDate').change( function() { 
            var eDate = $(this).val(); 
            $('#plan_end_date').val(eDate);
        });
        
    </script>


<?php
require_once('views/template/footer.php'); ?>