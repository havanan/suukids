<script>
$(function () {
    Common.datePicker(".datepicker");
    jQuery.datetimepicker.setLocale('vi');

    jQuery('#datetimepicker1').datetimepicker({
        i18n:{
            vi:{
                months:[
                    'Tháng 1','Tháng 2','Tháng 3','Tháng 4',
                    'Mai','Juni','Juli','August',
                    'September','Oktober','November','Dezember',
                ],
                dayOfWeek:[
                    "So.", "Mo", "Di", "Mi",
                    "Do", "Fr", "Sa.",
                ]
            }
        },
        format:'d/m/Y H:i'
    });
    $('#province-select').select2({
        placeholder: "Chọn tỉnh thành"
    });
    $('#district-select').select2({
        placeholder: "Chọn quận/huyện",
        ajax: {
            url: '{{route('admin.address.district.api-search')}}',
            data: function (params) {
                var query = {
                    province_id: $('#province-select').val(),
                    name: params.term,
                    page: params.page || 1
                }

                // Query parameters will be ?search=[term]&page=[page]
                return query;
            }
        }
    });

    $('#ward-select').select2({
        placeholder: "Chọn phường xã",
        ajax: {
            url: '{{route('admin.address.ward.api-search')}}',
            data: function (params) {
                var query = {
                    district_id: $('#district-select').val(),
                    name: params.term,
                    page: params.page || 1
                }
                console.log(query);
                // Query parameters will be ?search=[term]&page=[page]
                return query;
            }
        }
    });
    if ($('[name="customer[id]"]').length) {
        //Chế độ thêm đơn không cho biết số điện thoại mà dựa vào customer_id GET param
        autoLoadCustomerInfo()
    }
});

let appointmentCount = 0;
let appointments = []
for (let appointment of appointments) {
    addAppointment(appointment)
}
jQuery('#add-appointment-btn').on('click', function() {
    addAppointment();
});

function addAppointment(appointment) {
    appointmentCount += 1;
    let time = appointment ? `${(new Date(appointment.time)).toLocaleDateString()} ${(new Date(appointment.time)).toLocaleTimeString().substr(0,5)}` : '';
    let id = 'appointment-box-' + appointmentCount;
    jQuery('#appointment-list').append(
          '<div class="row info-box" id="' + id + '">'
        +   '<div class="col-md-10">'
        +        '<input type="text" class="form-control datetimepicker-input datetimepicker1" name=appointment['+appointmentCount+'][time]" id="time' + id + '" placeholder="Chọn thời gian" value="' + time + '" />'
        +        '<textarea class="form-control mt-1" rows="3" name=appointment['+appointmentCount+'][content] placeholder="Nội dung"> ' + (appointment ? appointment.content : '') + ' </textarea>'
        +   '</div>'
        +   '<div class="col-md-2">'
        +       '<button class="btn btn-danger" type="button" onClick="removeElementById(\'' + id +'\')">'
        +           '<i class="fas fa-trash"></i>'
        +       '</button>'
        +   '</div>'
        + '</div>'
    );
    jQuery.datetimepicker.setLocale('vi');

    $('.datetimepicker-input').datetimepicker({
        i18n:{
            vi:{
                months:[
                    'Tháng 1','Tháng 2','Tháng 3','Tháng 4',
                    'Tháng 6','Tháng 7','Tháng 8','Tháng 9',
                    'Tháng 9','Tháng 10','Tháng 11','Tháng 12',
                ],
            }
        },
        format:'d/m/Y H:i'
    });
}

function removeElementById(id) {
    jQuery('#' + id).remove();
}

let productCount = 0;
let orderProducts = @if(!empty($data->order_products)) {!! $data->order_products !!} @else [] @endif;
for (let index in orderProducts) {
    appendProduct(orderProducts[index]);
    onWarehouseChange(index)
}

jQuery('#add-product-btn').on('click', function() {
    appendProduct();
});

function appendProduct(orderProduct) {
    productCount += 1;
    var name = '';
    var quantity = 0;
    var price = 0;
    var size = '';
    var color = '';
    var weight = 0;
    if (orderProduct && orderProduct.product) {
        name = orderProduct.product.name;
        quantity = orderProduct.quantity;
        price = orderProduct.price;
        size = orderProduct.product.size || '';
        color = orderProduct.product.color || '';
        weight = orderProduct.weight;
    }

    var total = price * quantity;

    jQuery('#products').append(
        '<div class="card product" data-index='+productCount+' id="product-'+  productCount +'">'
        +   '<div class="card-header">'
        +       '<div class="row">'
        +           '<div class="col-5">'
        +               '<div class="input-group" style="width:100%">'
        +                   '<button type="button" class="btn btn-default">Sản phẩm</button>'
        +                   '<input type="text" name=order_product['+productCount+'][product_name] class="form-control" readonly id="product-name-'+ productCount +'" value="'+name+'" />'
        +                '</div>'
        +           '</div>'
        +           '<div class="col-7">'
        +               '<div class="input-group">'
        +                   '<select class="form-control select2 product-select" name=order_product['+productCount+'][product_id] id="product-select-'+productCount+'" data-index="' +productCount+ '" style="width:80%">'
        +                   '</select>'
        +                   '<button type="button" class="btn" style="float-right" onClick="removeElementById(\'product-' + productCount + '\')">'
        +                       '<i class="fas fa-trash"></i>'
        +                   '</button>'
        +               '</div>'
        +           '</div>'
        +       '</div>'
        +   '</div>'
        +  '<div class="card-body">'
        +   '<div class="bs-example" data-example-id="hoverable-table" id="oldStatus" style="background:#fff;">'
        +       '<table class="table table table-bordered" id="table-status">'
        +           '<thead>'
        +               '<tr>'
        +                   '<th>Màu sắc</th>'
        +                   '<th>Size</th>'
        +                   '<th>Trọng lượng (g)</th>'
        +                   '<th>Giá bán</th>'
        +                   '<th>Số lượng</th>'
        +                   '<th>Kho</th>'
        +                   '<th>Thành tiền</th>'
        +               '</tr>'
        +           '</thead>'
        +           '<tbody>'
        +               '<tr>'
        +                   '<td><input class="form-control" type="text" readonly="" id="product-color-'+ productCount +'" value="'+color+'"></td>'
        +                   '<td><input class="form-control" type="text" readonly="" id="product-size-' + productCount +'" value="'+size+'"></td>'
        +                   '<td><input class="form-control" type="number" name=order_product['+productCount+'][weight] id="product-weight-'+productCount+'" value="'+weight+'"></td>'
        +                   '<td><input class="form-control text-bold text-right" type="text" name=order_product['+productCount+'][price] onchange="productPriceChange('+productCount+')" id="product-price-'+productCount+'" value="'+numberFormat(price)+'"></td>'
        +                   '<td>'
        +                       '<input class="form-control text-right text-bold" type="text" name=order_product['+productCount+'][quantity] onchange="updateProductTotalPrice('+productCount+')" id="product-quantity-'+productCount+'" value="'+quantity+'">'
        +                   '</td>'
        +                   '<td>'
        +                        '<select class="form-control" onchange="onWarehouseChange('+productCount+')" name=order_product['+productCount+'][warehouse_id] id="product-warehouse-'+productCount+'">@foreach($stockGroups as $group) <option value="{{$group->id}}" '+ (orderProduct && orderProduct.stock_product_id == "{{$group->id}}" ? 'selected' : '') +' >{{$group->name}}</option>  @endforeach</select>'
        +                        '<div class="text-right text-success">Tồn: <span id="on-hand-'+productCount+'">0</span></div>'
        +                   '</td>'
        +                   '<td><input class="form-control text-danger text-bold text-right" type="text" disabled id="product-price-total-'+productCount+'" value="'+ (total > 0 ? numberFormat(total) : '') +'"></td>'
        +               '</tr>'
        +           '</tbody>'
        +       '</table>'
        +   '</div>'
        + '</div>'
    ).after(function () {
        setTimeout(function(){
            onWarehouseChange(productCount);
        }, 0)
    });

    // Product select
    $('.product-select').select2({
        placeholder: "Chọn sản phẩm",
        ajax: {
            url: '{{route('admin.sell.product.api-search')}}',
            data: function (params) {
                var query = {
                    name: params.term,
                    page: params.page || 1
                }

                // Query parameters will be ?search=[term]&page=[page]
                return query;
            }
        }
    });

    //Select Product
    $('.product-select').on('change', function(e) {
        let product = $(this).select2('data')[0];
        let index = $(this).data('index');
        $('#product-name-'+index).val(product.name);
        $('#product-color-'+index).val(product.color);
        $('#product-size-'+index).val(product.size);
        $('#product-price-'+index).val(numberFormat(product.price));
        $('#product-weight-'+index).val(0);
        $('#product-quantity-'+index).val(1);
        updateProductTotalPrice(index);
        onWarehouseChange(index);
    });

    if (orderProduct&&orderProduct.product) {
        let productSelect = $('#product-select-' + productCount);
        productSelect.append('<option value="' + orderProduct.product.id +'" selected="selected">['+ orderProduct.product.code + '] ' + orderProduct.product.name  +'</option>');
    }


}

function productPriceChange(index) {
    let priceInput = $('#product-price-'+index);
    priceInput.val(numberFormat(priceInput.val()));
    updateProductTotalPrice(index);
}

function updateProductTotalPrice(index) {
    let productPrice = unNumberFormat($('#product-price-'+index).val());
    console.log(productPrice);
    let quantity = $('#product-quantity-'+index).val();
    console.log(quantity);

    $('#product-price-total-'+index).val(numberFormat(productPrice * quantity));

    updateTotalPrice();
}

function updateTotalPrice() {
    let productItems = $('.product');
    var totalPrice = 0;
    $.each(productItems, function(idx, item) {
        let index = $(item).data('index');
        totalPrice += parseInt(unNumberFormat(($('#product-price-total-'+index).val())));
    });

    let discountPrice = parseInt(unNumberFormat($('#discount_price').val() || 0));
    let shippingPrice = parseInt(unNumberFormat($('#shipping_price').val() || 0));
    let otherPrice = parseInt(unNumberFormat($('#other_price').val()) || 0);

    $('#products-price').val(numberFormat(totalPrice));
    $('#total_price').val(numberFormat(totalPrice - discountPrice + shippingPrice + otherPrice));
}

function onWarehouseChange(index) {
    let warehouseId = $('#product-warehouse-' + index).val();
    let productId = $('#product-select-'+index).val();
    if (!warehouseId) {
        return;
    }
    if(!productId) {
        // return alert('Vui lòng chọn sản phẩm');
        $('#on-hand-' + index).html(0);
        return;
    }

    countOnHandProduct(warehouseId, productId, function(data) {
        if (data.quantity) {
            $('#on-hand-' + index).html(data.quantity);
        } else {
            $('#on-hand-' + index).html(0);
        }
    });
}

//Lấy ra danh sách tồn kho
function countOnHandProduct(warehouseId, productId, callback) {
    let url = "{{ route('admin.sell.product.api-on-hand-info') }}";
    let data = {
        'warehouse_id': warehouseId,
        'product_id': productId
    }

    $.ajax({
        url:url,
        type: "GET",
        data: data,
        dataType:"JSON",
        success: function (response) {
            callback(response);
        }
    }).fail(function(error){
        callback([]);
    })
}
function autoLoadCustomerInfo(){
    $.ajax({
        url:'/admin/sell/order/search-by-customer-id',method:'GET',data:{id:$('[name="customer[id]"]').val()},
        success:function(data){
            if (data && data.id == $('[name="customer[id]"]').val())
            fillCustomerInfo({
                address:data.address,bundleId:data.bundle_id,customerEmail:data.email,customerName:data.name,customerPhone2:data.phone2,
                districtId:data.district_id,districtName:data.district?data.district.name:'',
                provinceId:data.provinceId,provinceName:data.province?data.province.name:'',
                wardId:data.ward_id,wardName:data.ward?data.ward.name:''
            });
        }
    })
}
//Điền thông tin KH đã chọn từ Modal gợi ý danh sách Đơn hàng có SĐT đã gõ
function fillCustomerInfo({tags,address,bundleId,customerSaleNote,customerReferral,customerJob,customerEmail,customerName,customerPhone2,districtId,districtName,provinceId,provinceName,wardId,wardName}){
    $('#customer_bundle').val(bundleId);
    if (tags){
        tags = tags.toString().split(',');
        $('[name="customer[tags][]"]').prop('checked',false);
        for(var i=0;i< tags.length;i++){
            $('[name="customer[tags][]"][value="'+tags[i]+'"]').prop('checked',true);
        }
    }
    $('#bundle_id').val(bundleId);
    $('#customer_address').val(address);
    $('#customer_email').val(customerEmail);
    $('#customer_phone2').val(customerPhone2);
    $('#customer_name').val(customerName);
    $('#job').val(customerJob);
    $('#referral').val(customerReferral);
    $('#sale_note').val(customerSaleNote);
    $('#customer_province').val(provinceName);
    $('#province-select').val(provinceId).trigger('change');

    if (districtId) {
        var newOption = new Option(districtName, districtId, false, false);
        $('#district-select').append(newOption).trigger('change');
    }

    if (wardId) {
        var newOption = new Option(wardName, wardId, false, false);
        $('#ward-select').append(newOption).trigger('change');
    }
}
function onSaveAddress() {
    let ward = $('#ward-select').select2('data')[0].text;
    let district = $('#district-select').select2('data')[0].text;
    let province = $('#province-select').select2('data')[0].text;
    let address = " , " + ward + ", " + district + ", " + province;
    $('#customer_address').val(address);
    $('#customer_province').val(province);
}

$('.status-checkbox').on('change', function () {
    $('#modal-status').modal('hide');
    $('#stt-name-custom').html($(this).data('name'));
    $('#stt-color-custom').css('background-color', $(this).data('color'));
});

jQuery('#submit_btn').on('click', function () {
    let closeWhenDone = "{{ !empty($closeWhenDone) ? 1 : 0 }}"
    let data = new FormData(jQuery("#create-form")[0]);
    var _this = $(this);
    _this.attr("disabled",true);
    $.ajax({
        url:urlSubmit,
        type: "POST",
        data: data,
        contentType: false,
        processData: false,
        dataType:"JSON",
        success: function (response) {
            toastr.success(response.message);
            if (closeWhenDone == 1) {
                opener = window.opener;
                if (opener) {
                    opener.reloadList();
                }
                setTimeout(function(){ window.close(); }, 1000);
            } else {
                window.location = response.url;
            }
        }
    }).fail(function(error,message,detail){
        // reportError(error.responseJSON?error.responseJSON.message:message);
        toastr.error(error.responseJSON?error.responseJSON.message:message);
        _this.attr("disabled",false);
    })
});
function reportError(msg){
    $.ajax({url:'/admin/sell/order/report-error',method:'POST',data:{msg:`${msg} at ${location.pathname} by {{ auth()->user()->name }}`}})
}
function suggestCustomerByPhone() {
    let phone = $('#customer_primary_phone').val();
    if (!phone) {return}
    $.ajax({
        url:urlSearchOrderByPhone,
        type: "GET",
        data: {
            phone: phone
        },
        dataType:"JSON",
        success: function (response) {
            if (!response || response.length <= 0) {
                return;
            }

            jQuery('#modal-suggest-customers').modal('show');
            var html = '';
            for (let index in response) {
                let order = response[index];
                let user_created = order.user_created_obj;
                let customer = order.customer;
                let status = order.status;
                let bundle = order.bundle;
                let bundleName = bundle ? bundle.name : '';
                let province = order.province;
                let district = order.district;
                let ward = order.ward;
                html += '<tr>'
                        +   '<td>'+(index+1)+'</td>'
                        +   '<td>'+order.code+'</td>'
                        +   '<td>'+(user_created ? user_created.account_id : '')+ '<br>' + order.created_at +'</td>'
                        +   '<td>'+(customer ? customer.name : '')+'</td>'
                        +   '<td><span class="text-info text-bold">'+customer.phone+'</span><br><span class="label" style="font-size:12px; padding:2px; background-color: #d2d6de; color: #444">'+status.name+'</span></td>'
                        +   '<td>'+bundleName+'</td>'
                        +   '<td style="max-width: 250px;">'+ (customer ? (customer.address || '') : '') +'</td>'
                        +   '<td>' + (province ? province._name : '')  +  '</td>'
                        +   '<td>'
                        +   '<button type="button" class="btn btn-success btn-suggest-customer" data-bundle-id="'+(customer?customer.bundle_id:'')+'" data-tags="'+(customer?customer.tags:'')+'" data-customer-name="'+(customer ? customer.name : '')+'" data-customer-email="'+(customer ? customer.email : '')+'" data-customer-job="'+(customer ? customer.job : '')+'" data-customer-referral="'+(customer ? customer.referral : '')+'" data-customer-sale-note="'+(customer ? customer.sale_note : '')+'" data-customer-phone2="'+(customer ? customer.phone2 : '')+'" data-bundle-id="'+ (bundle ? bundle.id : '') +'" data-address="' + (customer ? customer.address : '') + '" data-province-id="'+(province ? province.id : '')+'" data-district-id="' + (district ? district.id : '') + '" data-ward-id="'+(ward ? ward.id : '')+'" data-province-name="'+ (province ? province._name : '') + '" data-district-name="'+ (district ? district._name : '') +'" data-ward-name="'+(ward ? ward._name : '')+'"> Chọn</button>'
                        +   '</td>'
                        + '</tr>';
            }
            $('#suggest-customer-table-body').html(html);

            $('.btn-suggest-customer').on('click', function() {
                fillCustomerInfo($(this).data());
                $('#modal-suggest-customers').modal('hide');
            });
        }
    }).fail(function(error){
        console.log(error);
    });
}
</script>
