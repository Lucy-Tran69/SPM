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
<!-- Toastr -->
<script src="../../include/template/plugins/toastr/toastr.min.js"></script>
<!-- jquery-validation -->
<script src="../../include/template/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="../../include/template/plugins/jquery-validation/additional-methods.min.js"></script>
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
                        return '<div class="row justify-content-center"><a href="' + data + '" class="btn btn-success" style="margin-right: 5px;"><i class="fas fa-pencil-alt"></i> 変更</a><button type="button" class="btn btn-danger js-deleteTopic"><i class="fas fa-trash"></i> 削除</button></div>';
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
                            return '<div class="row justify-content-center"><a href="' + data + '" class="btn btn-success" style="margin-right: 5px;"><i class="fas fa-pencil-alt"></i> 変更</a><button type="button" class="btn btn-danger js-deleteTopic"><i class="fas fa-trash"></i> 削除</button></div>';
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
                    toastr.success('You have successfully deleted "' + topicTitle + '"');
                    $(topicTable).DataTable().ajax.reload(null, false);
                });
            }
        });

        
        try {
            const datepickerBirthday = $('.datetimepicker-input');
            datepickerBirthday.daterangepicker({
                singleDatePicker: true,
                autoUpdateInput: true,
                minYear: 1901,
                minDate: moment(),
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 10,
                locale: {
                    format: 'YYYY-MM-DD HH:mm:ss',
                    daysOfWeek: ['日', '月', '火', '水', '木', '金', '土'],
                    monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
                },
            });
            $(datepickerBirthday).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
            });
        } catch (er) {
            console.log(er);
        }

        $.validator.setDefaults({
            submitHandler: function () {
                alert("Form successful submitted!");
            }
        });

        $('#addTopic').validate({
            rules: {
                title: {
                    required: true,
                },
                openDay: {
                    required: true,
                },
                body: {
                    required: true,
                },
                imgFile: {
                    required: true,
                },
            },
            messages: {
                title: "タイトルを入力して下さい",
                openDay: "公開日を入力して下さい",
                body: "本文を入力して下さい",
                imgFile: "画像を入力して下さい",
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
</body>

</html>