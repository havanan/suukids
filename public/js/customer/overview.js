function addTag(elm){
    $.ajax({
        url:urlOverviewTagAdd,
        method:'POST',
        data:{customer_id:elm.dataset.key,value:elm.dataset.value},
        success:function(resp) {
            if (resp.success) {
                toastr.success(resp.msg);
            } else {
                toastr.error(resp.msg);
            }
        },error:function(jqXHR, error, text) {
            toastr.error(error);
        }
    })
}
function updateTag(elm){
    $.ajax({
        url:urlOverviewTagUpdate,
        method:'POST',
        data:{customer_id:elm.dataset.key,value:elm.dataset.value.toUpperCase()},
        success:function(resp) {
            if (resp.success) {
                toastr.success(resp.msg);
            } else {
                toastr.error(resp.msg);
            }
        },error:function(jqXHR, error, text) {
            toastr.error(error);
        }
    })
}
function updateOverview(elm){
    if (elm.value.length)
    $.ajax({
        url:urlOverviewUpdate,
        method:'POST',
        data:{customer_id:elm.dataset.key,field:elm.dataset.column,value:elm.value},
        success:function(resp) {
            if (resp.success) {
                toastr.success(resp.msg);
            } else {
                toastr.error(resp.msg);
            }
        },error:function(jqXHR, error, text) {
            toastr.error(error);
        }
    })
}
$(function(){
    $('#frmSearch input').on('change',function(){
        $('#frmSearch').trigger('submit')
    });
    $('#frmSearch select').on('change',function(){
        $('#frmSearch').trigger('submit')
    });
    Common.datePicker(".datepicker")
    $('.select2').select2({})
})
