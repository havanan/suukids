$(document).ready(function(){
  Common.datePicker(".datepicker");
})
function alertConfirm(){
  var $url = urlDelete;
  Swal.fire({
      title: "Cảnh báo !",
      text: "Bạn chắc chắn muốn thực hiện ?",
      type: "warning",
      showConfirmButton:true,
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      cancelButtonColor: "#007bff",
      confirmButtonText: "Xóa",
      cancelButtonText: "Hủy",
      closeOnConfirm: false,
      closeOnCancel: false
  }).then(function (result) {
      if(result.value){
        var post_arr = [];
        // Get checked checkboxes
        $('#stock-table input[type=checkbox]').each(function() {
          if ($(this).is(":checked")) {
            var id = $(this).val();
            if(id != 'on') post_arr.push(id);
          }
        });
        console.log(post_arr)
        $.ajax({
            type:'POST',
            url: $url,
            data:{
                id: post_arr
            },
            success: function(res) {
              if (res.status == "NG") {
                  Common.showAlert(
                      'Vui lòng chọn hóa đơn'
                  );
                  return;
              }
              Swal.fire("", "Xóa thành công!", "success");
              setTimeout(function() {
                location.reload();
              }, 1000);
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
      }
  })
}