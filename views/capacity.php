<?php 
require_once('views/template/header.php');
require_once('core/functions.php');
require_once('views/template/nav.php');
?>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h3>View Capacity</h3>
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
            <div class="col-md-12">
                <div id="hc"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <hr />
                <h3>Edit Capacity</h3>
                <form name="team1CapacityForm" id="team1CapacityForm">
                    <div class="team">
                        <div class="row">
                            
                        </div>
                        <div class="row">
                            <br />
                            <div class="col-xs-12">
                                <table class="table table-hover center-td" id="memberTable-1">
                                    <thead>
                                        <tr>
                                            <th colspan="1">
                                                    <select type="text" class="form-control" id="teamInpt" name="team_name"  required>
                                                        <option value="" disabled selected>Edit a team</option>
                                                        <option value="cap_product">Product team</option>
                                                        <option value="cap_coms">Comms team</option>
                                                        <option value="cap_digital">Digital dawgs</option>
                                                        <option value="cap_design">Design studio</option>
                                                        <option value="cap_video">Video wizkids</option>
                                                        <option value="cap_external">External contractors</option>
                                                    </select>
                                                    <input type="hidden" name="team_count" value="1" id="team1_count">
                                            </th>
                                            <th colspan="5">Workdays</th>
                                            <th><!--<button class="btn btn-primary pull-right" id="addTeamBtn"><span class='glyphicon glyphicon-plus'></span> team</button>-->
                                                <button class="btn btn-primary btn-xs pull-right add-member-btn" ><span class='glyphicon glyphicon-plus'></span> Member</button>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Member</th>
                                            <th>Mon</th>
                                            <th>Tues</th>
                                            <th>Weds</th>
                                            <th>Thurs</th>
                                            <th>Fri</th>
                                            <th>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="members_append">
                                        <tr>
                                            <td colspan="7" class="text-center">Select team</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <button class="btn btn-default" id="finishCapacityBtn">Done</button>
                    </div>
                </div>
                <div id="result">
                </div>
            </div>
        </div>
    </div>
    
     
    <script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/highstock.js"></script>
    <script type="text/javascript" src="js/moment.js"></script>
    <script type="text/javascript" src="js/work.js"></script>
    <script type="text/javascript" src="js/capacity.js"></script>
    <script type="text/javascript">
        var wl = new work();
        
        var cap = <?php echo json_encode($capacity); ?>;
        var load = <?php echo json_encode($load); ?>;
        wl.build(load, cap);
        //wl.buildHighStock('hc');
        
    </script>

<?php
require_once('views/template/footer.php'); ?>