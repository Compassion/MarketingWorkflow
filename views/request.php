<?php 
require_once('views/template/header.php');
?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h3>Make a request <small><a href="index.php" class="pull-right btn btn-default">Menu</a></small></h3>
                <hr />
                <p class="text-center">
                <?php
                // show potential errors / feedback (from registration object)
                $alertTop_Danger = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
                $alertTop_Success = '<div class="alert alert-success alert-dismissible fade in" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
                $alertEnd = '</div>';
                
                if (isset($request)) {
                    if ($request->errors) {
                        foreach ($request->errors as $error) {
                            echo $alertTop_Danger;
                            echo $error;
                            echo $alertEnd;
                        }
                    }
                    if ($request->messages) {
                        foreach ($request->messages as $message) {
                            echo $alertTop_Success;
                            echo $message;
                            echo $alertEnd;
                        }
                    }
                } 
                ?></p>
                
                <br />
                <div id="infoMessage"></div>
                <!-- register form -->
                <form method="post" action="request.php" name="requestform" id="requestForm">
                    <input type="hidden" name="request_maker" value="<?php echo $_SESSION["user_email"] ?>" />
                    <input type="hidden" name="date_created" value="<?php echo date('Y-m-d'); ?>" />
                    
                    <div class="form-group">
                        <label for="request_name">Request name</label>
                        <input id="request_name" class="login_input form-control" type="text" name="request_name" required />
                    </div>
                    <div class="form-group">
                        <label for="rq_desc">Description</label>
                        <textarea id="rq_desc" class="login_input form-control" type="text" name="description" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="date_due">Date required</label>
                        <input id="date_due" class="login_input form-control" type="date" name="date_due" required autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="request_type">Request Type</label>
                        <select name="request_type" class="form-control">
                            <option value="New Marketing Request">New Marketing Request</option>
                            <option value="Ongoing Servicing">Ongoing Servicing</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="request_category">Category</label>
                        <select name="request_category" class="form-control">
                            <option value="Advocacy/Fundraising">Advocacy/Fundraising</option>
                            <option value="Events">Events</option>
                            <option value="Major Givers">Major Givers</option>
                            <option value="Trip">Trip</option>
                            <option value="Church">Church</option>
                            <option value="Ambassadors">Ambassadors</option>
                            <option value="Other">Other - Bequests, Schools, Ministry Parnters etc.</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" name="request_made" value="Request" />
                    </div>
                </form>

            </div>
        </div>
    </div>
    
     
<script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
                <?php /*if (isset($request)) {
                   echo "<script type='text/javascript>";
                   echo '$("requestForm :input").attr("disabled", true);';
                   echo "</script>"; 
                }*/ ?>

<?php
require_once('views/template/footer.php'); ?>