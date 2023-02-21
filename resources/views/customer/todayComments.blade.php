@extends('layout')
@section('content')
<main>
    <div class="container" style="margin-top:4%;">
     <h3 class="page-title">لیست نظر سنجی </h3> 
    <div class="card mb-4" style="margin: 0; padding:0;">
        <div class="card-body">
        <div class="row">
        <div class="col-sm-12">
                    <div class="well">
                        <div class="row">
                            <div class="alert col-sm-8">
                                <input type="text" id="customerSn" style="display:none"  value="" />
                                <input type="text" id="factorSn" style="display:none"  value="" />
                            </div>
                            <div class="col-sm-4 mb-2" style="display:flex; justify-content:flex-end;">
                               <button class='enableBtn btn btn-primary btn-sm buttonHover text-warning mx-1' type="button" disabled id='openDashboard'>داشبورد<i class="fal fa-dashboard fa-lg"></i></button>
                               <button class="enableBtn btn btn-primary btn-sm buttonHover text-warning mx-1" onclick="openAssesmentStuff()" id="openAssessmentModal1"  disabled  type="button"  > افزودن نظر <i class="fa fa-address-card"> </i> </button>
                            </div>
                        </div> 
                            <div class="col-sm-12 " style="padding:0;  margin-top: 0;">
                                <table class='homeTables crmDataTable table table-bordered table-striped table-sm' style="width:100%;">
                                <thead style="position: sticky;top: 0;">
                                    <tr>
                                        <th class="no-sort">ردیف</th>
                                        <th>اسم</th>
                                        <th>تلفن</th>
                                        <th>همراه</th>
                                        <th>مبلغ</th>
                                        <th>شماره فاکتور</th>
                                        <th>انتخاب</th>
                                    </tr>
                                    </thead>
                                    <tbody class="select-highlight" id="customersAssesBody">
                                        @forelse ($customers as $customer)
                                            
                                        <tr onclick="assesmentStuff(this)">
                                                <td class="no-sort">{{$loop->iteration}}</td>
                                                <td>{{trim($customer->Name)}}</td>
                                                <td>{{trim($customer->sabit)}}</td>
                                                <td>{{trim($customer->hamrah)}}</td>
                                                <td>{{number_format($customer->TotalPriceHDS/10)}} تومان</td>
                                                <td>{{trim($customer->FactNo)}}</td>
                                                <td> <input class="customerList form-check-input" name="factorId" type="radio" value="{{$customer->PSN.'_'.$customer->SerialNoHDS}}"></td>
                                            </tr>
                                            @empty
                                            <h3> داده ای وجود ندارد.</h3>
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
{{-- dashbor modal --}}
<div class="modal fade preventScrolling" id="customerDashboard"  data-backdrop="static"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable  modal-dialog-scrollable modal-xl">
        <div class="modal-content"  style="background-color:#d2e9ff;">
            <div class="modal-header" style="border-bottom:1px solid rgb(7, 42, 214);">
                <button type="button" class="btn-close btn-danger" data-dismiss="modal" aria-label="Close" style="background-color: red;"></button>
                <h5 class="modal-title">نظر سنجی  </h5>
            </div>
            <div class="modal-body"  style="background-color:#d2e9ff;">
                <div class="row">
                    <div class="col-lg-12">
                        <button class="btn btn-primary btn-sm buttonHover mx-3" id="customerEdit" style=" float:left;">ذخیره <i class="fa fa-save"> </i> </button>
                        <button class="btn btn-primary btn-sm buttonHover float-end" onclick="openAssesmentStuff()"  type="button" > افزودن نظر <i class="fa fa-address-card fa-lg"> </i> </Button>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-8 col-md-8">
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
                                        <input class="form-control noChange" type="text" placeholder="تلفن ثابت" name="" >
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control noChange" type="text" placeholder="تلفن همراه 1" id="mobile1" >
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-3">
                                    <div class="form-group">
                                        <input class="form-control noChange" type="text" placeholder="تلفن همراه 2" name="" >
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm noChange" id="customerAddress" value="آدرس">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="col-lg-4 col-md-4">
                        <label for="exampleFormControlTextarea1" class="form-label fw-bold">یاداشت  </label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="6" style="background-color:#fffaef"></textarea>
                    </div>
                </div>
                <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                    <div class="col-sm-8" style="margin: 0; padding:0;">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                            <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری کرده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#basketKalas"> کالاهای سبد خرید</a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#returnedFactors"> فاکتور های برگشت داده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments">  کامنت ها </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments"> نظرسنجی ها</a></li>
                        </ul>
                    </div>
                    <div class="c-checkout tab-content talbeDashboardTop">
                            <div class="row c-checkout rounded-3 tab-pane active tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="homeTables dashbordTables factor table table-bordered table-striped table-sm">
                                        <thead  style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> نام راننده</th>
                                            <th>مبلغ </th>
                                            <th>مشاهده</th>
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
                                    <table class="homeTables dashbordTables buyiedKala table table-bordered table-striped table-sm">
                                        <thead style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> نام کالا</th>
                                            <th> </th>
                                            <th> </th>
                                        </tr>
                                        </thead>
                                        <tbody id="goodDetail">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="basketKalas">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="homeTables dashbordTables basketKala table table-bordered table-striped table-sm">
                                        <thead style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> نام کالا</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody id="basketOrders">
                                        <tr>
                                            <td> 1 </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="c-checkout tab-pane talbeDashboardTop" id="returnedFactors" >
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="homeTables dashbordTables returnedFactor table table-bordered table-striped table-sm">
                                        <thead  style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> نام راننده</th>
                                            <th>مبلغ </th>
                                            <th></th>
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
                                    <table class="homeTables dashbordTables comments table table-bordered table-striped table-sm">
                                        <thead style="position: sticky;top: 0;">
                                        <tr>
                                            <th>ردیف</th>
                                            <th>تاریخ</th>
                                            <th>کامنت</th>
                                            <th>کامنت بعدی</th>
                                            <th>تاریخ بعدی </th>
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
                                    <table class="homeTables crmDataTable myCustomer tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                        <thead style="position: sticky;top: 0;">
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

        <!-- Modal for reading factor Detail -->
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
                                            <td id="factorDate1"></td>
                                        </tr>
                                        <tr>
                                            <td>مشتری:</td>
                                            <td id="customerNameFactor1"></td>
                                        </tr>
                                        <tr>
                                            <td>آدرس:</td>
                                            <td id="customerAddressFactor1"> </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-6">
                                    <table class="table table-borderless" style="background-color:#dee2e6">
                                        <tbody>
                                            <tr>
                                                <td>تلفن :</td>
                                                <td id="customerPhoneFactor1"></td>
                                            </tr>
                                        <tr>
                                            <td>کاربر :</td>
                                            <td id="Admin1"></td>
                                        </tr>
                                        <tr>
                                            <td>شماره فاکتور :</td>
                                            <td id="factorSnFactor1"></td>
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
                                    <tbody id="productList1">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            {{-- modal for adding comments --}}
            <div class="modal fade" id="assesmentDashboard" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-scrollable  modal-dialog-scrollable modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #d2e9ff; border-bottom: 1px solid blue;">
                            <button type="button" class="btn-close btn-danger" data-dismiss="modal" aria-label="Close" id="cancelAssesment" style="background-color:red;"></button>
                            <h5 class="modal-title" style="float:left;">افزودن نظر </h5>
                        </div>
                    <div class="modal-body">
                        <form action="{{url('/addAssessment')}}" id="addAssesment" method="get" style="background-color:transparent; box-shadow:none;">
                        <div class="row mb-2">
                            <div class="col-lg-10">
                              <label for="tahvilBar"> مشتری: &nbsp; </label>
                                <span id="customerComenter" style="font-size:18px;margin-bottom:11px;"></span>
                            </div>
                            <div class="col-lg-2" style="display:flex; justify-content:flex-end;">
                                <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save"></i></button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                 <div class="col-lg-4">
                                        <select class="form-select" name="shipmentProblem">
                                            <option hidden>مشکل در بار</option>
                                            <option value="1">بلی</option>
                                            <option value="0">خیر</option>
                                        </select>
                                     <input type="text" name="customerId" id="customerIdForAssesment" style="display:none;">
                                    <input type="text" name="factorId" id="factorIdForAssesment" style="display:none;">
                                 </div>
                                <div class="col-lg-4">
                                    <select class="form-select" name="behavior">
                                        <option hidden>برخورد راننده</option>
                                        <option value="1">عالی</option>
                                        <option value="2">خوب</option>
                                        <option value="3">متوسط </option>
                                        <option value="4">بد</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <input class="form-control" name="alarmDate" required autocomplete="off" id="commentDate2" placeholder="آلارم خرید بعدی">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <label for="tahvilBar" >کلاهای عودتی  </label>
                                    <textarea class="form-control" style="position:relative" name="firstComment" rows="3"  ></textarea>
                                </div>
                                <div class="col-lg-6">
                                    <label for="tahvilBar"> کامنت</label>
                                    <textarea class="form-control" required name="comment" rows="3" ></textarea>
                                </div>
                          </div>
                     </div>
                        <div class="row mt-3" style=" border:1px solid #dee2e6; padding:5px; background-color:#dee2e6; margin-right:3px;">
                            <h4 style="padding:10px; text-align:center;">فاکتور فروش </h4>
                                <table class="table table-bordered" style="justify-content:flex-start;">
                                    <tbody>
                                      <tr>
                                        <td>مشتری:</td>
                                        <td id="customerNameFactor" colspan="2"></td>
                                        <td>آدرس:</td>
                                        <td id="customerAddressFactor" colspan="4"> </td>
                                      </tr>
                                        <tr>
                                            <td>تلفن :</td>
                                            <td id="customerPhoneFactor"></td>
                                            <td>کاربر :</td>
                                            <td >{{Session::get("username")}}</td>
                                            <td>شماره فاکتور :</td>
                                            <td id="factorSnFactor"></td>
                                            <td>تاریخ فاکتور:</td>
                                           <td id="factorDate"></td>
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
                </form>
                </div>
                </div>
            </div>
</main>
<!-- <link rel="stylesheet" href="http://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script> -->
@endsection
