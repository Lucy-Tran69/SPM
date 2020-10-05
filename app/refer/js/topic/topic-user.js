$(function () {
     var is_busy = false;

     var page = 1;

     var stopped = false;

    $topicTable = $('#topicTable');

    // $($topicTable).scroll(function () {
    $element = $('#topicsList');
    $loadding = $('#loadding');

    $('#loadmore').click(function () {
        if (is_busy == true) {
            return false;
        }

        if (stopped == true) {
            return false;
        }

        is_busy = true;

        page++;

        $loadding.removeClass('hidden');

        $.ajax(
            {
                type: 'get',
                dataType: 'text',
                url: '../topics/topicsUserList.php',
                data: { page: page },
                success: function (result) {
                    $element.append(result);
                }
            })
            .always(function () {
                $loadding.addClass('hidden');
                is_busy = false;
            });
        return false;
    });
    // });

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

                    $('.modal-title').text(result['title']);
                    $('#openday').text(result['open_day']);
                    $('#imgLink').attr("href", image_link);
                    if (!$.trim(topicImage) == '') {
                        $('#topicImage').attr("src", "../../app/refer/images/topics/" + topicImage);
                    }
                    $('#topicBody').text(result['body']);
                    $('#topicLink').attr("href", link_url);
                    $('#topicLink').text(link_url);
                    if (!$.trim(link_title) == '') {
                        $('#topicLink').text(link_title);
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log('AJAX call failed.');
                    console.log(textStatus + ': ' + errorThrown);
                }
            });
    });
});