$(function () {
    let day = $('.openDay').val();
    var date = new Date();
    $('#openDay').datetimepicker({
        useCurrent: false,
        date: day,
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

    var formEditTopic = $('#editTopic');
    formEditTopic.validate({
        rules: {
            title: {
                required: true,
            },
            body: {
                required: true,
            },
        },
        messages: {
            title: "タイトルを入力して下さい。",
            body: "本文を入力して下さい。",
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

    $('.btn-modal-submit').click(function () {
        formEditTopic.submit();
        $("#previewTopic").modal('hide');
    });

     $('#btn-submit').click(function(e) {
        debugger
        var fileOld = $('#imgFileOld').val();
        var fileNew = $('#imgFile').get(0).files;
        $.ajax({
            url: "editTopic.php",
            type: "POST",
            dataType: "html",
            enctype: 'multipart/form-data',
            contentType: false
            processData: false
            data: {
                title: $("#title").val(),
                openDay:$("#openDay").val(),
                closeDay: $("#closeDay").val(),
                body:$("#body").val(),
                imgLink: $("#imgLink").val(),
                titleLink:$("#titleLink").val(),
                urlImage: $("#urlImage").val(),
                imgFileOld: fileOld,
                imgFile: fileNew,
            },
            success: function(response)  {
                debugger
                $("#d-message").empty();
                $("#d-message").html(response);
                 // alert(response);
            },
            error: function(jqXHR, textStatus, errorThrown)  {
                alert( "Bug" );
            },
        });
    });
    //  $("#editTopic").submit(function(event){
    //     console.log("cccccccc");
    //     $.ajax({
    //         url : "editTopic.php",
    //         type : "POST",
    //         dataType : "html",
    //         data : {
              
    //         },
    //         success: function(response)  {
    //             $("#d-message").empty();
    //             $("#d-message").html(response);
    //             alert( response );
    //         },
    //         error: function(jqXHR, textStatus, errorThrown)  {
    //             alert( "error" );

    //         }
    //     });
    //     event.preventDefault();
    // });
});

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

    $("#preTitle").text(preTitle);
    $("#preOpenday").text(preOpenDay);
    $("#preBody").empty();
    $("#preBody").append(preBody);
    $("#preImgLink").attr("href", preImgLink);
    $("#preLinkTitle").text(preLinkTitle);
    $("#preLink").attr("href", preLink);

    var img = $('#imgFile').val();
    if (img == ''){
       var output = document.getElementById('output_image');
        var imgOld = $('#imgFileOld').val();
        output.src = "../../app/refer/images/topics/" + imgOld;
    }

    var linkCode = '<a target="_blank" href="%link%">%title%</a>';
    
    if (preLink != '' && preLinkTitle == '') {
        linkCode = linkCode.replace('%link%', preLink);
        linkCode = linkCode.replace('%title%', preLink);
    }
    else if (preLink != '' && preLinkTitle != '') 
    {
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

