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
                    return '<div class="row justify-content-center"><a href="edit-topic.html?id=' + data + '" class="btn btn-success" style="margin-right: 5px;"><i class="fas fa-pencil-alt"></i> 変更</a><button type="button" class="btn btn-danger js-deleteTopic"><i class="fas fa-trash"></i> 削除</button></div>';
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
                        return '<div class="row justify-content-center"><a href=edit-topic.html?id="' + data + '" class="btn btn-success" style="margin-right: 5px;"><i class="fas fa-pencil-alt"></i> 変更</a><button type="button" class="btn btn-danger js-deleteTopic"><i class="fas fa-trash"></i> 削除</button></div>';
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
            $.post('../../include/topics.php', { 'topicID': topicID }, function (data) {
                toastr.success('You have successfully deleted "' + topicTitle + '"');
                $(topicTable).DataTable().ajax.reload(null, false);
            });
        }
    });

    $('#openDay').datetimepicker({
        useCurrent: false,
        date: date,
        minDate: date,
        format: 'YYYY-MM-DD HH:mm:ss'
    });

    $('#closeDay').datetimepicker({
        useCurrent: false,
        minDate: date,
        format: 'YYYY-MM-DD HH:mm:ss'
    });

    $("#openDay").on("change.datetimepicker", function (e) {
        $('#closeDay').datetimepicker('minDate', e.date);
    });
    $("#closeDay").on("change.datetimepicker", function (e) {
        $('#openDay').datetimepicker('maxDate', e.date);
    });

    // /*add method validate date format*/
    // $.validator.addMethod('dateFormat', function (value, element) {
    //         let regEx = /^\d{4}-\d{2}-\d{2}$/;
    //         if (!value.match(regEx)) return false;

    //         let inputDate = new Date(value);
    //         let currentDate = new Date();

    //         if (!inputDate.getTime() && inputDate.getTime() !== 0) return false;

    //         return inputDate.toISOString().slice(0, 10) === value;
    //     }, 'Please enter a date in the format yyyy-mm-dd.');

    $('#addTopic').validate({
        rules: {
            title: {
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
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
});

function redirectAddOrEditTopic() {
    window.location.href = "../../app/topic/add-topic.html";
}