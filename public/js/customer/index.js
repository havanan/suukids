 $(document).ready(function() {
     Common.datePicker(".datepicker");
     $('.note').summernote()
     $('#openCall').click(function() {
         // alert(1);
         let customer_id = $(this).attr('data-id');
         let customer_name = $(this).attr('data-name');
         $.ajax({
             type: "POST",
             url: urlGetHistoryCall,
             dataType: "JSON",
             data: { customer_id: customer_id },
             success: function(res) {
                 if (res.status == "OK") {
                     $('input[name="call_customer_name"]').val(customer_name);
                     $('input[name="call_customer_id"]').val(customer_id);
                     let content = '<div class="timeline">';
                     $.each(res.data, function(index, value) {

                         content += '<div>'
                         content += '    <i class="fas fa-phone-square-alt bg-blue"></i>'
                         content += '   <div class="timeline-item">'
                         content += '        <h3 class="timeline-header">Đã tạo cuộc gọi lúc : ' + value.date_create + '</h3>'
                         content += '    </div>'
                         content += '</div>'

                     });
                     content += '</div>'
                     $('#history_call').html(content)
                     $('#modelCall').modal('show');
                 } else {
                     Swal.fire("", "Có lỗi xảy ra vui lòng kiểm tra lại", "error");
                 }

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
     })

     $('#openNote').click(function() {
         let customer_id = $(this).attr('data-id');
         let customer_name = $(this).attr('data-name');
         $.ajax({
             type: "POST",
             url: urlGetHistoryNote,
             dataType: "JSON",
             data: { customer_id: customer_id },
             success: function(res) {
                 if (res.status == "OK") {
                     $('input[name="note_customer_name"]').val(customer_name);
                     $('input[name="note_customer_id"]').val(customer_id);
                     let content = '<div class="timeline">';
                     $.each(res.data, function(index, value) {

                         content += '<div>'
                         content += '    <i class="fas fa-file-alt bg-blue"></i>'
                         content += '   <div class="timeline-item">'
                         content += '        <h3 class="timeline-header">Đã tạo note lúc : ' + value.date_create + '</h3>'
                         content += '    </div>'
                         content += '</div>'

                     });
                     content += '</div>'
                     $('#history_note').html(content)
                     $('#modelNote').modal('show');
                 } else {
                     Swal.fire("", "Có lỗi xảy ra vui lòng kiểm tra lại", "error");
                 }

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
     })

     $('#saveCall').click(function() {
         var formData = Common.formData("#formCall");
         Common.hideAlert();
         $.ajax({
             type: "POST",
             url: urlStoreCall,
             dataType: "JSON",
             data: formData,
             contentType: false,
             processData: false,
             success: function(res) {
                 if (res.status == "NG") {
                     Swal.fire("", res.message[0], "error");
                     return;
                 }
                 Swal.fire("", "Lưu lịch  sử thành công", "success");
                 setTimeout(function() {
                     window.location.href = urlList;
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
     })

     $('#saveNote').click(function() {
         var formData = Common.formData("#formNote");
         Common.hideAlert();
         $.ajax({
             type: "POST",
             url: urlStoreNote,
             dataType: "JSON",
             data: formData,
             contentType: false,
             processData: false,
             success: function(res) {
                 if (res.status == "NG") {
                     Swal.fire("", res.message[0], "error");
                     return;
                 }
                 Swal.fire("", "Lưu ghi chú thành công", "success");
                 setTimeout(function() {
                     window.location.href = urlList;
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

     $('#btn-delete').on('click', function() {
         var ids = jQuery('input[name="delete[]"]:checked').map(function() {
             return jQuery(this).val()
         }).get();

         if (ids.length == 0) {
             Swal.fire("", "Vui lòng chọn ít nhất 1 khách hàng!");
             return;
         }

         deleteCustomers({ deleteAll: false, ids: ids });
     });

     $('#btn-delete-all').on('click', function() {
         deleteCustomers({ deleteAll: true });
     });

     function deleteCustomers({ deleteAll, ids }) {
         var message = deleteAll ? "Bạn chắc chắn muốn xóa tất cả khách hàng" : "Bạn chắc chắn muốn xóa những khách hàng được chọn";
         Swal.fire({
             title: 'Cảnh báo',
             text: message,
             icon: "warning",
             showCancelButton: true,
             confirmButtonClass: "btn-danger",
             confirmButtonText: "Xóa",
             cancelButtonText: "Hủy",
             closeOnConfirm: false
         }).then(
             function(result) {
                 console.log(result)
                 if (result.value) {
                     $.ajax({
                         type: 'DELETE',
                         url: urlDelete,
                         data: {
                             ids: ids,
                             'delete-all': deleteAll ? 1 : 0
                         },
                         success: function(response) {
                             window.location.reload();
                         },
                         error: function(e) {
                             Swal.fire("Có lỗi xảy ra vui lòng thử lại!", "error");
                             return;
                         }
                     });
                 }

             });
     }
 });