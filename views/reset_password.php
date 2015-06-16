<?php
require_once('views/template/header.php'); 
require_once('core/config.php');
?>


<p class="white-text text-center"><?php
// show potential errors / feedback (from login object)
if (isset($login)) {
    if ($login->errors) {
        foreach ($login->errors as $error) {
            echo $error;
        }
    }
    if ($login->messages) {
        foreach ($login->messages as $message) {
            echo $message;
        }
    }
}
?></p>
                    
                  
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <!-- login form box -->
            <form method="post" action="index.php" name="resetform">
                    <input id="" class="new_input form-control" type="hidden" name="user_name" value="<?= $user_name ?>" />
                <div class="form-group">
                    <label for="new_input_password">New Password</label>
                    <input id="new_input_password" class="new_input form-control" type="password" name="user_password" autocomplete="off" required />
                </div>
                <div class="form-group">
                    <input type="submit"  name="reset" value="reset" class="btn btn-primary" />
                </div>
            </form>
        </div>
    </div>
</div>

		

        <script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<?php
require_once('views/template/footer.php'); ?>