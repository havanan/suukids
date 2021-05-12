function addRow() {
    var d = new Date();
    var id = d.getTime();
    var prefecture = $('#prefecture').html();
    var row = $('<tr id="row' + id + '">' +
        '<td>' +
        '<div class="custom-control custom-checkbox">' +
        '<input type="checkbox" class="" id="customCheck' + id + '">' +
        '<label class="" for="customCheck' + id + '"></label>' +
        '</div>' +
        '</td>' +
        '<td><input type="text" class="form-control supplier-code"  name="new[' + id + '][code]"></td>' +
        '<td><input type="text" class="form-control" name="new[' + id + '][name]"></td>' +
        '<td><input type="text" class="form-control" name="new[' + id + '][phone]"></td>' +
        '<td><input type="text" class="form-control" name="new[' + id + '][address]"></td>' +
        '<td><select class="form-control" name="new[' + id + '][prefecture]">' + prefecture + '</select></td>' +
        '<td class="text-center"><button class="btn btn-danger" onclick="removeRow(' + id + ')"><i class="fa fa-trash-alt"></i></button></td>' +
        '</tr>');

    $('#supplier-body').append(row);
}

function removeRow(id) {
    if (id) {
        let supplierId = $('#row' + id).attr('data-id');
        let removeId = $('input[name ="removeSupplier"]').val();
        if (removeId == '') {
            $('input[name ="removeSupplier"]').val(supplierId)
        } else $('input[name ="removeSupplier"]').val(removeId + ',' + supplierId)
        $('#row' + id).remove();
    }

}

$(document).on("click", "#btn-save", function() {
    var formData = Common.formData("#base-information");
    Common.hideAlert();
    $.ajax({
        type: "POST",
        url: urlStore,
        dataType: "JSON",
        data: formData,
        contentType: false,
        processData: false,
        success: function(res) {
            if (res.status == "NG") {
                // alert(res.message)
                Common.showAlert(
                    res.message
                );
                return;
            }
            Swal.fire("", "Đã cập nhật thành công!", "success");
            setTimeout(function() {
                location.reload();
            }, 1500);
        },
        error: function(e) {
            if (
                typeof e.responseJSON !== "undefined" &&
                typeof e.responseJSON.message !== "undefined"
            ) {
                Common.showAlert(e.responseJSON.message);
            } else {
                Swal.fire("", "Có lỗi xảy ra vui lòng kiểm tra lại", "error");
            }
        }
    });
});