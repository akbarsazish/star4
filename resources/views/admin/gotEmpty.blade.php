@extends('layout')
@section('content')

<style>
    label{
        font-size:14px;
    }
    .theadTr, .tbodyTr{
        width: 100%;
    }
    .theadTr th, .tbodyTr td{
        width: 255px;
    }

</style>

<main>
    <div class="container-xl px-4" style="margin-top:6%;">
            <h3 class="page-title"> لیست مشتریان تخلیه شده</h3>
    <div class="card mb-4" style="margin: 0; padding:0;">
        <div class="card-body">
                <div class="row">
                    <div class="col-7">
                        <span class="row">
                                <div class="form-group col-sm-3">
                                    <input type="text" name="" size="20" placeholder="اسم یاآدرس" class="form-control publicTop" id="searchEmptyName">
                                </div>
                                <div class="form-group col-sm-3">
                                    <input type="text" name="" size="20" placeholder="کدحساب" class="form-control publicTop" id="searchEmptyPCode">
                                </div>
                                <div class="form-group col-sm-3">
                                    <input type="text" name="" size="20" placeholder="از تاریخ" class="form-control publicTop" id="searchEmptyFirstDate">
                                </div>
                                <div class="form-group col-sm-3">
                                    <input type="text" name="" size="20" placeholder="تا تاریخ" class="form-control publicTop" id="searchEmptySecondDate">
                                </div>
                        </span>
                    </div>
                    <div class="col-5">
                        <button class='enableBtn btn btn-primary btn-md text-warning' disabled  id='openDashboard' >داشبورد <i class="fal fa-dashboard fa-lg"> </i> </button>
                        <button class='enableBtn btn btn-primary btn-md text-warning' disabled id="takhsisButton">تخصیص کاربر  <i class="fal fa-tasks fa-lg"> </i> </button>
                        <button class='enableBtn btn btn-primary btn-md text-warning' disabled id="inactiveButton">غیر فعال کردن مشتری<i class="fal fa-ban fa-lg"> </i> </button>
                        <input type="text" id="customerSn"  value="" style="display: none;" />
                        <input type="text" id="adminSn"  value="" style="display: none;"/>
                    </div>
                </div>

           <div class="row">
              <div class="col-sm-12">
                    <div class="well" style="margin-top:2%;">
                        <div class="c-checkout container p-1 pb-4 rounded-3">
                            <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">
                                <table class='crmDataTable table table-bordered table-striped table-sm' style='width:100%'>
                                    <thead>
                                    <tr>
                                        <th style="width:40px">ردیف</th>
                                        <th style="width:90px;">اسم</th>
                                        <th style="width:40px;">کد</th>
                                        <th>آدرس  </th>
                                        <th style="width:90px;">همراه</th>
                                        <th style="width:90px;">تاریخ تخلیه</th>
                                        <th style="width:40px;">انتخاب</th>
                                    </tr>
                                    </thead>
                                    <tbody class="select-highlight" id="returnedCustomerList">

                                        @foreach ($customers as $customer)
                                            <tr onclick="returnedCustomerStuff(this)">
                                                <td style="width:40px;">{{$loop->iteration}}</td>
                                                <td>{{$customer->Name}}</td>
                                                <td style="width:40px;">{{$customer->PCode}}</td>
                                                <td>{{$customer->peopeladdress}}</td>
                                                <td>{{$customer->PhoneStr}}</td>
                                                <td>{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($customer->removedDate))->format('Y/m/d')}}</td>
                                                <td style="width:40px;"> <input class="customerList form-check-input" name="customerId[]" type="radio" value="{{$customer->PSN}}"></td>
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
                    <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close" style="background-color:red;"></button>
                    <h5 class="modal-title">  مشتریان تخلیه شده </h5>
                </div>
                <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6 col-md col-sm-12"></div>
                                <div class="col-lg-6 col-md col-sm-12">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-12">
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
                                        <div class="col-lg-4 col-md-4 col-sm-4 mb-3">
                                            <div class="form-group">
                                                <label class="dashboardLabel form-label">  تلفن همراه 2 </label>
                                                <input class="form-control noChange" type="text" name="" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label"> آدرس </label>
                                            <input type="text" class="form-control form-control-sm noChange" id="customerAddress" value="آدرس">
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <div class="mb-3" style="width:350px;">
                                        <label for="exampleFormControlTextarea1" class="form-label fw-bold">یاداشت  </label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="6" ></textarea>
                                    </div>
                                </div>
                            </div>

                    </div>
                    <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                        <div class="col-sm-8" style="margin: 0; padding:0;">
                            <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                                <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری کرده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#referedCard"> کالاهای سبد خرید</a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#referedReturnFactor"> فاکتور های برگشت داده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments"> کامنت ها </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments3"> نظر سنجی ها</a></li>
                            </ul>
                        </div>
                        <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="dashbordTables factor crmDataTable table table-bordered table-striped table-sm">
                                            <thead>
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام راننده</th>
                                                <th>مبلغ </th>
                                            </tr>
                                            </thead>
                                            <tbody  id="factorTable">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="dashbordTables buyiedKala dashbordTables buyiedKalacrmDataTable table table-bordered table-striped table-sm">
                                            <thead>
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام کالا</th>
                                                <th>تعداد </th>
                                                <th>فی</th>
                                            </tr>
                                            </thead>
                                            <tbody id="goodDetail">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="referedCard" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="dashbordTables basketKala crmDataTable table table-bordered table-striped table-sm">
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

                            <div class="c-checkout tab-pane" id="referedReturnFactor" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="dashbordTables returnedFactor crmDataTable table table-bordered table-striped table-sm">
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

                            <div class="c-checkout tab-pane" id="comments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="dashbordTables comments crmDataTable table table-bordered table-striped table-sm">
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
                            <div class="c-checkout tab-pane" id="assesments3" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="dashbordTables nazarSanji table table-bordered table-striped table-sm">
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
                    <div class="card px-3"> <h3> تخصیص ({{$customer->Name}}) به کاربر دیگر</h3></div>

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
                                    <td>{{$admin->name." ".$admin->lastName}}</td>
                                    <td>{{$admin->adminType}}</td>
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
            <button type="button" onclick="takhsisCustomer()" class="btn btn-primary">ذخیره <i class="fa fa-save"></i></button>
            </div>
        </div>
        </div>
    </div>
            <!-- modal of inactive customer -->
            <div class="modal" id="inactiveCustomer"  tabindex="-1" aria-labelledby="exampleModalLabel" >
                <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                       <h5 class="modal-title" id="exampleModalLabel"> غیر فعالسازی </h5>
                    </div>
                    <form action="{{url('/inactiveCustomer')}}" id="inactiveCustomerForm" method="get">
                    <div class="modal-body">
                        <label for="">دلیل غیر فعالسازی</label>
                    <textarea class="form-control" name="comment" id="" cols="30" rows="10"></textarea>
                    <input type="text" name="customerId" id="inactiveId">
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">بستن <i class="fa fa-xmark fa-lg"></i></button>
                    <button type="submit" class="btn btn-success" >ذخیره <i class="fa fa-save fa-lg"></i></button>
                    </div>
                </form>
                </div>
                </div>
            </div>
</main>

<link rel="stylesheet" href="http://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script>
    $("#searchEmptyFirstDate").persianDatepicker({
    cellWidth: 30,
    cellHeight: 12,
    fontSize: 12,
    formatDate: "YYYY/0M/0D"
});
    $("#searchEmptySecondDate").persianDatepicker({
    cellWidth: 30,
    cellHeight: 12,
    fontSize: 12,
    formatDate: "YYYY/0M/0D",
    onSelect:()=>{
        let secondDate=$("#searchEmptySecondDate").val();
        let firstDate=$("#searchEmptyFirstDate").val();
         $.ajax({
            method: 'get',
            url: baseUrl + "/searchEmptyByDate",
            data: {
                _token: "{{ csrf_token() }}",
                secondDate: secondDate,
                firstDate:firstDate
            },
            async: true,
            success: function(msg) {
                moment.locale('en');
                $("#returnedCustomerList").empty();
                msg.forEach((element,index)=>{
                    $("#returnedCustomerList").append(`
                    <tr onclick="returnedCustomerStuff(this)">
                        <td>`+(index+1)+`</td>
                        <td>`+element.Name+`</td>
                        <td>`+element.PCode+`</td>
                        <td>`+element.peopeladdress+`</td>
                        <td>`+element.PhoneStr+`</td>
                        <td>`+moment(element.removedDate, 'YYYY-M-D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D')+`</td>
                        <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="`+element.PSN+` `+element.adminId+`"></td>
                    </tr> `);
                });
            },
            error: function(data) {alert("bad");}
        });
    }
});
    </script>
@endsection
