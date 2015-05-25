<?php require_once('views/template/header.php'); ?>
<?php require_once('core/functions.php'); ?>

    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
      
                <h3>Hi <?php echo $_SESSION['user_name']; ?> <small><a href="index.php?logout" class="pull-right btn btn-default">Logout</a></small></h3>
                <span id="msg"></span>
                
                <?php /*var_dump($_SESSION);
                        echo date("Y-m-d");*/
                ?>
                <div class="list-group">
                    <a href='#' class='list-group-item active'>Menu</a>
                    <?php displayMenu($_SESSION['user_group']); ?>
                </div>
            </div>
        </div>
    </div>


    <script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

<?php
require_once('views/template/footer.php'); ?>