$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    }
});
function addRow() {
    var d = new Date();
    var id = d.getTime();

    var row = $(
        '<tr id="row' +
            id +
            '">' +
            "<td><p>(auto)</p></td>" +
            '<td><input type="text" class="form-control" name="new[' +
            id +
            '][name]"></td>' +
            '<td class="text-center">' +
            '<div class="icheck-primary d-inline">' +
            '    <input type="checkbox" id="checkbox' +
            id +
            '" value="1" name="new[' +
            id +
            '][default_select]">' +
            '    <label for="checkbox' +
            id +
            '"></label>' +
            "</div></td>" +
            '<td class="text-center"><button class="btn btn-danger" onclick="removeRow(' +
            id +
            ')"><i class="fa fa-trash-alt"></i></button></td>' +
            "</tr>"
    );
    $("#order-source-body").append(row);
}
function addNameRow() {
    var d = new Date();
    var id = d.getTime();

    var row = $(
        '<tr id="row' +
            id +
            '">' +
            '<td class="text-center">' +
            '<div class="icheck-primary d-inline">' +
            '    <input type="checkbox" id="checkbox' +
            id +
            '">' +
            '    <label for="checkbox' +
            id +
            '"></label></div></td>' +
            "<td><p>(auto)</p></td>" +
            '<td><input type="text" class="form-control" name="new[' +
            id +
            '][name]"></td>' +
            '<td class="text-center"><button class="btn btn-danger" onclick="removeRow(' +
            id +
            ')"><i class="fa fa-trash-alt"></i></button></td>' +
            "</tr>"
    );

    $("#order-source-body").append(row);
}

function removeRow(id) {
    if (id) {
        $("#row" + id).remove();
    }
}
function deleteItem(id) {
    if (id) {
        alertConfirm(id);
    }
}
function submitForm() {
    $("#frm-data").submit();
}
function alertPopup(type, message) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: toast => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        }
    });
    Toast.fire({
        icon: type,
        type: type,
        title: message
    });
}
function alertConfirm(id) {
    var $url = urlConfirm;
    Swal.fire({
        title: "Cảnh báo !",
        text: "Bạn chắc chắn muốn thực hiện ?",
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
            $.ajax({
                type: "POST",
                url: $url,
                data: {
                    id: id
                },
                success: function(response) {
                    if (response === "true") {
                        removeRow(id);
                        alertPopup("success", "Xóa thành công");
                    } else {
                        alertPopup("error", "Xóa thất bại");
                    }
                },
                error: function(e) {
                    alertPopup("error", "Xóa thất bại");
                }
            });
        }
    });
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
                window.location.href = urlDelete + "?id=" + id;
            }
        } else {
            return false;
        }
    });
}
function readImageUrl(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var new_img = e.target.result;
            $("#imagePreview").attr("src", new_img);
            $("#imagePreview").hide();
            $("#imagePreview").fadeIn(650);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function getUserByType(type) {
    if (type === 0) {
        urlList = urlList + "?type=0";
    }
    window.location.href = urlList;
    // let $id = '#nav-home';
    // if (type === 0){
    //     $id = '#nav-profile';
    // }
    // $.ajax({
    //     type:'POST',
    //     url: urlList,
    //     data:{
    //         type: type
    //     },
    //     success:function(response){
    //         $($id).html(response);
    //     },
    //     error: function(e){
    //         alertPopup('error','Lỗi load data');
    //     }
    // });
}
function datePicker() {
    $(".date-picker").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minYear: 1901,
        locale: {
            format: "DD-MM-YYYY",
            daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            monthNames: [
                "01",
                "02",
                "03",
                "04",
                "05",
                "06",
                "07",
                "08",
                "09",
                "10",
                "11",
                "12"
            ]
        }
    });
}
function formatPrice(element) {
    $(element).change(function() {
        var number = $(this).val();
        if (number) {
            number = new Intl.NumberFormat().format(number);
        }
        $(this).val(number);
    });
}
$("#checkAll").click(function() {
    $("input:checkbox")
        .not(this)
        .prop("checked", this.checked);
});
function goListCustomer(url) {
    window.open(url, "_blank");
}
function selectCustomer(id, name) {
    $("#modal-xl").modal("toggle");
    if (id) {
        $("#contact_id").val(id);
    }
    if (name) {
        $("#contact_name").val(name);
    }
}
function findCustomer() {}

