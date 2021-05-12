function nameFormat(value, row, index, field){
    return `${value} (<code>${row.code}</code>)`;
}
function actionFormat(value, row, index, field){

    var btnEdit = '<a href="product/edit/'+row.id+'" class="btn btn-warning text-white mr-2"><i class="fa fa-pen"></i> Sửa</a>';
    var btnDelete= '<button class="btn btn-danger" onclick="checkDelete('+row.id+')"><i class="fa fa-trash-alt"></i> Xóa</button>';

    return `<div class="btn-group">${btnEdit}${btnDelete}</div>`;
}
function propertiesFormat(value, row, index, field) {
    return '';// '<ul><li>Màu sắc: '+color+'</li><li>Kích cỡ: '+size+'</li></ul>'
    var color = row.color != null ? row.color : '';
    var size = row.size != null ? row.size : '';
}

function priceFormat(value, row, index, field) {
    if (value){
        return '<strong>'+formatNumber(value)+'</strong>';
    }
}
function avatarFormat(value, row, index, field) {
    if (value){
        return avatar = '<img src="/'+value+'" width="60px">';
    }
}
function convertDate(date){
    return moment(date,"YYYY-MM-DD").format('DD/MM/YYYY');
}
function convertDateTime(date){
    return  moment(date,"YYYY-MM-DD HH:mm").format('HH:mm DD/MM/YYYY');
}
function checkDelete(id) {
    Swal.fire({
        title: "Cảnh báo !",
        text: "Bạn chắc chắn muốn xóa ?",
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
            if (id){
                window.location.href = urlDelete+'/'+id;
            }
        }else{
            return  false;
        }
    })
}
function queryParamProductList(params) {
    var search = [];
    params.keyword = $('#keyword').val();
    params.bundle_id = $('#bundle_id').val();
    params.unit_id = $('#unit_id').val();
    params.status = $('#status').val();
    params.page = parseInt(params.offset / params.limit) + 1;
    return params;
}
function queryParamProductDisableList(params) {
    var search = [];
    params.keyword = $('#disable-keyword').val();
    params.bundle_id = $('#disable-bundle_id').val();
    params.unit_id = $('#disable-unit_id').val();
    params.status = $('#disable-status').val();
    params.page = parseInt(params.offset / params.limit) + 1;
    return params;
}
$(document).on("click","#customSearch",function(){
    $("#table-list").bootstrapTable("refresh");
})
$(document).on("click","#disable-customSearch",function(){
    $("#table-disable-list").bootstrapTable("refresh");
})
