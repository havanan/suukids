function makeSaleReport(sale_report) {
    Highcharts.chart('saleReportContainer', {
        chart: {
            type: 'bar',
            height: '300'
        },
        title: {
            text: 'Xếp hạng doanh thu'
        },
        subtitle: {
            text: chart_title
        },
        xAxis: {
            categories: sale_report.categories,
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Doanh thu Đơn vị triệu đồng',
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: ''
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: -40,
            y: 80,
            floating: true,
            borderWidth: 1,
            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
            shadow: true
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Số lượng',
            data: sale_report.amount,
        }, {
            name: 'Doanh thu',
            data: sale_report.data,
        }]
    });
}
function makeProductReport(product_report) {
    Highcharts.chart('productReportContainer', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Tỷ trọng sản phẩm / dịch vụ'
        },
        subtitle: {
            text: chart_title
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
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
            name: 'Brands',
            colorByPoint: true,
            data:product_report
        }]
    });

}
function makeStaffReport(staff_report) {
    Highcharts.chart('staffReportContainer', {
        chart: {
            type: 'line'
        },
        title: {
            text: "Biểu đồ doanh thu tháng"
        },
        subtitle: {
            text: chart_title
        },
        xAxis: {
            categories: staff_report.categories
        },
        yAxis: [
            {
                title: {
                    text: 'Doanh thu đơn vị triệu đồng'
                }
            }
        ],
        plotOptions: {
            area: {
                colors: '#f00',
                fillColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        series: [{
            type: 'area',
            color: {
                radialGradient: {cx: 0.5, cy: 0.5, r: 0.5},
                stops: [
                    [0, '#fdffd1'],
                    [1, '#ff562f']
                ]
            },
            name: 'Doanh thu',
            data: staff_report.data
        }]
    });
}
function makeDaeReport(date_report) {
    Highcharts.chart('doanhThuReportContainer', {
        chart: {
            zoomType: 'x'
        },
        title: {
            text: 'Biểu đồ doanh thu theo ngày'
        },
        subtitle: {
            text: chart_title
        },
        xAxis: {
            categories: date_report.categories
        },
        yAxis: {
            title: {
                text: 'Doanh thu (VNĐ)'
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        series: [{
            type: "area",
            name: "Xác nhận",
            color: "rgb(91, 192, 222)",
            data: date_report.data
        }]
    });
}
