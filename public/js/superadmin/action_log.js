function queryParamList(params) {
    params.shop_id = $('#shop_id').val();
    params.ip = $('#ip').val();
    params.source = $('#source').val();
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
    var contentQuery = JSON.parse(value);
    if (contentQuery.customer_phone_or_code && !contentQuery.customer_name)
    return "SĐT: "+escapeHtml(contentQuery.customer_phone_or_code);
    if (!contentQuery.customer_phone_or_code && contentQuery.customer_name)
    return "Tên khách hàng: "+escapeHtml(contentQuery.customer_name);
    return "SĐT: "+escapeHtml(contentQuery.customer_phone_or_code) + " - " +  "Tên khách hàng: " +escapeHtml(contentQuery.customer_name);
}
function formatterUrl(value){
    if (value.indexOf('create')!==-1) return 'Tạo đơn mới';
    return 'Quản lý đơn hàng';
}
function formatterIp(value){
    if (['42.113.205.153','27.67.16.251','42.118.38.142'].indexOf(value)!==-1) return 'IP Nội bộ';
    return '<code>IP khác: '+value+'</code>';
}
$('.select2').select2({});
Common.datePicker("#start_date");
Common.datePicker("#end_date");
