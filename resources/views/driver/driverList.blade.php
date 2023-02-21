@extends('layout')
@section('content')

<style>
    label{
        font-size: 14px;
        font-weight: bold;
    }
    .map-container-4{
        overflow:hidden;
        padding-bottom:56.25%;
        position:relative;
        height:0;
        }
        .map-container-4 iframe{
        left:0;
        top:0;
        height:100%;
        width:100%;
        position:absolute;
        }

   @media only screen and (max-width: 992px) {
    .driverTable .address,.choice {
        display:none;
    }
   }
</style>

<div class="container" style=" margin: auto; margin-top:55px;">
    <div class="row">
        <div class="col-sm-12 px-4">
            <div class="well" style="margin-top:2%;">
                    <h3 style="font-size:22px; font-weight:bold; border-bottom:2px solid blue; width:40%">لیست  بارگیری   </h3>
                    <div class="c-checkout container p-1 pb-4 rounded-3" style="">
                        <span class="row" style="margin: 0;">
                                <div class="form-group col-sm-2">
                                    <label class="form-label">  تاریخ  </label>
                                    <select class="form-select" id="searchGroup">
                                        <option value="0">همه</option>
                                        <option value="0">فعال</option>
                                        <option value="0"> غیر فعال</option>
                                    </select>
                                </div>
                                <div class="col-lg-4" style="margin-top:30px;">
                                    <button class="btn btn-primary" id="customerMap">مسیر فاکتور ها <i class="fa fa-route fa-lg"></i></button>
                                    <input type="text" style="display: none;" name="" value="@foreach($customerIDs as $id) @if($loop->iteration==1) {{$id}} @else {{','.$id}} @endif @endforeach" id="factorSn">
                                </div>
                        </span>
                    </div>
                    <table class="table table-bordered crmDataTable driverTable" id="tableGroupList">
                        <thead class="bg-primary text-warning">
                            <tr>
                                <th style="width:33px">ردیف</th>
                                <th style="width:100px;">نام مشتری</th>
                                <th class="address" style="width:300px;">ادرس</th>
                                <th style="width: 90px;">تلفن </th>
                                <th style="width:30px">موقعیت  </th>
                                <th style="width: 70px;">جزئیات فاکتور</th>
                                <th class="choice" style="width:25px"> انتخاب</th>
                            </tr>
                        </thead>
                        <tbody class="c-checkout" id="mainGroupList">
                            @foreach ($factors as $factor)

                            <tr onclick="setBargiryStuff(this)">
                                <td>{{$loop->iteration}}</td>
                                <td>{{$factor->Name}}</td>
                                <td class="address">{{$factor->peopeladdress}}</td>
                                <td>{{$factor->PhoneStr}}</td>
                                <td style="text-align: center;"><a style="text-decoration:none;" target="_blank" href="https://maps.google.com/?q={{$factor->LonPers.','.$factor->LatPers}}"><i class="fas fa-map-marker-alt fa-1xl" style="color:#116bc7; "></i></a></td>
                                <td style="text-align: center;" data-bs-toggle="modal" data-bs-target="#factorDeatials"><i class="fa fa-eye fa-1xl"> </i> </td>
                                <td class="choice"> <input class="customerList form-check-input" name="factorId" type="radio" value="{{$factor->SerialNoHDS}}"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="driverLocation" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color:red"></button>
            <h5 class="modal-title" id="exampleModalLabel"> موقعیت راننده </h5>
        </div>
            <div class="modal-body">
                <main class=" m-0 p-0">
                    <div class="container-fluid m-0 p-0">
                        <!--Google map-->
                        <div id="map2" class="z-depth-1-half map-container-4" style="height: 500px">
                        </div>
                    </div>
                    </main>
            </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">بستن </button>
        </div>
    </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="factorDeatials" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color:red"></button>
            <h5 class="modal-title" id="exampleModalLabel"> موقعیت راننده </h5>
        </div>
            <div class="modal-body">
                <div class="row" style=" border:1px solid #dee2e6; padding:10px">
                    <h4 style="padding:20px; border-bottom: 1px solid #dee2e6; text-align:center;">فاکتور فروش </h4>
                    <div class="col-lg-4 col-md-4 col-sm-6">
                        <table class="table table-borderless">
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
                    <div class="col-lg-4 col-md-4"></div>
                    <div class="col-lg-4 col-md-4 col-sm-6">
                        <table class="table table-borderless">
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
                    <table id="strCusDataTable"  class='css-serial display table table-bordered table-striped table-sm' style="background-color:#dee2e6">
                        <thead class="bg-primary">
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
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">بستن </button>
        </div>
    </div>
    </div>
</div>




@endsection
