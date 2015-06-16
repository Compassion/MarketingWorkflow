<?php 
require_once('views/template/header.php');
?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h3>View Capacity <small><a href="index.php" class="pull-right btn btn-default">Menu</a></small></h3>
                <hr />
                <input type="hidden" value="2015-05-01" id="sDate" />
                <input type="hidden" value="2015-12-31" id="eDate" />
                <br />
                
                <p>Based on 261 weekdays, 13 public holidays, 12 RDOs, 20 days annual leave and 10 sick leave a year working at 83% efficiency.</p>
                
                <table class="capacity table table-hover">
                    <thead>
                        <tr>
                            <th>Area</th>
                            <th>Daily Capacity (Hours)</th>
                            <th>Weekly Capacity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $capacity = $management->returnCapacity(); 

                            foreach($capacity as $key => $val) { 
                                if($key != "capacity_id") {
                                    
                                    $cutKey = ucfirst(substr($key, 4));
                                    
                                    echo "<tr><td>";
                                    echo $cutKey;
                                    echo "</td><td>";
                                    echo $val;
                                    echo "</td><td>";
                                    echo $val * 5;
                                    echo "</td></tr>";
                                }
                            }
                            ?>
                    </tbody>
                </table>
                
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
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div id="hc"></div>
            </div>
        </div>
    </div>
    
     
    <script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/highstock.js"></script>
    <script type="text/javascript" src="js/moment.js"></script>
    <script type="text/javascript" src="js/work.js"></script>
    <script type="text/javascript">
        var wl = new work();
        
        var cap = <?php echo json_encode($capacity); ?>;
        var load = <?php echo json_encode($load); ?>;
        wl.build(load, cap);
        wl.buildHighStock('hc');
        
        
    </script>

<?php
require_once('views/template/footer.php'); ?>