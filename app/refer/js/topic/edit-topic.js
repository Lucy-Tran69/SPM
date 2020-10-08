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

    // /*add method validate date format*/
    // $.validator.addMethod('dateFormat', function (value, element) {
    //         let regEx = /^\d{4}-\d{2}-\d{2}$/;
    //         if (!value.match(regEx)) return false;

    //         let inputDate = new Date(value);
    //         let currentDate = new Date();

    //         if (!inputDate.getTime() && inputDate.getTime() !== 0) return false;

    //         return inputDate.toISOString().slice(0, 10) === value;
    //     }, 'Please enter a date in the format yyyy-mm-dd.');

        $.validator.addMethod( "accept", function( value, element, param ) {

        var typeParam = typeof param === "string" ? param.replace( /\s/g, "" ) : "image/*",
        optionalValue = this.optional( element ),
        i, file, regex;

        if ( optionalValue ) {
            return optionalValue;
        }

        if ( $( element ).attr( "type" ) === "file" ) {

            typeParam = typeParam
            .replace( /[\-\[\]\/\{\}\(\)\+\?\.\\\^\$\|]/g, "\\$&" )
            .replace( /,/g, "|" )
            .replace( /\/\*/g, "/.*" );

            if ( element.files && element.files.length ) {
                regex = new RegExp( ".?(" + typeParam + ")$", "i" );
                for ( i = 0; i < element.files.length; i++ ) {
                    file = element.files[ i ];

                    if ( !file.type.match( regex ) ) {
                        return false;
                    }
                }
            }
        }

        return true;
    }, $.validator.format( "アップロードできる画像形式はJPG、JPEG、PNG、GIFのみご入力ください。" ));

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

     formEditTopic.submit(function(e) {
          event.preventDefault();
          if(!formEditTopic.valid()) 
          {
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
        form_data.append('imgFileOld', $("#imgFileOld").val());
       
        $.ajax({
            url: "editTopic.php",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            dataType:"html",
            success: function(response)  {
                //check response is blank if success 
                if (!$.trim(response)) {
                    window.location.href = "index.html";
                } 
                // if error
                else {
                    $("#flash-message").html(response);        
                }
            },
            error: function(jqXHR, textStatus, errorThrown)  {
                alert( "Bug" );
            },
        });
    });
});

function onBtnclick() {
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

    $("#preTitle").text(preTitle);
    $("#preOpenday").text(preOpenDay);
    $("#preBody").empty();
    $("#preBody").append(preBody);
    $("#preImgLink").attr("href", preImgLink);
    $("#preLinkTitle").text(preLinkTitle);
    $("#preLink").attr("href", preLink);

    var img = $('#imgFile').val();
    var imgOld = $('#imgFileOld').val();
    if (img == ''){
       var output = document.getElementById('output_image');
        output.src = "../../app/refer/images/topics/" + imgOld;
    }

    if (imgOld == '' && img == '') {
        $('#output_image').css('display','none');
    }

    if (img != '') {
        $('#output_image').css('display','inline');
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

function redirectListTopic() {
    window.location.href = "index.html";
}

function fileValidation(){
    var fileInput = document.getElementById('imgFile');
    var filePath = fileInput.value;
    var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
    if(!allowedExtensions.exec(filePath)){
        alert('アップロードできる画像形式はJPG、JPEG、PNG、GIFのみご入力ください。');
        fileInput.value = '';
        return false;
    }else{
        //Image preview
        if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var output = document.getElementById('output_image');
                output.src = e.target.result;
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
}

function deleteImage() {
    $('#imgFileOld').val(''); 
    $('label[id*="labelFile"]').text('');
    $('#imgFile').val('');
    $('label[id*="displayFile"]').text('');
}
