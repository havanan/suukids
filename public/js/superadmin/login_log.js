function queryParamList(params) {
    params.shop_id = $('#shop_id').val();
    params.user_name = $('#user_name').val();
    params.query_content = $('#query_content').val();
    params.start_date = $('#start_date').val();
    params.end_date = $('#end_date').val();
    params.page = parseInt(params.offset / params.limit) + 1;
    return params;
}
$(document).on("click","#customSearch",function(){
    $("#table-list").bootstrapTable('refresh');
    if ($('#user_name').val().length)
    setTimeout(function(){
        $('.bootstrap-table').parent().find('> .alert').remove();
        var a = [];
        $('#table-list tbody td:nth-child(4)').each(function(){
            var phone = this.innerText.split(' ').slice(-1).join('');
            if (a.indexOf(phone)===-1) a.push(phone);
        })
        $('.bootstrap-table').before(`<div class="alert alert-warning mt-3">Tìm thấy ${a.length} SĐT khác nhau nhân viên này đã tìm kiếm</div>`);
    },1000);
})
$('.select2').select2({});
function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
function formatterContentQuery(value){
    return '';
}
Common.datePicker("#start_date");
Common.datePicker("#end_date");
