@extends('layout')
@section('content')
<main>
    <div class="container" style="margin-top:4%">
        <h3 class="page-title">لیست مشتریان</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="form-group col-sm-2">
                                <input type="text" name="" size="20" placeholder="جستجو" class="form-control publicTop" id="searchCustomerName">
                            </div>
                            <div class="form-group col-sm-3">
                                <input type="number" name="" size="20" placeholder="جستجوی کد حساب" class="form-control publicTop" id="searchCustomerCode">
                            </div>
                            <div class="form-group col-sm-3">
                                <select class="form-select publicTop" id="orderByCodeOrName">
                                    <option value="1" hidden>مرتب سازی</option>
                                    <option value="1">اسم</option>
                                    <option value="0">کد</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-2">
                                <select class="form-select publicTop" id="findMantaghehByCity">
                                <option value="شهر" hidden>شهر</option>
                                    @foreach($cities as $city)

                                    <option value="{{$city->SnMNM}}">{{trim($city->NameRec)}}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="form-group col-sm-2">
                                <select class="form-select publicTop" id="searchCustomerByMantagheh">
                                    <option value="مناطق" hidden>مناطق</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4" style="display:flex; justify-content:flex-end;">
                        <button class='enableBtn btn btn-primary text-warning mx-1' type="button" disabled id='openDashboard'> داشبورد <i class="fal fa-dashboard"></i></button>
                        <button class='enableBtn btn btn-primary text-warning mx-1' type="button" disabled id='returnCustomer'> ارجاع به مدیر<i class="fal fa-history"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="well" style="margin-top:1%;">
                                <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">
                                    <table class='homeTables crmDataTable table table-bordered table-striped table-sm'  style='width:100%;'>
                                    <thead style="position: sticky;top: 0;">
                                        <tr>
                                            <th>ردیف</th>
                                            <th>کد</th>
                                            <th>اسم</th>
                                            <th>آدرس </th>
                                            <th>تلفن</th>
                                            <th>همراه</th>
                                            <th>منطقه </th>
                                            <th>درج </th>
                                            <th>انتخاب</th>
                                        </tr>
                                        </thead>
                                        <tbody class="select-highlight" id="customerListBody1">
                                            @foreach ($customers as $customer)

                                                <tr @if($customer->maxTime) style="background-color:lightblue" @endif>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{trim($customer->PCode)}}</td>
                                                    <td>{{trim($customer->Name)}}</td>
                                                    <td  class="scrollTd">{{trim($customer->peopeladdress)}}</td>
                                                    <td>{{trim($customer->sabit)}}</td>
                                                    <td>{{trim($customer->hamrah)}}</td>
                                                    <td>{{trim($customer->NameRec)}}</td>
                                                    <td>2</td>
                                                    <td> <input class="customerList form-check-input" name="customerId" type="radio" value="{{$customer->PSN.'_'.$customer->GroupCode}}"></td>
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
        <div class="modal fade notScroll" id="customerDashboard" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable  modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close btn-danger" style="background-color:red;" data-dismiss="modal" aria-label="Close"></button>
                        <h5 class="modal-title" id="exampleModalLabel"> داشبورد </h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <span class="fw-bold fs-4"  id="dashboardTitle" style="display:none;"></span>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <Button class="btn btn-sm buttonHover crmButtonColor float-end  mx-2" id="openAddCommentModal" type="button" value="" name="" > کامنت <i class="fas fa-comment fa-lg"> </i> </Button>
                                <form action="https://starfod.ir/crmLogin" target="_blank"  method="get">
                                    <input type="text" id="customerSn" style="display: none" name="psn" value="" />
                                    <input type="text"  style="display: none" name="otherName" value="{{trim(Session::get('username'))}}" />
                                    <Button class="btn btn-sm buttonHover crmButtonColor float-end" type="submit"> ورود جعلی  <i class="fas fa-sign-in fa-lg"> </i> </Button>
                                </form>
                            </div>
                        </div><hr>
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
                                            <input class="form-control noChange" id="tell" type="text" name="" >
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
                                            <input class="form-control noChange" type="text" id="mobile2" >
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
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <div class="mb-3" style="width:350px;">
                                        <label for="exampleFormControlTextarea1" class="form-label fw-bold">یاداشت  </label>
                                        <textarea class="form-control" id="customerProperty" onblur="saveCustomerCommentProperty(this)" rows="6"></textarea>
                                    </div>
                                </div>
                        </div>
                        </div>

                        <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                            <div class="col-sm-12" style="margin: 0; padding:0;">
                                <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                                    <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                                    <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری شده </a></li>
                                    <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#userLoginInfo1"> کالاهای سبد خرید</a></li>
                                    <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#customerLoginInfo">ورود به سیستم</a></li>
                                    <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#returnedFactors1"> فاکتور های برگشت داده </a></li>
                                    <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments"> کامنت ها </a></li>
                                    <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments"> نظرسنجی ها</a></li>
                                </ul>
                            </div>
                            <div class="c-checkout tab-content"   style="background-color:#f5f5f5; margin:0;padding:0.3%; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="homeTables factor crmDataTable tableSection4 table table-bordered table-striped table-sm">
                                            <thead  style="position: sticky;top: 0;">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام راننده</th>
                                                <th>مبلغ </th>
                                                <th>مشاهد جزئیات </th>
                                            </tr>
                                            </thead>
                                            <tbody  id="factorTable">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="col-sm-12">
                                            <table class="homeTables crmDataTable buyiedKala tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
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

                                <div class="row c-checkout rounded-3 tab-pane" id="userLoginInfo1" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="col-sm-12">
                                            <table class="homeTables crmDataTable basketKala tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                                <thead  style="position: sticky;top: 0;">
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

                                <div class="row c-checkout rounded-3 tab-pane" id="customerLoginInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="col-sm-12">
                                            <table class="homeTables crmDataTable returnedFactor tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                                <thead  style="position: sticky;top: 0;">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th>نوع پلتفورم</th>
                                                    <th>مرورگر</th>
                                                </tr>
                                                </thead>
                                                <tbody id="customerLoginInfoBody">
                                                <tr>
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

                                <div class="row c-checkout rounded-3 tab-pane" id="returnedFactors1"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="col-sm-12">
                                            <table class="homeTables crmDataTable comments tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                                <thead  style="position: sticky;top: 0;">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> نام راننده</th>
                                                    <th>مبلغ </th>
                                                </tr>
                                                </thead>
                                                <tbody id="returnedFactorsBody">
                                                <tr>
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
                                    <div class="row c-checkout rounded-3 tab-pane active"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="col-sm-12">
                                            <table class="homeTables crmDataTable nazarSanji tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                                <thead  style="position: sticky;top: 0;">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> کامنت</th>
                                                    <th> کامنت بعدی</th>
                                                    <th> تاریخ بعدی </th>
                                                </tr>
                                                </thead>
                                                <tbody id="customerComments"  >

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="c-checkout tab-pane" id="assesments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                    <div class="row c-checkout rounded-3 tab-pane active" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="col-sm-12">
                                            <table class="homeTables crmDataTable myCustomer tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
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
        <!-- Modal for factor detail-->
        <div class="modal fade" id="viewFactorDetail" tabindex="0" data-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                                <td id="Admin"></td>
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
                                        <thead  style="position: sticky;top: 0;">
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
        <div class="modal fade" id="viewComment" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
        <!-- Modal for returning customer-->
        <div class="modal fade" id="returnComment"  data-backdrop="static"  aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> ارجاع</h5>
                    </div>
                    <form action="{{url('/returnCustomer')}}" id="returnCustomerForm" method="get">
                        <div class="modal-body">
                            <input type="text" name="returnCustomerId" style="display:none" id="returnCustomerId">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="form-label" for="tahvilBar">دلیل ارجاع</label>
                                    <textarea class="form-control" required style="position:relative" name="returnComment" rows="3" ></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" id="cancelReturn" style="background-color:red;">انصراف<i class="fal fa-cancel"> </i></button>
                            <button type="submit" class="btn btn-sm btn-primary">ذخیره <i class="fal fa-save"> </i> </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--- modal for adding comments -->
        <div class="modal" id="addComment" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel">
            <div class="modal-dialog modal-dialog-scrollable ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> افزودن کامنت </h5>
                    </div>
                <div class="modal-body">
                    <form action="{{url('/addComment')}}" id="addCommentForm" method="get">
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar">نوع تماس</label>
                                <select class="form-select" name="callType">
                                    <option value="1">موبایل</option>
                                    <option value="2">تلفن ثابت</option>
                                    <option value="3">واتساپ</option>
                                    <option value="4">حضوری</option>
                                </select>
                                <input type="text" style="display:none" name="customerIdForComment" id="customerIdForComment">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar" >کامنت </label>
                                <textarea class="form-control" style="position:relative" required name="firstComment" id="firstComment" rows="3" ></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 fw-bold">
                                <label for="tahvilBar" >زمان تماس بعدی </label>
                                    <input class="form-control" autocomplete="off" required name="nextDate" id="commentDate2">
                                    <input class="form-control" autocomplete="off" style="display:none" value="0" required name="mantagheh" id="mantaghehId">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar">کامنت بعدی</label>
                                <textarea class="form-control" name="secondComment" required id="secondComment" rows="5" ></textarea>
                                <input class="form-control" type="text" style="display: none;" name="place" value="customers"/>
                            </div>
                        </div>
                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-danger" id="cancelComment">انصراف<i class="fa fa-xmark"></i></button>
                                <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
 <!-- <link rel="stylesheet" href="http://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css"> 
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script> -->
<!-- <link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet" />
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.12.1/sorting/persian.js"></script> -->
@endsection
