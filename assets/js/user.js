import $ from 'jquery';
import moment from 'moment';
import 'bootstrap';

/**
 * @param {string} account
 */
function getUser(account) {
    return $.get(`api/user/${account}`);
}

/**
 * @param {object} result
 */
function showUser(result) {
    if (!result) {
        return $(".container")
            .append("<div class='alert alert-secondary mt-4'>no user</div>");
    }

    setInfoPage(result);
    setRecordPage(result);
}

/**
 * @param {object} result
 */
function setInfoPage(result) {
    $(".user-info")
        .append(`<table class="table">
                    <tbody>
                        <tr class="account">
                            <th scope="row">#account</th>
                            <td>${result.account}</td>
                        </tr>
                        <tr class="cash">
                            <th scope="row">#cash</th>
                            <td>
                                <label>${result.cash}</label>
                            </td>
                        </tr>
                        <tr class="created-at">
                            <th scope="row">#createdAt</th>
                            <td>${result.createdAt}</td>
                        </tr>
                        <tr class="updated-at">
                            <th scope="row">#updateAt</th>
                            <td>${result.updatedAt}</td>
                        </tr>
                    </tbody>
        </table>`);

    $("tr.cash td")
        .append(`<div class="float-right">
            <input type="text" class="col-sm-5" value="0">
            <button class="add btn btn-outline-success btn-sm" value=''>增額</button>
            <button class="sub btn btn-outline-danger btn-sm" value=''>減額</button>
        </div>`);
}

/**
 * @param {object} result
 */
function setRecordPage(result) {
    var min = moment.utc()
        .subtract(3, 'months')
        .format('YYYY-MM-DD');
    var max = moment.utc()
        .add(1, 'day')
        .format('YYYY-MM-DD');
    var current = moment.utc().format('YYYY-MM-DD');

    $(".cash-records").append(`
    <form action="api/cash/${result.id}/records" method="GET" class="cash-records">
        <table class="table">
            <tbody>
                <tr class="account">
                    <th scope="row">#account</th>
                    <td>${result.account}</td>
                </tr>
                <tr class="start-date">
                    <th scope="row">#start date</th>
                    <td><input class="form-control" name="start" type="date"
                        value="${current}" min="${min}" max="${max}">
                    </td>
                </tr>
                <tr class="end-date">
                    <th scope="row">#end date</th>
                    <td><input class="form-control" name="end" type="date"
                        value="${current}" min="${min}" max="${max}">
                    </td>
                </tr>
                <tr class="submit">
                    <th></th>
                    <td><button type="submit" class="btn btn-primary">submit</button></td>
                </tr>
            </tbody>
        </table>
    </form>
    <div class="record-table"></div>`);
}

/**
 * @param {element} button
 * @param {int} id
 * @param {int} value
 */
function cashAction(button, id, value) {
    var action = button.hasClass('add') ? 'add' : 'sub';
    $(".block").attr('hidden', false);
    console.log($(".block"));

    $.ajax({
        type: 'PUT',
        dataType: 'json',
        url: `api/cash/${id}/${action}`,
        data: {
            "cash": value
        },
        success: function (response) {
            if (response.code == 0) {
                $(".cash td label").text(response.result.cash);
                $(".updated-at td").text(moment.utc().format('YYYY-MM-DD hh:mm:ss'));
                alert('操作成功');
            } else {
                alert(response.message);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(XMLHttpRequest.responseJSON.message);
        },
    });

    $(".block").attr('hidden', true);
}
/**
 * @param {element} form
 * @param {int} page
 */
function getCashRecords(form, page) {
    return $.get({
        url: form.attr('action'),
        data: form.serialize() + `&page=${page}`
    });
}

/**
 * @param {object} result
 * @param {int} currentPage
 */
function showCashRecords(result, currentPage = 1) {
    $("div.record-table").empty();

    if (!result || result.records.length == 0) {
        return $("div.record-table")
            .html("<div class='alert alert-secondary mt-4'>no records</div>");
    }

    setRecordsTable(result);

    if (result.pages > 1) {
        $("div.record-table").append('<ul class="pagination"></ul>');
        var pagination = '';
        for (var page = 1; page < result.pages + 1; page++) {
            if (currentPage == page) {
                pagination += `<li class="page-item active">\
                    <a class="page-link" href="#">${page}</a>\
                </li>`;
            } else {
                pagination += `<li class="page-item">\
                    <a class="page-link" href="#" value="${page}">${page}</a>\
                </li>`;
            }
        }

        $(".pagination").html(pagination);
    }
}

/**
 * @param {object} result
 */
function setRecordsTable(result) {
    $("div.record-table")
        .html(`<table class="records table">
            <thead>
                <tr>
                    <th scope="col">操作者</th>
                    <th scope="col">異動額度</th>
                    <th scope="col">新額度</th>
                    <th scope="col">ip</th>
                    <th scope="col">時間</th>
                </tr>
            </thead>
            <tbody class="records-body">
            </tbody>
        </table>`);

    var recordView = $.map(result.records, function (record) {
        return `<tr>
            <td>${record.operator}</td>
            <td>${record.diff}</td>
            <td>${record.current}</td>
            <td>${record.ip}</td>
            <td>${record.created_at}</td>
        </tr>`;
    });

    $("tbody.records-body").html(recordView.join(''));
}

$(function () {
    var urlParams = new URLSearchParams(window.location.search);
    var account = urlParams.get('account') ?? '';

    $.when(getUser(account))
        .done(function (response) {
            var result = (response.code == 0) ? response.result : false;
            showUser(result);

            $(".user-info").on('click', 'button', function () {
                var value = $('.cash input').val();
                var regExp = /^\+?[1-9][0-9]*$/;

                if (!regExp.test(value)) {
                    return alert('請輸入非零正整數');
                }

                cashAction($(this), result.id, value);
            });
    });

    $(".cash-records").on('submit', 'form', function (event) {
        event.preventDefault();
        $.when(getCashRecords($(this), 1))
            .done(function (response) {
                var result = (response.code == 0) ? response.result : false;
                showCashRecords(result);
            });
    });

    $(".cash-records").on("click", ".page-link", function (event) {
        event.preventDefault();

        if ($(this).parent().hasClass('active')) {
            return false;
        }

        var currentPage = $(this).attr("value");
        $.when(getCashRecords($('form.cash-records'), currentPage))
            .done(function (response) {
                var result = (response.code == 0) ? response.result : false;
                showCashRecords(result, currentPage);
            });
    });
});