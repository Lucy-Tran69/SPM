</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<footer class="main-footer">
    <strong>Copyright &copy; 2014-2019 <a href="#">NCI</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <!-- <b>Version</b> 3.0.5 -->
    </div>
</footer>
<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<!-- jQuery -->
<script src="../../include/template/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../../include/template/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="../../include/template/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- daterangepicker -->
<script src="../../include/template/plugins/moment/moment.min.js"></script>
<script src="../../include/template/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../../include/template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<!-- <script src="../include/template/plugins/summernote/summernote-bs4.min.js"></script> -->
<!-- overlayScrollbars -->
<script src="../../include/template/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../../include/template/dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="../include/template/dist/js/pages/dashboard.js"></script> -->
<!-- AdminLTE for demo purposes -->
<!-- <script src="../include/template/dist/js/demo.js"></script> -->
<!-- DataTables -->
<script src="../../include/template/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../include/template/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../include/template/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../include/template/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="https://adminlte.io/themes/AdminLTE/plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        const topicTable = $('#topicsTable');
        var table = topicTable.DataTable({
            'processing': true,
            'serverSide': true,
            'searching': false,
            'ordering': false,
            'info': true,
            'autoWidth': false,
            'responsive': true,
            'deferRender': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '../../include/topics.php'
            },
            'columns': [
                { data: 'insert_day', "defaultContent": "", "title": '作成日' },
                { data: 'open_day', "defaultContent": "", "title": '公開日時' },
                { data: 'close_day', "defaultContent": "", "title": '終了日' },
                { data: 'title', "defaultContent": "", "title": 'タイトル' },
                {
                    data: "no",
                    render: function (data, type, row) {
                        return '<div class="row justify-content-center"><a href="' + data + '" class="btn btn-success" style="margin-right: 5px;">変更</a><button type="button" class="btn btn-danger js-deleteTopic">削除</button></div>';
                    }
                },
            ],
        });

        $('#searchTopic').on('submit', function (e) {
            e.preventDefault();
            RequestData();
        });

        function RequestData() {
            let title = $('#title').val();
            let status = '';

            if ($('#status').is(":checked")) {
                status = $('#status').val();
            }

            if ($.fn.DataTable.isDataTable(topicTable)) {
                $(topicTable).DataTable().destroy();
            }

            $(topicTable).DataTable({
                'processing': true,
                'serverSide': true,
                'searching': false,
                'ordering': false,
                'info': true,
                'autoWidth': false,
                'responsive': true,
                'deferRender': true,
                "ajax": {
                    type: 'POST',
                    url: '../../include/topics.php',
                    data: { 'title': title, 'status': status },
                },
                "columns": [
                    { data: 'insert_day', "defaultContent": "", "title": '作成日' },
                    { data: 'open_day', "defaultContent": "", "title": '公開日時' },
                    { data: 'close_day', "defaultContent": "", "title": '終了日' },
                    { data: 'title', "defaultContent": "", "title": 'タイトル' },
                    {
                        data: "no",
                        render: function (data, type, row) {
                            return '<div class="row justify-content-center"><a href="' + data + '" class="btn btn-success" style="margin-right: 5px;">変更</a><button type="button" class="btn btn-danger js-deleteTopic">削除</button></div>';
                        }
                    },
                ],
            });
        }

        $('#topicsTable tbody').on('click', '.js-deleteTopic', function () {
            var row = $(this).closest('tr');

            var topicID = $(topicTable).DataTable().row(row).data()["no"];
            var topicTitle = $(topicTable).DataTable().row(row).data()["title"];

            let c = confirm('You want to delete "' + topicTitle + '"');

            if (c == true) {
                $(topicTable).DataTable().row(row).remove().draw(false);
                console.log(topicID);

                $.post('../../include/topics.php', { 'topicID': topicID }, function (data) {
                    $(topicTable).DataTable().ajax.reload(null, false);;
                });
            }
        });

        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        })
    });
</script>
</body>

</html>