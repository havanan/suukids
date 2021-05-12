    Common.datePicker(".datepicker");
    function fnExcelReport()
    {
        var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
        var textRange; var j=0;
        tab = document.getElementById('ReportTable'); // id of table

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
            txtArea.document.open("txt/html","replace");
            txtArea.document.write(tab_text);
            txtArea.document.close();
            txtArea.focus();
            sa=txtArea.document.execCommand("SaveAs",true,"");
        }
        else                 //other browser not tested on IE 11
            sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

        return (sa);
    }
    $('#print').click(function() {
      window.print();
    });

    (function() {
      var beforePrint = function() {
        $('.card-header').addClass('d-none')
        $('footer').addClass('d-none')
        $('.hr-form').addClass('d-none')
        $('#search').addClass('d-none')
      };

      var afterPrint = function() {
        $('.card-header').removeClass('d-none')
        $('footer').removeClass('d-none')
        $('.hr-form').removeClass('d-none')
        $('#search').removeClass('d-none')
      };

      if (window.matchMedia) {
          var mediaQueryList = window.matchMedia('print');
          mediaQueryList.addListener(function(mql) {
              if (mql.matches) {
                  beforePrint();
              } else {
                  afterPrint();
              }
          });
      }

      window.onbeforeprint = beforePrint;
      window.onafterprint = afterPrint;

    }());
    function printDiv(iframe,target)
    {

        try{
            var oIframe = document.getElementById(iframe);
            var oContent = document.getElementById(target).innerHTML;
            var oDoc = (oIframe.contentWindow || oIframe.contentDocument);
            if (oDoc.document) oDoc = oDoc.document;
            oDoc.write('<head><title>title</title>');
            oDoc.write('</head><body onload="this.focus(); this.print();">');
            oDoc.write(oContent + '</body>');
            oDoc.close();
        } catch(e){
            self.print();
        }
    }
    function pieChart(id,result,title,series_name) {
        Highcharts.chart(id, {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: title
            },
            subtitle: {
                text: ''
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}% / {point.y} đơn</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true
                    },
                }
            },
            series: [{
                name: series_name,
                colorByPoint: true,
                data: result
            }]
        });
    }
    function search() {
        $('#frm').submit();
    }
