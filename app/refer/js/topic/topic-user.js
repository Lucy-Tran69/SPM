$(function () {
    var is_busy = false;

    var page = 1;

    var stopped = false;

    var record_per_page = 5;

    $element = $('#topicsList');
    $button = $('#loadmore');

    $button.click(function () {
        if (is_busy == true) {
            return false;
        }

        is_busy = true;

        page++;

        $button.html('ロード中…');

        $.ajax(
            {
                type: 'get',
                dataType: 'json',
                url: '../topics/topicsUserList.php',
                data: { page: page },
                success: function (result) {
                    var html = '';
                    var currentDate = new Date();
                    if (result.length <= record_per_page) {
                        $.each(result, function (key, obj){
                           html += '<tr data-id="'+obj.no+'" class="item-topic" data-toggle="modal" data-target="#modal-detail-topic">' +
                                        '<td><a href="javascript:void(0)">'+obj.open_day+'</a></td>' +
                                        '<td>';

                                 var opday = new Date(obj.open_day);
                                 if (opday.setDate(opday.getDate() + 7) >= currentDate) {
                                    html +='<b style="color: red;">New!</b>';
                                }
                                html += '<a href="javascript:void(0)">' + obj.title + '</a>' + '</td>' + '</tr>';
                        });
     
                        $element.append(html);
     
                        $button.remove();
                    } else {
                        $.each(result, function (key, obj){
                            if (key < result.length - 1){
                                html += '<tr data-id="'+obj.no+'" class="item-topic" data-toggle="modal" data-target="#modal-detail-topic">' +
                                        '<td><a href="javascript:void(0)">'+obj.open_day+'</a></td>' +
                                        '<td>';

                                 var opday = new Date(obj.open_day);
                                 if (opday.setDate(opday.getDate() + 7) >= currentDate) {
                                    html +='<b style="color: red;">New!</b>';
                                }
                                html += '<a href="javascript:void(0)">' + obj.title+'</a>' + '</td>' + '</tr>';
                            }
                        });
                        $element.append(html);
                    }
                }
            })
            .always(function() {
                $button.html('もっと見る');
                is_busy = false;
            });
    });

    $('.item-topic').click(function () {
        var id = $(this).data("id");
        $.ajax(
            {
                type: 'post',
                dataType: 'json',
                url: '../topics/topicDetail.php',
                data: { id: id },
                success: function (result) {
                    let link_title = result['link_title'];
                    let link_url = result['link_url'];
                    let image_link = result['image_link'];
                    let topicImage = result['image'];

                    $('#title').text(result['title']);
                    $('#openday').text(result['open_day']);
                    if (!$.trim(topicImage) == '') {
                        $('#topicImage').attr("src", "../../app/refer/images/topics/" + topicImage);
                    }

                    var opday = new Date(result['open_day']);
                    var currentDate = new Date();
                    if (opday.setDate(opday.getDate() + 7) >= currentDate) {
                        $("#newTitle").text("New!");
                    }

                    if (!image_link.match("^http") && image_link != '') {
                        image_link = '//' + image_link;
                    }
                    else {
                        image_link = image_link;
                    }

                     var linkCode = '<a target="_blank" href="%link%">%title%</a>';

                     if (!link_url.match("^http") && link_url != '') {
                        link_url = '//' + link_url;
                    }
                    else {
                        link_url = link_url;
                    }

                    if (link_url != '' && link_title == '') {
                        linkCode = linkCode.replace('%link%', link_url);
                        linkCode = linkCode.replace('%title%', link_url);
                    }
                    else if (link_url != '' && link_title != '') 
                    {
                        linkCode = linkCode.replace('%link%', link_url);
                        linkCode = linkCode.replace('%title%', link_title);
                    } 
                    else {
                        linkCode = '';
                    }

                    $("#link").empty();
                    $("#link").append(linkCode);

                    $('#imgLink').attr("href", image_link);
                    $('#topicBody').text(result['body']);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log('AJAX call failed.');
                    console.log(textStatus + ': ' + errorThrown);
                }
            });
    });
});