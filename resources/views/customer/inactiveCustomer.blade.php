@extends('layout')
@section('content')
<main>
    <div class="container-xl px-4" style="margin-top:6%;">
        <h3 class="page-title">لیست مشتریان غیر فعال</h3>
    <div class="card mb-4" style="margin: 0; padding:0;">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-8">
                    <div class="row">
                    <div class="form-group col-sm-3">
                            <input type="text" name="" size="20" placeholder="نام مشتری" class="form-control publicTop" id="searchInActiveByName">
                        </div>
                        <div class="form-group col-sm-3">
                            <input type="text" name="" size="20" placeholder="کد مشتری" class="form-control publicTop" id="searchInActiveByCode">
                        </div>
                        <div class="form-group col-sm-3">
                            <select class="form-select publicTop" id="searchInActiveByLocation">
                                <option value="-1"> موقعیت  </option>
                                <option value="0"> همه </option>
                                <option value="1">موقعیت دار </option>
                                <option value="2"> بدون موقعیت </option>
                            </select>
                        </div>
                        <div class="form-group col-sm-3">
                            <select class="form-select publicTop" id="orderInactiveCustomers">
                                <option value="-1">مرتب سازی</option>
                                <option value="2">اسم</option>
                                <option value="1">کد</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4" style="display:flex; justify-content:flex-end;">
                        <input type="text" id="customerSn" style="display: none" name="customerSn" value="" />
                        <button class='enableBtn btn btn-primary text-warning mx-2' type="button" id='openDashboard'> داشبورد <i class="fal fa-dashboard"></i></button>
                        <button class='enableBtn btn btn-primary text-warning mx-2' id="takhsisButton">تخصیص کاربر  <i class="fal fa-tasks fa-lg"> </i> </button>
                </div>
            </div>
             <div class="row">
               <div class="col-sm-12">
                    <div class="well" style="margin-top:2%;">
                        <div class="c-checkout container p-1 pb-4 rounded-3">
                            <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">
                                <table class='homeTable crmDataTable table table-bordered table-striped table-sm' style='width:100%;'>
                                <thead style="position: sticky;top:0;">
                                    <tr>
                                        <th style="width:40px">ردیف</th>
                                        <th style="width:80px">اسم</th>
                                        <th style="width:80px">همراه</th>
                                        <th style="width:80px">ت-غیرفعال</th>
                                        <th style="width:80px">ک-غیرفعال</th>
                                        <th style="width:40px">منطقه </th>
                                        <th> کامنت  </th>
                                        <th style="width:40px">انتخاب</th>
                                    </tr>
                                    </thead>
                                    <tbody class="select-highlight" id="inactiveCustomerBody">
                                        @foreach ($customers as $customer)

                                        <tr onclick="setInActiveCustomerStuff(this)">
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{trim($customer->CustomerName)}}</td>
                                            <td>{{trim($customer->hamrah)}}</td>
                                            <td>{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($customer->TimeStamp))->format('Y/m/d H:i:s')}}</td>
                                            <td>{{trim($customer->name).' '.trim($customer->lastName)}}</td>
                                            <td>{{trim($customer->NameRec)}}</td>
                                            <td>{{trim($customer->comment)}}</td>
                                            <td><input class="customerList form-check-input" name="customerId" type="radio" value="{{$customer->PSN}}"></td>
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

    <div class="modal fade" id="customerDashboard" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close btn-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel"> داشبورد </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 coll-sm-12">
                            <span class="fw-bold fs-4" id="dashboardTitle"></span>
                        </div>
                    </div>
                    <div class="row">
                       <div class="col-lg-8 col-md-8 col-sm-8">
                           <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-2">
                                    <div class="form-outline" style="padding-bottom:1%">
                                        <label class="dashboardLabel form-label">کد</label>
                                        <input type="text" class="form-control form-control-sm noChange" id="customerCode" value="">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-outline " style="padding-bottom:1%">
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
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label">  تلفن ثابت </label>
                                        <input class="form-control noChange" type="text" name="" >
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label">  تلفن همراه 1 </label>
                                        <input class="form-control noChange" type="text" id="mobile1" >
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label">  تلفن همراه 2 </label>
                                        <input class="form-control noChange" type="text" name="" >
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-12 col-md-12">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> آدرس </label>
                                        <input type="text" class="form-control form-control-sm noChange" id="customerAddress" value="آدرس">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                                    <label for="exampleFormControlTextarea1" class="form-label fw-bold fs-6">یاداشت  </label>
                                    <textarea class="form-control" id="customerProperty"  onblur="saveCustomerCommentProperty(this)" rows="6" ></textarea>
                               
                       </div>
                    </div>

                    <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                        <div class="col-sm-8" style="margin: 0; padding:0;">
                            <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                                <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری کرده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#shoppingBascketGoods"> کالاهای سبد خرید</a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#returnedFactors"> فاکتور های برگشت داده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments"> کامنت ها </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments1"> نظر سنجی</a></li>
                            </ul>
                        </div>
                        <div class="c-checkout tab-content talbeDashboardTop">
                                <div class="row c-checkout rounded-3 tab-pane active tableDashboardMiddle" id="custAddress">
                                    <div class="col-sm-12">
                                        <table class="dashbordTables dashbordTables factor table table-bordered table-striped table-sm">
                                            <thead>
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام راننده</th>
                                                <th>مبلغ </th>
                                                <th>مشاهده جزئیات</th>
                                            </tr>
                                            </thead>
                                            <tbody  id="factorTable">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="moRagiInfo" >
                                <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress" >
                                    <div class="col-sm-12">
                                        <table class="dashbordTables buyiedKala table table-bordered table-striped table-sm">
                                            <thead>
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

                            <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="shoppingBascketGoods">
                                <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                    <div class="col-sm-12">
                                        <table class="dashbordTables basketKala table table-bordered table-striped table-sm">
                                            <thead>
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام کالا</th>
                                                <th>تعداد </th>
                                                <th>فی</th>
                                            </tr>
                                            </thead>
                                            <tbody id="basketOrders">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="c-checkout tab-pane talbeDashboardTop" id="returnedFactors">
                                <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                    <div class="col-sm-12">
                                        <table class="dashboardLabel dashbordTables returnedFactor table table-bordered table-striped table-sm">
                                            <thead>
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام کالا</th>
                                                <th>تعداد </th>
                                                <th>فی</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td> 1 </td>
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

                            <div class="c-checkout tab-pane talbeDashboardTop" id="comments">
                                <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                    <div class="col-sm-12">
                                        <table class="dashboardLabel dashbordTables comments table table-bordered table-striped table-sm">
                                            <thead>
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
                            <div class="c-checkout tab-pane talbeDashboardTop" id="assesments1">
                                <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                    <div class="col-sm-12">
                                        <table class="dashbordTables dashbordTables nazarSanji table table-bordered table-striped table-sm" >
                                            <thead>
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
                                            <tbody id="customerAssesments">

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
            <!-- Modal for reading factor details-->
    <div class="modal fade" id="viewFactorDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog   modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                            <td>آدرس:</td>
                                            <td id="customerAddressFactor"> </td>
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
                                            <td >3</td>
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
    <div class="modal fade" id="takhsesKarbar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" style="background-color:red;"></button>
                <h5 class="modal-title"> تخصیص </h5>
            </div>
            <div class="modal-body" id="readCustomerComment">
                <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">


                    @if(isset($customer))
                    <div class="card px-3"> <h3> تخصیص ({{trim($customer->CustomerName)}}) به کاربر دیگر</h3></div>

                    <table class="crmDataTable table table-bordered table-hover table-sm" id="tableGroupList">
                        <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>نام کاربر</th>
                                <th>نقش کاربری</th>
                                <th>فعال</th>
                            </tr>
                        </thead>
                        <tbody class="c-checkout" id="mainGroupList" style="max-height: 350px;">
                            @foreach ($admins as $admin)
                                
                            <tr onclick="setAdminStuff(this)">
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{trim($admin->name)." ".trim($admin->lastName)}}</td>
                                    <td>{{trim($admin->adminType)}}</td>
                                    <td>
                                        <input class="mainGroupId" type="radio" name="AdminId" value="{{$admin->id}}">
                                    </td>
                                </tr>
                            @endforeach
                        
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">انصراف <i class="fa fa-xmark"></i></button>
            <button type="button" onclick="activateCustomer()" class="btn btn-primary">ذخیره <i class="fa fa-save"></i></button>
            </div>
        </div>


        </div>
    </div>
            {{-- modal for reading comments --}}
            <div class="modal fade" id="inactiveReadingComment" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel">
                <div class="modal-dialog modal-dialog-scrollable ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close btn-danger" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                    <div class="modal-body">
                        <p>کامنت غیر فعالی </p>

                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar">کامنت مدیر</label>
                                <textarea class="form-control" rows="5" ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" onclick="deleteConfirm()">بستن</button>
                            <button type="button" class="btn btn-info btn-sm crmButtonColor">ذخیره <i class="fa fa-save"> </i> </button>
                    </div>
                </div>
                </div>
            </div>
            <div class="modal fade" id="viewComment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    سلام
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">بستن</button>
                    </div>
                </div>
                </div>
            </div>
</main>
<!-- 
<link rel="stylesheet" href="http://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
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

        $('.select-highlight tr').click(function() {
         $(this).children('td').children('input').prop('checked', true);
         $(".enableBtn").prop("disabled",false);
         if($(".enableBtn").is(":disabled")){
             alert("good");
         }else{
            $(".enableBtn").css("color","red !important");
         }
            $('.select-highlight tr').removeClass('selected');

            $(this).toggleClass('selected');
            $('#customerSn').val($(this).children('td').children('input').val().split('_')[0]);
            $('#customerGroup').val($(this).children('td').children('input').val().split('_')[1]);
        });
       let oTable = $('#strCusDataTable').DataTable();
       $('#dataTableComplateSearch').keyup(function(){
          oTable.search($(this).val()).draw() ;
    });

    $('.withQuality').select2({
        dropdownParent: $('#addComment'),
        width: '100%'
    });

    $('.noQuality').select2({
        dropdownParent: $('#addComment'),
        width: '100%'
    });

    $('.returned').select2({
        dropdownParent: $('#addComment'),
        width: '100%'
    });

    $.ajax({
        method: 'get',
        url: baseUrl + "/getProducts",
        data: {
            _token: "{{ csrf_token() }}"
        },
        async: true,
        success: function(arrayed_result) {
            $('#prductQuality').empty();
            $('#prductNoQuality').empty();
            $('#returnedProducts').empty();
            arrayed_result.forEach((element, index) => {

                $('#prductQuality').append(`
                    <option value="`+element.GoodSn+`">`+element.GoodName+`</option>
                `);

                $('#returnedProducts').append(`
                    <option value="`+element.GoodSn+`">`+element.GoodName+`</option>
                `);

                $('#prductNoQuality').append(`
                    <option value="`+element.GoodSn+`">`+element.GoodName+`</option>
                `);
            });
        },
        error: function(data) {
        }

    });

    </script>
@endsection
