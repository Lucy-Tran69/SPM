$(function () {
    var publicDate = $('.openDay').val();
    var date = new Date();
    $('#openDay').datetimepicker({
        useCurrent: false,
        date: publicDate,
        minDate: publicDate,
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

    var formEditTopic = $('#editTopic');
    formEditTopic.validate({
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
            title: "タイトルを入力して下さい。",
            body: "本文を入力して下さい。",
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
        formEditTopic.submit();
        $("#previewTopic").modal('hide');
    });

    formEditTopic.submit(function (event) {
        event.preventDefault();
        if (!formEditTopic.valid()) {
            return false;
        }

        var statusImage = 'false';
        if ($('#deleteImage').is(":checked")) {
            statusImage = 'true';
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
        form_data.append('imgFileOld', $("#imgFileOld").val());
        form_data.append('statusImage', statusImage);

        $.ajax({
            url: "editTopic.php",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            dataType: "html",
            success: function (response) {
                debugger
                //check response is blank if success 
                if (!$.trim(response)) {
                    //window.location.href = "index.html?";
                    $(window).scrollTop(0);
                    window.history.back();
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

    $('.form-group').on('change', '#deleteImage', function (e) {
        if ($(this).is(":checked")) {
            $('#labelFile').text('');
            $('#imgFile').val('');
            $('#displayFile').hide();
        }
        else {
            $('#displayFile').show();
            $('#labelFile').text($('#imgFileOld').val());
        }
    });

    $('#imgFile').on('change', function (e) {
        var valueSelected = this.value;
        if (valueSelected != '') {
            var delImg = '<input type="checkbox" name="deleteImage" class="custom-control-input" id="deleteImage" /><label class="custom-control-label" for="deleteImage" style="font-weight: 100 !important">画像を削除する</label>';
            $('.check-delete-image').html(delImg);
        }
    });
});

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

        $("#preTitle").text(preTitle);
        $("#preOpenday").text(preOpenDay);
        $("#preBody").empty();
        $("#preBody").append(preBody);
        $("#preImgLink").attr("href", preImgLink);
        $("#preLinkTitle").text(preLinkTitle);
        $("#preLink").attr("href", preLink);

        var img = $('#imgFile').val();
        var imgOld = $('#imgFileOld').val();
        var labelFile = $('label[id*="labelFile"]').text();
        var output = document.getElementById('output_image');
        if (img == '' && labelFile != '') {
            output.src = "../../app/refer/images/topics/" + imgOld;
        }

        if (imgOld == '' && img == '' || labelFile == '') {
            $('#output_image').css('display', 'none');
        }

        if (img != '' || labelFile != '') {
            $('#output_image').css('display', 'inline');
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
