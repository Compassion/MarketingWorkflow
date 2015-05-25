<?php 
require_once('views/template/header.php');
?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h1>Make a request</h1>
                <p class="text-center">
                <?php
                // show potential errors / feedback (from registration object)

                
                if (isset($request)) {
                    if ($request->errors) {
                        foreach ($request->errors as $error) {
                            echo $error;
                        }
                    }
                    if ($request->messages) {
                        foreach ($request->messages as $message) {
                            echo $message;
                        }
                    }
                } 
                ?></p>
                
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
                        <textarea id="rq_desc" class="login_input form-control" type="text" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="date_due">Date due</label>
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
                        <input type="submit" class="btn btn-primary" name="request_made" value="Request" />
                    </div>
                </form>

                <!-- backlink -->
                <a href="index.php">Back to Menu</a>
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