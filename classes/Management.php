<?php
/** 
  * Class Management
  * handles the management of requests. Essential it interfaces between ASANA and WorkFlow App
  */

require_once("classes/Mandrill.php"); 
require_once("classes/Asana.php");
require_once("core/functions.php");
require_once("core/keys.php");

class Management
{
    // Basic Class Setup
    private $db_connection = null;
    public $errors = array();
    public $messages = array();
    
    public $asana = null;
    public $mandrill = null;
    
    // On init check post variables and then submit request
    public function __construct()
    {
        session_start();
        
        // Make sure people are logged in
        if(!isset($_SESSION['user_login_status'])) {
            header("Location: /");
            die();
        } elseif ($_SESSION['user_login_status'] != true ) {
            header("Location: /");
            die();
        }
        
        // Create Asana Class
        $this->asana = new Asana(array('apiKey' => KEY_ASANA));
        
        // Create Mandrill Class
        $this->mandrill = new Mandrill(KEY_MANDRILL);
        
        // Backgroundy making do-e stuff-y
        if (isset($_POST["request_made"])) {
            $this->makeRequest();
        }
        if (isset($_POST['submit_to_asana'])) {
            $this->sendRequestToAsana($_POST);
        }
        if (isset($_POST['submit_to_backlog'])) {
            $this->updateRequestStatus($_POST['submit_to_backlog'], "Backlog");
        }
        // If the send_to param is set use that to send to whereever it says
        if (isset($_GET['send_to'])) {
            $this->updateRequestStatus($_GET['rq_id'], $_GET['send_to']);
        }
        // If the send_to param is set use that to send to whereever it says
        if (isset($_GET['sync'])) {
            $this->buildWorkDataBase();
        }
        
        if (isset($_GET['audit_approve'])) {
            $audit = array();
            
            $audit['rq_id'] = $_GET['rq_id'];
            $audit['status'] = urldecode($_GET['status']);
            $audit['creator'] = $_GET['creator'];
            $audit['assigned'] = urldecode($_GET['assigned']);
            
            $this->createAuditRecord($audit);
            $this->checkAuditApproval($_GET['rq_id']);
        }
        
        if (isset($_GET['audit'])) {
            $audit = array();
            $audit['rq_id'] = $_GET['rq_id'];
            $audit['status'] = $_GET['send_to'];
            $audit['creator'] = $_SESSION['user_email'];
            $audit['comment'] = "Requested " . urldecode($_GET['audit']);
            
            $this->createAuditRecord($audit);
        }
         
        /*
        if (isset($_POST["request_made"])) {
            $this->makeRequest();
        } */
    }
    
    // View tasks by status - this will be extended to show by person assigned
    public function viewTasks($status)
    {
        // Establish connection
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }
        
        // If no errors grab all the tasks with the right status
        if (!$this->db_connection->connect_errno) {
            $sql = "SELECT * FROM `requests` WHERE `status` = '". $status ."';";
            $rq_list = $this->db_connection->query($sql);
            
            return $rq_list;
        }
    }
    // View tasks by status - this will be extended to show by person assigned
    public function viewTasksByAssigned($request_assigned)
    {
        // Establish connection
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }
        
        // If no errors grab all the tasks with the right status
        if (!$this->db_connection->connect_errno) {
            if($request_assigned == 'Admin') {
                $sql = "SELECT * FROM `requests` ORDER BY `status` DESC, `date_created`;";
                //WHERE `status` NOT LIKE 'Complete' AND `status` NOT LIKE 'Declined'
                $rq_list = $this->db_connection->query($sql);

                return $rq_list;
                
            } else {
                $sql = "SELECT * FROM `requests` WHERE `request_assigned` = '". $request_assigned ."' ORDER BY `status` DESC, `date_created`;";
                $rq_list = $this->db_connection->query($sql);

                return $rq_list;
            }
        }
    }
    
    // View all task details by the task id
    public function getTaskById($id)
    {
        // Establish connection
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        
        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }
        
        // If no errors grab all the tasks with the right status
        if (!$this->db_connection->connect_errno) {
            $sql = "SELECT * FROM `requests` WHERE `request_id` = '". $id ."';";
            $rq_list = $this->db_connection->query($sql);
            
            return $rq_list->fetch_assoc();
        }
    }
    
    // Get a list of current projects from Asana.
    public function getAsanaProjects() 
    {
        $result = $this->asana->getProjects();
        if ($this->asana->responseCode != '200' || is_null($result)) {
            $this->errors[] = 'Error while trying to connect to Asana, response code: ' . $this->asana->responseCode;
            return;
        }
        
        $resultJson = json_decode($result);
        
        return $resultJson;        
    }
    
    // Update Task Status
    public function updateRequestStatus($id, $status) 
    {
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }

        // If no errors push away!
        if (!$this->db_connection->connect_errno) {

            $update = "UPDATE `requests` SET `status`='" . $status . "' WHERE `request_id` = '". $id ."';";
            // Update scoping request.
            $update_rq = $this->db_connection->query($update);

            // Check if it worked
            if ($update_rq) {
                $this->messages[] = "<strong>Request status updated!</strong> The status is now " . $status;
                return true;
            } else {
                $this->errors[] = "<strong>Ahhh!</strong> Sorry something broke. Status not updated";
                return false;
            }

        } else {
            $this->errors[] = "<strong>Epic Fail!</strong> Sorry, no database connection.";
            return false;
        }
    }
    // Update Task Status
    public function updateRequestAssigned($id, $assign) 
    {
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }

        // If no errors push away!
        if (!$this->db_connection->connect_errno) {

            $update = "UPDATE `requests` SET `request_assigned`='" . $assign . "' WHERE `request_id` = '". $id ."';";
            // Update scoping request.
            $update_rq = $this->db_connection->query($update);

            // Check if it worked
            if ($update_rq) {
                $this->messages[] = "<strong>Request reassigned!</strong> Fantastic right?";
                return true;
            } else {
                $this->errors[] = "<strong>Ahhh!</strong> Sorry something broke. Request not reassigned";
                return false;
            }

        } else {
            $this->errors[] = "<strong>Epic Fail!</strong> Sorry, no database connection.";
            return false;
        }
    }
    
    // Create audit record
    public function createAuditRecord($audit) 
    {
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }
        
        // If no errors push away!
        if (!$this->db_connection->connect_errno) {
            
            $rq_id = $audit['rq_id'];
            $date = date('Y-m-d');
            $status = $audit['status'];
            $creator = $audit['creator'];
            
            
            // IF an Approval request is required.
            if($status == 'Awaiting Approval') {
                $marketing_manager = 'Marketing Manager';
                $coms_manager = 'Coms Manager';
                $creative_manager = 'Creative Manager';
                
                $insert = "INSERT INTO `audit_action`(`request_id`, `audit_date`, `audit_person`, `audit_assigned`, `audit_status`) VALUES ('$rq_id','$date','$creator','$marketing_manager', '$status'), ('$rq_id','$date','$creator','$coms_manager', '$status'), ('$rq_id','$date','$creator','$creative_manager', '$status');";
            } 
            elseif (isset($audit['assigned'])) {
                $assigned = $audit['assigned'];
                
                $insert = "INSERT INTO `audit_action`(`request_id`, `audit_date`, `audit_person`, `audit_status`, `audit_assigned`) VALUES ('$rq_id','$date','$creator', '$status', '$assigned');";
            }
            elseif (isset($audit['comment'])) {
                // If assigned is set but not comment
                $comment = $this->db_connection->real_escape_string(strip_tags($audit['comment'], ENT_QUOTES));
                
                $insert = "INSERT INTO `audit_action`(`request_id`, `audit_date`, `audit_person`, `audit_comment`, `audit_status`) VALUES ('$rq_id','$date','$creator','$comment', '$status');";
            } 
            else {
                $insert = "INSERT INTO `audit_action`(`request_id`, `audit_date`, `audit_person`, `audit_status`) VALUES ('$rq_id','$date','$creator', '$status');";
            }
            

            // Insert into db
            $insert_rq = $this->db_connection->query($insert);

            // Check if it worked
            if ($insert_rq) {
                $this->messages[] = "<strong>Audit log created!</strong> The status is now " . $status;
                return true;
            } else {
                $this->errors[] = "<strong>Ahhh!</strong> Sorry something broke. Audit not updated";
                echo $insert;
                var_dump($insert_rq);
                return false;
            }

        } else {
            $this->errors[] = "<strong>Epic Fail!</strong> Sorry, no database connection";
            return false;
        }
    }
    
    // Get all the audit history
    public function getAuditLog($id) 
    {
        // Establish connection
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }
        
        // If no errors grab all the tasks with the right status
        if (!$this->db_connection->connect_errno) {
            $sql = "SELECT * FROM `audit_action` WHERE `request_id` = '". $id ."';";
            $rq_list = $this->db_connection->query($sql);
            
            return $rq_list;
        }
    }
    
    // Get all the all scoped deliverables / subtasks
    public function getDeliverables($id) 
    {
        // Establish connection
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }
        
        // If no errors grab all the tasks with the right status
        if (!$this->db_connection->connect_errno) {
            $sql = "SELECT * FROM `scope_subtask` WHERE `request_id` = '". $id ."';";
            $st_list = $this->db_connection->query($sql);
            
            return $st_list;
        }
    }
    
    // Check approval (audit) status
    public function returnAuditStatus($id, $who) 
    {
        // Establish connection
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }
        
        // If no errors start the querying
        if (!$this->db_connection->connect_errno) {

            $sql = "SELECT * FROM `audit_action` WHERE `request_id` = '$id' AND `audit_assigned` = '$who' ORDER BY `audit_id` DESC LIMIT 1;";
            
            $au_list = $this->db_connection->query($sql);
            
            $audit = $au_list->fetch_assoc();
            $status = $audit['audit_status'];
            
            if($status == null) {
                $status = "null";
            }
            
            return $status;
        }
    }
    
    // Three audit oks = task approved
    private function checkAuditApproval($id)
    {
        $MM = "Marketing Manager";
        $CrM = "Creative Manager";
        $CoM = "Coms Manager";
        
        $statuses = array();
        $statuses['MM'] = $this->returnAuditStatus($id, $MM);
        $statuses['CrM'] = $this->returnAuditStatus($id, $CrM);
        $statuses['CoM'] = $this->returnAuditStatus($id, $CoM);
        
        $freq = array_count_values($statuses);
        
        if(isset($freq['Scope Approved'])) {
            if($freq['Scope Approved'] >= 2) 
            {
               $this->updateRequestStatus($id, "Approved");
            }
        }
        if(isset($freq['Scope Declined'])) {
            if($freq["Scope Declined"] >= 2) 
            {
               $this->updateRequestStatus($id, "Declined");
            }
        }
    }
    
    // Create a scoping record
    public function createScopeRecord($post, $audit) 
    {
        //var_dump($post);
        
        if (empty($post['scope'])) {
            $this->errors[] = "Request has not been posted correctly";
        } else {
            $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            // Check DB
            if (!$this->db_connection->set_charset("utf8")) {
                $this->errors[] = $this->db_connection->error;
            }
            
            // If no errors push away!
            if (!$this->db_connection->connect_errno) {
                
                // Create variables and clean them of junk
                $scoper = $this->db_connection->real_escape_string(strip_tags($post['scoper'], ENT_QUOTES));
                $date_scoped = $this->db_connection->real_escape_string(strip_tags($post['date_scoped'], ENT_QUOTES));
                // Turn these into numbers
                $request_id = $this->db_connection->real_escape_string(strip_tags($post['request_id'], ENT_QUOTES));
                
                $project_assign = $this->db_connection->real_escape_string(strip_tags($post['project_assign'], ENT_QUOTES));
                
                $scope_prod = $this->db_connection->real_escape_string(strip_tags($post['scope_prod'], ENT_QUOTES));
                $scope_coms = $this->db_connection->real_escape_string(strip_tags($post['scope_coms'], ENT_QUOTES));
                $scope_dig = $this->db_connection->real_escape_string(strip_tags($post['scope_dig'], ENT_QUOTES));
                $scope_des = $this->db_connection->real_escape_string(strip_tags($post['scope_des'], ENT_QUOTES));
                $scope_vid = $this->db_connection->real_escape_string(strip_tags($post['scope_vid'], ENT_QUOTES));
                $scope_ext = $this->db_connection->real_escape_string(strip_tags($post['scope_ext'], ENT_QUOTES));
                
                $sum = $scope_prod + $scope_coms + $scope_dig + $scope_des + $scope_vid + $scope_ext;
                // Check if request id already exists in scope table
                $chq = "SELECT `request_id` FROM `scope_record` WHERE `request_id` = '" . $request_id . "';";
                $chq_query = $this->db_connection->query($chq);
                
                if($chq_query->num_rows > 0) {
                    $this->messages[] = "<strong>Scoping plan already exists.</strong> Attempting to update.";
                    $update = "UPDATE `scope_record` SET `date_scoped`='" . $date_scoped . "',`project_assigned`='" . $project_assign . "',`scoper`='" . $scoper . "',`scope_product`='" . $scope_prod . "',`scope_coms`='" . $scope_coms . "',`scope_digital`='" . $scope_dig . "',`scope_design`='" . $scope_des . "',`scope_video`='" . $scope_vid . "',`scope_external`='" . $scope_ext . "' WHERE `request_id` = '". $request_id ."';";
                    
                    // Update scoping request.
                    $update_rq = $this->db_connection->query($update);
                    
                    // Check if it worked
                    if ($update_rq) {
                        $this->messages[] = "<strong>Scoping updated!</strong> That means everything is good.";
                        $this->updateRequestStatus($request_id, "Scoped");
                        if($sum <= 4) {
                            $this->updateRequestStatus($request_id, "Approved");
                            $this->messages[] = "<strong>Auto Approved!</strong> Due to the low amount of work required. Win!";
                            
                            $audit['status'] = "System auto-approved scope";
                            $this->createAuditRecord($audit);
                        } else {
                            $this->createAuditRecord($audit);
                        }
                    } else {
                        $this->errors[] = "<strong>Ahhh!</strong> Sorry something broke.";
                    }
                    
                } else {
                    // Build SQL
                    $insert = "INSERT INTO `scope_record`(`request_id`, `date_scoped`, `project_assigned`, `scoper`, `scope_product`, `scope_coms`, `scope_digital`, `scope_design`, `scope_video`, `scope_external`)
                           VALUES('" . $request_id . "', '" . $date_scoped . "', '" . $project_assign . "', '" . $scoper . "', '" . $scope_prod . "', '" . $scope_coms . "', '" . $scope_dig . "', '" . $scope_des . "', '" . $scope_vid . "', '" . $scope_ext . "');";   

                    // Insert into database
                    $insert_rq = $this->db_connection->query($insert);

                    // Check if it worked
                    if ($insert_rq) {
                        $this->messages[] = "<strong>WINNING!</strong> Scoping successfully received.";

                        $this->updateRequestStatus($request_id, "Scoped");
                        
                        if($sum <= 4) {
                            $this->updateRequestStatus($request_id, "Approved");
                            $this->messages[] = "<strong>Auto Approved!</strong> Due to the low amount of work required. Win!";
                            $audit['status'] = "System auto-approved";
                            $this->createAuditRecord($audit);
                        } else {
                            $this->createAuditRecord($audit);
                        }
                    } else {
                        $this->errors[] = "<strong>Bugger!</strong> Sorry something broke.";
                        echo $insert;
                    }
                }     
            } else {
                $this->errors[] = "<strong>Epic Fail!</strong> Sorry, no database connection.";
            }
        }
    }
    
    // Create a deliverable record
    public function createDeliverable($post) 
    {
        //var_dump($post);
        if (empty($post['scope'])) {
            $this->errors[] = "Request has not been posted correctly";
        } else {
            $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            // Check DB
            if (!$this->db_connection->set_charset("utf8")) {
                $this->errors[] = $this->db_connection->error;
            }
            
            
            // If no errors push away!
            if (!$this->db_connection->connect_errno) {
                
                // Clean data
                $request_id = $this->db_connection->real_escape_string(strip_tags($post['request_id'], ENT_QUOTES));
                
                $scope_id = $this->db_connection->real_escape_string(strip_tags($post['scope_id'], ENT_QUOTES));
                
                $date_created = date('Y-m-d');
                $date_required =  $this->db_connection->real_escape_string(strip_tags($post['st_due'], ENT_QUOTES));
                $name =  $this->db_connection->real_escape_string(strip_tags($post['st_name'], ENT_QUOTES));
                $comment =  $this->db_connection->real_escape_string(strip_tags($post['st_comment'], ENT_QUOTES));
                
                // Build SQL
                $insert = "INSERT INTO `scope_subtask`(`request_id`, `scope_id`, `st_date_created`, `st_date_required`, `st_name`, `st_comment`) VALUES ('$request_id','$scope_id','$date_created','$date_required','$name','$comment');"; 

                // Insert into database
                $insert_del = $this->db_connection->query($insert);

                // Check if it worked
                if ($insert_del) {
                    $this->messages[] = "<strong>Success!</strong> Subtask injected.";  
                } else {
                    $this->errors[] = "<strong>FAILURE!</strong> Subtask rejected for some strange reason. SQL - " .$insert; 
                }     
            } else {
                $this->errors[] = "<strong>Epic Fail!</strong> Sorry, no database connection.";
            }
        }
        
    }
    
    public function deleteDeliverables($request_id)
    {
        // Deletes subtasks associated with a request ID
        $delete = "DELETE FROM `scope_subtask` WHERE `request_id` = '$request_id';";

        // Insert into database
        $delete_del = $this->db_connection->query($delete);

        // Check if it worked
        if ($delete_del) {
            $this->messages[] = "<strong>Boom!</strong> Previous subtasks have been deleted. Make room for the future!";  
        } else {
            $this->errors[] = "<strong>Nope...</strong> Subtask deletion rejected for some strange reason. SQL - " .$delete; 
        }           
    }
    
    // Update Task Status
    public function getScopeRecord($id) 
    {
        // Establish connection
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }
        
        // If no errors grab all the tasks with the right status
        if (!$this->db_connection->connect_errno) {
            $sql = "SELECT * FROM `scope_record` WHERE `request_id` = '". $id ."';";
            $rq_list = $this->db_connection->query($sql);
            
            return $rq_list->fetch_assoc();
        }
    }
    
    // Create a deliverable record
    private function createWorkdates($post) 
    {
        //var_dump($post);
        if (empty($post['submit_to_asana'])) {
            $this->errors[] = "This is really weird.";
        } else {
            $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            // Check DB
            if (!$this->db_connection->set_charset("utf8")) {
                $this->errors[] = $this->db_connection->error;
            }
            
            // If no errors push away!
            if (!$this->db_connection->connect_errno) {
                
                // Clean data
                $request_id = $this->db_connection->real_escape_string(strip_tags($post['submit_to_asana'], ENT_QUOTES));
                $scope_id = $this->db_connection->real_escape_string(strip_tags($post['scope_id'], ENT_QUOTES));
                
                $wd_submitted = date('Y-m-d');
                $wd_start =  $this->db_connection->real_escape_string(strip_tags($post['plan_start_date'], ENT_QUOTES));
                $wd_end =  $this->db_connection->real_escape_string(strip_tags($post['plan_end_date'], ENT_QUOTES));
                
                // Build SQL
                $insert = "INSERT INTO `work_dates`(`request_id`, `scope_id`, `wd_submitted`, `wd_start`, `wd_end`) VALUES ('$request_id', '$scope_id', '$wd_submitted', '$wd_start', '$wd_end');";

                // Insert into database
                $insert_wd = $this->db_connection->query($insert);

                // Check if it worked
                if ($insert_wd) {
                    $this->messages[] = "<strong>Done!</strong> Log created.";  
                } else {
                    $this->errors[] = "<strong>FAILURE!</strong> Something broke - " .$insert; 
                }     
            } else {
                $this->errors[] = "<strong>Epic Fail!</strong> Sorry, no database connection.";
            }
        }
        
    }
    
    /*
     *
     * This is the bigging of the work into Asana functions
     *
     */
    
    // View Capacity
    public function returnCapacity()
    {
        // Establish connection
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }
        
        // If no errors grab all the tasks with the right status
        if (!$this->db_connection->connect_errno) {
            $sql = "SELECT * FROM `capacity` WHERE `capacity_id` = '1';";
            $cap_list = $this->db_connection->query($sql);
            
            return $cap_list->fetch_assoc();
        }
    }
    
    // Add Capacity Member
    private function createCapacityMember($member)
    {
        if($member['name'] == 'undefined') {
            return false;
        }
            
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }

        // If no errors push away!
        if (!$this->db_connection->connect_errno) {

            // Create variables and clean them of junk
            $name = $this->db_connection->real_escape_string(strip_tags($member['name'], ENT_QUOTES));
            // Create variables and clean them of junk
            $hours = $this->db_connection->real_escape_string(strip_tags($member['hours'], ENT_QUOTES));
            // Create variables and clean them of junk
            $team = $this->db_connection->real_escape_string(strip_tags($member['team'], ENT_QUOTES));
            
            if(in_array("mon", $member['days'])) {
                $cm_mon = 1;
            } else { $cm_mon = 0; }
            
            if(in_array("tues", $member['days'])) {
                $cm_tues = 1;
            } else { $cm_tues = 0; }
            
            if(in_array("weds", $member['days'])) {
                $cm_wed = 1;
            } else { $cm_wed = 0; }
            
            if(in_array("thurs", $member['days'])) {
                $cm_thurs = 1;
            } else { $cm_thurs = 0; }
            
            if(in_array("fri", $member['days'])) {
                $cm_fri = 1;
            } else { $cm_fri = 0; }
            
            /*var_dump($member['days']);
            echo $cm_mon ." <br />";
            echo $cm_tues ." <br />";
            echo $cm_wed ." <br />";
            echo $cm_thurs ." <br />";
            echo $cm_fri ." <br />";*/

            $insert = "INSERT INTO `capacity_members`(`cm_team`, `cm_name`, `cm_hours`, `cm_mon`, `cm_tues`, `cm_weds`, `cm_thurs`, `cm_fri`) VALUES ('$team','$name','$hours','$cm_mon','$cm_tues','$cm_wed','$cm_thurs','$cm_fri');";
            
            //echo $insert ."<br />";

            // Update scoping request.
            $insert_rq = $this->db_connection->query($insert);

            // Check if it worked
            if ($insert_rq) {
                $this->messages[] = "<strong>Members updated!</strong> That means everything is good.";
            } else {
                $this->errors[] = "<strong>Ahhh!</strong> Sorry something broke.";
            }    
        } else {
            $this->errors[] = "<strong>Epic Fail!</strong> Sorry, no database connection.";
        }
    }
    
    // Remove Capacity team
    private function deleteCapacityTeam($team)
    {
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }

        // If no errors push away!
        if (!$this->db_connection->connect_errno) {

            // Remove previous team entry
            $del = "DELETE FROM `capacity_members` WHERE `cm_team` = '$team'";
            $del_query = $this->db_connection->query($del);

            if($del_query) {
            } else {
                $this->errors[] = "<strong>Ahahahakk!</strong> I can't delete that team.";
            }    
        } else {
            $this->errors[] = "<strong>Epic Fail!</strong> Sorry, no database connection.";
        }
    }

    // Update Capacity
    public function updateCapacity($post) 
    {
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }

        // If no errors push away!
        if (!$this->db_connection->connect_errno) {
           
            $team = $post['name'];
            $day = $post['day'];

            $team = $this->db_connection->real_escape_string(strip_tags($team, ENT_QUOTES));

            $sql = "UPDATE `capacity` SET `$team`='$day' WHERE `capacity_id` = 1";

            // Update capacity.
            $sql_result = $this->db_connection->query($sql);

            // Check if it worked
            if ($sql_result) {
                $this->messages[] = "<strong>Boom!</strong>";
                
                $this->deleteCapacityTeam($team);
                
                // Be awesome. Create members
                foreach($post['members'] as $member=>$detail) {
                    $detail['name'] = $member;
                    $detail['team'] = $team;

                    $this->createCapacityMember($detail);
                }
            } else {
                $this->errors[] = "<strong>Ahhh!</strong> Sorry something else broke";
            }
        } else {
            $this->errors[] = "<strong>Epic Fail!</strong> Sorry, no database connection.";
        }
        
    }
    
    // Get the capcity team members
    public function getCapacityTeam($team) 
    {
        // Establish connection
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }
        
        // If no errors grab all the tasks with the right status
        if (!$this->db_connection->connect_errno) {
            $sql = "SELECT * FROM `capacity_members` WHERE `cm_team` = '". $team ."';";
            $cm_list = $this->db_connection->query($sql);
            
            return $cm_list;
        }  
    }
    
    
    // Gets all tasks from a project. It then calls the insertTaskIntoBb method. It will only call insertTask when a task has a due date and has at least one tag.
    public function getProjectTasks($projectId)
    {
        $options = array('opt_fields' => 'name,created_at,due_on,modified_at,tags, tags.name, tags.id, assignee.name, assignee.email');
        
        $result = $this->asana->getProjectTasks($projectId, $options);
        if ($this->asana->responseCode != '200' || is_null($result)) {
            $this->errors[] = 'Error while trying to connect to Asana, response code: ' . $this->asana->responseCode;
            return;
        }
        
        $taskList = json_decode($result);
        
        $depts = array('product', 'coms', 'design', 'digital', 'design', 'video', 'external');
     
        /*
        $test = array('asana'=>'value of asana', 'coms'=>'whatever', 'email'=>'josh@hosg.com');
        var_dump($test);
        echo $test['asana'];
        */
        
            
        foreach($taskList->data as $obj) {
            if ( gettype($obj->due_on) == "string" && !empty($obj->tags)) {
                $created = explode("T", $obj->created_at);
                $modified = explode("T", $obj->modified_at);

                $created = $created[0];
                $modified = $modified[0];
                
                // Sanitize variables
                $asana_id = $obj->id;
                $asana_name = $obj->name;
                $date_started = $created;
                $date_due =  $obj->due_on;
                $project_assigned = $projectId;
                $workDays = calculateWorkDays($date_started, $date_due);
                
                // Start building array
                $taskArr = array();
                
                $taskArr['asana_id'] = $asana_id;
                $taskArr['asana_name'] = $asana_name;
                $taskArr['date_started'] = $date_started;
                $taskArr['date_due'] = $date_due;
                $taskArr['project_assigned'] = $project_assigned;
                $taskArr['work_days'] = $workDays;
                

                // Check if object or null
                if(gettype($obj->assignee) == "object") {
                    //echo $obj->assignee->name . "<br />";
                    //echo $obj->assignee->email . "<br />";
                    
                    $person_assigned = $obj->assignee->email;
                    
                    $taskArr['person_assigned'] = $person_assigned;
                }

                // Check if tag array exists and is not empty
                if(gettype($obj->tags) == "array" && !empty($obj->tags)) {
                    //echo $obj->tags;
                    foreach( $obj->tags as $fullTag ) {
                        $tag = explode(":", $fullTag->name);
                        $area = strtolower($tag[0]);
                        if(isset($tag[1])) {  
                            $work = $tag[1];
                        } else {
                            $work = 0;
                        }
                        
                        if(in_array($area, $depts)) {
                            $name = "asana_" . $area;
                            $$name = round(($work / $workDays), 2);
                            //echo "$name ${$name} <br />";
                            
                            $taskArr[$name] = $$name;  
                        }
                    }
                }
                $this->insertTaskIntoDb($taskArr);
            }
        }
    }
    
    // This inserts tasks into the db.
    public function insertTaskIntoDb($task) 
    {
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }

        // If no errors push away!
        if (!$this->db_connection->connect_errno) {
            // Sanitize input first though
            $asana_id = $this->db_connection->real_escape_string(strip_tags($task['asana_id'], ENT_QUOTES));
            $date_started = $this->db_connection->real_escape_string(strip_tags($task['date_started'], ENT_QUOTES));
            $date_due = $this->db_connection->real_escape_string(strip_tags($task['date_due'], ENT_QUOTES));
            $project_assigned = $this->db_connection->real_escape_string(strip_tags($task['project_assigned'], ENT_QUOTES));
            
            if(isset($task['person_assigned'])) {
                $person_assigned = $this->db_connection->real_escape_string(strip_tags($task['person_assigned'], ENT_QUOTES));
            } else {
                $person_assigned = "";
            }
            $asana_name = $this->db_connection->real_escape_string(strip_tags($task['asana_name'], ENT_QUOTES));
            $work_days = $this->db_connection->real_escape_string(strip_tags($task['work_days'], ENT_QUOTES));
            
            if(array_key_exists('asana_product', $task)) {
                $asana_product = $task['asana_product'];
            } else {
                $asana_product = 0;
            }
            
            if(array_key_exists('asana_coms', $task)) {
                $asana_coms = $task['asana_coms'];
            } else {
                $asana_coms = 0;
            }
            
            if(array_key_exists('asana_digital', $task)) {
                $asana_digital = $task['asana_digital'];
            } else {
                $asana_digital = 0;
            }
            
            if(array_key_exists('asana_design', $task)) {
                $asana_design = $task['asana_design'];
            } else {
                $asana_design = 0;
            }
            if(array_key_exists('asana_video', $task)) {
                $asana_video = $task['asana_video'];
            } else {
                $asana_video = 0;
            }
            if(array_key_exists('asana_external', $task)) {
                $asana_external = $task['asana_external'];
            } else {
                $asana_external = 0;
            }
            
            $insert = "INSERT INTO `work_load`(`asana_id`, `date_started`, `date_due`, `project_assigned`, `person_assigned`, `asana_product`, `asana_coms`, `asana_digital`, `asana_design`, `asana_video`, `asana_external`, `asana_name`, `work_days`) VALUES ('$asana_id', '$date_started', '$date_due', '$project_assigned', '$person_assigned', '$asana_product', '$asana_coms', '$asana_digital', '$asana_design', '$asana_video', '$asana_external', '$asana_name', '$work_days') ON DUPLICATE KEY UPDATE `date_started`=VALUES(`date_started`), `date_due`=VALUES(`date_due`), `project_assigned`=VALUES(`project_assigned`), `person_assigned`=VALUES(`person_assigned`), `asana_product`=VALUES(`asana_product`), `asana_coms`=VALUES(`asana_coms`), `asana_digital`=VALUES(`asana_digital`), `asana_design`=VALUES(`asana_design`), `asana_video`=VALUES(`asana_video`), `asana_external`=VALUES(`asana_external`), `asana_name`=VALUES(`asana_name`), `work_days`=VALUES(`work_days`);";
            
            // Insert task.
            $insert_wl = $this->db_connection->query($insert);

            // Check if it worked
            if ($insert_wl) {
                $this->messages[] = "<strong>Task updated!</strong> For task " . $asana_name;
                
                $today = date('Y-m-d');
                $w_update = "INSERT INTO `work_last_updated`(`w_updated`) VALUES ('$today')";
                
                $w_updated = $this->db_connection->query($w_update);
                if ($w_updated) { 
                    $this->messages[] = "<strong>ACHIEVEMENT UNLOCKED!</strong> I have made a note of this occasion";
                } else {
                    $this->errors[] = "<strong>Hmm weirdness...</strong> Look everything worked but for some reason I couldn't make a note of it. Weird...";
                }
            } else {
                $this->errors[] = "<strong>Ahhh!</strong> Sorry something broke. Task work not updated " . $asana_name . "<br />" . $insert;
            }
        } else {
            $this->errors[] = "<strong>Epic Fail!</strong> Sorry, no database connection.";
        }
    }
    
    // Attempt to pull everything from Asana and smash it into the work table - can be manually called
    public function buildWorkDataBase()
    { 
        $projects = $this->getAsanaProjects()->data;
    
        foreach ($projects as $project ) { 
            $id = $project->id; 
            $this->getProjectTasks($id);
            
            //$this->messages[] = "<strong>Syncing...</strong> Attempting to sync " . $project->name;
        }
    }
    
    // Pull everything down from the Workload database and spit it out as json object
    public function buildWorkArray()
    {
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }

        // If no errors query away!
        // If no errors grab all the tasks with the right status
        if (!$this->db_connection->connect_errno) {
            $sql = "SELECT * FROM work_load";
            $wl_list = $this->db_connection->query($sql);
            
            $task_list = array();
            
            foreach($wl_list as $arr=>$val)
            {
                $task;
                $task['task'] = $val['asana_name'];
                $task['sdate'] = $val['date_started'];
                $task['edate'] = $val['date_due'];
                $task['duration'] = (int)$val['work_days'];
                $task['workdays'] = (int)$val['work_days'];
                
                $task['product'] = (float)$val['asana_product'];
                $task['coms'] = (float)$val['asana_coms'];
                $task['digital'] = (float)$val['asana_digital'];
                $task['design'] = (float)$val['asana_design'];
                $task['video'] = (float)$val['asana_video'];
                $task['external'] = (float)$val['asana_external'];
                
                $task_list[] = $task;
            }
            
            return $task_list;
        }  
    }
    
    // Simple check of when last updated, attempt update if not updated in the last year.
    public function onLoadBuildWorkDataBase() 
    { 
        // Establish connection
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check DB
        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = $this->db_connection->error;
        }
        
        // If no errors grab all the tasks with the right status
        if (!$this->db_connection->connect_errno) {
            $sql = "SELECT `w_updated` FROM work_last_updated ORDER BY `w_updated` DESC LIMIT 1";
            $sql_result = $this->db_connection->query($sql);
            
            $date = $sql_result->fetch_assoc();
            $date = $date['w_updated'];
            
            $last_updated = new DateTime($date);
            $today = new DateTime(date('Y-m-d'));
            
            $interval = $last_updated->diff($today);
            $interval = $interval->d;
            
            if($interval > 0) {
                $this->buildWorkDataBase();
            } else {
                $this->messages[] = "<strong>All set</strong> Workload is up to date.";
            }
        }
        
    }
    
    // CREATE MORE WORK FOR US! Sad times...
    private function sendRequestToAsana($post) 
    {
        $rq_id = $post['submit_to_asana'];
        
        $options = array('opt_fields' => 'name,created_at,due_on,modified_at,tags, tags.name, tags.id, assignee.name, assignee.email');
        
        $task = $this->getTaskById($rq_id);
        $scope = $this->getScopeRecord($rq_id);
        $subtasks = $this->getDeliverables($rq_id);
        
        $taskId = $rq_id;
        $name = $taskId. " - ". $task['request_name'];
        
        $created_at = '-- Gantt data, leave at the end --
start: "' .$post['plan_start_date'] .'"'; // This is READ ONLY so pump into instagantt
        $due_on = $post['plan_end_date']; //$task['date_due'];
        $projectId = $scope['project_assigned'];
        $assignee = $scope['scoper'];
        
        $followers = array(array(
            'email' => $scope['scoper']
        ));
        
        
        $description = $task['description'] . " \n \n Request ID " . $taskId .", Requested by " . $task['request_maker'] ." \n Originally due " .$task['date_due'] .".\n Requested on " .$task['date_created'] ." \n \n " .$created_at;
        
        /*
        $task['date_due']
        $task['date_created']
        $post['plan_start_date'];
        $post['plan_end_date'];
        */
        $tags = array( "Product: " . $scope['scope_product'], "Coms: " . $scope['scope_coms'], "Digital: " . $scope['scope_digital'], "Design: " . $scope['scope_design'], "Video: " . $scope['scope_video'], "External: " . $scope['scope_external']           
        );
       
        $asana_task = array(
            'workspace' => KEY_ASANA_WS,
            'name' => $name,
            'assignee' => $assignee,
            'assignee_status' => 'upcoming',
            'due_on' => $due_on,
            'notes' => $description,
            'followers' => $followers
            //'tags' => $tags // can't add tags at creation
        );
        
        /*
        $json = json_encode($asana_task);
        
        echo $json; */

        // time for ASANA stuff!
        $createTask = $this->asana->createTask($asana_task);
        
        // As Asana API documentation says, when a task is created, 201 response code is sent back so...
        if($this->asana->responseCode != '201' || is_null($createTask)){
            $this->errors[] = "<strong>Unable to create task in Asana</strong> Response code: " . $this->asana->responseCode;
            return;
        } else {
            $this->messages[] = "<strong>Task created!</strong> Who's awesome? You are!";
            $this->createWorkdates($post);
            $this->updateRequestStatus($rq_id, "Complete");
        }
        

        $resultJson = json_decode($createTask);

        $taskId = $resultJson->data->id; // Here we have the id of the task that have been created

        // Now we do another request to add the task to a project
        $result = $this->asana->addProjectToTask($taskId, $projectId);

        if ($this->asana->responseCode != '200') {
            $this->errors[] = "<strong>Bad news...<strong> Task created but can\'t associate with project, response code:" . $this->asana->responseCode;
        } else {
            $this->messages[] = "<strong>Everything worked!</strong> Yay more work!";
        }
        
        $this->dealWithTags($taskId, $tags);
        
        if($subtasks->num_rows != null) {
            $this->sendSubtasksToAsana($rq_id, $taskId);
        }
    }
    
    // Send subtask to Asana
    private function sendSubtasksToAsana($rq_id, $parentId) {
        $subtasks = $this->getDeliverables($rq_id);
        
        while($task = $subtasks->fetch_assoc()) {
            
            $due_on = $task['st_date_required'];
            $description = $task['st_comment'];
            $name = $rq_id. " - ". $task['st_name'];
            
            // Build task array
            $asana_task = array(
                'workspace' => KEY_ASANA_WS,
                'name' => $name,
                'assignee_status' => 'upcoming',
                'due_on' => $due_on,
                'notes' => $description
            );
            
            $createSubTask = $this->asana->createSubTask($parentId, $asana_task);
            
            // Check if it worked
            if($this->asana->responseCode != '201' || is_null($createSubTask)){
                $this->errors[] = "<strong>Unable to create subtask in Asana</strong> Response code: " . $this->asana->responseCode;
            } else {
                $this->messages[] = "<strong>Subtask created!</strong> Flipping sweet!";
            } 
        }
    }
    
    // Tags in ASANA are stupid, so I thought I would split this function out here to deal with them separately.
     private function dealWithTags($taskId, $scopingNums) 
     { 
         // Get Tags from ASANA
         $getTags = $this->asana->getTags();
         
         if($this->asana->responseCode != '200' || is_null($getTags)){
            $this->errors[] = "<strong>Tags have broken.</strong> Response code: " . $this->asana->responseCode;
            return;
        } else {
            $this->messages[] = "<strong>Tag list found...</strong>";
        }
            
         $tagList = json_decode($getTags);
         $tagList = $tagList->data;
         
         //var_dump($tagList);
         
         $tags = array();
         $pairs = array();
         
         // Create an array of the tag names
         foreach($tagList as $tag) {
             $name = $tag->name;
             array_push($tags, $name);
         }

         // Check if each tag in the scoping numbers exists, if not create it and pin against task, if not get ID and pin against task
         
         foreach($scopingNums as $scope)
         {
             // if in array we need to get the ID
            if(in_array($scope, $tags)) {
                // Search for matches, if one is found set tagId
                foreach($tagList as $val) {
                    if($scope == $val->name) {
                        //echo "Match! " . $val->name . " " . $scope;
                        $tagId = $val->id;
                    }
                }
            } else {
                
                // create a new tag
                $new_tag = array(
                    'name' => $scope,
                    'workspace' => KEY_ASANA_WS,
                    'color' => 'light-teal'
                );
                
                $createTag = $this->asana->createTag($new_tag);
                
                if($this->asana->responseCode != '201' || is_null($createTag)){
                    $this->errors[] = "<strong>I have failed...</strong> Great shame has been cast upon my family. No new tag was created. Response code: " . $this->asana->responseCode;
                    return;
                } else {
                    $this->messages[] = "<strong>Tag created</strong>";
                }
                
                $tagResult = json_decode($createTag);
                $tagId = $tagResult->data->id;
            }

            // AFTER THE DUST SETTLES THERE CAN ONLY BE ONE TAG ID!
            $addTag = $this->asana->addTagToTask($taskId, $tagId);
         
            if($this->asana->responseCode != '200' || is_null($addTag)){
                $this->errors[] = "<strong>Failed at the last step...</strong> Stupid tags... Don't need to use them with stuff anyway... Response code: " . $this->asana->responseCode;
                return;
            } else {
                $this->messages[] = "<strong>The task and tag is now one.</strong>";
            }
         }
     }
    
    
    // MANDRILL STUFF!
    private function sendEmail($email)
    {   
        try {
            $mandrill = $this->mandrill;
            
            $message = array(
                'html' => $email['content'],
                //'text' => 'Example text content',
                'subject' => $email['subject'],
                'from_email' => $email['from_email'],
                'from_name' => $email['from_name'],
                'to' => array(
                    array(
                        'email' => $email['to_email'],
                        'name' => $email['to_name'],
                        'type' => 'to'
                    )
                ),
                //'headers' => array('Reply-To' => 'message.reply@example.com'),
                //'important' => false,
                'track_opens' => true,
                //'track_clicks' => null,
                'auto_text' => true,
                //'auto_html' => null,
                'inline_css' => true,
                'url_strip_qs' => false,
                //'preserve_recipients' => null,
                //'view_content_link' => null,
                //'bcc_address' => 'message.bcc_address@example.com',
                //'tracking_domain' => null,
                //'signing_domain' => null,
                //'return_path_domain' => null,
                'merge' => true,
                'merge_language' => 'mailchimp',
                'global_merge_vars' => array(
                    array(
                        'name' => $email['to_name'],
                        'content' => 'merge1 content'
                    )
                ),
                /*
                'merge_vars' => array(
                    array(
                        'rcpt' => 'recipient.email@example.com',
                        'vars' => array(
                            array(
                                'name' => 'merge2',
                                'content' => 'merge2 content'
                            )
                        )
                    )
                ),*/
                'tags' => array($email['tag'])
                //'subaccount' => 'customer-123',
                //'google_analytics_domains' => array('example.com'),
                //'google_analytics_campaign' => 'message.from_email@example.com',
                //'metadata' => array('website' => 'www.example.com'),
                /*'recipient_metadata' => array(
                    array(
                        'rcpt' => 'recipient.email@example.com',
                        'values' => array('user_id' => 123456)
                    )
                ),
                'attachments' => array(
                    array(
                        'type' => 'text/plain',
                        'name' => 'myfile.txt',
                        'content' => 'ZXhhbXBsZSBmaWxl'
                    )
                ),
                'images' => array(
                    array(
                        'type' => 'image/png',
                        'name' => 'IMAGECID',
                        'content' => 'ZXhhbXBsZSBmaWxl'
                    )
                )*/
            );
            $async = false;
            $ip_pool = 'Main Pool';
            //$send_at = $email['send_at'];
            $result = $mandrill->messages->send($message, $async, $ip_pool/*, $send_at */);
            //print_r($result);
            /*
            Array
            (
                [0] => Array
                    (
                        [email] => recipient.email@example.com
                        [status] => sent
                        [reject_reason] => hard-bounce
                        [_id] => abc123abc123abc123abc123abc123
                    )

            )
            */
        } catch(Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            $this->errors[] = '<strong>A mandrill error occurred:</strong> ' . get_class($e) . ' - ' . $e->getMessage();
            // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
            throw $e;
            return false;
        }
        $this->messages[] = "<strong>Email sent!</strong>";
        return true;
        
    }
    
    private function sendRequestReceived($rq_id)
    {
        $rq = $this->getTaskById($rq_id);
        //var_dump($rq);

        $email['content'] = "<p>Thanks for your request! We will be in touch.</p><p>Your request ID is " .$rq_id ."</p><p>Request name - " .$rq['request_name'] .".<br />Description - " .$rq['description'] ."<br />Requested on " .$rq['date_created'] ."</p><p><br />Thanks!<br />Have a great day.</p>";
        
        $email['subject'] = 'Thank you for your request!';
        $email['from_email'] = 'workflow@compassion.com.au';
        $email['from_name'] = 'Workflow Wombat';
        $email['to_email'] = $rq['request_maker'];
        $email['to_name'] = 'friend';
        $email['tag'] = 'Rq Received';
        
        $this->sendEmail($email);
    }
    
    
    // REQUEST MAKING!
    private function makeRequest()
    {
        if (empty($_POST['request_name'])) {
            $this->errors[] = "You have no name!";
        } else {
            $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            // Check DB
            if (!$this->db_connection->set_charset("utf8")) {
                $this->errors[] = $this->db_connection->error;
            }
            
            // If no errors push away!
            if (!$this->db_connection->connect_errno) {
                // Create variables and clean them of junk
                $rq_maker = $this->db_connection->real_escape_string(strip_tags($_POST['request_maker'], ENT_QUOTES));
                $rq_created = $this->db_connection->real_escape_string(strip_tags($_POST['date_created'], ENT_QUOTES));
                $rq_name = $this->db_connection->real_escape_string(strip_tags($_POST['request_name'], ENT_QUOTES));
                $rq_desc = $this->db_connection->real_escape_string(strip_tags($_POST['description'], ENT_QUOTES));
                $rq_due = $this->db_connection->real_escape_string(strip_tags($_POST['date_due'], ENT_QUOTES));
                $rq_type = $this->db_connection->real_escape_string(strip_tags($_POST['request_type'], ENT_QUOTES)); 
                $rq_category = $this->db_connection->real_escape_string(strip_tags($_POST['request_category'], ENT_QUOTES)); 
                
                
                // Logic for who to assign stuff to
                if($_POST['request_type'] == "Ongoing Servicing") {
                    $rq_assigned = 'Workflow';
                } else {
                    switch($_POST['request_category']) {
                        case "Advocacy/Fundraising":
                        case "Trip":
                            $rq_assigned = 'Product Area 1';
                            break;
                        case "Major Givers":
                        case "Church":
                            $rq_assigned = 'Product Area 2';
                            break;
                        case "Events":
                        case "Ambassadors":
                            $rq_assigned = 'Product Area 3';
                            break;
                        default:
                            $rq_assigned = 'Marketing Manager';
                            break;
                    }
                }
                
                // Check if request name already exists
                $chq = "SELECT `request_name` FROM `requests` WHERE `request_name` = '" . $rq_name . "' AND `date_due` = '" . $rq_due . "';";
                $chq_query = $this->db_connection->query($chq);
                
                if($chq_query->num_rows > 0) {
                    $this->messages[] = "<strong>Quit hitting refresh.</strong> Your request has already been received.";
                } else {
                    // Build SQL
                    $sql = "INSERT INTO requests (request_maker, date_created, request_name, description, date_due, request_type, request_category, request_assigned, status)
                           VALUES('" . $rq_maker . "', '" . $rq_created . "', '" . $rq_name . "', '" . $rq_desc . "', '" . $rq_due . "', '" . $rq_type . "', '" . $rq_category . "', '" . $rq_assigned . "', 'Query');";   


                    // Insert into database
                    $query_new_user_insert = $this->db_connection->query($sql);

                    // Check if it worked
                    if ($query_new_user_insert) {
                        $this->messages[] = "<strong>WINNING!</strong> Request successfully received.";
                        $last_insert = $this->db_connection->insert_id;
                        $this->sendRequestReceived($last_insert);
                    } else {
                        $this->errors[] = "Sorry something broke.";
                    }
                }     
            } else {
                $this->errors[] = "Sorry, no database connection.";
            }
        }
        
    }
    
    // END OF THE ROAD BUDDY
}
    
