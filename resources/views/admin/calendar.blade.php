@extends('layout')
@section('content')
<div class="container-fluid" style="margin-top:4%;">
        <section id="myCalendar" class="crmCalendar">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">تقویم کاری من </h4>
                            <form action="{{url('/changeDate')}}" method="POST">
                                @csrf

                            <select name="month" style="font-size:16px;">
                            
                                @for ($i = 1; $i < 13; $i++)
                                @switch($i)
                                    @case(1)

                                    <option @if($i==$month) selected @endif value="1">فروردین</option>

                                        @break
                                    @case(2)
                                    
                                    <option @if($i==$month) selected @endif  value="2">اردبهشت</option>
                                        @break
                                    @case(3)

                                    <option @if($i==$month) selected @endif  value="3">خرداد</option>
                                        @break
                                    @case(4)

                                    <option @if($i==$month) selected @endif  value="4">تیر</option>
                                        @break
                                    @case(5)

                                    <option @if($i==$month) selected @endif  value="5">مرداد</option>
                                        @break
                                    @case(6)

                                    <option @if($i==$month) selected @endif  value="6">شهریور</option>
                                        @break
                                    @case(7)

                                    <option @if($i==$month) selected @endif  value="7">مهر</option>
                                        @break
                                    @case(8)

                                    <option @if($i==$month) selected @endif  value="8">آبان</option>
                                        @break
                                    @case(9)

                                    <option @if($i==$month) selected @endif  value="9">آذر</option>
                                        @break
                                    @case(10)

                                    <option @if($i==$month) selected @endif  value="10">دی</option>
                                        @break
                                    @case(11)

                                    <option @if($i==$month) selected @endif  value="11">بهمن</option>
                                        @break
                                    @case(12)

                                    <option @if($i==$month) selected @endif  value="12">اسفند</option>
                                        @break
                                    @default
                                @endswitch
                                @endfor

                            </select>
                            <select name="year" style="font-size:16px;">
                                @for ($i = 1397; $i < 1420; $i++)

                                    <option @if($i==$year) selected @endif value="{{$i}}">{{$i}}</option>
                                @endfor

                            </select>
                            <button type="submit" class="btn btn-primary btn-md">تغییر </button>
                        </form>
                        </div>
                        <div class="card-content">
                            <div class="card-body card-dashboard">
                                <table class="table table-bordered border-primary" id="timeTable">
                                    <thead class="monthDay text-warning">
                                        <th style="width:100px">روزهای هفته</th>
                                        @for ($v = 1; $v < 32; $v++)

                                        <th >{{$v}}</th>
                                        @endfor

                                    </thead>
                                    <tbody class="monthDay" style="background-color:#3fa7ef">
                                        @for ($i = 0; $i < 7; $i++)

                                        <tr><td>
                                            @switch($i)
                                                @case(0)

                                                    شنبه
                                                    @break
                                                @case(1)

                                                    یکشنبه
                                                    @break
                                                @case(2)

                                                    دوشنبه
                                                    @break
                                                @case(3)
                                                
                                                    سه شنبه
                                                    @break
                                                @case(4)

                                                    چهار شنبه
                                                    @break
                                                @case(5)

                                                    پنجشنبه
                                                    @break
                                                @case(6)

                                                    جمعه
                                                    @break
                                                @default
                                        @endswitch

                                    </td>
                                    @for($j = 1; $j < 32; $j++)

                                            <td onclick="showTimeTableTasks(this)" style="cursor:pointer" class=""><span>
                                                @foreach ($commenDates as $dt)
                                                @php
                                                    $monthDay=\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($dt->specifiedDate))->getDay();
                                                    $commenYear=\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($dt->specifiedDate))->getYear();
                                                    $commenMonth=\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($dt->specifiedDate))->getMonth();
                                                    $weekDay=\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($dt->specifiedDate))->getDayOfWeek();
                                                @endphp
                                                    @if( $monthDay==$j and $weekDay==$i and $commenYear==$year and $commenMonth==$month)
                                                    {{$dt->count}}

                                                    <input type="radio" style="display: none;" name="" value="{{$dt->specifiedDate}}" id="">
                                                    @endif
                                                @endforeach

                                                </span>
                                            </td>
                                            @endfor

                                        </tr>
                                        @endfor

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="col-sm-12 " id="customerListSection" style="padding:0; padding-left:25px;display:none;  margin-top: 0; ">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 mt-3 mb-3">
                    <input type="text" style="display: none;" id="customerSn" style="" name="customerSn" value="" />
                    <button class="enableBtn btn-primary buttonHover btn-lg crmButtonColor text-warning" disabled id="openDashboard" style="width:142px; float-left" >داشبورد<i class="fal fa-dashboard"> </i> </button>
                    <button class='enableBtn btn-primary buttonHover btn-lg crmButtonColor text-warning' disabled id='returnCustomer'> ارجاع به مدیر<i class="fal fa-history"></i></button>
                </div>
            </div>    
        <table class='homeTables crmDataTable css-serial display table table-bordered table-striped table-sm' style='td:hover{ cursor:move;}; text-align:center; margin-top:-10 !important;'>
            <thead style="position: sticky;top: 0;">
                <tr>
                    <th>ردیف</th>
                    <th>کد</th>
                    <th>اسم</th>
                    <th>آدرس </th>
                    <th>تلفن</th>
                    <th>همراه</th>
                    <th>منطقه </th>
                    <th>انتخاب</th>
                </tr>
            </thead>
            <tbody class="select-highlight" id="customerListBody">
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="customerDashboard"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable  modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close btn-danger" data-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title" id="exampleModalLabel"> داشبورد </h5>
            </div>
            <div class="modal-body">
                   <Button class="btn btn-primary btn-sm buttonHover float-end" id="openCommentTimeTable" type="button" value="" name="" > کامنت <i class="fas fa-comment fa-lg"> </i> </Button>
                    <span class="fw-bold fs-4"  id="dashboardTitle"></span> <br>
                <div class=" tab-pane active" id="custInfo" style="border-radius:10px 10px 2px 2px; padding:0">
                    <fieldset class="row c-checkout rounded-3 m-0" style='padding-right:0; padding-top:0.5%'>
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
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label"> آدرس </label>
                                            <input type="text" class="form-control form-control-sm noChange" id="customerAddress" value="آدرس">
                                        </div>
                                    </div>

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
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                    <label  class="form-label fw-bold">یاداشت  </label>
                                    <textarea class="form-control" 
                                     id="customerProperty"  onblur="saveCustomerCommentProperty(this)" rows="5" ></textarea>
                            </div>
                        </div>

                </div>
                <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                    <div class="col-sm-8" style="margin: 0; padding:0;">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                            <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری کرده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#userLoginInfo"> کالاهای سبد خرید</a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#pictures"> فاکتور های برگشت داده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments"> کامنت ها </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments"> نظرسنجی </a></li>
                        </ul>
                    </div>
                    <div class="c-checkout tab-content talbeDashboardTop">
                            <div class="row c-checkout rounded-3 tab-pane active tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="homeTables table  dashbordTables table-bordered table-striped crmDataTable table-sm">
                                        <thead class="text-warning"  style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> نام راننده</th>
                                            <th>مبلغ </th>
                                            <th>مشاهد جزئیات</th>
                                        </tr>
                                        </thead>
                                        <tbody  id="factorTable">

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="moRagiInfo">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="homeTables table dashbordTables table-bordered crmDataTable table-striped table-sm">
                                        <thead class="text-warning"  style="position: sticky;top: 0;">
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

                        <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="userLoginInfo">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="homeTables crmDataTable table table-bordered table-striped table-sm basketKala dashbordTables dashbordTables">
                                        <thead class="text-warning"  style="position: sticky;top: 0;">
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

                        <div class="c-checkout tab-pane talbeDashboardTop" id="pictures">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="homeTables crmDataTable table table-bordered table-striped table-sm returnedFactor  dashbordTables">
                                        <thead class="text-warning"  style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> نام کالا</th>
                                            <th>تعداد </th>
                                            <th>فی</th>
                                        </tr>
                                        </thead>
                                        <tbody id="returnedFactorsBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="c-checkout tab-pane talbeDashboardTop" id="comments">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="homeTables crmDataTable dashbordTables table table-bordered table-striped table-sm">
                                        <thead class="text-warning"  style="position: sticky;top: 0;">
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
                        <div class="c-checkout tab-pane" id="assesments" style="margin:0; border-radius:10px 10px 2px 2px;">
                            <div class="row c-checkout rounded-3 tab-pane active" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="col-sm-12">
                                    <table class="homeTables crmDataTable myCustomer  dashbordTables tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                        <thead  style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> کامنت</th>
                                            <th> برخورد راننده</th>
                                            <th> مشکل در بارگیری</th>
                                            <th> کالاهای برگشتی</th>
                                        </tr>
                                        </thead>
                                        <tbody id="customerAssesments"  >

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
        <!-- Modal for reading comments-->
    <div class="modal fade" id="viewFactorDetail" data-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <table id="strCusDataTable"  class='homeTables crmDataTable dashbordTables table table-bordered table-striped table-sm' style="background-color:#dee2e6">
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

    <!-- Modal for returning customer-->
    <div class="modal fade" id="returnComment"  data-backdrop="static"  aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <form action="{{url('/returnCustomer')}}" id="returnCustomerForm" method="get">
                    <div class="modal-body">
                        <input type="text" name="returnCustomerId" id="returnCustomerId" style="display:none;">
                        <div class="row">
                            <div class="col-sm-12 fw-bold">
                                <label for="tahvilBar">دلیل ارجاع</label>
                                <textarea class="form-control" style="position:relative" name="returnComment" rows="3" ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" id="cancelReturn" style="background-color:red;">انصراف<i class="fal fa-cancel"> </i></button>
                        <button type="submit" class="btn btn-sm btn-primary">ارجاع<i class="fal fa-history"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal for reading comments-->
<div class="modal fade" id="viewComment" tabindex="1"  data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title" id="exampleModalLabel">کامنت ها</h5>
            </div>
            <div class="modal-body" >
                <h3 id="readCustomerComment1"></h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">بستن</button>
            </div>
        </div>
    </div>
</div>
{{-- modal for adding comments --}}
<div class="modal" id="addComment"  data-backdrop="static" >
    <div class="modal-dialog modal-dialog-scrollable ">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"> افزودن کامنت </h5>
            </div>
            <div class="modal-body">
                <form action="{{url('/addComment')}}" id='addCommentTimeTable' method="get" style="background-color:transparent; box-shadow:none;">
                    @csrf
                <div class="row">
                    <div class="col-lg-12 fw-bold">
                        <label for="tahvilBar">نوع تماس</label>
                        <select class="form-select" name="callType">
                            <option value="1">موبایل  </option>
                            <option value="2"> تلفن ثابت </option>
                            <option value="3"> واتساپ</option>
                            <option value="4">حضوری </option>
                        </select>
                        <input type="text" name="customerIdForComment" id="customerIdForComment" style="display:none;">
                    </div>
                </div>
                <input type="text" value="" id="dayDate" >
                <div class="row">
                    <div class="col-sm-12 fw-bold">
                        <label for="tahvilBar" >کامنت </label>
                        <textarea class="form-control" style="position:relative" required name="firstComment" rows="3" ></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 fw-bold">
                        <label for="tahvilBar" >زمان تماس بعدی </label>
                        <input class="form-control" autocomplete="off" required name="nextDate" id="commentDate2">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 fw-bold">
                        <label for="tahvilBar">کامنت بعدی</label>
                        <textarea class="form-control" name="secondComment" required rows="5" ></textarea>
                        <input type="text" id="lastCommentId" style="display: none" name="lastCommentId">
                        <input type="text"  style="display: none" name="place" value="calendar">
                    </div>
                </div>
            
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelComment">انصراف <i class="fa fa-xmark"></i></button>
                    <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
