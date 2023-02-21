@extends('layout')
@section('content')
<style>
    label {
        font-size:14px;
        font-weight: bold;
    }
    body {
  touch-action: none;
}
</style>
<main>
    <div class="container" style="margin-top:6%;">
                <h3 class="page-title">  مشتری جدید </h3>
     <div class="card mb-4">
          <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <span class="row" >
                        <div class="form-group col-sm-4">
                            <input type="text" name="" size="20" placeholder="جستجو" class="form-control publicTop" id="allKalaFirst">
                        </div>
                        <div class="form-group col-sm-4">
                            <select class="form-select publicTop" id="searchGroup">
                                <option value="0"> موقعیت </option>
                                <option value="0">موقعیت دار </option>
                                <option value="0"> بدون موقعیت </option>
                            </select>
                        </div>
                    </span>
                </div>
                <div class="col-sm-6" style="display:flex; justify-content:flex-end">
                    @csrf
                    <button class='enableBtn btn btn-primary text-warning mx-1' type="button" disabled id='openDashboard'> داشبورد <i class="fal fa-dashboard"></i></button>
                    @if(Session::get('adminType')==1 or Session::get('adminType')==5)
                    <button class='enableBtn btn btn-primary btn-md text-warning buttonHover'   style="width:170px;"  disabled id="takhsisButton">تخصیص کاربر  <i class="fal fa-tasks fa-lg"> </i> </button>
                    @endif
                    @if(Session::get('adminType')==1 or Session::get('adminType')==3)
                    <button class='enableBtn btn btn-primary text-warning mx-1' type="button" disabled onclick="openEditCustomerModalForm()">ویرایش مشتری<i class="fa fa-plus-square fa-xl"></i></button>            
                    @endif
                    <button class='enableBtn btn btn-primary text-warning mx-1' type="button" data-toggle="modal" data-target="#addingNewCutomer">افزودن مشتری جدید  <i class="fa fa-plus-square fa-xl"></i></button>            
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="well" style="margin-top:2%;">
                        <div class="c-checkout container p-1 pb-4 rounded-3">
                            <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">
                                <table class='crmDataTable table table-bordered table-striped' style="width:100%">
                                    <thead>
                                    <tr>
                                        <th style="width:40px">ردیف</th>
                                        <th style="width:80px;">اسم</th>
                                        <th style="width:80px;">همراه</th>
                                        <th style="width:80px;">تلفن </th>
                                        <th style="width:50px">منطقه </th>
                                        <th style="width:80px">تاریخ ثبت</th>
                                        <th> ادرس</th>
                                        <th> ادمین</th>
                                        <th style="width:40px">انتخاب</th>
                                    </tr>
                                    </thead>
                                    <tbody class="select-highlight" id="customerListBody1">
                                        @foreach($customers as $customer)
                                        <tr>
                                            <td style="width:40px">{{$loop->iteration}}</td>
                                            <td>{{$customer->Name}}</td>
                                            <td>{{$customer->hamrah}}</td>
                                            <td>{{$customer->sabit}}</td>
                                            <td>{{$customer->NameRec}}</td>
                                            <td>{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($customer->TimeStamp))->format("Y/m/d")}}</td>
                                            <td>{{$customer->peopeladdress}}</td>
                                            <td>{{$customer->adminName.' '.$customer->adminLastName}}</td>
                                            <td style="width:40px"> <input class="customerList form-check-input" name="customerId" type="radio" value="{{$customer->PSN.'_'.$customer->GroupCode}}"></td>
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

    <div class="modal fade" id="takhsesKarbar" tabindex="-1" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close" style="background-color:red;"></button>
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
                                        <td>
                                        @switch($admin->adminType)
                                            @case(2)
                                            پشتیبان
                                            @break
                                            @case(3)
                                            بازاریاب
                                            @break
                                        @endswitch
                                        </td>
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
                <button type="button" class="btn btn-danger" id="cancelTakhsis">انصراف <i class="fa fa-xmark"></i></button>
                <button type="button" onclick="takhsisNewCustomer()" class="btn btn-primary">ذخیره <i class="fa fa-save"></i></button>
                </div>
            </div>
            </div>
        </div>
    </div>
    <!-- modal of adding new customer -->
    <div class="modal fade" id="addingNewCutomer" tabindex="-1"  data-backdrop="static" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="margin:0; border:none">
                    <button type="button" class="btn-close btn-danger" style="background-color:red;" data-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLongTitle"> افزودن مشتری </h5>
                </div>
                <div class="modal-body">
                    <form action="{{url('/addCustomer')}}" method="POST"  enctype="multipart/form-data">
                    @csrf    
                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label">نام و نام خانوادگی</label>
                                    <input type="text" required class="form-control" autocomplete="off" name="name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label">کد</label>
                                    <input type="text" required class="form-control" autocomplete="off" name="PCode">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شماره همراه  </label>
                                    <input type="number" required class="form-control" autocomplete="off" name="mobilePhone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شماره ثابت </label>
                                    <input type="number" required class="form-control" autocomplete="off" name="sabitPhone">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> جنسیت</label>
                                    <select class="form-select" name="gender">
                                        <option value="2" >مرد</option>
                                        <option value="1" >زن</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شهر</label>
                                    <select class="form-select" id="searchCity" name="snNahiyeh">
                                        @foreach($cities as $city)
                                        <option value="{{$city->SnMNM}}" >{{$city->NameRec}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> منطقه </label>
                                    <select class="form-select" id="searchMantagheh" name="snMantagheh">
                                        @foreach ($mantagheh as $mantaghe) {
                                        <option value="{{$mantaghe->SnMNM}}">{{$mantaghe->NameRec}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> ادرس کامل  </label>
                                    <input type="text" required class="form-control" autocomplete="off" name="peopeladdress">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> رمز</label>
                                    <input type="text" required class="form-control" autocomplete="off" name="password" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> نوع مشتری </label>
                                    <select class="form-select" name="groupCode">
                                            <option value="1" >رستوران</option>
                                            <option value="2" >کیترینک</option>
                                            <option value="3" >فست فود</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">           
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> عکس </label>
                                    <input type="file" class="form-control" name="picture" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-6 mt-1">
                              <!-- <div class="form-check mt-4">
                                <label class="form-check-label text-center" for="customerLocation">تعیین لوکیشن  </label>
                                <input class="form-check-input p-2 float-end ms-2" type="checkbox" value="" id="customerLocation">
                                            
                                </div> -->
                                <button type="button" class="btn btn-success" onclick="findMeButton()">Find Location</button>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:4%">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"> انصراف <i class="fa-solid fa-xmark"> </i> </button>
                            <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save" aria-hidden="true"> </i> </button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- modal of editting new customer -->
    <div class="modal fade" id="editNewCustomer" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="margin:0; border:none">
                    <h5 class="modal-title" id="exampleModalLongTitle"> ویرایش مشتری</h5>
                </div>
                <div class="modal-body">
                    <form action="{{url('/editCustomer')}}" method="POST"  enctype="multipart/form-data">
                    @csrf   
                    <input type="text" name="customerId" id="customerID" value="3004345"> 
                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label">نام و نام خانوادگی</label>
                                    <input type="text" required class="form-control" autocomplete="off" name="name" id="name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label">کد</label>
                                    <input type="text" required class="form-control" autocomplete="off" name="PCode" id="PCode">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شماره همراه  </label>
                                    <input type="number" required class="form-control" autocomplete="off" name="mobilePhone" id="mobilePhone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شماره ثابت </label>
                                    <input type="number" required class="form-control" autocomplete="off" name="sabitPhone" id="sabitPhone">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> جنسیت</label>
                                    <select class="form-select" name="gender" id="gender">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شهر</label>
                                    <select class="form-select" name="snNahiyeh" id="snNahiyehE">
                                        @foreach($cities as $city)
                                        <option value="{{$city->SnMNM}}" >{{$city->NameRec}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> منطقه </label>
                                    <select class="form-select" name="snMantagheh" id="snMantaghehE">
                                        @foreach ($mantagheh as $mantaghe) {
                                        <option value="{{$mantaghe->SnMNM}}">{{$mantaghe->NameRec}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> ادرس کامل  </label>
                                    <input type="text" required class="form-control" autocomplete="off" name="peopeladdress" id="peopeladdress">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> رمز</label>
                                    <input type="text" required class="form-control" autocomplete="off" name="password" id="password">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> نوع مشتری </label>
                                    <select class="form-select" name="groupCode" id="groupCode">
                                            <option value="1" >جدید</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> دریافت لوکیشن </label>
                                    <input type="file" class="form-control" name="location" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-6">           
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> عکس </label>
                                    <input type="file" class="form-control" name="picture" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:4%">
                            <button type="button" class="btn btn-danger" id="cancelEditCustomer"> انصراف <i class="fa-solid fa-xmark"> </i> </button>
                            <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save" aria-hidden="true"> </i> </button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
  {{-- dashbor modal --}}
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
 </main>


<script>
 findMeButton.on('click', function(){
 
 navigator.geolocation.getCurrentPosition(function(position) {

     // Get the coordinates of the current position.
     var lat = position.coords.latitude;
     var lng = position.coords.longitude;

     // Create a new map and place a marker at the device location.
     var map = new GMaps({
         el: '#map',
         lat: lat,
         lng: lng
     });

     map.addMarker({
         lat: lat,
         lng: lng
     });

 });

});
</script>
@endsection
