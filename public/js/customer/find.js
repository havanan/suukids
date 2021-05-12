function queryParamCustomerFind(params) {
    var search = [];
    params.type = $('#type').val();
    params.created_from = $('#created_from').val();
    params.created_to = $('#created_to').val();
    params.name = $('#name').val();
    params.phone = $('#phone').val();
    params.customer_group_id = $('#customer_group_id').val();
    params.page = parseInt(params.offset / params.limit) + 1;
    return params;
}
$(document).on("click","#customSearch",function(){
    $("#table-list").bootstrapTable("refresh");
})
function customerFormat(value, row, index, field) {
    if (value){
        var input = row.id;
        var btn  = '<button class="btn btn-warning text-white" id="btn-select-'+input+'" data-name="'+value+'" onclick="selectCustomer('+input+')">Chọn</button>';
        var text = '<p>'+value+'</p>';
        return btn+text;
    }
}
function orderFormat(value, row, index, field) {
    var link = '';
    var note_histories = row.orders ? row.orders : null;
    var histories = getNoteHistories(note_histories);
    if (histories){
        link = '<a href="/admin/order/edit/'+histories.id+'">'+histories.id+'</a><br>';
        if (histories.status_id){
            var label = ' <small class="badge badge-danger"><i class="far fa-clock"></i> '+histories.status_id+'</small>';
            link+=label;
        }

    }
    return link;
}
function groupFormat(value, row, index, field) {
    var name = '-'
    if (value){
        name = value;
    }
    return name;
}
function userConfirmFormat(value, row, index, field) {
    var name = '-'
    if (value){
        name = value;
    }
    return name;
}
function userCreatedFormat(value, row, index, field) {
    var name = '-'
    if (value){
        name = value;
        name+='<br><small class="text-gray"> lúc'+row.created_at+'</small>';
    }
    return name;
}
function bntViewFormat(value, row, index, field) {
    if (value){
        return '<a href="view/'+value+'" class="btn btn-primary"><i class="fa fa-eye"></i></a>'
    }
}
function bntNoteFormat(value, row, index, field) {
    if (value){
        var date_create = '';
        var btn = '<button id="openNote" class="btn btn-warning text-white"  data-name ="'+row.name+'" data-id = "'+row.id+'"><i class="fa fa-newspaper"></i></button><br>';
        var note_histories = row.note_histories ? row.note_histories : null;
        var histories = getNoteHistories(note_histories);
        if (histories){
            if (histories.date_create){
                date_create = '<span>'+histories.date_create+'</span><br>';
            }
        }
        return date_create;
    }
}
function bntCallFormat(value, row, index, field) {
    if (value){
        var btn = '<button id="openCall" class="btn btn-success"  data-name ="'+row.name+'" data-id = "'+row.id+'"><i class="fa fa-phone-square"></i></button><br>';
        var date_create = '';
        var last = '';
        var note_histories = row.note_histories ? row.note_histories : null;
        var histories = getNoteHistories(note_histories);
        if (histories){
            if (histories.date_create){
                date_create = '<span>'+histories.date_create+'</span><br>';
            }
            if (histories.ctm_care.name){
                last = '<span>'+histories.ctm_care.name+'</span><br>';
            }
        }
        return date_create+last;
    }
}
function bntEditFormat(value, row, index, field) {
    if (value){
        return '<a href="edit/'+value+'" class="btn btn-default"><i class="fa fa-pen"></i></a>'
    }
}
function getNoteHistories(note_histories) {
    var histories = null;
    if (note_histories && note_histories.length > 0){
         histories = note_histories[note_histories.length - 1];
    }
    return histories;
}
function selectCustomer(id) {
    $('#modal-xl').modal('toggle');

    if (id){
        var btnId = '#btn-select-'+id;
        $('#contact_id').val(id);
        var name = $(btnId).attr('data-name');
        $('#contact_name').val(name);
    }
}
function findCustomer() {

}
