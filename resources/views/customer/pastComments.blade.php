@extends('layout')
@section('content')
<main>
    <div class="container-xl px-4 mt-n10" style="margin-top:4%;">
        <h3 class="page-title">لیست مشتریان باقی مانده</h3>
           <div class="card mb-4" style="margin: 0; padding:0;">
              <div class="card-body">
                 <div class="row">
                    <div class="col-sm-7">
                       <div class="row">
                        <div class="form-group col-sm-3 px-0">
                                <input type="text" name="" size="20" class="form-control publicTop" id="firstDate" placeholder="از تاریخ ">
                            </div>
                            <div class="form-group col-sm-1" style="text-align:center">
                              <label class="dashboardLabel form-label" >الی</label>
                            </div>
                            <div class="form-group col-sm-3 px-0">
                                <input type="text" name="" size="20" class="form-control publicTop" id="secondDate" placeholder="تا تاریخ " />
                            </div>
                          </div>
                        </div>
                    <div class="col-sm-5" style="display:flex; justify-content:flex-end">
                       <div class="alert" style="padding: 0; padding-right:1%; margin:0;">
                            <input type="text" id="customerSn" style="display:none"  value="" />
                            <input type="text" id="factorSn"   style="display:none"  value="" />
                            <button class='enableBtn btn btn-primary btn-md text-warning' type="button" disabled id='openDashboard'>داشبورد<i class="fal fa-dashboard fa-lg"></i></button>
                            <Button class="btn buttonHover btn-primary text-warning"  id="openAssesmentModal" disabled onclick="openAssesmentStuff()"  type="button"  > افزودن نظر <i class="fa fa-address-card"> </i> </Button>
                        </div>
                    </div>
                 </div>
               <div class="row">
                  <div class="col-sm-12">
                    <div class="well" style="margin-top:2%;">
                        <span class="row" style="margin: 0;">
                            <div class="col-sm-12 " style="padding:0;  margin-top: 0;">
                                <table class='homeTables crmDataTable table table-bordered table-striped text-center' style="width:100%">
                                <thead style="position: sticky;top: 0;">
                                    <tr>
                                        <th >ردیف</th>
                                        <th>اسم</th>
                                        <th>تلفن</th>
                                        <th>همراه</th>
                                        <th>مبلغ</th>
                                        <th >شماره فاکتور</th>
                                        <th >انتخاب</th>
                                    </tr>
                                    </thead>
                                    <tbody class="select-highlight" id="customerListBody1">
                                        @foreach ($customers as $customer)

                                            <tr onclick="assesmentStuff(this)">
                                                <td >{{$loop->iteration}}</td>
                                                <td>{{trim($customer->Name)}}</td>
                                                <td>{{trim($customer->sabit)}}</td>
                                                <td>{{trim($customer->hamrah)}}</td>
                                                <td>{{number_format($customer->TotalPriceHDS/10)}} تومان</td>
                                                <td >{{$customer->FactNo}}</td>
                                                <td > <input class="customerList form-check-input" name="factorId" type="radio" value="{{$customer->PSN.'_'.$customer->SerialNoHDS}}"></td>
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


{{-- dashbor modal --}}

<div class="modal fade" id="customerDashboard" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable  modal-xl">
        <div class="modal-content"  style="background-color:#d2e9ff;">
            <div class="modal-header" style="border-bottom:1px solid rgb(7, 42, 214);">
                <button type="button" class="btn-close btn-danger" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"  style="background-color:#d2e9ff;">

                    <div class="row">
                        <div class="col-lg-6">
                        <span class="fw-bold fs-4" id="dashboardTitle"></span>
                        </div>
                        <div class="col-lg-6">
                            <button class="btn btn-sm btn-primary buttonHover float-end" onclick="openAssesmentStuff()"  type="button" > افزودن نظر <i class="fa fa-address-card fa-lg"> </i> </Button>
                        </div>
                    </div>
                    
                    <div class=" tab-pane active" id="custInfo" style="border-radius:10px 10px 2px 2px; padding:0; background-color:#d2e9ff">
                    <fieldset class="row c-checkout rounded-3 m-0" style='padding-right:0; padding-top:0.5%; background-color:#d2e9ff'>
                        <div class="row" style="background-color:#d2e9ff;">
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <div class="row mt-2">
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
                                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
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
                                    <div class="col-lg-4 col-md-4 col-sm-4 mb-3">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label">  تلفن همراه 2 </label>
                                            <input class="form-control noChange" type="text" name="" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 mt-2">
                                <div class="mb-3">
                                    <label for="exampleFormControlTextarea1" class="form-label fw-bold">یاداشت  </label>
                                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="6" style="background-color:#d2e9ff" ></textarea>
                                </div>
                            </div>
                        </div>

                </div>
                <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                    <div class="col-sm-8" style="margin: 0; padding:0;">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                            <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری کرده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#shoppingList"> کالاهای سبد خرید</a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#returnedFactors"> فاکتور های برگشت داده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments">  کامنت ها </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments"> نظرسنجی </a></li>
                        </ul>
                    </div>
                    <div class="c-checkout tab-content talbeDashboardTop">
                            <div class="row c-checkout rounded-3 tab-pane active tableDashboardMiddle" id="custAddress" >
                                <div class="col-sm-12">
                                    <table class="homeTables dashbordTables factor table table-bordered table-striped table-sm">
                                        <thead  style="position: sticky;top: 0;">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ </th>
                                                <th> نام راننده </th>
                                                <th>مبلغ </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="factorTable">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="moRagiInfo">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="homeTables dashbordTables buyiedKala table table-bordered table-striped table-sm">
                                        <thead  style="position: sticky;top: 0;">
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

                        <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="shoppingList">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="homeTables dashbordTables basketKala table table-bordered table-striped table-sm">
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
                                          
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="c-checkout tab-pane talbeDashboardTop" id="returnedFactors">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="homeTables dashbordTables returnedFactor table table-bordered table-striped table-sm">
                                        <thead  style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ </th>
                                            <th>نام راننده</th>
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
                                        <thead  style="position: sticky;top: 0;">
                                        <tr class="theadTr">
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> کامنت</th>
                                            <th> کامنت بعدی</th>
                                            <th> تاریخ بعدی </th>
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
        <!-- Modal for reading factor Detail -->
        <div class="modal fade" id="viewFactorDetail" tabindex="-1" data-backdrop="static" aria-hidden="true">
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
            <div class="modal fade" id="assesmentDashboard" data-backdrop="static" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog modal-dialog-scrollable  modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close btn-danger" data-dismiss="modal" aria-label="Close" id="cancelAssesment"></button>
                            <h5 class="modal-title" id="exampleModalLabel"> افزودن نظر </h5>
                        </div>
                    <div class="modal-body">
                        <form action="{{url('/addAssessment')}}" id="addAssesment" method="get" style="background-color:transparent; box-shadow:none;">
                            @csrf
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
                                <input type="text" name="customerId" id="customerIdForAssesment" style="display:none">
                                <input type="text" name="factorId" id="factorIdForAssesment" style="display:none">
                            </div>
                            <div class="col-lg-4">
                                <select class="form-select" name="behavior">
                                    <option hidden>برخورد راننده</option>
                                    <option value="1">عالی</option>
                                    <option value="2">خوب</option>
                                    <option value="3">متوسط</option>
                                    <option value="4">بد</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <input class="form-control" name="alarmDate" autocomplete="off" id="commentDate1" placeholder="آلارم خرید بعدی">
                            </div>
                       </div>
                       <div class="row">
                                <div class="col-lg-6">
                                    <label for="tahvilBar" >کلاهای عودتی  </label>
                                    <textarea class="form-control" style="position:relative" name="firstComment" rows="3"  ></textarea>
                                </div>
                                <div class="col-lg-6">
                                    <label for="tahvilBar"> کامنت</label>
                                    <textarea class="form-control" name="comment" rows="3" ></textarea>
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
@endsection
