$(".addDel").click(function() {
    event.preventDefault();
    createDeliverableForm();
});

$("#submitScope").click(function() {
    var that = $(this);
    
    that.button('loading');
    
    $.ajax({
        type: 'POST',
        url: 'scope.php',
        data: $("form").serialize(),
        success: function(msg){
            //var current = $('#infoMessage').html(),
            //    newMsg = current + " " + msg;
            $("#formsToHide").slideUp()
            $('#infoMessage').html(msg);
        }
    });
});

function createDeliverableForm() {
    var form = $('.deliverableForm'),
        n = form.length,
        container = $('#deliverableFormContainer');
        
    var table = "<form method='post' post='scope.php' name='deliverableForm-"+n+"' id='deliverableForm-"+n+"' class='row deliverableForm'><div class='form-group col-sm-8'><label for='st_name-"+n+"'>Deliverable</label><input class='form-control' placeholder='Name' name='st_name-"+n+"' id='st_name-"+n+"' /></div><div class='form-group col-sm-4'><label for='st_due-"+n+"'>Due</label><input class='form-control' type='date' name='st_due-"+n+"' id='st_due-"+n+"' /></div><div class='form-group col-sm-8'><textarea class='form-control' rows='1' placeholder='Comment' name='st_comment-"+n+"' id='st_comment-"+n+"'></textarea></div></form>";
    
    container.append(table);
   
    $("html, body").animate({ scrollTop: $(document).height() }, "slow");
}

/*
$.ajax({
    type: 'POST',
    url: 'ajax.php',
    data: $("form").serialize(),
    success: function(msg){
        var current = $('#infoMessage').html(),
            newMsg = current + " " + msg;
        $('#infoMessage').html(newMsg);
    }
});
*/

