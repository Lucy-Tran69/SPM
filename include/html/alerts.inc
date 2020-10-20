<!-- Error modal -->
<div class="modal fade" id="alertFail">
    <div class="modal-dialog">
        <div class="modal-content bg-danger">
            <div class="modal-header">
                <h4 class="col-12 modal-title text-center">Error</h4>
            </div>
            <div class="modal-body" style="text-align: center;">
                <p id="alertFailText">&hellip;</p>
            </div>
            <div class="modal-footer justify-content">
                <button type="button" class="btn btn-outline-light" data-dismiss="modal" onclick="$('#searchForm').submit();">はい</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- Success modal -->
<div class="modal fade" id="alertSuccess">
    <div class="modal-dialog">
        <div class="modal-content bg-success">
            <div class="modal-header">
                <h4 class="col-12 modal-title text-center">Success</h4>
            </div>
            <div class="modal-body" style="text-align: center;">
                <p id="alertSuccessText">&hellip;</p>
            </div>
            <div class="modal-footer justify-content">
                <button type="button" class="btn btn-outline-light" data-dismiss="modal" onclick="$('#searchForm').submit();">はい</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- Warning modal -->
<div class="modal fade" id="alertWarning">
    <div class="modal-dialog">
        <div class="modal-content bg-warning">
            <div class="modal-header">
                <h4 class="col-12 modal-title text-center">Warning</h4>
            </div>
            <div class="modal-body" style="text-align: center;">
                <p id="alertWarningText">&hellip;</p>
            </div>
            <div class="modal-footer justify-content">
                <button type="button" class="btn btn-outline-light" data-dismiss="modal" onclick="warningOk();">はい</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>