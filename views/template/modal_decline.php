<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" role="dialog" aria-labelledby="declineModal" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="manage.php" name="declineForm" id="declineForm">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Decline Reason</h4>
          <input type="hidden" name="request_id" value="" id="declineRqId" />
      </div>
      <div class="modal-body form-group">
          <label for="decline_reason">Reason for declining?</label>
          <textarea name="decline_reason" type="text" class="form-control" id="decline_reason" rows="2"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Decline</button>
      </div>
    </form>
  </div>
</div>
<!-- // Decline Modal -->