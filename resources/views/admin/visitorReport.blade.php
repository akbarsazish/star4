@extends('layout')
@section('content')

<style>
  #chartdiv {
    width: 100%;
    height: 500px;
    text-align: center;
    }

 </style>

        <div class="container" style="margin-top:4%">
            <div class="c-checkout container-fluid" style="background-image: linear-gradient(to right, #ffffff,#3fa7ef,#3fa7ef); margin:0.2% 0; margin-bottom:0; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                <div class="col-sm-6" style="margin: 0; padding:0;">
                    <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                        <li><a class="active" data-toggle="tab" style="color:black;"  href="#custAddress"> گزارش ورود به سیستم (نموداری) </a></li>
                        <li><a data-toggle="tab" style="color:black;"  href="#karbarLogin">  گزارش ورود به سیستم (اشخاص)  </a></li>
                       
                    </ul>
                </div>
                <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                        <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="col-sm-12">
                                <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                <span class="card p-4">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                   <input type="date" class="form-control">
                                                </div>
                                            </div>
                                        </div> <br>
                                        <div class="col-lg-12 col-md-12 col-sm-12 card">
                                             <div id="chartdiv"></div>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                     


                    <div class="row c-checkout rounded-3 tab-pane" id="karbarLogin" style="background-color:#f5f5f5; width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                         <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                <div class="card-body">
                                <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                   <input type="text" class="form-control" id="visitorSearchName" placeholder="جستجو">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                   <input type="text" placeholder="تاریخ" id="LoginDate2" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                    <div class="row">
                                       <table class="crmDataTable table table-bordered table-striped table-sm">
                                            <thead>
                                            <tr class="theadTr">
                                                <th style="width:40px"> ردیف</th>
                                                <th style="width:60px">اولین ورود</th>
                                                <th style="width:60px">آخرین ورود</th>
                                                <th style="width:60px"> نام مشتری</th>
                                                <th style="width:60px">سیستم </th>
                                                <th style="width:60px">مرورگر</th>
                                                <th style="width:60px">تعداد ورود </th>
                                            </tr>
                                            </thead>
                                            <tbody id="listVisitorBody">
                                              @foreach($visitors as $visitor)
                                                <tr>
                                                    <td style="width:40px">{{$loop->iteration}}</td>
                                                    <td style="width:60px">{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($visitor->firstVisit))->format("Y/m/d")}}</td>
                                                    <td style="width:60px">{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($visitor->lastVisit))->format("Y/m/d")}}</td>
                                                    <td style="width:60px">{{$visitor->Name}}</td>
                                                    <td style="width:60px">{{$visitor->platform}}</td>
                                                    <td style="width:60px">{{$visitor->browser}}</td>
                                                    <td style="width:60px">{{$visitor->countLogin}}</td>
                                                </tr>
                                              @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                         </div>
                    </div>
                 </div>
             </div>
          </div>

  
<!-- Chart code -->
<script>
    am5.ready(function() {

    // Create root element
    var root = am5.Root.new("chartdiv");
    root._logo.dispose();

    // Set themes
    root.setThemes([
      am5themes_Animated.new(root)
    ]);

    // Create chart
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
      panX: true,
      panY: false,
      wheelX: "panX",
      wheelY: "zoomX",
      layout: root.verticalLayout
    }));

    // Add scrollbar
    chart.set("scrollbarX", am5.Scrollbar.new(root, {
      orientation: "horizontal"
    }));

    var data = [{
      "country": "12/02/1400",
      "year2004": 30,
      
    }, {
      "country": "12/02/1401",
      "year2004": 25,
     
    }, {
      "country": "12/01/1400",
      "year2004": 20,
      
    }, {
      "country": "1/02/1400",
      "year2004": 15,
   
    }, {
      "country": "7/02/1400",
      "year2004": 10,
     
    }, {
      "country": "12/09/1400",
      "year2004": 5,
     
    }, 
     {
      "country": "12/09/1400",
      "year2004": 0,
     
    }, 
];

    // Create axes
    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
      categoryField: "country",
      renderer: am5xy.AxisRendererX.new(root, {}),
      tooltip: am5.Tooltip.new(root, {
        themeTags: ["axis"],
        animationDuration: 200
      })
    }));

    xAxis.data.setAll(data);

    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
      min: 0,
      renderer: am5xy.AxisRendererY.new(root, {})
    }));

    // Add series

    var series0 = chart.series.push(am5xy.ColumnSeries.new(root, {
      name: "Income",
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: "year2004",
      categoryXField: "country",
      clustered: false,
      tooltip: am5.Tooltip.new(root, {
        labelText: "نفر: {valueY}"
      })
    }));

    series0.columns.template.setAll({
      width: am5.percent(50),
      tooltipY: 0
    });


    series0.data.setAll(data);


    // var series1 = chart.series.push(am5xy.ColumnSeries.new(root, {
    //   name: "Income",
    //   xAxis: xAxis,
    //   yAxis: yAxis,
    //   valueYField: "year2005",
    //   categoryXField: "country",
    //   clustered: false,
    //   tooltip: am5.Tooltip.new(root, {
    //     labelText: "2005: {valueY}"
    //   })
    // }));

    // series1.columns.template.setAll({
    //   width: am5.percent(50),
    //   tooltipY: 0,

    // });

    // series1.data.setAll(data);

    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));


    // Make stuff animate on load
    chart.appear(1000, 100);
    series0.appear();
    series1.appear();

    }); // end am5.ready()

    </script>

 <link rel="stylesheet" href="http://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>

@endsection
