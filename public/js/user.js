function nameFormat(value, row, index, field) {
    var last_online = "OFF";
    var url_edit = 'user/edit/' + row.id;
    var group_name = '';
    var extension = '';
    if (row.last_online) {
        last_online = "ON";
    }
    if (row.group_name) {
        group_name = ' <div class="small">' +
            '                            <span class="text-orange">Nhóm tài khoản: ' + row.group_name + '</span>' +
            '                        </div>';
    }
    if (row.extension) {
        extension = '<div class="small">' +
            '                            <span class="text-success">Đầu số tổng đài : ' + row.extension + '</span>' +
            '                        </div>';
    }
    var name = ' <strong>' +
        // '                 <span>'+last_online+'</span>' +
        '                        <a href="' + url_edit + '">' + row.account_id + '</a>' +
        '                 </strong>' +
        '                    <div class="small"></div>';
    return name + group_name + extension;
}

function infoAccFormat(value, row, index, field) {
    var email = row.email ? row.email : '';
    var phone = row.phone ? row.phone : '';
    var prefecture_name = row.prefecture_name ? row.prefecture_name : '';
    var info = ' <span class="text-bold">' + row.name + '</span>';
    return info;
}

function expriedDayFormat(value, row, index, field) {
    var status = '<div><span class="badge bg-success">Đã kích hoạt</span></div>';
    var from_date = '';
    var expried_date = '';

    if (row.status === 0) {
        status = '<div><span class="badge bg-secondary">Chưa kích hoạt</span></div>';
    }
    if (row.created_at) {
        from_date = convertDate(row.created_at)
    }
    if (row.expried_day) {
        expried_date = convertDate(row.expried_day)
    }
    var info = '<div class="small mt-3">Từ ' + from_date + '</div>' +
        '                    <div class="small text-bold"> đến  ' + expried_date + '</div>';
    return status;

}

function userCreatedFormat(value, row, index, field) {
    var user = '';
    var created_at = '';
    if (row.user_create) {
        user = row.user_create;
    }
    if (row.created_at) {
        created_at = convertDateTime(row.created_at)
    }
    var info = created_at;
    return info;

}

function roleFormat(value, row, index, field) {

    var info = '';
    if (value.length > 0) {
        for (var i = 0; i < value.length; i++) {
            info += '<span class="badge badge-secondary mr-2">' + value[i].name + '</span>';
        }
    }
    return info;

}

function actionFormat(value, row, index, field) {
    var shop_manager_flag = '';
    var btnEdit = '<a href="user/edit/' + row.id + '" class="btn btn-xs btn-warning text-white mr-2"><i class="fa fa-pen "></i> Sửa</a>';
    var btnDelete = '<button class="btn btn-xs btn-danger" onclick="checkDelete(' + row.id + ')"><i class="fa fa-trash-alt "></i> Xóa</button>';

    if (row.shop_manager_flag === 1) {
        shop_manager_flag = '[<span class="fas fa-check text-success " aria-hidden="true"></span>]';
    }
    return shop_manager_flag + `<div class="btn-group">${btnEdit}${btnDelete}</div>`;
}

function convertDate(date) {
    return moment(date, "YYYY-MM-DD").format('DD/MM/YYYY');
}

function convertDateTime(date) {
    return moment(date, "YYYY-MM-DD HH:mm").format('HH:mm DD/MM/YYYY');
}

function checkDelete(id) {
    Swal.fire({
        title: "Cảnh báo !",
        text: "Bạn chắc chắn muốn xóa ?",
        type: "warning",
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        cancelButtonColor: "#007bff",
        confirmButtonText: "Xóa",
        cancelButtonText: "Hủy",
        closeOnConfirm: false,
        closeOnCancel: false
    }).then(function(result) {
        if (result.value) {
            if (id) {
                window.location.href = urlDelete + '?id=' + id;
            }
        } else {
            return false;
        }
    })
}

function queryParamUserList(params) {
    var search = [];
    params.keyword = $('#keyword').val();
    params.account_group_id = $('#account_group_id').val();
    params.status = 1;
    params.permission_type = $('#permission_type').val();
    params.page = parseInt(params.offset / params.limit) + 1;
    return params;
}

function queryParamUserDisableList(params) {
    var search = [];
    params.keyword = $('#disable-keyword').val();
    params.account_group_id = $('#disable-account_group_id').val();
    params.status = 0;
    params.permission_type = $('#permission_type').val();
    params.page = parseInt(params.offset / params.limit) + 1;
    return params;
}

function countTableRow(id_table, id_show) {
    // var x = document.getElementById(id_table).rows.length;
    // x = x - 1;
    // $('#' + id_show).html(x);
}

function addUserGroupRow() {
    var d = new Date();
    var id = d.getTime();
    var userList = '<option value="">Chọn tài khoản</option><option value="">---->Không chọn</option>';

    if (users) {
        for (var i = 0; i < users.length; i++) {
            userList += '<option value="' + users[i].id + '">' + users[i].name + '</option>';
        }
    }
    var row = $('<tr id="row' + id + '">' +
        '<td></td>' +
        '<td><input type="text" class="form-control" name="new[' + id + '][name]"></td>' +
        '<td class="text-center">' +
        '<select class="form-control" name="new[' + id + '][admin_user_id]">' + userList + '</select></td>' +
        '<td class="text-center"><button class="btn btn-danger" onclick="removeRow(' + id + ')"><i class="fa fa-trash-alt"></i></button></td>' +
        '</tr>');

    $('#order-source-body').append(row);
}
$(document).on("click", "#customSearch", function() {
    $("#table-list").bootstrapTable("refresh");
    countTableRow('table-list', 'count-data')
})
$(document).on("click", "#disable-customSearch", function() {
    $("#table-disable-list").bootstrapTable("refresh");
    countTableRow('table-disable-list', 'disable-count-data')
})
