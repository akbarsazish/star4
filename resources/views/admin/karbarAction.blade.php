@extends('layout')
@section('content')
<style>
    .fa-dashboard:hover{
        color:rgb(251, 162, 54)
    }
    /* input[type="text"]{
       background-color: #e7e9eb;
       pointer-events: none;
    } */
    #chartdiv {
    width: 100%;
    height: 500px;
    text-align: center;
    }

    #ohclChart {
      width: 100%;
      height: 500px;
      max-width: 100%;
      text-align: right;
    }

</style>
<main>

    <div class="container" style="margin-top:5%;">
        <h3 class="page-title"> عملکرد کاربران </h3>
        <i class="fa fa-spinner fa-spin" id="waitToDashboard" style="font-size:30px;color:red;display:none; "></i>
    <div class="card mb-4" style="margin: 0; padding:0;">
        <div class="card-body">
            <div class="row">
                 <div class="col-lg-8">
                         <div class="row">
                            <div class="form-group col-lg-4 mt-1">
                                <input type="text" name="" size="20" placeholder="نام" class="form-control publicTop" id="searchAdminNameCode"/>
                            </div>
                            <div class="form-group col-lg-4 mt-1">
                                <select class="form-select publicTop" id="searchAdminGroup">
                                    <option value="-1" hidden>گروه بندی</option>
                                    <option value="0">همه</option>
                                    @foreach ($adminTypes as $element)

                                        <option value="{{$element->id}}">{{$element->adminType}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-4 mt-1">
                                <select class="form-select publicTop" id="searchAdminActiveOrNot">
                                    <option value="-1" hidden>فعال</option>
                                    <option value="0">همه</option>
                                    <option value="1">فعال</option>
                                    <option value="2"> غیر فعال</option>
                                </select>
                            </div>
                        </div>
                             <div class="row">
                                   <div class="form-group col-lg-4 mt-1">
                                        <select class="form-select publicTop" id="searchAdminFactorOrNot">
                                            <option value="-1" hidden>فاکتور</option>
                                            <option value="0">همه</option>
                                            <option value="1">دارد</option>
                                            <option value="2">ندارد</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-4 mt-1">
                                        <select class="form-select publicTop" id="searchAdminLoginOrNot">
                                            <option value="-1" hidden>ورود به سیستم مشتری</option>
                                            <option value="0">همه</option>
                                            <option value="1">بله</option>
                                            <option value="2">خیر</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-4 mt-1">
                                        <select class="form-select publicTop" id="searchAdminCustomerLoginOrNot">
                                            <option value="-1" hidden>ورود به سیستم ادمین</option>
                                            <option value="0">همه</option>
                                            <option value="1">بله</option>
                                            <option value="2">خیر</option>
                                        </select>
                                    </div>
                            </div>
                        </div>
                    <div class="col-lg-4" style="display:flex; justify-content:flex-end;">
                            <div>
                            <input type="text" name="" id="adminSn" style="display: none">
                            <button class='enableBtn btn btn-primary mx-1 text-warning' id="openkarabarDashboard"
                             type="button">عملکرد <i class="fas fa-balance-scale fa-lg"></i></button>
                            <button class='enableBtn btn btn-primary mx-1 text-warning' id="chart" type="button" data-toggle="modal" data-target="#karbarChart">نمودار عملکرد <i class="fas fa-bar-chart fa-lg"></i></button>
                    </div>
                    </div>
            </div>
        <div class="row">
        <div class="col-sm-12">
                    <div class="well" style="margin-top:2%;">
                        <div class="c-checkout container p-1 pb-4 rounded-3">
                            <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">
                                <table class="crmDataTable table table-bordered table-hover table-striped" id="tableGroupList" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th style="width:40px">ردیف</th>
                                            <th>نام کاربر</th>
                                            <th>نقش کاربری</th>
                                            <th class="descriptionForMobile" style="width:500px;">توضیحات</th>
                                            <th style="width:40px">انتخاب</th>
                                        </tr>
                                    </thead>
                                    <tbody id="adminList" style="max-height: 350px;">
                                        @foreach ($admins as $admin)

                                            <tr onclick="setAdminStuffForAdmin(this)">
                                                <td style="width:40px">{{$loop->iteration}}</td>
                                                <td>{{$admin->name." ".$admin->lastName}}</td>
                                                <td>{{$admin->adminType}}</td>
                                                <td class="descriptionForMobile"></td>
                                                <td style="width:40px">
                                                    <input class="mainGroupId" type="radio" name="AdminId[]" value="{{$admin->id.'_'.$admin->adminTypeId}}">
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table> 
                                <br>
                                <h3 class="page-title"> عملکرد روزانه  <span id="adminName" style="display: none;"></span> </h3>
                                <br>
                                <div class="row">
                                    <span class='row c-checkout container p-1 p-2  rounded-3' style="margin: 0; border:1px solid rgb(223, 211, 211);">
                                        <div class="col-lg-3 col-md-3 col-sm-6">
                                            <p>ساعت ورود: <span id="loginTimeToday"></span></p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6">
                                            <p>تعداد کامنت های امروز: <span id="countCommentsToday"></span></p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6">
                                            <p>تعداد فاکتور های امروز: <span id="countFactorsToday"></span></p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6">
                                            <p>تعداد مشتریان: <span id="countCustomersToday"></span></p>
                                        </div>
                                    </span>
                                </div> <br>

                                <table class="crmDataTable table table-bordered table-hover table-sm" id="tableGroupList" style='td:hover{ cursor:move;}'>
                                    <thead>
                                        <tr>
                                            <th style="width:40px">ردیف</th>
                                            <th>نام مشتری</th>
                                            <th>ساعت کامنت </th>
                                            <th>تعداد فاکتور</th>
                                        </tr>
                                    </thead>
                                    <tbody class="c-checkout" id="adminCustomers" style="max-height: 350px;">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- modal for karabarn action  --}}
<div class="modal fade" id="karbarAction" data-keyboard="false"  data-backdrop="static"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" >
        <div class="modal-content"  style="background-color:#d4d4d4;">
            <div class="modal-header" style="border-bottom:1px solid rgb(7, 42, 214);">
                <button type="button" class="btn-close btn-danger" data-dismiss="modal" aria-label="Close" style="display: inline; background-color:red;"></button>
                <h5 class="modal-title" style="text-align: center;">عملکرد <span id="adminNameModal"></span></h5>
            </div>
            <div class="modal-body"  style="background-color:#d4d4d4;">
                    <div class="row">
                        <div class="col-lg-6  col-md-6 col-sm-6">
                            <span class="fw-bold fs-4"  id="dashboardTitle"></span>
                        </div>
                        <div class="col-lg-6  col-md-6 col-sm-6">
                        </div>
                    </div>
                    <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-outline">
                                            <label class="dashboardLabel form-label">تاریخ تخصیص مشتری</label>
                                            <input type="text" class="form-control form-control-sm noChange" id="assignCustomerDate" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-outline ">
                                            <label class="dashboardLabel form-label"> تعداد مشتری</label>
                                            <input type="text" class="form-control form-control-sm noChange" id="countCustomer"  value="علی حسینی" >
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label"> تعداد مشتری های خرید کرده </label>
                                            <input type="text" class="form-control form-control-sm noChange" id="countCustomerBought">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label"> کل فاکتور های فروش </label>
                                            <input class="form-control noChange" type="text" id="countFactors" name="" >
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label">جمع کل فروش </label>
                                            <input class="form-control noChange" type="text" id="allMoneyFactor" >
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label">تعداد فاکتور برگشتی</label>
                                            <input class="form-control noChange" type="text" id="countReturnedFactor" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label">مبلغ فاکتورها برگشتی</label>
                                            <input class="form-control noChange" type="text" id="allMoneyReturnedFactor" >
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label"> روزها که وارد CRM نشده </label>
                                            <input type="text" class="form-control form-control-sm noChange" id="notlogedIn" value="آدرس">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="mb-3">
                                <label class="dashboardLabel form-label">یاداشت  </label>
                                    <textarea class="form-control" id="comment" rows="6" style="background-color:#ffcfcf" ></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                    <label class="dashboardLabel form-label">توضیحات   </label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="2" disabled></textarea>
                                    
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="dashboardLabel form-label"> فاکتورهای ماه قبل این کارتابل</label>
                                    <input class="form-control noChange" type="text" id="lastMonthAllFactorMoney" value="346545646546" style="font-weight: bold; color:red;">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="dashboardLabel form-label"> فاکتورهای برگشتی ماه قبل این کارتابل</label>
                                    <input class="form-control noChange" type="text" id="lastMonthAllFactorMoneyReturned" value="346545646546" style="font-weight: bold; color:red;">
                                </div>
                            </div>
                       </div>
                 </div>
                 <div class="row">
                    <div class="col-lg-1 "></div>
                    <div class="col-lg-10 "  style="background-color:beige;" >
                        <h3 style="padding: 5px; ">عملکرد مشتریان تخصیصی در ماه قبل </h3>
                       <table class="table table-bordered table-striped">
                           <thead>
                             <tr>
                               <th scope="col">تعداد مشتری</th>
                               <th scope="col">فاکتورها</th>
                               <th scope="col">مبلغ فاکتورها </th>
                               <th scope="col">برگشتی </th>
                               <th scope="col">جمع کل مبلغ </th>
                             </tr>
                           </thead>
                           <tbody id="lastMonthActions">
                           </tbody>
                         </table>
                    </div>
                    <div class="col-lg-1 "></div>
                </div>
                <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                    <div class="col-sm-8">
                        <ul class="header-list nav nav-tabs float-start" data-tabs="tabs">
                            <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress">تاریخچه </a></li>
                        </ul>
                    </div>
                    <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                            <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                        <thead>
                                        <tr>
                                            <th style="width:40px;"> ردیف</th>
                                            <th style="width:100px;"> تعداد مشتری  </th>
                                            <th style="width:150px;">تعداد خرید کرده  </th>
                                            <th style="width:150px;">  تعداد فاکتور فروش </th>
                                            <th style="width:300px; color:red;">  مبلغ فاکتورها برگشتی  </th>
                                            <th style="width:300px;">  خالص کل فاکتور فروش  </th>
                                            <th style="width:300px;">خالص خرید ماه قبلی مشتریان </th>
                                            <th style="width:300px;">میانگین رشد  </th>
                                            <th style="width:300px;">م بدون کامنت </th>
                                            <th style="width:300px;">ک انجام نشده </th>
                                            <th style="width:300px;">کامنت </th>
                                        </tr>
                                        </thead>
                                        <tbody  id="factorTable">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="readDiscription" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-scrollable modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close btn-danger" data-dismiss="modal" aria-label="Close"></button>
            </div>
        <div class="modal-body" style="background-color: #d2e9ff;">
            <h3 id="discription"></h3>
        </div>
    </div>
</div>

{{-- modal for karabarn action  --}}
<div class="modal fade" id="karbarChart" data-keyboard="false"  data-backdrop="static"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" >
        <div class="modal-content"  style="background-color:#d4d4d4;">
            <div class="modal-header" style="border-bottom:1px solid rgb(7, 42, 214);">
                <button type="button" class="btn-close btn-danger" data-dismiss="modal" aria-label="Close" style="display: inline; background-color:red;"></button>
                <h5 class="modal-title" style="text-align: center;">نمودار عملکرد {{$admin->name." ".$admin->lastName}}</h5>
            </div>

            <div class="modal-body"  style="background-color:#d4d4d4;;">
                <div class="c-checkout container-fluid" style="background-image: linear-gradient(to right, #ffffff,#3fa7ef,#3fa7ef); margin:0.2% 0; margin-bottom:0; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                    <div class="col-sm-6" style="margin: 0; padding:0;">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                            <li><a class="active" data-toggle="tab" style="color:black;"  href="#siteAdmin"> عملکرد ماهای قبل </a></li>
                            <li><a data-toggle="tab" style="color:black;"  href="#moRagiInfo">  عملکرد کاربران   </a></li>
                            <li><a data-toggle="tab" style="color:black;"  href="#kalaInfo"> عملکرد </a></li>
                        </ul>
                    </div>
                        <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="background-color:#f5f5f5; width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                             <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-12 fs-6">
                                                <div class="row mt-1">
                                                    <span style="width:30px; height:30px; background-color:#67b7dc; margin-right:11px;"></span> &nbsp;  عملکرد {{$admin->name." ".$admin->lastName}}
                                                </div> <br>
                                                <div class="row">
                                                    <span style="width:30px; height:30px; background-color:#6794dc;  margin-right:11px;"></span>  &nbsp; عملکرد کاربران دیگر
                                                </div>
                                            </div>
                                            <div class="col-lg-8 col-md-8 col-sm-12 card">
                                                <div id="chartdiv"></div>
                                            </div>
                                        </div>
                                    </div>
                             </div>
                        </div>
                          <div class="row c-checkout rounded-3 tab-pane active" id="siteAdmin" style="background-color:#f5f5f5; width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                             <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                 <div class="col-lg-3 col-md-3 col-sm-3"></div>
                                   <div class="col-lg-9 col-md-9 col-sm-9">
                                     <div id="ohclChart"></div>
                                  </div>
                             </div>
                           </div>
                        </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
    </div>
</div>
</main>
<!-- 
<link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet" />
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.12.1/sorting/persian.js"></script> -->


<!-- Chart code
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
      "country": "کاربر فعلی",
      "year2004": 3.5,
      "year2005": 4.2
    }, {
      "country": "دیگر کاربران",
      "year2004": 1.7,
      "year2005": 3.1
    }, {
      "country": "کابرفعلی",
      "year2004": 2.8,
      "year2005": 2.9
    }, {
      "country": "کاربران دیگر ",
      "year2004": 2.6,
      "year2005": 2.3
    }, {
      "country": "کاربرفعلی ",
      "year2004": 1.4,
      "year2005": 2.1
    }, {
      "country": "کاربران دیگر",
      "year2004": 2.6,
      "year2005": 4.9
    }];

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
        labelText: "2004: {valueY}"
      })
    }));

    series0.columns.template.setAll({
      width: am5.percent(80),
      tooltipY: 0
    });


    series0.data.setAll(data);


    var series1 = chart.series.push(am5xy.ColumnSeries.new(root, {
      name: "Income",
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: "year2005",
      categoryXField: "country",
      clustered: false,
      tooltip: am5.Tooltip.new(root, {
        labelText: "2005: {valueY}"
      })
    }));

    series1.columns.template.setAll({
      width: am5.percent(50),
      tooltipY: 0,

    });

    series1.data.setAll(data);

    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));


    // Make stuff animate on load
    chart.appear(1000, 100);
    series0.appear();
    series1.appear();

    }); // end am5.ready()

    </script>






{{-- script of OHCL Chart --}}

<script>
    am5.ready(function() {
    // Create root element
    var root = am5.Root.new("ohclChart");
    root._logo.dispose();

    // Set themes
    root.setThemes([
      am5themes_Animated.new(root)
    ]);

    function generateChartData() {
      var chartData = [];
      var firstDate = new Date();
      firstDate.setDate(firstDate.getDate() - 1000);
      firstDate.setHours(0, 0, 0, 0);
      var value = 1200;
      for (var i = 0; i < 5000; i++) {
        var newDate = new Date(firstDate);
        newDate.setDate(newDate.getDate() + i);

        value += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
        var open = value + Math.round(Math.random() * 16 - 8);
        var low = Math.min(value, open) - Math.round(Math.random() * 5);
        var high = Math.max(value, open) + Math.round(Math.random() * 5);

        chartData.push({
          date: newDate.getTime(),
          value: value,
          open: open,
          low: low,
          high: high,
        });
      }
      return chartData;
    }

    var data = generateChartData();

    // Create chart
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
      focusable: true,
      panX: true,
      panY: true,
      wheelX: "panX",
      wheelY: "zoomX"
    }));


    // Create axes
    var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
      maxDeviation:0.5,
      groupData: true,
      baseInterval: { timeUnit: "day", count: 1 },
      renderer: am5xy.AxisRendererX.new(root, {pan:"zoom"}),
      tooltip: am5.Tooltip.new(root, {})
    }));

    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
      maxDeviation:1,
      renderer: am5xy.AxisRendererY.new(root, {pan:"zoom"})
    }));


    var color = root.interfaceColors.get("background");

    // Add series
    var series = chart.series.push(am5xy.OHLCSeries.new(root, {
      fill: color,
      calculateAggregates: true,
      stroke: color,
      name: "CRM",
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: "value",
      openValueYField: "open",
      lowValueYField: "low",
      highValueYField: "high",
      valueXField: "date",
      lowValueYGrouped: "low",
      highValueYGrouped: "high",
      openValueYGrouped: "open",
      valueYGrouped: "close",
      legendValueText: "open: {openValueY} low: {lowValueY} high: {highValueY} close: {valueY}",
      legendRangeValueText: "{valueYClose}",
      tooltip: am5.Tooltip.new(root, {
      pointerOrientation: "horizontal",
    labelText: "open: {openValueY}\nlow: {lowValueY}\nhigh: {highValueY}\nclose: {valueY}"
      })
    }));


    // Add cursor
    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
      xAxis: xAxis
    }));
    cursor.lineY.set("visible", false);

    // Stack axes vertically
    chart.leftAxesContainer.set("layout", root.verticalLayout);

    // Add scrollbar
    var scrollbar = am5xy.XYChartScrollbar.new(root, {
      orientation: "horizontal",
      height: 50
    });
    chart.set("scrollbarX", scrollbar);

    var sbxAxis = scrollbar.chart.xAxes.push(am5xy.DateAxis.new(root, {
      groupData: true,
      groupIntervals: [{ timeUnit: "week", count: 1 }],
      baseInterval: { timeUnit: "day", count: 1 },
      renderer: am5xy.AxisRendererX.new(root, {
        opposite: false,
        strokeOpacity: 0
      })
    }));

    var sbyAxis = scrollbar.chart.yAxes.push(am5xy.ValueAxis.new(root, {
      renderer: am5xy.AxisRendererY.new(root, {})
    }));

    var sbseries = scrollbar.chart.series.push(am5xy.LineSeries.new(root, {
      xAxis: sbxAxis,
      yAxis: sbyAxis,
      valueYField: "value",
      valueXField: "date"
    }));

    // Add legend
    var legend = yAxis.axisHeader.children.push(
      am5.Legend.new(root, {})
    );

    legend.data.push(series);

    legend.markers.template.setAll({
      width: 10
    });

    legend.markerRectangles.template.setAll({
      cornerRadiusTR: 0,
      cornerRadiusBR: 0,
      cornerRadiusTL: 0,
      cornerRadiusBL: 0
    });

    series.data.setAll(data);
    sbseries.data.setAll(data);

    // Make stuff animate on load
    series.appear(1000);
    chart.appear(1000, 100);

    }); // end am5.ready()
    </script> -->
@endsection