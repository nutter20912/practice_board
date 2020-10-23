<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>留言板</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</head>

<body>
    <div class="panel-body mt-4 ml-4" style="vertical-align: top; width:38em;">
        <table>
            <tr>
                <td>
                    <a href="javascript:history.back()" class="btn btn-outline-primary btn-sm">回留言板</a>
                </td>
                <td>
                    <!-- 刪除表單 -->
                    <form action="{{ url_for('message.delete', {'message': message.getId()}) }}" method="POST" class="form-horizontal">
                        <input type="hidden" name="_METHOD" value="DELETE" />
                        <button type="submit" class="btn btn-outline-danger btn-sm">刪除</button>
                    </form>
                </td>
            </tr>
        </table>

        <!-- 編輯留言表單 -->
        <form action="{{ url_for('message.delete', {'message': message.getId()}) }}" method="POST" class="form-horizontal mt-4 ml-4">
            <input type="hidden" name="_METHOD" value="PUT" />
            <div class="form-group row">
                <label for="staticAuthor" class="col-sm-2 col-form-label">作者</label>
                <div class="col-sm-10">
                    <input type="text" readonly class="form-control-plaintext" id="staticAuthor" value="{{ message.getAuthor() }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="staticTitle" class="col-sm-2 col-form-label">標題</label>
                <div class="col-sm-10">
                    <input type="text" readonly class="form-control-plaintext" id="staticTitle" value="{{ message.getTitle() }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">內容</label>
                <div class="col-sm-10">
                    <textarea name="content" id="message-content" rows="4" cols="50" class="form-control" style="vertical-align: top; width:38em;">{{ message.getContent() }}</textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm" align="center">更新</button>
        </form>
        <div>
</body>

</html>
