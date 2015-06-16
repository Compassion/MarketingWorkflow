<?php
/** 
  * Class Request
  * handles the submission of requests
  */

class Request
{
    // Basic Class Setup
    private $db_connection = null;
    public $errors = array();
    public $messages = array();
    
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
        
        if (isset($_POST["request_made"])) {
            $this->makeRequest();
        }
    }
    
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
                    } else {
                        $this->errors[] = "Sorry something broke.";
                    }
                }     
            } else {
                $this->errors[] = "Sorry, no database connection.";
            }
        }
        
    }
    
}