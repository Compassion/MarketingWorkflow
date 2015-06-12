<!-- escalate Modal -->
<div class="modal fade" id="escalateModal" tabindex="-1" role="dialog" aria-labelledby="escalateModal" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="manage.php" name="escalateForm" id="escalateForm">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Escalate Reason</h4>
          <input type="hidden" name="request_id" value="" id="escalateRqId" />
      </div>
      <div class="modal-body form-group">
          <label for="escalate_reason">Reason for declining?</label>
          <textarea name="escalate_reason" type="text" class="form-control" id="escalate_reason" rows="2"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Escalate</button>
      </div>
    </form>
  </div>
</div>
<!-- // escalate Modal -->