$('.note').summernote()
let urlList = ''
$('.edit-note').click(function(){
   urlList = urlListNote;
  let note_id =  $(this).attr('data-id');
  // let customer_name = $(this).attr('data-name');
  $.ajax({
    type: "POST",
    url: urlGetDetailNote,
    dataType: "JSON",
    data: {note_id:note_id},
    success: function(res) {
        if (res.status == "OK") {
          $('.note_customer_name').val(res.data.customers_name);
          $('.note_customer_id').val(res.data.customer_id);
          $('.note_customer_emotions').val(res.data.customer_emotions);
          $('.note_content').summernote('code',res.data.content);
          $('.note_date_create').val(res.data.date_create);
          $('.note_create_by').val(res.data.create_by_name);
          $('.note_create_by_id').val(res.data.create_by);
          $('.note_id').val(res.data.id);
          let content = '<ul  class="list-unstyled">';
          
          $.each(res.historyUpdate, function (index,value) {
            content += '<li class="list-group-item">'
            content +=  '<div class="row">'
            content +=  '<div class="col-md-12"><i class="far fa-clock"></i> '
            content +=  value.time +' </div>'
            content +=  '<div class="col-md-12">'
            content +=  '<strong>'+ value.user +'</strong>'
            content +=  '<span class="small"> đã sửa note: <br>'
            $.each(value.content, function (key, val) {
              if(key == 'emotions'){
                content +=  '<strong>Cảm xúc</strong>: '+CUSTOMER_EMOTIONS[val.old]+' <strong class="text-red">-> </strong>'+CUSTOMER_EMOTIONS[val.new]+' <br></span>'
              }
              if(key == 'content'){
                content +=  '<strong>Nội dung</strong>: '+val.old.replace("<p>", "").replace("</p>", "")+' <strong class="text-red">-> </strong>'+val.new.replace("<p>", "").replace("</p>", "")+' <br></span>'
              }
            });
            
            content +=  '</div>'
            content +=  '</div>'
            content +='</li>'

          });
          content +='</ul>'
          
          $('#history_note').html(content)
          $('#modelNote').modal('show');
        }else{
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

$('.edit-call').click(function(){
  urlList = urlListCall;
  let call_id =  $(this).attr('data-id');
  $.ajax({
    type: "POST",
    url: urlGetDetailCall,
    dataType: "JSON",
    data: {call_id:call_id},
    success: function(res) {
        if (res.status == "OK") {
          $('.call_customer_name').val(res.data.customers_name);
          $('.call_customer_id').val(res.data.customer_id);
          $('.call_customer_emotions').val(res.data.customer_emotions);
          $('.call_content').summernote('code',res.data.content);
          $('.call_date_create').val(res.data.date_create);
          $('.call_create_by').val(res.data.create_by_name);
          $('.call_create_by_id').val(res.data.create_by);
          $('.call_customer_care_id').val(res.data.customer_care_id);
          $('.call_id').val(res.data.id);
          let content = '<ul  class="list-unstyled">';
          $.each(res.historyUpdate, function (index,value) {
            content += '<li class="list-group-item">'
            content +=  '<div class="row">'
            content +=  '<div class="col-md-12"><i class="far fa-clock"></i> '
            content +=  value.time +' </div>'
            content +=  '<div class="col-md-12">'
            content +=  '<strong>'+ value.user +'</strong>'
            content +=  '<span class="small"> đã sửa note: <br>'
            $.each(value.content, function (key, val) {
              if(key == 'emotions'){
                content +=  '<strong>Cảm xúc</strong>: '+CUSTOMER_EMOTIONS[val.old]+' <strong class="text-red">-> </strong>'+CUSTOMER_EMOTIONS[val.new]+' <br></span>'
              }
              if(key == 'content'){
                content +=  '<strong>Nội dung</strong>: '+val.old.replace("<p>", "").replace("</p>", "")+' <strong class="text-red">-> </strong>'+val.new.replace("<p>", "").replace("</p>", "")+' <br></span>'
              }
              if(key == 'customer_care'){
                content +=  '<strong>Trạng thái KH</strong>: '+customerCareStatus[val.old]+' <strong class="text-red">-> </strong>'+customerCareStatus[val.new]+' <br></span>'
              }
            });
            
            content +=  '</div>'
            content +=  '</div>'
            content +='</li>'

          });
          content +='</ul>'
          $('#history_call').html(content)
          $('#modeCall').modal('show');
        }else{
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

$('.edit-pathological').click(function(){
  urlList = urlListPathological;
  let pathological_id =  $(this).attr('data-id');
  $.ajax({
    type: "POST",
    url: urlGetDetailPathological,
    dataType: "JSON",
    data: {pathological_id:pathological_id},
    success: function(res) {
        if (res.status == "OK") {
          $('.pathological_customer_name').val(res.data.customers_name);
          $('.pathological_name').val(res.data.name);
          $('.pathological_customer_id').val(res.data.customer_id);
          $('.pathological_status').val(res.data.status);
          $('.pathological_date_create').val(res.data.date_create);
          $('.pathological_create_by').val(res.data.create_by_name);
          $('.pathological_create_by_id').val(res.data.create_by);
          $('.pathological_id').val(res.data.id);
          let content = '';
          $.each(res.historyUpdate, function (index,value) {
            content += '<li class="list-group-item">'
            content +=  '<div class="row">'
            content +=  '<div class="col-md-12"><i class="far fa-clock"></i> '
            content +=  value.time +' </div>'
            content +=  '<div class="col-md-12">'
            content +=  '<strong>'+ value.user +'</strong>'
            content +=  '<span class="small"> đã sửa note: <br>'
            $.each(value.content, function (key, val) {
              if(key == 'name'){
                content +=  '<strong>Tên bệnh lý</strong>: '+val.old+' <strong class="text-red">-> </strong>'+val.new+' <br></span>'
              }
              if(key == 'status'){
                content +=  '<strong>Tình trạng bệnh lý</strong>: '+val.old.replace("<p>", "").replace("</p>", "")+' <strong class="text-red">-> </strong>'+val.new.replace("<p>", "").replace("</p>", "")+' <br></span>'
              }
            });
            
            content +=  '</div>'
            content +=  '</div>'
            content +='</li>'

          });
          $('#history_pathological').html(content)
          $('#modePathological').modal('show');
        }else{
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


$('#openPathological').click(function(){
  let customer_id =  $(this).attr('data-id');
  let customer_name = $(this).attr('data-name');
  $('input[name="pathological_customer_name"]').val(customer_name);
          $('input[name="pathological_customer_id"]').val(customer_id);
  $('#modelPathological').modal('show');
  
})

$('#savePathological').click(function(){
  urlList = urlListPathological;
  var formData = Common.formData("#formPathological");
  Common.hideAlert();
  $.ajax({
      type: "POST",
      url: urlStoreformPathological,
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
