/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import $ from 'jquery';
import 'bootstrap';

/**
 * @param {element} form
 */
function formAction(form) {
    $.ajax({
        type: form.attr('method'),
        url: form.attr('action'),
        data: form.serialize(),
        success: function (response) {
            alert('submit success.');
            window.location.href = "/board";
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert('submit failed.');
        },
    });
}

/**
 * @param {string} url
 */
function showEditPage(url) {
    $.getJSON(url, function (response) {
        if (response.code == 0) {
            $(".delete-message").attr('action', url);
            $(".edit-message").attr('action', url);
            $("#staticAuthor").val(response.result.author);
            $("#staticTitle").val(response.result.title);
            $("#staticContent").val(response.result.content);
            $("#list-home-list").removeClass('active');
            $(".all-message").removeClass('show active');
            $("#list-edit-list").addClass('active');
            $(".edit-page").addClass('show active');
        }
    });
}

/**
 * @param {string} url
 * @param {element} button
 */
function deleteComment(url, button) {
    $.ajax({
        type: "delete",
        url: url,
        data: "data",
        dataType: 'json',
        success: function () {
            var collapse = button.closest('.collapse').attr('id');
            var count = $('[data-target="#' + collapse + '"]').find('span');
            count.text(count.text() - 1);
            button.closest('li').remove();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert('delete failed.');
        },
    });
}

/**
 * @param {string} url
 * @param {element} button
 */
function editComment(url, button) {
    $.ajax({
        type: 'PUT',
        dataType: 'json',
        url: url,
        data: {
            "content": button.prev('input').val()
        },
        success: function (response) {
            if (response.code == 0) {
                button.prev('input').attr({
                    "readonly": true,
                    "class": 'form-control-plaintext col-sm-7',
                });
                button.attr({
                    "style": "display:none"
                });
            } else {
                alert('edit failed.');
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert('edit failed.');
        },
    });
}

/**
 * @param {element} button
 */
function switchDisplay(button) {
    var commentText = button.closest('li').find('input');

    if (commentText.is('[readonly]')) {
        commentText.attr({
            "readonly": false,
            "class": 'form-control col-sm-7',
        });

        button.closest('li').find('.submit').attr({
            "style": "display:inline"
        });
    } else {
        button.closest('li').find('input').attr({
            "readonly": true,
            "class": 'form-control-plaintext col-sm-7',
        });

        button.closest('li').find('.submit').attr({
            "style": "display:none"
        });
    }
}

/**
 * @return {array|boolean} result
 */
function getMessages(current) {
    return $.get(`api/board/message?page=${current}`);
}

/**
 * @param {array} result
 */
function showMessages(result, current) {
    if (!result) {
        return $(".container")
            .append("<div class='alert alert-secondary mt-4'>no message</div>");
    }

    var messageView = $.map(
        result.messages,
        getMessageView,
        getTemplate('card')
    );
    $(".all-message").html(messageView.join(''));

    var addTemp = getTemplate('addMessage');
    addTemp.closest('form.add-message').attr('action', 'api/board/message');
    $(".add-page").html(addTemp);

    var editTemp = getTemplate('editMessage');
    $(".edit-page").html(editTemp);

    if (result.pages > 1) {
        $(".all-message").append('<ul class="pagination"></ul>');

        for (var page = 1; page < result.pages + 1; page++) {
            if (current == page) {
                $(".pagination").append(
                    `<li class="page-item active">\
                        <a class="page-link" href="#">${page}</a>\
                    </li>`);
            } else {
                $(".pagination").append(
                    `<li class="page-item">\
                        <a class="page-link" href="?page=${page}">${page}</a>\
                    </li>`);
            }
        }
    }
}

/**
 * @param {array} message
 * @param {int} key
 * @param {template} cardTemp
 *
 * @return {element}
 */
function getMessageView(message, key, cardTemp) {
    cardTemp.find(".message-id").text(`#${message.id}`);
    cardTemp.find("a").attr('href', `api/board/message/${message.id}`);
    cardTemp.find(".message-title").text(message.title);
    cardTemp.find(".message-content ").text(message.content);
    cardTemp.find(".message-time")
        .text(`${message.author} : ${message.created_at.date}`);
    cardTemp.find('button.message-count')
        .attr('data-target', `#commentCollapse${key}`)
        .attr('aria-controls', `commentCollapse${key}`);
    cardTemp.find("span.message-count").text(message.comments.length);
    cardTemp.find(".comment-collapse").attr('id', `commentCollapse${key}`);

    var commentTemp = getTemplate('comment');
    var commentView = $.map(message.comments, getCommentView, {
        temp: commentTemp,
        messageId: message.id
    });

    cardTemp.find('ul.commentList').html(commentView.join(''));
    cardTemp.find("form.comment").attr('action', `api/board/message/${message.id}/comment`);

    return cardTemp[0].outerHTML;
}

/**
 * @param {array} comment
 * @param {int} key
 * @param {array} data
 *
 * @return {element}
 */
function getCommentView(comment, key, data) {
    var commentTemp = data.temp;
    commentTemp.closest("li.comment")
        .attr('id', `comment_${data.messageId}_${comment.id}`)
        .attr("value", `{ "messageId":"${ data.messageId }", "commentId":"${ comment.id }"}`);
    commentTemp.find(".comment-name").text(comment.name);
    commentTemp.find(".comment-content").attr('value', comment.content);
    commentTemp.find(".comment-created-at").text(comment.created_at.date);

    return commentTemp[0].outerHTML;
}

/**
 * @param {string} name
 *
 * @return {template} cardTemp
 */
function getTemplate(name) {
    var html = $(`template.${name}`).html();
    return $(html).clone();
}

$(function () {
    var urlParams = new URLSearchParams(window.location.search);
    var current = urlParams.get('page') ?? 1;

    $.when(getMessages(current))
        .done(function (response) {
            var result = (response.code == 0) ? response.result : false;
            showMessages(result, current);

            $('form').on('submit', function (event) {
                event.preventDefault();
                formAction($(this));
            });

            $('.edit-message-link').on('click', function (event) {
                event.preventDefault();
                showEditPage($(this).attr('href'));
            });

            $(".commentList li button").on('click', function () {
                var data = $.parseJSON($(this).closest('li').attr('value'));
                var url = `api/board/message/${data.messageId}/comment/${data.commentId}`;

                if ($(this).hasClass('edit')) {
                    switchDisplay($(this));
                } else if ($(this).hasClass('submit')) {
                    editComment(url, $(this));
                } else if ($(this).hasClass('delete')) {
                    deleteComment(url, $(this));
                }
            });
        });
});