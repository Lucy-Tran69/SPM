$(function () {
    const topicTable = $('#topicsTable');
    var table = topicTable.DataTable({
        'processing': true,
        'serverSide': true,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': false,
        'deferRender': true,
        "lengthChange": false,
        'serverMethod': 'post',
        'ajax': {
            'url': 'topics.php'
        },
        'columns': [
            { data: 'insert_day', "defaultContent": "", "title": '作成日' },
            { data: 'open_day', "defaultContent": "", "title": '公開日時' },
            { data: 'close_day', "defaultContent": "", "title": '終了日' },
            { data: 'title', "defaultContent": "", "title": 'タイトル' },
            {
                data: "no",
                render: function (data, type, row) {
                    return '<div class="row justify-content-center"><a href=edit-topic.html?id=' + data + ' class="btn btn-success margin-bottom-5px m-r-0" style="margin-right: 5px;"><i class="fas fa-pencil-alt mr-2"></i> 変更</a><a href="" class="btn btn-danger js-deleteTopic"><i class="fas fa-trash mr-2"></i> 削除</a></div>';
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
            'info': false,
            'autoWidth': false,
            'deferRender': true,
            "ajax": {
                type: 'POST',
                url: 'topics.php',
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
                        return '<div class="row justify-content-center"><a href=edit-topic.html?id=' + data + ' class="btn btn-success margin-bottom-5px m-r-0" style="margin-right: 5px;"><i class="fas fa-pencil-alt mr-2"></i> 変更</a><a href="" class="btn btn-danger js-deleteTopic"><i class="fas fa-trash mr-2"></i> 削除</a></div>';
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
            $.post('topics.php', { 'topicID': topicID, 'topicTitle': topicTitle }, function (data) {
                $("#flash-message").html(response);
                $(topicTable).DataTable().ajax.reload(null, false);
            });
        }
    });

    var date = new Date();
    $('#openDay').datetimepicker({
        useCurrent: false,
        date: date,
        minDate: date,
        format: 'YYYY/MM/DD'
    });

    $('#closeDay').datetimepicker({
        useCurrent: false,
        minDate: date,
        format: 'YYYY/MM/DD'
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

    var formAddTopic = $('#addTopic');
    formAddTopic.validate({
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
        }
    });

    $('.btn-modal-submit').click(function () {
        formAddTopic.submit();
        $("#previewTopic").modal('hide');
    });

    formAddTopic.submit(function (event) {
        event.preventDefault();
        if (!formAddTopic.valid()) {
            return false;
        }
        var file_data = $('#imgFile').prop('files')[0];
        var form_data = new FormData();
        form_data.append('imgFile', file_data);
        form_data.append('no', $("#no").val());
        form_data.append('title', $("#title").val());
        form_data.append('openDay', $(".openDay").val());
        form_data.append('closeDay', $(".closeDay").val());
        form_data.append('body', $("#body").val());
        form_data.append('imgLink', $("#imgLink").val());
        form_data.append('titleLink', $("#titleLink").val());
        form_data.append('urlImage', $("#urlImage").val());

        $.ajax({
            url: "createTopic.php",
            type: "POST",
            contentType: false,
            processData: false,
            data: form_data,
            dataType: "html",
            success: function (res) {
                debugger
                //check response is blank if success 
                if (!$.trim(res)) {
                    window.location.href = "index.html";
                }
                // if error
                else {
                    $("#flash-message").html(res);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // alert( "Bug" );
            },
        });
    });

});

function redirectAddOrEditTopic() {
    window.location.href = "add-topic.html";
}

function onBtnclick() {
    // debugger
    var preTitle = $("#title").val();
    var preOpenDay = $(".openDay").val();
    var preBody = $("#body").val();
    var preImgLink = $("#imgLink").val();
    var preLinkTitle = $("#titleLink").val();
    var preLink = $("#urlImage").val();

    var opday = new Date(preOpenDay);
    var currentDate = new Date();
    if (opday.setDate(opday.getDate() + 7) >= currentDate) {
        $("#newTitle").text("New!");
    }
    preBody = preBody.replace(/\n/g, '<br>');

    if (!preImgLink.match("^http") && preImgLink != '') {
        preImgLink = '//' + preImgLink;
    }
    else {
        preImgLink = preImgLink;
    }

    var linkCode = '<a target="_blank" href="%link%">%title%</a>';

    if (!preLink.match("^http") && preLink != '') {
        preLink = '//' + preLink;
    }
    else {
        preLink = preLink;
    }

    if (preLink != '' && preLinkTitle == '') {
        linkCode = linkCode.replace('%link%', preLink);
        linkCode = linkCode.replace('%title%', preLink);
    }
    else if (preLink != '' && preLinkTitle != '') {
        linkCode = linkCode.replace('%link%', preLink);
        linkCode = linkCode.replace('%title%', preLinkTitle);
    }
    else {
        linkCode = '';
    }

    $("#pr-link").empty();
    $("#pr-link").append(linkCode);

    $("#preTitle").text(preTitle);
    $("#preOpenday").text(preOpenDay);
    $("#preBody").empty();
    $("#preBody").append(preBody);
    $("#preImgLink").attr("href", preImgLink);


    var img = $('#imgFile').val();
    if (img == '') {
        $('#img').hide();
    }

    $("#previewTopic").modal('show');
}

function preview_image(event) {
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById('output_image');
        output.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}

function redirectListTopic() {
    window.location.href = "index.html";
}
