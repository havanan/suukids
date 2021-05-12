function addRow() {
  var d = new Date();
  var id = d.getTime();
  
  var row = $('<tr id="row'+id+'">' +
      '<td>' +
        '<div class="custom-control custom-checkbox">' +
          '<input type="checkbox" class="custom-control-input checkRow" id="customCheck'+id+'">' +
          '<label class="custom-control-label" for="customCheck'+id+'"></label>' +
        '</div>'+
      '</td>' +
      '<td><input type="text" class="form-control" name="new['+ id +'][name]"></td>' +
      '<td>' +
        '<div class="custom-control custom-checkbox">' +
          '<input type="checkbox" name="new['+ id +'][no_revenue_flag]" value="1" class="custom-control-input" id="no_revenue_flag_'+id+'">' +
          '<label class="custom-control-label" for="no_revenue_flag_'+id+'"></label>' +
        '</div>'+
      '</td>' +
      '<td>' +
        '<div class="custom-control custom-checkbox">' +
          '<input type="checkbox" name="new['+ id +'][no_reach_flag]" class="custom-control-input" value="1" id="no_reach_flag_'+id+'">'+
          '<label class="custom-control-label" for="no_reach_flag_'+id+'"></label>'+
        '</div>'+
      '</td>' +
      '<td class="text-center"><button class="btn btn-danger" onclick="removeRow('+id+')"><i class="fa fa-trash-alt"></i></button></td>' +
      '<input type="hidden" name="newStatus[]" class="new-status" value = "">' + 
      '</tr>');
  $('#status-body').append(row);
}
function removeRow(id) {
  if(id){
    let statusId = $('#row'+id).attr('data-id');
    let removeId = $('input[name ="removeStatus"]').val();
    if(removeId == ''){
      $('input[name ="removeStatus"]').val(statusId)
    }else $('input[name ="removeStatus"]').val(removeId + ',' + statusId)
    $('#row'+id).remove();
  }
}
$(document).ready(function(){
  $("div#statusEdit table").delegate('tr', 'click', function() {
    if($(this).attr('data-system') == 1 ){
      alert('Trạng thái mặc định, quý khách vui lòng không sửa xóa')
    }
  });
  $("div#statusEdit table").on('change','.name, .revenue, .reach',function(){
    let name = $(this).closest('tr').find('.name').val();
    let id =  $(this).closest('tr').attr('data-id');
    let revenue = '';
    let reach = '';
    
    
    if($(this).closest('tr').find('.revenue').is(":checked")){
      revenue = 1;
    }else revenue = 0;
    if($(this).closest('tr').find('.reach').is(":checked")){
      reach = 1;
    }else reach = 0;
    $('input[name="edit['+ id +'][name]"]').val(name)
    $('input[name="edit['+ id +'][no_reach_flag]"]').val(reach)
    $('input[name="edit['+ id +'][no_revenue_flag]"]').val(revenue)
  })

});
$(document).on("click", ".btn-save", function() {
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
              Swal.fire("Cảnh báo thêm trạng thái: ",  res.message, "error");
              return;
          }
          Swal.fire("", "Cập nhật thành công!", "success");
          setTimeout(function() {
              window.location.href = urlStore;
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

$(document).on("click", "#btn-save-shop", function() {
  var formData = Common.formData("#frm-data-shop");
  $.ajax({
      type: "POST",
      url: urlStoreShop,
      dataType: "JSON",
      data: formData,
      contentType: false,
      processData: false,
      success: function(res) {
          if (res.status == "NG") {
            let message = ''
              $.each(res.message,function(mess, value){
                message += value[0]+'<br/>'
              })
              Swal.fire("Cảnh báo lưu shop: ", message, "error");
              return;
          }
          Swal.fire("", "Cập nhật thành công!", "success");
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
});