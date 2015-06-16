<?php require_once('views/template/header.php'); ?>

    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
      
                <h3>Hi <?php echo $_SESSION['user_name']; ?> <small><a href="index.php?logout" class="pull-right btn btn-default">Logout</a></small></h3>
                <hr />
                <br />
                <span id="infoMessage"></span>
                <div class="list-group">
                    <a href='#' class='list-group-item active'>Menu</a>
                    <?php displayMenu($_SESSION['user_group'], $backlogCount, $queryCount, $scopeCount, $approveCount, $pendingCount); ?>
                </div>
            </div>
        </div>
    </div>


    <script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        
        function updateButton(id, status) {
            var button = id,
                btn = $(button);

                //btn.button('reset');

            if(status == "Success") {
                btn.removeClass("btn-warning btn-danger").addClass("btn-success");
                btn.html("<span class='glyphicon glyphicon-ok'></span>");
                console.log(status);
            }
        }
        
        $(document).ready(function() {
            var btn = "#syncBtn";
                    $(btn).attr('data-loading-text', "<span class='glyphicon glyphicon-hourglass'></span> Updating, please wait...");
            // Get the request id and find the individual button
            $("#syncPanel").click(function(){
                event.preventDefault();
                // On click change the buttons status
                var btn = "#syncBtn";
                    $("#syncPanel").addClass("list-group-item-warning");
                    $(btn).button('loading');
                    $(btn).removeClass("btn-success btn-danger").addClass("btn-warning");
                // Do the Ajax
                $.ajax({
                    type: "GET",
                    url: "manage.php",
                    data: 'ajax=true&sync=true',
                    success: function(msg){
                        var current = $('#infoMessage').html(),
                            newMsg = current + " " + msg;
                        $('#infoMessage').html(newMsg);
                        updateButton(btn, "Success");
                        $("#syncPanel").removeClass("list-group-item-warning");
                    }
                }); // Ajax Call

            });
            
            $("#linkGen").click(function(){
                var user = $("#usernameInput").val(),
                    url = 'cmVzZXQ=' + btoa(user);
                
                $('#pw_here').html('You\' reset URL is <br /> /index.php?' + url);
            });
            
        });
    </script>

<?php
require_once('views/template/footer.php'); ?>