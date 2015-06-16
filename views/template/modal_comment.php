<!-- Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Post Comment</h4>
      </div>
      <form class="modal-body form-group" method="post" action="manage.php" name="commentForm" id="commentForm">
          <input type="hidden" name="audit" value="comment" />
          <input type="hidden" name="rq_id" value="" id="commentRqId" />
          <input type="hidden" name="status" value="<?php echo ucfirst($status) . " comment"; ?>" id="commentStatus" />
          <input type="hidden" name="creator" value="<?= $_SESSION['user_email'] ?>" id="commentCreator" />
          <label for="comment">Comment for audit log</label>
          <textarea name="comment" type="text" class="form-control" id="commentComment" rows="2"></textarea>
      </form>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="submitComment">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- // Comment Modal -->