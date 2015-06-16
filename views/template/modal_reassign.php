<!-- Reassign Modal -->
<div class="modal fade" id="reassignModal" tabindex="-1" role="dialog" aria-labelledby="reassignModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="reassignModalHeading">Reassign task</h4>
      </div>
      <form class="modal-body form-group" method="post" action="manage.php" name="reassignForm" id="reassignForm">
          <input type="hidden" name="audit" value="reassign" />
          <input type="hidden" name="rq_id" value="" id="reassignRqId" />
          <input type="hidden" name="currently_assigned" value="" id="reassignAssignedTo" />
          <input type="hidden" name="status" value="Task reassigned" id="reassignStatus" />
          <input type="hidden" name="creator" value="<?= $_SESSION['user_email'] ?>" id="reassignCreator" />
          <label for="reassign">Who should this task be reassigned to?</label>
          <select name="reassign_to" type="text" class="form-control" id="reassign" rows="2">
            <option value="Product Area 1">Danny McKibben - Advocacy/Fundraising/Trip</option>
            <option value="Product Area 3">Eddie Figueroa - Events/Ambassadors</option>
            <option value="Product Area 2">Hayley Hughes - Major Givers/Church</option>
            <option value="Marketing Manager">Jono Kirk</option>
            <option value="Workflow">Laura Allen - Ongoing Servicing</option>
          </select>
      </form>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="submitReassign">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- // Reassign Modal -->