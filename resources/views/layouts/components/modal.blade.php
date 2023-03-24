<div class="modal fade" id="balance" tabindex="-1" aria-labelledby="balance" aria-hidden="true">
    <div class="modal-dialog secondary">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="balance">Edit Balance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form enctype="multipart/form-data" method="POST" action="{{route('update-balance')}}">
            @csrf
            <div class="modal-body">
                <label for="balance">Balance:</label><br>
                <input type="number" id="balance" name="balance" step="0.01" min="0.1" value="{{Auth::user()->balance}}">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Save changes</button>
            </div>
        </form>
      </div>
    </div>
</div>
