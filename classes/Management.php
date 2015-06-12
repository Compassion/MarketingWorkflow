<?php
/** 
  * Class Management
  * handles the management of requests. Essential it interfaces between ASANA and WorkFlow App
  */

require_once("classes/Asana.php");
require_once("core/functions.php");

class Management
{
    // Basic Class Setup
    private $db_connection = null;
    public $errors = array();
    public $messages = array();
    
    public $asana = null;
    
    // On init check post variables and then submit request
    public function __construct()
    {
        session_start();
        
        // Create Asana Class
        $this->asana = new Asana(array('apiKey' => KEY_ASANA));
        
        if (isset($_POST['submit_to_asana'])) {
            $this->sendRequestToAsana($_POST['submit_to_asana']);
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
                $comment = $audit['comment'];
                
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
            
            return $rq_list->fetch_assoc();
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
                        $work = $tag[1];
                        
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
            $person_assigned = $this->db_connection->real_escape_string(strip_tags($task['person_assigned'], ENT_QUOTES));
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
    private function sendRequestToAsana($rq_id) 
    {
        $options = array('opt_fields' => 'name,created_at,due_on,modified_at,tags, tags.name, tags.id, assignee.name, assignee.email');
        
        $task = $this->getTaskById($rq_id);
        $scope = $this->getScopeRecord($rq_id);
        
        $taskId = $rq_id;
        $name = $task['request_name'];
        $description = $task['description'] . "Request ID " . $taskId .", Requested by " . $task['request_maker'];
        $created_at = $task['date_created']; // This is READ ONLY :(
        $due_on = $task['date_due'];
        $projectId = $scope['project_assigned'];
        $assignee = $scope['scoper'];
        $followers = array(array(
            'email' => $scope['scoper']
        ));
        
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
            $this->messages[] = "<strong>Task created!</strong> Who\'s awesome? You are!";
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
    
    
    // END OF THE ROAD BUDDY
}
    
