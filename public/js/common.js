$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    }
});
$(function() {
    $("#table-status .multi-edit-text-input").each(function() {
        var init = $(this).val();
        var el = $(this)
            .parent()
            .find(".color-picker");
        $(el).spectrum({
            type: "component",
            color: init,
            showInput: "true",
            flat: true,
            showInitial: true,
            change: function(color) {
                var colorChoose = color.toHexString();
                $(this).css("background-color", colorChoose);
                $(this)
                    .parent()
                    .parent()
                    .find("input")
                    .val(colorChoose);
            }
        });
    });
});
$("#table-status .multi-edit-text-input").change(function() {
    var init = $(this).val();
    var el = $(this)
        .parent()
        .find(".color-picker");
    el.css("background-color", init);
    $(el).spectrum({
        type: "component",
        color: init,
        showInput: "true",
        flat: true,
        showInitial: true,
        change: function(color) {
            var colorChoose = color.toHexString();
            $(this).css("background-color", colorChoose);
            $(this)
                .parent()
                .parent()
                .find("input")
                .val(colorChoose);
        }
    });
});
//check all checkbox customer group

$("#CrmCustomerGroup_all_checkbox,#checkall").click(function() {
    $("input:checkbox")
        .not(this)
        .prop("checked", this.checked);
});

$("body").on("change", "input:checkbox", function() {
    if ($(this).prop("checked") == false) {
        $("#CrmCustomerGroup_all_checkbox").prop("checked", false);
        $("#checkall").prop("checked", false);
    }
});

$("#checkall").click(function() {
    $("input:checkbox.checkRow")
        .not(this)
        .prop("checked", this.checked);
});

$(".group-item")
    .mouseover(function() {
        $(this).css("background-color", "#EFEFEF");
    })
    .mouseout(function() {
        $(this).css("background-color", "");
    });

function alertPopup(type, message) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: toast => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        }
    });
    Toast.fire({
        icon: type,
        type: type,
        title: message
    });
}

var Common = {
    datePicker: function(element) {
        $(element).datepicker({
            dateFormat: "dd/mm/yy"
        });
    },
    datePickerFrom: function(element) {
        $(element).datepicker({
            dateFormat: "dd/mm/yy",
            defaultDate: -3
        });
    },
    formData: function(element) {
        var formData = new FormData();
        var baseInfo = $(element).serializeArray();
        $.each(baseInfo, function(index, el) {
            formData.append(el.name, el.value);
        });
        return formData;
    },
    showAlert: function(messages, title = "Cảnh báo!") {
        var messageHtml = messages;
        if (Array.isArray(messages)) {
            messageHtml = "";
            $.each(messages, function(index, value) {
                messageHtml +=
                    '<i class="fa fa-exclamation-triangle"></i> ' +
                    value +
                    "<br>";
            });
        }
        var html =
            '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button ><h5><i class="icon fas fa-ban"></i> ' +
            title +
            "</h5> " +
            messageHtml +
            "</div >";
        $("#alert-message-content").empty();
        $("#alert-message-content").show();
        $("#alert-message-content").html(html);
    },
    hideAlert: function() {
        $("#alert-message-content").hide();
    },
    checkEmptyArray: function(emptyArray) {
        return (
            typeof emptyArray != "undefined" &&
            emptyArray != null &&
            emptyArray.length != null &&
            emptyArray.length > 0
        );
    },
    formatPriceByElement: function(element) {
        $(element).change(function() {
            var number = $(this).val();
            if (number) {
                number = new Intl.NumberFormat().format(number);
            }
            $(this).val(number);
        });
    },
    formatPrice: function(els) {
        $(document).on("keyup", els, function() {
            $(this).val(formatNumber($(this).val()));
        });
    }
};

function setInputFilter(textbox, inputFilter) {
    var elements = $(textbox);
    for (var i = 0; i < elements.length; i++) {
        var el = elements[i];
        [
            "input",
            "keydown",
            "keyup",
            "mousedown",
            "mouseup",
            "select",
            "contextmenu",
            "drop"
        ].forEach(function(event) {
            el.addEventListener(event, function() {
                if (inputFilter(this.value)) {
                    this.oldValue = this.value;
                    this.oldSelectionStart = this.selectionStart;
                    this.oldSelectionEnd = this.selectionEnd;
                } else if (this.hasOwnProperty("oldValue")) {
                    this.value = this.oldValue;
                    this.setSelectionRange(
                        this.oldSelectionStart,
                        this.oldSelectionEnd
                    );
                } else {
                    this.value = "";
                }
            });
        });
    }
}

function fnExcelReport(elm) {
    var tab_text="<table ><tr>";
    var textRange; var j=0;
    tab = document.getElementById(elm); // id of table

    for(j = 0 ; j < tab.rows.length ; j++)
    {
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
        //tab_text=tab_text+"</tr>";
    }

    tab_text=tab_text+"</table>";
    tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
    {
        txtArea1.document.open("txt/html","replace");
        txtArea1.document.write(tab_text);
        txtArea1.document.close();
        txtArea1.focus();
        sa=txtArea1.document.execCommand("SaveAs",true,"Export.xls");
    }
    else                 //other browser not tested on IE 11
        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

    return (sa);
}
/*
Array.prototype.clean = function (deleteValue = '') {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == deleteValue) {
            this.splice(i, 1);
            i--;
        }
    }
    return this;
};
Array.prototype.unique = function () {
    if (this.actionName) return;
    var a = this.concat();
    for (var i = 0; i < a.length; ++i) {
        for (var j = i + 1; j < a.length; ++j) {
            if (a[i] === a[j])
                a.splice(j--, 1);
        }
    }
    return a;
};

Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};*/
function toArray(obj){
    var a = [];
    Object.keys(obj).forEach(prop => {
        a.push(obj[prop]);
    });
    return a;
}
function formatToDate(val){
    return (new Date(val.split('/')[1]+'/'+val.split('/')[0]+'/'+val.split('/')[2]));
}
function isDateBetween(val,begin,end){
    val = formatToDate(val);
    begin = formatToDate(begin);
    end = formatToDate(end);
    return val>=begin && val<=end;
}
function formatToNumeric(num) {
    if ($.isNumeric(num)) return num * 1;
    if (!num) return 0;
    return num.replace(/\,/gm, '') * 1;
}

function formatNumber(num) {
    if (num == "") {
        return "";
    }
    var numb = String(num).match(/\d/g);
    if (!numb) return '';
    numb = numb.join("");
    var flag = String(num).indexOf('-');
    return (flag===0?'-':'')+numb.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}
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

$(document).ready(function() {
    $('.js-currency').on('change',function(){
        $(this).val(formatNumber(formatToNumeric(this.value)));
    });
    var url = window.location.href;
    var splitUrl = [url];
    if (url.indexOf("?") > -1) {
        splitUrl = url.split("?");
    }
    if (url.indexOf("edit") > -1) {
        splitUrl = url.split("/edit");
    }
    // if (url.indexOf('create') > -1){
    //      splitUrl = url.split('/create')
    // }
    url = splitUrl[0];
    console.log(url);
    var loc = '#nav ul li a[href="' + url + '"]';
    $(loc)
        .parent()
        .parent()
        .parent()
        .addClass("menu-open");
    $(loc).addClass("active");
});

$(function() {
    $("#search_orders_all").select2({
        placeholder: "Số điện thoại",
        ajax: {
            url: searchOrderByPhoneUrl,
            dataType: "json",
            type: "GET",
            data: function(params) {
                var query = {
                    phone: params.term
                };
                return query;
            }
        },
        select: function(e) {
            console.log(e.params);
        }
    });

    $("#search_orders_all").on("select2:select", function(e) {
        var data = e.params.data;
        console.log(e.params);
        window.location = indexOrderUrl + "?code=" + data.phone;
    });
});
