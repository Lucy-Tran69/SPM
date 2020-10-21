$(function () {
    const topicTable = $('#topicsTable');
    // var currentURL = $(location).attr('href');
    var table = {
        'processing': true,
        'serverSide': true,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': false,
        'deferRender': true,
        "lengthChange": false,
        'serverMethod': 'post',
        'columns': [
            { data: 'insert_day', "defaultContent": "", "title": '作成日' },
            { data: 'open_day', "defaultContent": "", "title": '公開日' },
            { data: 'close_day', "defaultContent": "", "title": '終了日' },
            { data: 'title', "defaultContent": "", "title": 'タイトル' },
            {
                data: "no",
                render: function (data, type, row) {
                    return '<div class="row justify-content-center w-200px"><a href=edit-topic.html?id=' + data + ' class="btn btn-success m-b-5px m-r-0 m-b-0" style="margin-right: 5px;"><i class="fas fa-pencil-alt mr-2"></i> 変更</a><a href="" class="btn btn-danger js-deleteTopic m-l-5 m-b-5px res-button-del"><i class="fas fa-trash mr-2"></i> 削除</a></div>';
                }
            },
        ],
    };

    table.ajax = {
            url: 'topics.php',
            type: 'POST',
            data: function (d) {
                d.title = $('#title').val();
                d.status = $('#status').is(":checked") ? $('#status').val() : '';

                delete d.columns;
            },
            datatype: "json",

        };

    topicTable.DataTable(table);

    $('#searchTopic').on('submit', function () {
        $('#flash-message').remove();
        topicTable.DataTable().ajax.reload();
        new_url = window.location.search + '?title=' + title+ '&status=' + status;
        window.location.href = new_url;
    });

    $('#topicsTable tbody').on('click', '.js-deleteTopic', function (e) {
        e.preventDefault();
        var row = $(this).closest('tr');

        var topicID = $(topicTable).DataTable().row(row).data()["no"];
        var topicTitle = $(topicTable).DataTable().row(row).data()["title"];

        var messages = '「' + topicTitle + '」トピックスを削除します。よろしいですか？';

        $("#deleteMessage").text(messages);

        $("#confirmDelete").modal('show');

        $('#agreeDelete').on('click', function () {
            $(topicTable).DataTable().row(row).remove().draw(false);
            $.post('topics.php', { 'topicID': topicID, 'topicTitle': topicTitle }, function (data, response) {
                var html = '';
                if (response == "success") {
                    html += '<div class="alert dismissable alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>「' + topicTitle + '」トピックスの削除に成功しました。</div>';
                }
                else {
                    html += '<div class="alert dismissable alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>「' + topicTitle + '」トピックスの削除に失敗しました。</div>';
                }
                $('#flash-message').html(html);
                $(topicTable).DataTable().ajax.reload(null, false);
                $("#confirmDelete").modal('hide');
                $(window).scrollTop(0);
            });
        });
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

    $.validator.addMethod("accept", function (value, element, param) {

        var typeParam = typeof param === "string" ? param.replace(/\s/g, "") : "image/*",
            optionalValue = this.optional(element),
            i, file, regex;

        if (optionalValue) {
            return optionalValue;
        }

        if ($(element).attr("type") === "file") {

            typeParam = typeParam
                .replace(/[\-\[\]\/\{\}\(\)\+\?\.\\\^\$\|]/g, "\\$&")
                .replace(/,/g, "|")
                .replace(/\/\*/g, "/.*");

            if (element.files && element.files.length) {
                regex = new RegExp(".?(" + typeParam + ")$", "i");
                for (i = 0; i < element.files.length; i++) {
                    file = element.files[i];

                    if (!file.type.match(regex)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }, $.validator.format("アップロードできる画像形式はJPG、JPEG、PNG、GIFのみご入力ください。"));

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
                accept: "image/png, image/jpeg, image/jpg, image/PNG, image/JPG, image/JPEG, image/gif"
            },
        },
        messages: {
            title: "タイトルを入力して下さい",
            body: "本文を入力して下さい",
            imgFile: {
                extension: "アップロードできる画像形式はJPG、JPEG、PNG、GIFのみご入力ください。"
            }
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
        form_data.append('title', noscript($.trim($("#title").val())));
        form_data.append('openDay', $(".openDay").val());
        form_data.append('closeDay', $(".closeDay").val());
        form_data.append('body', noscript($.trim($("#body").val())));
        form_data.append('imgLink', noscript($.trim($("#imgLink").val())));
        form_data.append('titleLink', noscript($.trim($("#titleLink").val())));
        form_data.append('urlImage', noscript($.trim($("#urlImage").val())));

        $.ajax({
            url: "createTopic.php",
            type: "POST",
            contentType: false,
            processData: false,
            data: form_data,
            dataType: "html",
            success: function (response) {
                //check response is blank if success 
                if (!$.trim(response)) {
                    window.location.href = "index.html";
                    $(window).scrollTop(0);
                }
                // if error
                else {
                    $("#flash-message").html(response);
                    $(window).scrollTop(0);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
        });
    });

});

function redirectAddOrEditTopic() {
    window.location.href = "add-topic.html";
}

function onBtnclick() {
    var preTitle = $("#title").val();
    var preOpenDay = $(".openDay").val();
    var preBody = noscript($("#body").val());
    var preImgLink = $("#imgLink").val();
    var preLinkTitle = $("#titleLink").val();
    var preLink = $("#urlImage").val();

    if (preTitle == '' && preBody == '') {
        alert("Please input text before preview");
    }
    else {
        var opday = new Date(preOpenDay);
        var currentDate = new Date();
        if (opday.setDate(opday.getDate() + 7) >= currentDate) {
            $("#newTitle").text("New!");
        }

        preBody = $.trim(preBody.replace(/<[^>]+>/g, ''));
        preBody = preBody.replace(/\n/g, '<br>');

        if (!preImgLink.match("^http") && preImgLink != '') {
            preImgLink = '//' + preImgLink;
            $('#preImgLink').css("pointer-events", "auto");
        }
        else {
            preImgLink = preImgLink;
            $('#preImgLink').css("pointer-events", "auto");
        }

        if (preImgLink == '') {
            $('#preImgLink').css("pointer-events", "none");
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
        else {
            $('#img').show();
        }

        $("#previewTopic").modal('show');
    }
}

function fileValidation() {
    var fileInput = document.getElementById('imgFile');
    var filePath = fileInput.value;
    var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
    if (!allowedExtensions.exec(filePath)) {
        alert('アップロードできる画像形式はJPG、JPEG、PNG、GIFのみご入力ください。');
        fileInput.value = '';
        return false;
    } else {
        //Image preview
        if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var output = document.getElementById('output_image');
                output.src = e.target.result;
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
}

function noscript(strCode) {
    var html = $(strCode.bold());
    html.find('script,noscript,style').remove();
    return html.html();
}
