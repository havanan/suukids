let filterDateKeys = [
    "create",
    "share",
    "close",
    "assign_accountant",
    "delivery",
    "complete",
    "refund",
    "collect_money"
];
let queryParam = "";

for (let key of filterDateKeys) {
    Common.datePickerFrom("#" + key + "_from_date");
    Common.datePicker("#" + key + "_to_date");
}

function indexFormat(value, row, index, field) {
    return index + 1;
}

//Check All
function isCheckAll() {
    return jQuery('input[name="delete[]"]:not(:checked)').length == 0;
}

function checkAll(element) {
    if (element.checked) {
        jQuery('input[name="delete[]"]').prop("checked", true);
    } else {
        jQuery('input[name="delete[]"]').prop("checked", false);
    }
}

function clickCheckBox(element) {
    if ($('input[name="delete[]"]:not(:checked)').length == 0) {
        jQuery(".check-all-order-item").prop("checked", true);
    } else {
        jQuery(".check-all-order-item").prop("checked", false);
    }
}

function checkboxFormat(value, row, index, field) {
    return (
        '<input type="checkbox" name="delete[]" value="' +
        value +
        '" class="order-item-checkbox">'
    );
}

function checkAllStatus() {
    $("input[name='status[]']").prop("checked", true);
    reloadList();
}

function unCheckAllStatus() {
    $("input[name='status[]']").prop("checked", false);
    reloadList();
}

function assignedUserFormat(value, row, index, field) {
    if (!value) {
        return "";
    }
    return value.account_id;
}

function sourceFormat(value, row, index, field) {
    if (!value) {
        return "";
    }
    return value.name;
}
function bundleFormat(value, row, index, field) {
    if (!value) {
        return "";
    }
    return value.name;
}

function customerFormat(value, row, index, field) {
    var name = "<div>" + value.name + "</div>";

    if (value.email) {
        name += '<span class="small">' + value.email + "</span>";
    }

    return name;
}

function callCloudfone(orderId, phoneNumber) {
    Swal.fire({
        title: "Bạn có chắc chắn muốn gọi",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        confirmButtonText: "Chắc chắn",
        cancelButtonText: "Không"
      }).then(function(isConfirm){
            if (isConfirm.value){
                jQuery.ajax({
                    url:callCloudfoneUrl,
                    cache:false,
                    type:"POST",
                    dataType:"JSON",
                    data:{"_token":token,"order_id":orderId, "phone_index": phoneNumber},
                    success:function(){
                        toastr.success("Gọi thành công");
                    }
                   }).fail(function(err){
                       toastr.error(err.responseJSON.message);
                 })
            }
      })
}
function customerAddressFormat(value, row, index, field){
    return row.customer ? (row.customer.address || '') : '';
}
function customerReturnedFormat(value, row, index, field){
    return row.returned || '0';
}
function customerPhoneFormat(value, row, index, field) {
    var order = row;
    var phone = order.phone;

    if (order.phone2) {
        phone += " " + order.phone2;
    }

    var borderColor = "#76ec39";
    if (row.status && row.status.color) {
        borderColor = row.status.color;
    }

    var closeDuplicate = order.close_duplicated_order_id;
    var textColor = closeDuplicate ? 'red' : 'black';
    var btnCallPhone1 = order.phone ? '<button type="button" class="btn btn-xs btn-success" onClick="callCloudfone('+ order.id +', 1)">Gọi</button>' : '';
    var btnCallPhone2 = order.phone2 ? '<button type="button" class="btn btn-xs ml-2 btn-success" onClick="callCloudfone('+ order.id +', 2)">Gọi Phone 2</button>' : '';
    var btnCallCreateOrder = `<a type="button" class="btn btn-xs ml-2 btn-info" href="/admin/sell/order/create?close_when_done=1&customer_id=${row.customer_id}">Tạo đơn</a>`;
    var buttons = [btnCallPhone1,btnCallPhone2,btnCallCreateOrder];

    var callContent = `<div>${buttons.filter(x => x !== '').join('')}</div>`;
    var showContent = `<div style="border: 1px solid ${borderColor}; border-left: 3px solid ${borderColor}; color: ${textColor};"><span>${phone}</span></div>`;

    return order.show_call_btns ? callContent : showContent;
}

function customerCallHistoryFormat(value, row, index, field) {
    var order = row;
    var phone = order.phone;
    return ('<div> <a href="' + callHistoryCloudfoneUrl + '?phone=' +  phone  +'" class="btn btn-danger btn-xs"> Xem Lịch Sử gọi </a> </div>');
}

function statusFormat(value, row, index, field) {
    if (!value) {
        return "";
    }
    return (
        '<span style = "border: 1px solid ' +
        value.color +
        ";border-left: 3px solid" +
        value.color +
        ';" >' +
        value.name +
        "</span>"
    );
}

function priceFormat(value, row, index, field) {
    return numberFormat(value);
}

function numberFormat(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}

function productsFormat(value, row, index, field) {
    if (!value) {
        return "";
    }

    var infos = "";

    for (let item of value) {
        if (!item.product) {
            continue;
        }
        infos += `<div>${item.quantity} ${item.product.name}` +
            (item.product.size ? " size " + item.product.size : "") +
        `</div>`;
    }

    return infos;
}

function upsaleFormat(value, row, index, field) {
    if (!value) {
        return "hotline";
    }
    return value;
}

function showQuickEditModal(order_id) {
    $.ajax({
        type: "GET",
        url: getOrderInfoUrl,
        data: {
            id: order_id
        },
        success: function(response) {
            var data = response.data;
            var order_products = data.order_products;
            if (order_products.length > 0) {
                createProductEdit(order_products);
            }
            $("#quick-edit-order-code").html(data.code);
            $("#quick-edit-customer-name").html(data.customer_name);
            $("#quick-edit-customer-phone").html(data.customer_phone);
            $("#quick-edit-address").val(data.customer.address);
            $("#quick-edit-note1").val(data.note1);
            $("#quick-edit-note2").val(data.note2);
            $("#quick-edit-id").val(data.id);
            $("#quick-edit-price").html(numberFormat(data.price));
            $("#quick-edit-discount-price").val(
                numberFormat(data.discount_price)
            );
            $("#quick-edit-discount-price").attr(
                "data-old",
                numberFormat(data.discount_price)
            );
            $("#quick-edit-total-price").html(numberFormat(data.total_price));
            if (isAdmin === 0 && data.status_id != noProcessOrderStatusId) {
                // disableInput(ids,true);
                $("#modal-body-quick-edit :input").attr("disabled", false);
            } else if (isAdmin === 1) {
                $("#modal-body-quick-edit :input").attr("disabled", false);
            } else {
                $("#modal-body-quick-edit :input").attr("disabled", true);
            }
        },
        error: function(e) {
            alertPopup("error", "Lỗi load thông tin order, vui lòng thử lại");
        }
    });

    $("#modal-quick-edit").modal("show");
}

function disableInput(ids, isDisable) {
    if (ids.length > 0) {
        for (var i = 0; i < ids.length; i++) {
            if (ids[i]) {
                if (isDisable === true) {
                    $("#" + ids[i]).attr("disabled", true);
                } else {
                    $("#" + ids[i]).attr("disabled", false);
                }
            }
        }
    }
}

function rowStyle(row, index) {
    let color = row.duplicated ? "bg-red bg-trung-don" : "bg-white";
    return {
        classes: color
    };
}

function headerProductEdit(table) {
    var header = table.insertRow(0);
    var header_name = header.insertCell(0);
    var header_quantity = header.insertCell(1);
    var header_price = header.insertCell(2);

    header_name.innerHTML = "Tên Sp";
    header_quantity.innerHTML = "Số lượng sản phẩm";
    header_price.innerHTML = "Giá tiền";
}

function createProductEdit(order_products) {
    var table = document.getElementById("tbl-product");
    //clear all old row
    $("#tbl-product").html("");
    //insert header
    headerProductEdit(table);
    //insert new row

    for (var i = 0; i < order_products.length; i++) {
        var item = order_products[i];
        var product = item.product;
        if (product) {
            var row = table.insertRow(1);
            var name = row.insertCell(0);
            var quantity = row.insertCell(1);
            var price = row.insertCell(2);
            name.innerHTML = product.name != null ? product.name : "-";
            quantity.innerHTML = `<input type="number" name="quantity_${product.id}" value="${item.quantity}"  class="form-control product-quantity-${product.id}" onchange="updateQuickProductPrice(${product.id})" >`;
            price.innerHTML = `<input type="text" name="price_${product.id}" data-total="${item.price * item.quantity}" value="${formatNumber(item.price)}" class="form-control data-total price product-price-${product.id}" onchange="updateQuickProductPrice(${product.id})" >`;
        }
    }
}

function showOrderHistoryModal() {
    let orderId = $("#quick-edit-id").val();
    let orderCode = $("#quick-edit-order-code").text();
    $("#order-history-order-code").html(orderCode);
    $("#order_history_list").empty();

    $.ajax({
        type: "GET",
        url: getOrderHistory,
        dataType: "JSON",
        data: {
            order_id: orderId
        },
        success: function(response) {
            let htmlData = "";
            if (response.data.length > 0) {
                let dateArr = [];
                $.each(response.data, function(key, value) {
                    if (
                        dateArr.indexOf(getDateFormat(value.created_at)) == -1
                    ) {
                        dateArr.push(getDateFormat(value.created_at));
                    }
                });

                htmlData += '<div class="timeline">';
                $.each(dateArr, function(key, value) {
                    let htmlItem =
                        '<div><span class="order-history-date">' +
                        value +
                        "</span></div>";

                    $.each(response.data, function(k, v) {
                        if (value != getDateFormat(v.created_at)) {
                            return;
                        }

                        htmlItem +=
                            '<div><i class="fas fa-file-alt bg-blue"></i><div class="timeline-item"><span class="time mr-3"><i class="fas fa-clock"></i> ' +
                            v.created_at +
                            '</span><h3 class="timeline-header">' +
                            v.user_created.account_id +
                            '</h3><div class="timeline-body"><div class="row"><div class="col">' +
                            v.message +
                            "</div></div></div></div></div>";
                    });

                    htmlData += htmlItem;
                });
                htmlData += "</div>";
            } else {
                htmlData =
                    '<div class="empty-data-order-history">Không có lịch sử</div>';
            }

            $("#order_history_list").append(htmlData);
        },
        error: function(e) {}
    });

    $("#modal-quick-edit").modal("hide");
    $("#modal-orders-history").modal("show");
}

function getDateFormat(date) {
    let dateData = new Date(date);
    let year = dateData.getFullYear();
    let month = (1 + dateData.getMonth()).toString().padStart(2, "0");
    let day = dateData
        .getDate()
        .toString()
        .padStart(2, "0");

    return day + "/" + month + "/" + year;
}

function flashUpdateOrder() {
    var product = $("#frm-product-body").serializeArray();

    $.ajax({
        type: "POST",
        url: flashEditOrderUrl,
        dataType: "JSON",
        data: {
            id: $("#quick-edit-id").val(),
            note1: $("#quick-edit-note1").val(),
            note2: $("#quick-edit-note2").val(),
            address: $("#quick-edit-address").val(),
            product: product,
            discount_price: $("#quick-edit-discount-price").val(),
            quick_edit_price: $("#quick-edit-price").text(),
            quick_edit_total_price: $("#quick-edit-total-price").text()
        },
        success: function(response) {
            $("#modal-share-orders").modal("hide");
            alertPopup("success", response.message);
            reloadList();
        },
        error: function(e) {
            alertPopup("error", e.responseJSON.message);
        }
    });
}

function priceToNumber(price) {
    var number = 0;
    if (price) {
        var numberArr = price.split(",");
        if (numberArr.length > 0) {
            number = numberArr.join("");
            number = parseInt(number);
        }
    }
    return number;
}

function updateQuickProductPrice(product_id) {
    // Số liệu mới của sp
    var quantity = $(".product-quantity-" + product_id).val() ?
        parseInt($(".product-quantity-" + product_id).val()) :
        0;
    var price_ipt = $(".product-price-" + product_id).val();
    price_ipt = priceToNumber(price_ipt);

    var product_price = quantity * price_ipt;
    $(".product-price-" + product_id).attr("data-total", product_price);
    updateQuickOrderPrice();
}
// Tính tổng giá của các sp
function updateQuickOrderPrice() {
    var total = 0;
    var discount = $("#quick-edit-discount-price").val();
    discount = priceToNumber(discount);
    var old_discount = $("#quick-edit-discount-price").attr("data-old");
    $(".data-total").each(function() {
        var product_price = parseInt($(this).attr("data-total"));
        total += product_price;
    });

    //nếu nhập giá giảm lớn hơn tổng tiền
    if (discount > total) {
        $("#quick-edit-discount-price").val(old_discount);
        alert("Tiền giảm phải nhỏ hơn tổng tiền");
        updateQuickOrderPrice();
        return false;
    }
    var total_price = total - discount;

    $("#quick-edit-price").html(numberFormat(total));
    $("#quick-edit-total-price").html(numberFormat(total_price));
}

function getTotallRevenue() {
    $.ajax({
        type: "GET",
        url: getTotallRevenueUrl,
        data: queryParam,
        success: function(response) {
            $(".total-order").html(formatNumber(response.countOrder));
            $(".total-amount").html(formatNumber(response.countAmount));
        },
        error: function(e) {
            alertPopup("error", e.responseJSON.message);
        }
    });
}

function actionFormat(value, row, index, field) {
    var btnEdit = `<button onClick="showQuickEditModal(${row.id})" class="btn btn-warning btn-xs text-white"><i class="fa fa-pen"></i></button>`;
    var btnDelete = `br><button class="btn btn-danger" onclick="checkDelete(${row.id})"><i class="fa fa-trash-alt mr-2"></i> Xóa</button>`;
    return btnEdit;
}

function reloadList() {
    $("#table-list").bootstrapTable("refresh");
    getTotallRevenue();
}

function queryParamOrderList(parameters) {
    let params = parameters || {};
    let dateKeys = filterDateKeys.filter(function(key) {
        return jQuery("#" + key + "_date_checkbox").prop("checked");
    });

    let dateQuery = {};
    for (key of dateKeys) {
        let _key = key + "_date";
        let _value = {
            from: jQuery("#" + key + "_from_date").val(),
            to: jQuery("#" + key + "_to_date").val()
        };
        dateQuery[_key] = _value;
    }
    params = {...params, ...{
        page                  : params.limit ? (parseInt(params.offset / params.limit) + 1) : 1,
        dates                 : dateQuery,
        status                : $("input[name='status[]']:checked").map(function(){return $(this).val();}).get() || [],
        source_id             : $('#source_id').val(),
        shipping_service_id   : $('#shipping_service_id').val(),
        product_code          : $('#product_code').val(),
        assigned_user_id      : $('#assigned_user_id').val(),
        marketing_id          : $('#marketing_id').val(),
        staff_id              : $('#staff_id').val(),
        bundle_id             : $('#bundle_id').val(),
        customer_phone_or_code: $('#customer_phone_or_code').val(),
        customer_name         : $('#customer_name').val(),
        type                  : $('#order_type').val(),
        returned              : $('#returned').val(),
    }};
    queryParam = params;
    return params;
}

function countTableRow(id_table, id_show) {
    var x = document.getElementById(id_table).rows.length;
    x = x - 1;
    $("#" + id_show).html(x);
}

$(document).on("click", "#customSearch", function() {
    $("#table-list").bootstrapTable("refresh");
    countTableRow("table-list", "count-data");
});
$(document).on("click", "#disable-customSearch", function() {
    $("#table-disable-list").bootstrapTable("refresh");
    countTa;
    bleRow("table-disable-list", "disable-count-data");
});
$(document).on(
    "dblclick",
    ".order-list-table tbody tr:not(.no-records-found)",
    function() {
        let orderId = $(this)
            .find(".order-item-checkbox")
            .val();
        window.open(urlEdit + "/" + orderId + "?close_when_done=1", "_blank");
    }
);

$(document).on(
    "click",
    ".order-list-table tbody tr:not(.no-records-found)",
    function(e) {
        let checkBox = $(this).find(".order-item-checkbox");
        if (e.target.type != "checkbox") {
            if (checkBox.is(":checked")) {
                checkBox.prop("checked", false);
                $(this).removeClass("order-active");
            } else {
                checkBox.prop("checked", true);
                $(this).addClass("order-active");
            }
        }
        clickCheckBox(checkBox);
    }
);
$(document).ready(function() {
    getTotallRevenue();
});
