function addRow() {
  var d = new Date();
  var id = d.getTime();

  var productUnitSelect = $(
      '<select class="form-control product-stock-group" name="new['+ id +'][unit_id]"></select>'
  );
  $.each(productUnit, function(optionText, optionValue) {
      $(productUnitSelect).append(new Option(optionValue, optionText));
  });
  var productBundleSelect = $(
    '<select class="form-control product-stock-group" name="new['+ id +'][bundle_id]"></select>'
  );
  $.each(productBundle, function(optionText, optionValue) {
      $(productBundleSelect).append(new Option(optionValue, optionText));
  });
  var row = $(
    '<tr class="product-item">'+
      '<td>'+
        '<div>'+
          '<img src="'+ imgNoProduct +'" alt="" class="img-product-size"><br>' +
          '<i class="fas fa-upload upload-img-pro" style="cursor: pointer;"></i>' +
          '<input type="file" accept="image/*" onchange="readURL(this)" name="new['+ id +'][product_image]" class="w-25 d-none">' +
        '</div>' +
      '</td>'+
      '<td><input type="text" placeholder="Ví dụ SP01" name="new['+ id +'][code]"></td>'+
      '<td><input type="text" name="new['+ id +'][name]"></td>'+
      '<td><input type="number" name="new['+ id +'][price]"></td>'+
      '<td><input type="number" name="new['+ id +'][cost_price]"></td>'+
      '<td><input type="text" name="new['+ id +'][weight]"></td>'+
      '<td><input type="text" name="new['+ id +'][color]"></td>'+
      '<td><input type="text" name="new['+ id +'][size]"></td>'+
      "<td>" +
        $("<div></div>")
            .append(productBundleSelect)
            .html() +
        "</td>" +
      "<td>" +
        $("<div></div>")
            .append(productUnitSelect)
            .html() +
      "</td>" +
      '<td></td>'+
      '<td>'+
        '<div class="custom-control custom-checkbox">'+
          '<input type="checkbox" class="checkRow custom-control-input" name="new['+ id +'][status]" id="checkbox' + id +'" >'+
          '<label class="custom-control-label" for="checkbox' + id +'"></label>'+
        '</div>'+
      '</td>'+
      '<td>'+
        '<span class="table-remove btn-remove-product-item"><button type="button"'+
            'class="btn btn-danger btn-rounded btn-sm my-0"><i class="far fa-trash-alt"></i></button></span>'+
      '</td>'+
    '</tr>'
  );
  $("#product-body").append(row);
  // $(".product-expired-date").datepicker({ dateFormat: "dd/mm/yy" });
}

function readURL(input) {
  
  if (input.files && input.files[0]) {

    var file = input.files[0];//get file
    if(Math.round(file.size/(1024*1024)) > 8){ // make it in MB so divide by 1024*1024
      Swal.fire("", "Vui lòng chọn ảnh nhỏ hơn 8Mb", "error");
      return false;
   }
    var reader = new FileReader();
    reader.onload = function(e) {
      console.log(e.target.result)
      $(input).parent().find('img').attr('src', e.target.result);
      $(input).parent().find('.changeImage').val('1');
    }
    
    reader.readAsDataURL(input.files[0]);
  }
}

function alertConfirm(id){
  var $url = urlConfirm;
  Swal.fire({
      title: "Cảnh báo !",
      text: "Bạn chắc chắn muốn thực hiện ?" + "\n" + "(Hãy chú ý có thể ảnh hưởng tới đơn hàng)",
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
          $.ajax({
              type:'POST',
              url: $url,
              data:{
                  id: id
              },
              success:function(response){
                  if(response === 'true'){
                    $('#pro_'+id).remove();
                    let count = $('.count-product').text();
                    $('.count-product').text(count-1);
                    alertPopup('success','Xóa thành công');
                  }else {
                      alertPopup('error','Xóa thất bại');
                  }
              },
              error: function(e){
                  alertPopup('error','Xóa thất bại');
              }
          });
      }
  })
}

$(document).on("click", ".btn-remove-product-item", function() {
  $(this)
      .closest(".product-item")
      .remove();
});

$(document).on("click", ".upload-img-pro", function() {
  $(this)
      .closest("div")
      .find('input[type="file"]')
      .click();
});

$(document).on("click", "#btn-save", function() {
  var formData = Common.formData("#form-products");
  $.ajax({
      type: "POST",
      url: urlValidateBeforeSave,
      dataType: "JSON",
      data: formData,
      contentType: false,
      processData: false,
      success: function(res) {
          if (res.status == "NG") {
            Swal.fire("Cảnh báo thêm sản phẩm dòng: " + res.row, res.message, "error");
            return;
          }
          $('#form-products').submit();
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


