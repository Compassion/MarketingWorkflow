<?php
/** 
  * Class Management
  * handles the management of requests. Essential it interfaces between ASANA and WorkFlow App
  */

require_once("classes/Asana.php");

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
            } else {
                $this->errors[] = "<strong>Ahhh!</strong> Sorry something broke. Status not updated";
            }

        } else {
            $this->errors[] = "<strong>Epic Fail!</strong> Sorry, no database connection.";
        }
    }
        
    // Create a scoping record
    public function createScopeRecord($post) 
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
        
}
    
