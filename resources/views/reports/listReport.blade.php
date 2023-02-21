@extends('layout')
@section('content')
<main>
    <div class="container"style="margin-top:3.5%;">
        <h3 style="width:40%; border-bottom:2px solid blue;"> عملکرد مشتری </h3>
     <div class="card mb-4">
          <div class="card-body">
            <div class="row">
                <div class="col-sm-10">
                    <div class="row">
                    <div class="form-group col-sm-3 mt-2">
                                        <input type="text" name="" size="20" placeholder="جستجو" class="form-control publicTop" id="searchAllName">
                                    </div>
                                    <div class="form-group col-sm-2 mt-2">
                                        <select class="form-select publicTop" id="searchAllActiveOrNot">
                                            <option value="0" hidden>وضعیت</option>
                                            <option value="0">همه</option>
                                            <option value="1">فعال</option>
                                            <option value="2"> غیر فعال</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-2 mt-2">
                                        <select class="form-select publicTop" id="locationOrNot">
                                            <option value="0" hidden>موقعیت</option>
                                            <option value="1">همه</option>
                                            <option value="2">موقعیت دار </option>
                                            <option value="3">بدون موقعیت</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-2 mt-2">
                                        <select class="form-select publicTop" id="searchAllFactorOrNot">
                                            <option value="0" hidden>فاکتور</option>
                                            <option value="0">همه</option>
                                            <option value="1">دارد</option>
                                            <option value="2">ندارد</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-2 mt-2">
                                        <select class="form-select publicTop" id="searchAllBasketOrNot">
                                            <option value="0">وضعیت سبد</option>
                                            <option value="3">همه</option>
                                            <option value="2"> نگذاشته اند</option>
                                            <option value="1">گذاشته اند</option>
                                        </select>
                                    </div>
                    </div>
                    <div class="row">
                    <div class="form-group col-sm-3 mt-2">
                        <select class="form-select publicTop" id="searchAllLoginOrNot">
                            
                            <option value="0" hidden>ورودبه سیستم </option>
                            <option value="3">همه</option>
                            <option value="1">وارد شده</option>
                            <option value="2">وارد نشده</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-2 mt-2">
                        <select class="form-select publicTop" id="searchByAdmin">
                        <option value="0" hidden> کاربر</option>
                        <option value="0"> همه</option>
                            @foreach($amdins as $admin)

                            <option value="{{$admin->id}}"> {{trim($admin->name)}} {{trim($admin->lastName)}}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="form-group col-sm-2 mt-2">
                        <select class="form-select publicTop" id="searchByCity">
                        <option value="0" hidden> شهر</option>
                        <option value="0"> همه</option>
                            @foreach($cities as $city)

                            <option value="{{$city->SnMNM}}"> {{trim($city->NameRec)}}</option>
                            @endforeach
                            
                        </select>
                    </div>
                    <div class="form-group col-sm-2 mt-2">
                        <select class="form-select publicTop" id="searchByMantagheh">
                        <option value="0" hidden>منطقه</option>
                        <option value="0">همه</option>
                        </select>
                    </div>
                    </div>
                </div>
                <div class="col-sm-2">
                <button class='enableBtn btn btn-primary btn-md text-warning' data-toggle="modal" data-target="#reportCustomerModal" disabled  type="button" id="openCustomerActionModal">داشبورد<i class="fal fa-dashboard fa-lg"></i></button>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="well">
                            <div class="col-sm-12">
                                <table class='homeTables crmDataTable table table-bordered table-striped table-hover mt-4'>
                                    <thead style="position: sticky;top: 0;">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>اسم</th>
                                        <th>همراه</th>
                                        <th>آدرس</th>
                                        <th> فاکتورها  </th>
                                        <th>تاریخ فاکتور</th>
                                        <th> تاریخ ورود </th>
                                        <th>کاربر</th>
                                        <th> انتخاب</th>
                                    </tr>
                                    </thead>
                                    <tbody class="select-highlight" id="allCustomerReportyBody">
                                        @forelse ($customers as $customer)

                                        <tr>
                                            <td >{{$loop->iteration}}</td>
                                            <td >{{trim($customer->Name)}}</td>
                                            <td >{{trim($customer->hamrah)}}</td>
                                            <td class="scrollTd">{{trim($customer->peopeladdress)}}</td>
                                            <td >{{trim($customer->countFactor)}}</td>
                                            <td >{{trim($customer->lastDate)}}</td>
                                            <td >هنوز نیست</td>
                                            <td >{{trim($customer->adminName).' '.trim($customer->lastName)}}</td>
                                            <td > <input class="customerList form-check-input" name="customerId" type="radio" value="{{$customer->PSN}}"></td>
                                        </tr>
                                        @empty
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="reportCustomerModal"  data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable  modal-xl">
        <div class="modal-content"  style="background-color:#d2e9ff;">
            <div class="modal-header" style="border-bottom:1px solid rgb(7, 42, 214);">
                <button type="button" class="btn-close btn-danger" data-dismiss="modal" aria-label="Close" style="background-color:red;"></button>
                <h5 class="modal-title" style="float:left;">نظر سنجی  </h5>
            </div>
            <div class="modal-body"  style="background-color:#d2e9ff;">
                   <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <span class="fw-bold fs-4"  id="dashboardTitle"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <form action="https://starfod.ir/crmLogin" target="_blank"  method="get" style="background-color:transparent; box-shadow:none;">
                                <input type="text" id="customerSn" style="display: none" name="psn" value="" />
                                <input type="text"  style="display:none;" name="otherName" value="{{trim(Session::get('username'))}}" />
                                    <Button class="btn btn-primary buttonHover float-end" type="submit"> ورود جعلی  <i class="fas fa-sign-in fa-lg"> </i> </Button>
                            </form>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-outline">
                                            <label class="dashboardLabel form-label">کد</label>
                                            <input type="text" class="form-control form-control-sm noChange" id="customerCode" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-outline">
                                            <label class="dashboardLabel form-label">نام و نام خانوادگی</label>
                                            <input type="text" class="form-control form-control-sm noChange" id="customerName"  value="علی حسینی" >
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label"> تعداد فاکتور </label>
                                            <input type="text" class="form-control form-control-sm noChange" id="countFactor">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label"> آدرس </label>
                                            <input type="text" class="form-control form-control-sm noChange" id="customerAddress" value="آدرس">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label">  تلفن ثابت </label>
                                            <input class="form-control noChange" type="text" name="tell" >
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label">  تلفن همراه 1 </label>
                                            <input class="form-control noChange" type="text" id="mobile1" >
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 mb-3">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label">  تلفن همراه 2 </label>
                                            <input class="form-control noChange" type="text" name="mobile2" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 mt-2">
                                <div style="width:350px;">
                                <label class="dashboardLabel form-label">یاداشت  </label>
                                    <textarea class="form-control" id="customerProperty" rows="4"></textarea>
                                </div>
                            
                                <div class="form-group">
                                    <label class="dashboardLabel form-label"> کاربر مربوطه </label>
                                    <input class="form-control noChange" id="admin"  type="text" name="" >
                                </div>
                            </div>
                        </div>
                </div>
                <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                    <div class="col-sm-8" style="margin: 0; padding:0;">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                            <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری کرده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#customerCard"> کالاهای سبد خرید</a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#customerRetunFactor"> فاکتور های برگشت داده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments">  نظرها </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#customerAssesmentAdmin">نظرسنجی ها</a></li>
                        </ul>
                    </div>
                    <div class="c-checkout tab-content talbeDashboardTop">
                            <div class="row c-checkout rounded-3 tab-pane active  tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="dashbordTables factor table table-bordered table-striped table-sm">
                                        <thead  style="position: sticky;top: 0;">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام راننده</th>
                                                <th>مبلغ </th>
                                                <th>مشاهده جزئیات</th>
                                            </tr>
                                        </thead>
                                        <tbody id="factorTable">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="moRagiInfo">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle">
                                <div class="col-sm-12">
                                    <table class="dashbordTables buyiedKala table table-bordered table-striped table-sm">
                                        <thead  style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> نام کالا</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody id="goodDetail">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="customerCard">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="dashbordTables basketKala table table-bordered table-striped table-sm">
                                        <thead style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> نام کالا</th>
                                            <th>تعداد </th>
                                            <th>فی</th>
                                        </tr>
                                        </thead>
                                        <tbody id="basketOrders">
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="c-checkout tab-pane talbeDashboardTop" id="customerRetunFactor" >
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="dashbordTables returnedFactor table table-bordered table-striped table-sm">
                                        <thead   style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> نام راننده</th>
                                            <th>مبلغ </th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody id="returnedFactorTable">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="c-checkout tab-pane talbeDashboardTop" id="comments">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="dashbordTables comments table table-bordered table-striped table-sm">
                                        <thead   style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> کامنت</th>
                                            <th> کامنت بعدی</th>
                                            <th> تاریخ بعدی </th>
                                            <th> انتخاب </th>
                                        </tr>
                                        </thead>
                                        <tbody id="customerComments">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="c-checkout tab-pane talbeDashboardTop" id="customerAssesmentAdmin">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="dashbordTables nazarSanji table table-bordered table-striped table-sm">
                                        <thead   style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> کامنت</th>
                                            <th> برخورد راننده</th>
                                            <th> مشکل در بارگیری</th>
                                            <th> کالاهای برگشتی</th>
                                            <th> انتخاب </th>
                                        </tr>
                                        </thead>
                                        <tbody id="karbarActionAssesment">

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
</div>
           <!-- Modal for reading factorDetails-->
    <div class="modal fade" id="viewFactorDetail" tabindex="-1"  data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog  modal-dialog   modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel">جزئیات فاکتور</h5>
                </div>
                <div class="modal-body" id="readCustomerComment">
                    <div class="container">
                        <div class="row" style=" border:1px solid #dee2e6; padding:10px">
                                <h4 style="padding:10px; border-bottom: 1px solid #dee2e6; text-align:center;">فاکتور فروش </h4>
                                <div class="col-sm-6">
                                    <table class="crmDataTable table table-borderless" style="background-color:#dee2e6">
                                        <tbody>
                                        <tr>
                                            <td>تاریخ فاکتور:</td>
                                            <td id="factorDate"></td>
                                        </tr>
                                        <tr>
                                            <td>مشتری:</td>
                                            <td id="customerNameFactor"></td>
                                        </tr>
                                        <tr>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-6">
                                    <table class="table table-borderless" style="background-color:#dee2e6">
                                        <tbody>
                                            <tr>
                                                <td>تلفن :</td>
                                                <td id="customerPhoneFactor"></td>
                                            </tr>
                                        <tr>
                                            <td>کاربر :</td>
                                            <td id="Admin">3</td>
                                        </tr>
                                        <tr>
                                            <td>شماره فاکتور :</td>
                                            <td id="factorSnFactor"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <table id="strCusDataTable"  class='crmDataTable dashbordTables table table-bordered table-striped table-sm' style="background-color:#dee2e6">
                                    <thead>
                                    <tr>
                                        <th scope="col">ردیف</th>
                                        <th scope="col">نام کالا </th>
                                        <th scope="col">تعداد/مقدار</th>
                                        <th scope="col">واحد کالا</th>
                                        <th scope="col">فی (تومان)</th>
                                        <th scope="col">مبلغ (تومان)</th>
                                    </tr>
                                    </thead>
                                    <tbody id="productList">

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

        <!-- Modal for reading comments-->
        <div class="modal fade" id="viewComment" tabindex="1"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel">کامنت ها</h5>
                </div>
                <div class="modal-body">
                    <h3 id="readCustomerComment1"></h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- <link rel="stylesheet" href="http://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script> -->
<script>
        $('#strCusDataTable').DataTable({
            "paging" :true,
            "scrollCollapse" :true,
            "searching" :true,
            "info" :true,
            "columnDefs": [ {
                "searchable": false,
                "orderable": false,
                "targets":[0,8],
            } ],

            "dom":"lrtip",
            "order": [[ 1, 'asc' ]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/fa.json"
            }
        } );


       let oTable = $('#strCusDataTable').DataTable();
       $('#dataTableComplateSearch').keyup(function(){
          oTable.search($(this).val()).draw() ;
    });
</script>
@endsection
