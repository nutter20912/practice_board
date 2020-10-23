<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>留言板</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</head>

<script>
    $(function() {
        $(".commentList li button").click(function() {
            var button = $(this);
            var data = jQuery.parseJSON(button.val());
            var clickedComment = button.closest('li').attr('id');
            var url = "message/" + data.messageId + "/comment/" + data.commentId;

            switch (data.act) {
                case 'submit':
                    $.ajax({
                        type: 'PUT',
                        dataType: 'json',
                        url: url,
                        data: {
                            "comment": $("#" + clickedComment).find('input').val()
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                $("#" + clickedComment).find('input').attr({
                                    "readonly": true,
                                    "class": 'form-control-plaintext col-sm-7',
                                });

                                $("#" + clickedComment).find('.submit').attr({
                                    "style": "display:none"
                                });
                            } else {
                                alert('edit failed.');
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert('edit failed.');
                        },
                    });
                    break;

                case 'edit':
                    var commentText = $("#" + clickedComment).find('input');

                    if (commentText.is('[readonly]')) {
                        commentText.attr({
                            "readonly": false,
                            "class": 'form-control col-sm-7',
                        });

                        $("#" + clickedComment).find('.submit').attr({
                            "style": "display:inline"
                        });
                    } else {
                        $("#" + clickedComment).find('input').attr({
                            "readonly": true,
                            "class": 'form-control-plaintext col-sm-7',
                        });

                        $("#" + clickedComment).find('.submit').attr({
                            "style": "display:none"
                        });
                    }
                    break;

                case 'delete':
                    $.ajax({
                        type: "delete",
                        url: url,
                        data: "data",
                        dataType: 'json',
                        success: function(response) {
                            var collapse = button.closest('.collapse').attr('id');
                            var count = $('[data-target="#' + collapse + '"]').find('span');
                            count.text(count.text() - 1);
                            $('#' + clickedComment).remove();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert('delete failed.');
                        },
                    });
                    break;
            }
        });

    });
</script>

<body>
    <div class="row">
        <!-- navbar -->
        <div class="col-4">
            <div class="list-group" id="list-tab" role="tablist">
                <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">home</a>
                <a class="list-group-item list-group-item-action" id="list-add-list" data-toggle="list" href="#list-add" role="tab" aria-controls="add">add message</a>
            </div>
        </div>
        <!-- content -->
        <div class="col-8">
            <div class="tab-content" id="nav-tabContent">
                <!-- show all messages -->
                <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
                    {% if messages|length > 0 %}
                        {% for key, message in messages %}
                            <div class="card mb-4 mt-4" style="width: 36rem;">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">#{{ message.getId() }}</h6>
                                    <a href="{{ url_for('message.show', {'message': message.getId()}) }}">
                                        <h5 class="card-title">{{ message.getTitle() }}</h5>
                                    </a>
                                    <p class="card-text">{{ message.getContent() }}</p>
                                    <p class="card-subtitle mb-2 text-muted">{{ message.getAuthor() }} : {{ message.getCreatedAt() }}</p>
                                    <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#commentCollapse{{ key }}" aria-expanded="false" aria-controls="commentCollapse{{ key }}">
                                        comments <span class="badge badge-light">{{ message.getComments()|length }}</span>
                                    </button>
                                    <div class="collapse" id="commentCollapse{{ key }}">
                                        <ul class="list-group commentList">
                                            {% for key, comment in message.getComments() %}
                                            <li class="list-group-item" id="comment_{{ message.getId() }}_{{ comment.getId() }}">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ comment.getName() }}</label>
                                                    <input type="text" readonly class="form-control-plaintext col-sm-7 text-monospace" value="{{ comment.getComment() }}">
                                                    <button class="btn btn-outline-primary btn-sm submit" value='{"act":"submit","messageId":"{{ message.getId }}","commentId":"{{ comment.getId() }}"}' style="display:none">submit</button>
                                                </div>
                                                <div class="float-right">
                                                    <label class="card-subtitle mb-2 text-muted">{{ comment.getCreatedAt() }}</label>
                                                    <button class="btn btn-outline-secondary btn-sm edit" value='{"act":"edit","messageId":"{{ message.getId() }}","commentId":"{{ comment.getId() }}"}'>edit</button>
                                                    <button class="btn btn-outline-danger btn-sm delete" value='{"act":"delete","messageId":"{{ message.getId() }}","commentId":"{{ comment.getId() }}"}'>delete</button>
                                                </div>
                                            </li>
                                            {% endfor %}
                                        </ul>
                                        <form action="{{ url_for('comment.store', {'message': message.getId()}) }}" method="POST" class="form-horizontal border mb-4 mt-4">
                                            <label class="col-sm-3">name</label>
                                            <input class="col-sm-3 form-control-inline" type="text" name="name">
                                            <div>
                                                <label class="col-sm-3">comment</label>
                                                <input class="col-sm-7 form-control-inline" type="text" name="comment">
                                                <button type="submit" class="btn btn-primary btn-sm">submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        {% endfor %}
                        {% if pages > 1 %}
                            <ul class="pagination">
                                {% for i in 1..pages %}
                                    <li class="page-item {{ current == loop.index ? 'active' }}">
                                        <a class="page-link" href="{{ url_for('message.index') ~ '?page=' ~ loop.index }}">{{ loop.index }}</a>
                                    </li>
                                {% endfor %}
                            </ul>
                        {% endif %}
                    {% else %}
                    <div class="alert alert-secondary mt-4">no message</div>
                    {% endif %}
                </div>
                <!-- add message -->
                <div class="tab-pane fade" id="list-add" role="tabpanel" aria-labelledby="list-add-list">
                    <form action="{{ url_for('message.index') }}" method="POST" class="form-horizontal border mb-4 mt-4">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">作者</label>
                            <div class="col-sm-5">
                                <input type="text" name="author" id="message-author" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">標題</label>
                            <div class="col-sm-5">
                                <input type="text" name="title"" id=" message-title" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">內容</label>
                            <div class="col-sm-5">
                                <textarea name="content" id="message-content" rows="4" cols="50" class="form-control"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">留言</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>