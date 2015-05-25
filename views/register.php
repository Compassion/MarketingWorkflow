<?php 
require_once('views/template/header.php');
?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <p class="text-center">
                <?php
                // show potential errors / feedback (from registration object)
                if (isset($registration)) {
                    if ($registration->errors) {
                        foreach ($registration->errors as $error) {
                            echo $error;
                        }
                    }
                    if ($registration->messages) {
                        foreach ($registration->messages as $message) {
                            echo $message;
                        }
                    }
                }
                ?></p>

                <div id="infoMessage"></div>
                <!-- register form -->
                <form method="post" action="register.php" name="registerform">
                    <div class="form-group">
                        <!-- the user name input field uses a HTML5 pattern check -->
                        <label for="login_input_username">Username (only letters and numbers, 2 to 64 characters)</label>
                        <input id="login_input_username" class="login_input form-control" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" required />
                    </div>
                    <div class="form-group">
                        <!-- the email input field uses a HTML5 email type check -->
                        <label for="login_input_email">User's email</label>
                        <input id="login_input_email" class="login_input form-control" type="email" name="user_email" required />
                    </div>
                    <div class="form-group">
                        <label for="login_input_password_new">Password (min. 6 characters)</label>
                        <input id="login_input_password_new" class="login_input form-control" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="login_input_password_repeat">Repeat password</label>
                        <input id="login_input_password_repeat" class="login_input form-control" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="user_group">User group</label>
                        <select name="user_group" class="form-control">
                            <option value="Admin">Admin</option>
                            <option value="Workflow">Workflow</option>
                            <option value="Marketing Manager">Marketing Manager</option>
                            <option value="Coms Manager">Coms Manager</option>
                            <option value="Creative Manager">Creative Manager</option>
                            <option value="Product Area 1">Product Area 1</option>
                            <option value="Product Area 2">Product Area 2</option>
                            <option value="Product Area 3">Product Area 3</option>
                            <option value="Requester">Requester</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <input id="department" class="login_input form-control" type="text" name="department" required autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="position">Position</label>
                        <input id="position" class="login_input form-control" type="text" name="position" required autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" name="register" value="Register" />
                    </div>
                </form>

                <!-- backlink -->
                <a href="index.php">Back to Login Page</a>
            </div>
        </div>
    </div>
     
<script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		<script type="text/javascript">
			$(function() {
				$('#myModal').modal('show');
			});
		</script>
<?php
require_once('views/template/footer.php'); ?>