function updateButton(id, status) {
    var button = "#button" + id,
        btn = $(button);

        //btn.button('reset');

    if(status == "Approved") {
        btn.removeClass("btn-warning btn-danger").addClass("btn-success");
        btn.html("Approved <span class='glyphicon glyphicon-ok'></span>");
        console.log(status);
    } else if(status == "Declined") { 
        btn.removeClass("btn-warning btn-success").addClass("btn-danger");    
        btn.html("Declined <span class='glyphicon glyphicon-remove'></span>"); 
        console.log(status);           
    } else if(status == "Pending") {
        btn.removeClass("btn-success btn-danger").addClass("btn-warning"); 
        btn.html("<span class='glyphicon glyphicon-flag'></span> Escalated");  
        console.log(status);          
    } else if(status == "Backlog") {
        btn.removeClass("btn-success btn-danger").addClass("btn-warning"); 
        btn.html("<span class='glyphicon glyphicon-list-alt'></span> Backlog");  
        console.log(status);
    } else {
        btn.removeClass("btn-success btn-danger").addClass("btn-info"); 
        btn.html("<span class='glyphicon glyphicon-comment'></span> Roger!");  
        console.log(status);
    }
}

function addAjax(assigned, creator) {
    // Send to backlog
    // Get all backlog buttons
    for(j = 0; j < $(".backlog").length; j++ ) {
            // Get the request id and find the individual button
            var rqId = $($('.backlog')[j]).attr('data-val');

            var backlog = "#backlog" + rqId;


            $(backlog).click(function(){
                event.preventDefault();
                // On click change the buttons status
                var rqId = $(this).attr("data-val"),
                    btn = "#button" +rqId;
                    $(btn).button('loading');
                // Do the Ajax
                $.ajax({
                    type: "GET",
                    url: "manage.php",
                    data: 'ajax=true&audit=send+to+backlog&rq_id=' + rqId + '&send_to=Backlog',
                    success: function(msg){
                        var current = $('#infoMessage').html(),
                            newMsg = current + " " + msg;
                        $('#infoMessage').html(newMsg);
                        updateButton(rqId, "Backlog");
                    }
                }); // Ajax Call

            });
        }

    // Escalate action
    // Get all pending buttons
    for(i = 0; i < $(".pending").length; i++ ) {
        // Get the request id and find the individual button
        var rqId = $($('.pending')[i]).attr('data-val');

        var pending = "#pending" + rqId;

        $(pending).click(function(){
            event.preventDefault();
            // On click change the buttons status
            var rqId = $(this).attr("data-val"),
                btn = "#button" +rqId;
                $(btn).button('loading');
            // Do the Ajax
            $.ajax({
                type: "GET",
                url: "manage.php",
                data: 'ajax=true&audit=escalation&rq_id=' + rqId + '&send_to=Pending',
                success: function(msg){
                    var current = $('#infoMessage').html(),
                        newMsg = current + " " + msg;
                    $('#infoMessage').html(newMsg);
                    updateButton(rqId, "Pending");
                }
            }); // Ajax Call

        });
    }

    // Approve with audit creation!
    // Get all approve buttons
    for(i = 0; i < $(".approve").length; i++ ) {
            // Get the request id and find the individual button
            var rqId = $($('.approve')[i]).attr('data-val');

            var approve = "#approve" + rqId;


            $(approve).click(function(){
                event.preventDefault();
                // On click change the buttons status
                var rqId = $(this).attr("data-val"),
                    btn = "#button" +rqId;

                    $(btn).button('loading');

                var status = encodeURI("Scope Approved");

                var ajaxUrl = 'ajax=true&audit_approve=true&rq_id='+rqId+'&status='+status+'&creator='+creator+'&assigned='+assigned;


                // Do the Ajax
                $.ajax({
                    type: "GET",
                    url: "approve.php",
                    data: ajaxUrl,
                    success: function(msg){
                        var current = $('#infoMessage').html(),
                            newMsg = current + " " + msg;
                        $('#infoMessage').html(newMsg);
                        updateButton(rqId, "Approved");
                    }
                }); // Ajax Call

            });
        }

    // Decline with audit creation!
    // Get all decline buttons
    for(i = 0; i < $(".decline").length; i++ ) {
            // Get the request id and find the individual button
            var rqId = $($('.decline')[i]).attr('data-val');

            var decline = "#decline" + rqId;


            $(decline).click(function(){
                event.preventDefault();
                // On click change the buttons status
                var rqId = $(this).attr("data-val"),
                    btn = "#button" +rqId;
                    $(btn).button('loading');

                var status = encodeURI("Scope Declined");

                var ajaxUrl = 'ajax=true&audit_approve=true&rq_id='+rqId+'&status='+status+'&creator='+creator+'&assigned='+assigned;
                // Do the Ajax
                $.ajax({
                    type: "GET",
                    url: "approve.php",
                    data: ajaxUrl,
                    success: function(msg){
                        var current = $('#infoMessage').html(),
                            newMsg = current + " " + msg;
                        $('#infoMessage').html(newMsg);
                        updateButton(rqId, "Declined");
                    }
                }); // Ajax Call

            });
        }

    // Rescope!
    // Request rescope
    for(j = 0; j < $(".rescope").length; j++ ) {
        // Get the request id and find the individual button
        var rqId = $($('.rescope')[j]).attr('data-val');

        var rescope = "#rescope" + rqId;


        $(rescope).click(function(){
            event.preventDefault();
            // On click change the buttons status
            var rqId = $(this).attr("data-val"),
                btn = "#button" +rqId;
                $(btn).button('loading');
            // Do the Ajax
            $.ajax({
                type: "GET",
                url: "manage.php",
                data: 'ajax=true&audit=rescope&rq_id=' + rqId + '&send_to=Query',
                success: function(msg){
                    var current = $('#infoMessage').html(),
                        newMsg = current + " " + msg;
                    $('#infoMessage').html(newMsg);
                    updateButton(rqId, "Query");
                }
            }); // Ajax Call

        });
    }
    
    // Pre approve!
    // Pre approval for scoping
    for(j = 0; j < $(".preapprove").length; j++ ) {
        // Get the request id and find the individual button
        var rqId = $($('.preapprove')[j]).attr('data-val');

        var preapprove = "#preapprove" + rqId;

        $(preapprove).click(function(){
            event.preventDefault();
            // On click change the buttons status
            var rqId = $(this).attr("data-val"),
                btn = "#button" +rqId;
                $(btn).button('loading');
            // Do the Ajax
            $.ajax({
                type: "GET",
                url: "manage.php",
                data: 'ajax=true&audit=pre+approved+for+scoping&rq_id=' + rqId + '&send_to=Query',
                success: function(msg){
                    var current = $('#infoMessage').html(),
                        newMsg = current + " " + msg;
                    $('#infoMessage').html(newMsg);
                    updateButton(rqId, "Query");
                }
            }); // Ajax Call
        });
    }
    
    // Decline thanks to chili!
    for(j = 0; j < $(".chilidecline").length; j++ ) {
        // Get the request id and find the individual button
        var rqId = $($('.chilidecline')[j]).attr('data-val');

        var chilidecline = "#chilidecline" + rqId;

        $(chilidecline).click(function(){
            event.preventDefault();
            // On click change the buttons status
            var rqId = $(this).attr("data-val"),
                btn = "#button" +rqId;
                $(btn).button('loading');
            // Do the Ajax
            $.ajax({
                type: "GET",
                url: "manage.php",
                data: 'ajax=true&audit=to+use+Chili&rq_id=' + rqId + '&send_to=Declined',
                success: function(msg){
                    var current = $('#infoMessage').html(),
                        newMsg = current + " " + msg;
                    $('#infoMessage').html(newMsg);
                    updateButton(rqId, "Declined");
                }
            }); // Ajax Call
        });
    }
    // Remove from backlog AKA DELETE!!!
    for(j = 0; j < $(".delete").length; j++ ) {
        // Get the request id and find the individual button
        var rqId = $($('.delete')[j]).attr('data-val');

        var delet = "#delete" + rqId;

        $(delet).click(function(){
            event.preventDefault();
            // On click change the buttons status
            var rqId = $(this).attr("data-val"),
                btn = "#button" +rqId;
                $(btn).button('loading');
            // Do the Ajax
            $.ajax({
                type: "GET",
                url: "manage.php",
                data: 'ajax=true&audit=remove+from+backlog&rq_id=' + rqId + '&send_to=Declined',
                success: function(msg){
                    var current = $('#infoMessage').html(),
                        newMsg = current + " " + msg;
                    $('#infoMessage').html(newMsg);
                    updateButton(rqId, "Declined");
                }
            }); // Ajax Call
        });
    }
    
    // Move back to plan
    for(j = 0; j < $(".re_approve").length; j++ ) {
        // Get the request id and find the individual button
        var rqId = $($('.re_approve')[j]).attr('data-val');

        var re_approve = "#re_approve" + rqId;

        $(re_approve).click(function(){
            event.preventDefault();
            // On click change the buttons status
            var rqId = $(this).attr("data-val"),
                btn = "#button" +rqId;
                $(btn).button('loading');
            // Do the Ajax
            $.ajax({
                type: "GET",
                url: "manage.php",
                data: 'ajax=true&audit=retry+scheduling&rq_id=' + rqId + '&send_to=Approved',
                success: function(msg){
                    var current = $('#infoMessage').html(),
                        newMsg = current + " " + msg;
                    $('#infoMessage').html(newMsg);
                    updateButton(rqId, "Approved");
                }
            }); // Ajax Call
        });
    }
    
    // Add comment modal popup
    for(j = 0; j < $(".comment").length; j++ ) {
        // Get the request id and find the individual button
        var rqId = $($('.comment')[j]).attr('data-val');

        var comment = "#comment" + rqId;

        $(comment).click(function(){
            event.preventDefault();
            // On click change the buttons status
            var rqId = $(this).attr("data-val"),
                btn = "#button" +rqId;
            $('#commentRqId').val(rqId);
            $('#commentModal').modal('show'); 
        });
    }
    // Create comment as audit 
    $('#submitComment').click(function(){
            event.preventDefault();
            // On click change the buttons status
            $.ajax({
                type: 'POST',
                url: 'manage.php',
                data: $("#commentForm").serialize(),
                success: function(msg){
                    //var current = $('#infoMessage').html(),
                    //    newMsg = current + " " + msg;
                    $('#commentModal').modal('hide');
                    $('#infoMessage').html(msg);
                }
            });
    });
    
    
    // Add comment modal popup
    for(j = 0; j < $(".reassign").length; j++ ) {
        // Get the request id and find the individual button
        var rqId = $($('.reassign')[j]).attr('data-val');
        console.log(rqId);
        var reassign = "#reassign" + rqId;
        
        $(reassign).click(function(){
            event.preventDefault();
            
            // On click change the buttons status
            var rqId = $(this).attr("data-val"),
                btn = "#button" +rqId,
                assignedTo = $("#heading" + rqId).attr('data-assigned');
            
            $('#reassignRqId').val(rqId);
            $('#reassignAssignedTo').val(assignedTo);
            $('#reassignModal').modal('show'); 
            console.log(assignedTo);
        });
    }
    // Create reassign as audit 
    $('#submitReassign').click(function(){
            event.preventDefault();
            // On click change the buttons status
            $.ajax({
                type: 'POST',
                url: 'manage.php',
                data: $("#reassignForm").serialize(),
                success: function(msg){
                    //var current = $('#infoMessage').html(),
                    //    newMsg = current + " " + msg;
                    $('#reassignModal').modal('hide');
                    $('#infoMessage').html(msg);
                }
            });
    });
}