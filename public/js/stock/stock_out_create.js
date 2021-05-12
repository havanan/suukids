function addRow(type = 0) {
  var d = new Date();
  var id = d.getTime();

  var stockGroupSelect = $(
      '<select class="form-control product-stock-group" name="product_stock_group_id[]"></select>'
  );
  
  $.each(stockGroups, function(optionText, optionValue) {
      $(stockGroupSelect).append(new Option(optionValue, optionText));
  });
  var row = $(
      '<tr class="product-item">' +
          '<input type="hidden" name="product_id[]" class="product-id">' +
          '<input type="hidden" name="product_unit_id[]" class="product-unit-id">' +
          '<td><input type="text" name="product_code[]" class="form-control product-code"></td>' +
          '<td><input type="text" class="form-control product-name" name="product_name[]"></td>' +
          '<td><input type="number" class="form-control product-quantity" name="product_quantity[]"  min="0"></td>' +
          
          '<td><input type="text" name="product_unit_name[]" class="form-control product-unit-name" readonly></td>' +
          '<td><input type="number" name="product_price[]" min="0" class="form-control product-price"></td>' +
          '<td><input type="text" name="product_total[]" value="0" class="form-control product-total" readonly></td>' +
          "<td>" +
          $("<div></div>")
              .append(stockGroupSelect)
              .html() +
          "</td>" +
          '<td><button type="button" class="btn btn-danger btn-remove-product-item"><i class="fa fa-trash-alt"></i></button></td>' +
        "</tr>"
    );
    if(type == 1){
        var stockGroupSelectTo = $(
            '<select class="form-control to-product-stock-group" name="product_to_stock_group_id[]"><option value="">Kho xuất</option></select>'
        );
        
        $.each(stockGroups, function(optionText, optionValue) {
            $(stockGroupSelectTo).append(new Option(optionValue, optionText));
        });
        row = $(
            '<tr class="product-item">' +
                '<input type="hidden" name="product_id[]" class="product-id">' +
                '<input type="hidden" name="product_unit_id[]" class="product-unit-id">' +
                '<td><input type="text" name="product_code[]" class="form-control product-code"></td>' +
                '<td><input type="text" class="form-control product-name" name="product_name[]"></td>' +
                '<td><input type="number" class="form-control product-quantity" name="product_quantity[]"  min="0"></td>' +
                
                '<td><input type="text" name="product_unit_name[]" class="form-control product-unit-name" readonly></td>' +
                '<td><input type="number" name="product_price[]" min="0" class="form-control product-price"></td>' +
                '<td><input type="text" name="product_total[]" value="0" class="form-control product-total" readonly></td>' +
                "<td>" +
                $("<div></div>")
                    .append(stockGroupSelect)
                    .html() +
                "</td>" +
                "<td>" +
                $("<div></div>")
                    .append(stockGroupSelectTo)
                    .html() +
                "</td>" +
                '<td><button type="button" class="btn btn-danger btn-remove-product-item"><i class="fa fa-trash-alt"></i></button></td>' +
              "</tr>"
        );
    }

  $("#order-source-body").append(row);
  $(".product-expired-date").datepicker({ dateFormat: "dd/mm/yy" });
}
    
$(document).on("change", ".product-quantity", function() {
  calAmount($(this));
});
$(document).on("change", ".product-price", function() {
  calAmount($(this));
});
$(document).on("click", ".btn-remove-product-item", function() {
  $(this)
      .closest(".product-item")
      .remove();
  calTotalAmount();
});

function calAmount(element) {
  var parent = $(element).closest(".product-item");
  var qty = $(parent)
      .find(".product-quantity")
      .val();
  var price = $(parent)
      .find(".product-price")
      .val();
  $(parent)
      .find(".product-total")
      .val(price * qty);
  calTotalAmount();
}
function calTotalAmount() {
  var totalAmount = 0;
  $("#addProduct > tbody  > tr").each(function() {
      totalAmount += parseFloat(
          $(this)
              .find(".product-total")
              .val()
      );
  });
  $("#total_amount_value").val(totalAmount);
  $("#total_amount").val(formatNumber(totalAmount));
}
function formatNumber(num) {
  return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}

function toogleSupplierSelect() {
  $("#supplier_select_bound").toggle("slow");
}
$("#checkAll").click(function() {
  $("input:checkbox")
      .not(this)
      .prop("checked", this.checked);
});

$(document).ready(function() {
  Common.datePicker("#create_date");
  if ($("#supplier_select").prop("checked")) {
      toogleSupplierSelect();
  }
  calTotalAmount();
});

$(document).on("click", "#btn-save", function() {
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
              Common.showAlert(
                  res.message,
                  "Cảnh báo thêm sản phẩm dòng: " + res.row
              );
              return;
          }
          Swal.fire("", "Đã xuất kho thành công!", "success");
          setTimeout(function() {
              window.location.href = urlList;
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

$(document).on("change", ".product-code", function() {
  var productCode = $(this).val();
  var parent = $(this).closest(".product-item");

  $($(parent).find("input.product-id")).val("");
  $($(parent).find("input.product-name")).val("");
  $($(parent).find("input.product-price")).val("");
  $($(parent).find("input.product-unit-id")).val("");
  $($(parent).find("input.product-unit-name")).val("");
  if(productCode == ""){
      return;
  }
  $.ajax({
      type: "GET",
      url: urlGetProduct + "?code=" + productCode,
      dataType: "JSON",
      contentType: false,
      processData: false,
      success: function(res) {
          if(Common.checkEmptyArray(res.data)){
              $.each(res.data, function(index, product) {
                  $($(parent).find("input.product-id")).val(product.id);
                  $($(parent).find("input.product-name")).val(product.name);
                  $($(parent).find("input.product-price")).val(product.price);
                  $($(parent).find("input.product-unit-id")).val(product.unit_id);
                  $($(parent).find("input.product-unit-name")).val(
                      product.unit_name
                  );
              });
          } else{
              $($(parent).find("input.product-name")).val("Không có sản phẩm phù hợp");
          }
      },
      error: function(e) {}
  });
});


//upload file 
$('#btn-upoad').click(function() {
  Common.hideAlert();
  let type = $(this).attr('data-type');
  var extension = $('#excel_file').val().split('.').pop().toLowerCase();
  if ($.inArray(extension, ['csv', 'xls', 'xlsx']) == -1) {
      alert('Please Select Valid File... ');
  } else {
    var file_data = $('#excel_file').prop('files')[0];
    
    var form_data = new FormData();
    form_data.append('file', file_data);
    form_data.append('type', type);
    $.ajax({
        type: "POST",
        url: urlUpload,
        dataType: "JSON",
        data: form_data,
        contentType: false,
        processData: false,
        success: function(res) {
            if (res.status == "NG") {
              Swal.fire("Cảnh báo thêm sản phẩm dòng: " + res.row, res.message, "error");
              return;
            }
            if (res.status == "NG_COUNT") {
              Swal.fire("Cảnh báo thêm sản ", res.message, "error");
              return;
            }
            $("#order-source-body").append(res.html);
            calTotalAmount();
            Swal.fire("", "Import thành công!", "success");
            setTimeout(function() {
              $('.modal').modal('hide')
            }, 1500);
            $('#excel_file').val('')
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
  }
});
