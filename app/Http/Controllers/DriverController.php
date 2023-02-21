<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use DB;
use Response;
use Carbon\Carbon;
use \Morilog\Jalali\Jalalian;
use Session;
class DriverController extends Controller
{
    //
    public function index(Request $request)
    {
        $drivers=DB::table("Shop.dbo.sla_Drivers")->get();
        return $drivers;
    }

    public function crmDriver() {
        $adminId=Session::get("dsn");
        $factors=DB::select("select Peopels.PSN,Peopels.Name,FactorHDS.FactNo,Peopels.LatPers,Peopels.LonPers,FactorHDS.SerialNoHDS,FactorHDS.FactDate,Sla_Drivers.NameDriver,PhoneDetail.PhoneStr,Peopels.peopeladdress from Shop.dbo.BargiryBYS Join Shop.dbo.FactorHDS on BargiryBYS.SnFact=FactorHDS.SerialNoHDS Join Shop.dbo.Peopels on FactorHDS.CustomerSn=Peopels.PSN 
        Join Shop.dbo.BargiryHDS on BargiryHDS.SnMasterBar=BargiryBYS.SnMaster Join Shop.dbo.Sla_Drivers on Sla_Drivers.SnDriver=BargiryHDS.SnDriver
        Join Shop.dbo.PhoneDetail on Peopels.PSN=PhoneDetail.SnPeopel 
         where FactDate='1400/07/18' and Sla_Drivers.SnDriver=".$adminId);
         $customerIds=array();
         foreach ($factors as $factor) {
            array_push($customerIds,$factor->PSN);
         }
         
        return view('driver.driverList',['factors'=>$factors,'customerIDs'=>$customerIds]);
    }
    public function getFactorInfo(Request $request)
    {
        $fsn=$request->get("fsn");
        $factorInfo=DB::select("select PubGoods.GoodName,FactorBYS.Amount,PUBGoodUnits.UName,FactorBYS.Fi,FactorBYS.Price from Shop.dbo.FactorHDS 
        join Shop.dbo.Peopels on FactorHDS.CustomerSn=Peopels.PSN 
        Join Shop.dbo.FactorBYS on FactorHDS.SerialNoHDS=FactorBYS.SnFact 
        join Shop.dbo.PubGoods on FactorBYS.SnGood=PubGoods.GoodSn 
        Join Shop.dbo.PhoneDetail on PhoneDetail.SnPeopel=Peopels.PSN
        Join Shop.dbo.PUBGoodUnits on PubGoods.DefaultUnit=PUBGoodUnits.USN
        where PubGoods.GoodGroupSn>49 and FactorHDS.SerialNoHDS=".$fsn);
        return Response::json($factorInfo);
    }
}
