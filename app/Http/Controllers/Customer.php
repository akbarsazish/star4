<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Response;
use DB;
use Carbon\Carbon;
use \Morilog\Jalali\Jalalian;
use Session;
class Customer extends Controller
{
    public function index(Request $request)
    {
        $adminId=Session::get('asn');
        $todayDate=Carbon::now()->format('Y-m-d');
        $customers=DB::select("SELECT TOP 20 * FROM(
                        SELECT * FROM(SELECT * FROM(
                        SELECT * FROM(
                        SELECT PSN,Name,SnMantagheh,admin_id,returnState,PCode,peopeladdress,GroupCode FROM Shop.dbo.Peopels JOIN (SELECT * FROM CRM.dbo.crm_customer_added)a ON Peopels.PSN=a.customer_id)b
                        where  b.admin_id=".$adminId." AND b.returnState=0)e
                        JOIN(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f on e.SnMantagheh=f.SnMNM)g
                        left JOIN (SELECT  maxTime,customerId FROM(
                        SELECT customerId,Max(TimeStamp) as maxTime FROM(
                        SELECT crm_comment.TimeStamp,customerId FROM CRM.dbo.crm_comment
                        JOIN CRM.dbo.crm_workList 
                        on crm_comment.id=crm_workList.commentId where doneState=0 and crm_workList.specifiedDate>'".$todayDate."'
                        )a group by customerId)b)h on g.PSN=h.customerId)i order by i.maxTime asc");
        foreach ($customers as $customer) {
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            $hamrah="";
            $sabit="";
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.="\n".$phone->PhoneStr;
                }else{
                    $hamrah.="\n".$phone->PhoneStr;
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }
        $cities=DB::table("Shop.dbo.MNM")->where("RecType",1)->where("FatherMNM",79)->get();
        return View('customer.customerList',['customers'=>$customers,'cities'=>$cities]);
    }

    public function getCustomer(Request $request)
    {
        $customers=DB::select("SELECT * FROM Shop.dbo.Peopels where Peopels.PSN 
                            not in (SELECT distinct customer_id FROM CRM.dbo.crm_customer_added 
                            where customer_id not in(SELECT customerId FROM CRM.dbo.crm_inactiveCustomer where state=0) and customer_id 
                            not in(SELECT customerId FROM CRM.dbo.crm_returnCustomer where returnState=0) and returnState=0)  
                            AND CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).") And Name!=''");
        return Response::json($customers);
    }
    public function searchCustomerByRegion(Request $request)
    {
        $rsn=$request->get("rsn");
        $customers=DB::select("SELECT * FROM Shop.dbo.Peopels where Peopels.PSN 
        not in (SELECT distinct customer_id FROM CRM.dbo.crm_customer_added where customer_id not in(SELECT customerId FROM CRM.dbo.crm_inactiveCustomer where state=0) and customer_id not in(SELECT customerId
        FROM CRM.dbo.crm_returnCustomer where returnState=0) and returnState=0)  AND CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).") And Name!=''
        and SnMantagheh=".$rsn);
        return Response::json($customers);
    }
    public function searchAddedCustomerByRegion(Request $request)
    {
        $rsn=$request->get("rsn");
        $asn=$request->get("asn");
        $customers=DB::select("SELECT * FROM (
            SELECT * FROM Shop.dbo.Peopels where CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).")
                    and SnMantagheh=".$rsn." and PSN in(SELECT distinct customer_id FROM CRM.dbo.crm_customer_added where admin_id=".$asn." and returnState=0))a");
        return Response::json($customers);
    }
    public function getAddedCustomer(Request $request)
    {
       $adminId=$request->get("adminId");
       $customers=DB::select("SELECT * FROM Shop.dbo.Peopels where Peopels.PSN in (SELECT customer_id FROM CRM.dbo.crm_customer_added where admin_id=".$adminId." and crm_customer_added.returnState=0)  AND CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).")");
       return Response::json($customers);
    }
    public function assesCustomer(Request $request)
    {
        $adminId=Session::get('asn');
        $psn=$request->post("customerSn");
        $customer=DB::select("SELECT * FROM Shop.dbo.Peopels JOIN CRM.dbo.crm_customer_added ON Peopels.PSN=crm_customer_added.customer_id
        JOIN Shop.dbo.PhoneDetail on PhoneDetail.SnPeopel=crm_customer_added.customer_id
        where Shop.dbo.Peopels.CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).") AND crm_customer_added.admin_id=".$adminId." and Peopels.PSN=".$psn);
        $exactCustomer;
        foreach ($customer as $cust) {
            $exactCustomer=$cust;
        }
        return View("customer.customerDashboard",['customer'=>$exactCustomer]);
    }

    public function todayComment(){
        $yesterdayOfWeek = Jalalian::fromCarbon(Carbon::yesterday())->getDayOfWeek();
        $yesterday;
        if($yesterdayOfWeek==6){
            $yesterday = Jalalian::fromCarbon(Carbon::yesterday()->subDays(1))->format('Y/m/d');
        }else{
            $yesterday = Jalalian::fromCarbon(Carbon::yesterday())->format('Y/m/d');
        }
        $adminId=Session::get('asn');
        $customers = DB::select("SELECT * from(
            SELECT NetPriceHDS as TotalPriceHDS,* FROM (
                        SELECT maxFactorId as SerialNoHDS,CustomerSn,NetPriceHDS,FactNo from
                        (SELECT MAX(SerialNoHDS) as maxFactorId FROM Shop.dbo.FactorHDS where FactType=3 and FactorHDS.FactDate='".$yesterday."' group by FactorHDS.CustomerSn)a
                        join Shop.dbo.FactorHDS on a.maxFactorId=FactorHDS.SerialNoHDS)d
                        join (SELECT Name,PSN,PCode FROM Shop.dbo.Peopels)e on d.CustomerSn=e.PSN)f
                         where f.SerialNoHDS not in (SELECT factorId FROM CRM.dbo.crm_alarm) ");
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n";   
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }
        return view ("customer.todayComments",['customers'=>$customers]);
    }
    public function inactiveCustomer(Request $request)
    {
        $customerId=$request->get("customerId");
        $comment=$request->get("comment");
        $adminId=Session::get("asn");
        DB::table("CRM.dbo.crm_inactiveCustomer")->insert(['adminId'=>$adminId,'customerId'=>$customerId,'comment'=>"".$comment."",'state'=>1]);
        DB::table("CRM.dbo.crm_returnCustomer")->where("customerId",$customerId)->where('returnState',1)->update(['returnState'=>0]);
        DB::table("CRM.dbo.crm_customer_added")->where("customer_id",$customerId)->where('returnState',0)->update(['returnState'=>1,'gotEmpty'=>1]);
        $customers=DB::table("Shop.dbo.Peopels")
        ->join("CRM.dbo.crm_returnCustomer","Peopels.PSN","=","crm_returnCustomer.customerId")
        ->join("CRM.dbo.crm_admin","crm_returnCustomer.adminId","=","crm_admin.id")
        ->where("crm_returnCustomer.returnState",1)
        ->select("Peopels.PSN","Peopels.PCode","Peopels.Name","crm_returnCustomer.returnDate","crm_admin.name as adminName","crm_admin.lastName as adminLastName","Peopels.peopeladdress","crm_returnCustomer.adminId")
        ->get();
        foreach ($customers as $customer) {
            $phones=DB::select("SELECT * FROM Shop.dbo.PhoneDetail where SnPeopel=".$customer->PSN);
            $hamrah="";
            $sabit="";
            foreach ($phones as $phone) {
                if($phone->PhoneType==2){
                    $hamrah.=$phone->PhoneStr."\n";
                }else{
                    $sabit.=$phone->PhoneStr."\n";
                }
            }
            $customer->hamrah=$hamrah;
            $customer->sabit=$sabit;
        }
        return Response::json($customers);
    }
    public function searchInActiveCustomerByName(Request $request)
    {
        $searchTerm=$request->get("searchTerm");

        $customers=DB::select("SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM CRM.dbo.crm_inactiveCustomer
                            join(SELECT name,lastName,id as admin_id FROM CRM.dbo.crm_admin)a on a.admin_id=adminId)b
                            join (SELECT Name as CustomerName,PSN,PCode FROM Shop.dbo.Peopels)c on c.PSN=b.customerId)d
                            join (SELECT SnPeopel,PhoneStr FROM Shop.dbo.PhoneDetail)e on e.SnPeopel=d.PSN
                            where state=1 and CustomerName like '%".$searchTerm."%'");
        
        return Response::json($customers);
    }

    public function searchInActiveCustomerByCode(Request $request)
    {
        $searchTerm=$request->get("searchTerm");

        $customers=DB::select("SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM CRM.dbo.crm_inactiveCustomer
                            join(SELECT name,lastName,id as admin_id FROM CRM.dbo.crm_admin)a on a.admin_id=adminId)b
                            join (SELECT Name as CustomerName,PSN,PCode FROM Shop.dbo.Peopels)c on c.PSN=b.customerId)d
                            join (SELECT SnPeopel,PhoneStr FROM Shop.dbo.PhoneDetail)e on e.SnPeopel=d.PSN
                            where state=1 and PCode like '%".$searchTerm."%'");

        return Response::json($customers);
    }
    public function searchInActiveCustomerByLocation(Request $request)
    {
        $searchTerm=$request->get("searchTerm");

        if($searchTerm==1){

            $customers=DB::select("SELECT * FROM (
                                SELECT * FROM (
                                SELECT * FROM CRM.dbo.crm_inactiveCustomer
                                join(SELECT name,lastName,id as admin_id FROM CRM.dbo.crm_admin)a on a.admin_id=adminId)b
                                join (SELECT Name as CustomerName,PSN,PCode,LatPers,LonPers FROM Shop.dbo.Peopels)c on c.PSN=b.customerId)d
                                join (SELECT SnPeopel,PhoneStr FROM Shop.dbo.PhoneDetail)e on e.SnPeopel=d.PSN
                                where state=1 and LatPers>0 and LonPers>0");
            
            return Response::json($customers);
        }

        if($searchTerm==2){
            $customers=DB::select("SELECT * FROM (
                                SELECT * FROM (
                                SELECT * FROM CRM.dbo.crm_inactiveCustomer
                                join(SELECT name,lastName,id as admin_id FROM CRM.dbo.crm_admin)a on a.admin_id=adminId)b
                                join (SELECT Name as CustomerName,PSN,PCode,LatPers,LonPers FROM Shop.dbo.Peopels)c on c.PSN=b.customerId)d
                                join (SELECT SnPeopel,PhoneStr FROM Shop.dbo.PhoneDetail)e on e.SnPeopel=d.PSN
                                where state=1 and LatPers=0 and LonPers=0");
            
            return Response::json($customers);
        }

        if($searchTerm==0){

            $customers=DB::select("SELECT * FROM (
                                SELECT * FROM (
                                SELECT * FROM CRM.dbo.crm_inactiveCustomer
                                join(SELECT name,lastName,id as admin_id FROM CRM.dbo.crm_admin)a on a.admin_id=adminId)b
                                join (SELECT Name as CustomerName,PSN,PCode,LatPers,LonPers FROM Shop.dbo.Peopels)c on c.PSN=b.customerId)d
                                join (SELECT SnPeopel,PhoneStr FROM Shop.dbo.PhoneDetail)e on e.SnPeopel=d.PSN
                                where state=1");
            
            return Response::json($customers);
        }
    }
    public function orderInactiveCustomers(Request $request)
    {
        $searchTerm=$request->get("searchTerm");

        if($searchTerm==1){

            $customers=DB::select("SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM CRM.dbo.crm_inactiveCustomer
                            join(SELECT name,lastName,id as admin_id FROM CRM.dbo.crm_admin)a on a.admin_id=adminId)b
                            join (SELECT Name as CustomerName,PSN,PCode,LatPers,LonPers FROM Shop.dbo.Peopels)c on c.PSN=b.customerId)d
                            join (SELECT SnPeopel,PhoneStr FROM Shop.dbo.PhoneDetail)e on e.SnPeopel=d.PSN
                            where state=1 order by PCode asc");

            return Response::json($customers);
        }
        if($searchTerm==2){

            $customers=DB::select("SELECT * FROM (
                                SELECT * FROM (
                                SELECT * FROM CRM.dbo.crm_inactiveCustomer
                                join(SELECT name,lastName,id as admin_id FROM CRM.dbo.crm_admin)a on a.admin_id=adminId)b
                                join (SELECT Name as CustomerName,PSN,PCode,LatPers,LonPers FROM Shop.dbo.Peopels)c on c.PSN=b.customerId)d
                                join (SELECT SnPeopel,PhoneStr FROM Shop.dbo.PhoneDetail)e on e.SnPeopel=d.PSN
                                where state=1 order by CustomerName asc");

            return Response::json($customers);
        }
    }

    public function pastComment(){

        $adminId=Session::get('asn');

        $yesterday = Jalalian::fromCarbon(Carbon::yesterday())->format('Y/m/d');

        $customers = DB::select("SELECT TOP 20 NetPriceHDS as TotalPriceHDS,* FROM (
                            SELECT maxFactorId as SerialNoHDS,CustomerSn,NetPriceHDS,FactNo from
                            (SELECT MAX(SerialNoHDS) as maxFactorId FROM Shop.dbo.FactorHDS where FactorHDS.FactDate<='".$yesterday."' and FactorHDS.FactDate>='1401/04/20' group by FactorHDS.CustomerSn)a
                            join Shop.dbo.FactorHDS on a.maxFactorId=FactorHDS.SerialNoHDS)d
                            join Shop.dbo.Peopels on d.CustomerSn=Peopels.PSN
                            where d.SerialNoHDS not in (SELECT factorId FROM CRM.dbo.crm_alarm)
                            and CustomerSn in (SELECT customer_id FROM CRM.dbo.crm_customer_added) ");

            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n";   
                    }
                }
                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;
            }
        return view ("customer.pastComments",['customers'=>$customers]);
    }

    public function searchAllCustomerByName(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        
        $customers=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                        left join (
                        SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                        on a.PSN=b.CustomerSn )c
                        left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                        )d
                        on d.customerId=c.PSN )e
                        join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id)h on e.PSN=h.customer_id)i
                        where i.GroupCode IN (".implode(",",Session::get("groups")).") and
                        i.CompanyNo=5 and i.PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0) and i.returnState=0
                        and i.Name like '%".$searchTerm."%' ORDER BY countFactor desc");
            
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n";   
                    }
                }
                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;
            }
            return Response::json($customers);
    }

    public function searchAllCustomerByPCode(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        
        $customers=DB::select("SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
            left join (
            SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
            on a.PSN=b.CustomerSn )c
            left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
            )d
            on d.customerId=c.PSN )e
            join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id)h on e.PSN=h.customer_id)i
            where i.GroupCode IN ( ".implode(",",Session::get("groups")).") and
            i.CompanyNo=5 and i.PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
            and i.PCode like '%".$searchTerm."%' and i.returnState=0 ORDER BY countFactor desc");

            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n";   
                    }
                }
                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;
            }
            return Response::json($customers);
    }
public function searchAllCustomerByMantagheh(Request $request)
{
    $searchTerm=$request->get("searchTerm");

    $customers=DB::select("SELECT * FROM (
                    SELECT * FROM (
                    SELECT * FROM (
                    SELECT * FROM (
                    SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                    left join (
                    SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                    on a.PSN=b.CustomerSn )c
                    left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                    )d
                    on d.customerId=c.PSN )e
                    join (SELECT SnMNM,NameRec FROM Shop.dbo.MNM where CompanyNo=5)f on e.SnMantagheh=f.SnMNM)g
                    join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added
                    join CRM.dbo.crm_admin on admin_id=crm_admin.id)h on g.PSN=h.customer_id)i
                    where i.GroupCode IN ( ".implode(",",Session::get("groups")).") and i.SnMNM=".$searchTerm." and i.returnState=0 and i.CompanyNo=5 ORDER BY countFactor desc");
    
    foreach ($customers as $customer) {
        $sabit="";
        $hamrah="";
        $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
        foreach ($phones as $phone) {
            if($phone->PhoneType==1){
            $sabit.=$phone->PhoneStr."\n";
            }else{
                $hamrah.=$phone->PhoneStr."\n";   
            }
        }
        $customer->sabit=$sabit;
        $customer->hamrah=$hamrah;
    }
        return Response::json($customers);
}
    public function searchAllCustomerByAdmin(Request $request)
    {
        $searchTerm=$request->get("searchTerm");

        $customers=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                        left join (
                        SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                        on a.PSN=b.CustomerSn )c
                        left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                        )d
                        on d.customerId=c.PSN )e
                        join (SELECT SnMNM,NameRec FROM Shop.dbo.MNM where CompanyNo=5)f on e.SnMantagheh=f.SnMNM)g
                        join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added
                        join CRM.dbo.crm_admin on admin_id=crm_admin.id where admin_id=".$searchTerm.")h on g.PSN=h.customer_id)i
                        where i.GroupCode IN ( ".implode(",",Session::get("groups")).") and i.returnState=0 and i.CompanyNo=5 ORDER BY countFactor desc");
        
        foreach ($customers as $customer) {
            
            $sabit="";
            $hamrah="";
            
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            
            foreach ($phones as $phone) {

                if($phone->PhoneType==1){

                $sabit.=$phone->PhoneStr."\n";

                }else{

                    $hamrah.=$phone->PhoneStr."\n"; 

                }

            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }

        return Response::json($customers);
    }

    public function searchAllCustomerActiveOrNot(Request $request)
    {
        $searchTerm=$request->get('searchTerm');
        if($searchTerm==2){

            $customers=DB::select("SELECT * FROM (
                                SELECT * FROM (
                                SELECT * FROM (
                                SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                                left join (
                                SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                                on a.PSN=b.CustomerSn )c
                                left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                                )d
                                on d.customerId=c.PSN )e
                                join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                                where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.returnState=0 and h.CompanyNo=5 and h.PSN in (SELECT customerId FROM CRM.dbo.crm_inactiveCustomer where state=1) and Name!=''");
                
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n";   
                    }
                }
                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;
            }
            return Response::json($customers);    
        }
        if($searchTerm==1){
            $customers=DB::select("SELECT * FROM (
                                SELECT * FROM (
                                SELECT * FROM (
                                SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                                left join (
                                SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                                on a.PSN=b.CustomerSn )c
                                left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                                )d
                                on d.customerId=c.PSN )e
                                join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                                where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and returnState=0 and h.CompanyNo=5 and h.PSN not in (SELECT customerId FROM CRM.dbo.crm_inactiveCustomer where state=1) and Name!=''");
                
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                
                foreach ($phones as $phone) {

                    if($phone->PhoneType==1){

                    $sabit.=$phone->PhoneStr."\n";

                    }else{

                        $hamrah.=$phone->PhoneStr."\n";  

                    }

                }

                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;

            }

            return Response::json($customers);
        }
        
    }

    public function searchAllCustomerLocationOrNot(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm==2){
            $customers=DB::select("SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                            left join (
                            SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                            on a.PSN=b.CustomerSn )c
                            left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                            )d
                            on d.customerId=c.PSN )e
                            join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                            where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.CompanyNo=5 and h.returnState=0
                            and h.LatPers>0 and h.LonPers>0");
            
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n";   
                    }
                }
                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;
            }
                return Response::json($customers);
            }
        if($searchTerm==3){
            
            $customers=DB::select("SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                            left join (
                            SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                            on a.PSN=b.CustomerSn )c
                            left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                            )d
                            on d.customerId=c.PSN )e
                            join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                            where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.CompanyNo=5 and h.returnState=0
                            and h.LatPers=0 and h.LonPers=0");
            
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n";   
                    }
                }
                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;
            }
                return Response::json($customers); 
        }
            if($searchTerm==1){
                $customers=DB::select("SELECT * FROM (
                                SELECT * FROM (
                                SELECT * FROM (
                                SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                                left join (
                                SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                                on a.PSN=b.CustomerSn )c
                                left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                                )d
                                on d.customerId=c.PSN )e
                                join(SELECT name as adminName,lastName,customer_id FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                                where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.CompanyNo=5 and h.returnState=0
                                "); 
                
                foreach ($customers as $customer) {
                    $sabit="";
                    $hamrah="";
                    $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                    foreach ($phones as $phone) {
                        if($phone->PhoneType==1){
                        $sabit.=$phone->PhoneStr."\n";
                        }else{
                            $hamrah.=$phone->PhoneStr."\n";   
                        }
                    }
                    $customer->sabit=$sabit;
                    $customer->hamrah=$hamrah;
                }
                return Response::json($customers);
            }
    }

    public function searchAllCustomerFactorOrNot(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm==1){
            $customers=DB::select("SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                            left join (
                            SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                            on a.PSN=b.CustomerSn )c
                            left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                            )d
                            on d.customerId=c.PSN )e
                            join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                            where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.returnState=0 and h.CompanyNo=5 and h.PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
                            and h.PSN in(SELECT CustomerSn FROM Shop.dbo.FactorHDS)");
                foreach ($customers as $customer) {
                    $sabit="";
                    $hamrah="";
                    $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                    foreach ($phones as $phone) {
                        if($phone->PhoneType==1){
                        $sabit.=$phone->PhoneStr."\n";
                        }else{
                            $hamrah.=$phone->PhoneStr."\n";   
                        }
                    }
                    $customer->sabit=$sabit;
                    $customer->hamrah=$hamrah;
                }
                return Response::json($customers);
        }
        if($searchTerm==2){
            $customers=DB::select("SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                            left join (
                            SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                            on a.PSN=b.CustomerSn )c
                            left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                            )d
                            on d.customerId=c.PSN )e
                            join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                            where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.returnState=0 and h.CompanyNo=5 and h.PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
                            and h.PSN not in(SELECT CustomerSn FROM Shop.dbo.FactorHDS)");
            
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n";   
                    }
                }
                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;
            }
                return Response::json($customers);
        }
        if($searchTerm==3){
            $customers=DB::select("SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                            left join (
                            SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                            on a.PSN=b.CustomerSn )c
                            left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                            )d
                            on d.customerId=c.PSN )e
                            join(SELECT name as adminName,lastName,customer_id,returnState=0 FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                            where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.returnState=0 and h.CompanyNo=5 and h.PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
                            ");
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n";   
                    }
                }
                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;
            }
                return Response::json($customers);
        }
    }

    public function searchAllCustomerBasketOrNot(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $customers;
        if($searchTerm==1){
        $customers=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                        left join (
                        SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                        on a.PSN=b.CustomerSn )c
                        left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                        )d
                        on d.customerId=c.PSN )e
                        join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                        where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.returnState=0 and h.CompanyNo=5 and h.PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
                        and h.PSN in(SELECT CustomerSn FROM NewStarfood.dbo.FactorStar where OrderStatus=0)");
            
        }
        if($searchTerm==2){
            $customers=DB::select("SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                            left join (
                            SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                            on a.PSN=b.CustomerSn )c
                            left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                            )d
                            on d.customerId=c.PSN )e
                            join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                            where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.returnState=0 and h.CompanyNo=5 and h.PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
                            and h.PSN not in(SELECT CustomerSn FROM NewStarfood.dbo.FactorStar where OrderStatus=0)");
                
        }
        if($searchTerm==3){
            $customers=DB::select("SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                            left join (
                            SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                            on a.PSN=b.CustomerSn )c
                            left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                            )d
                            on d.customerId=c.PSN )e
                            join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                            where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.returnState=0 and h.CompanyNo=5 and h.PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
                            ");  
        }
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n";   
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }
        return Response::json($customers);
    }

    public function searchAllCustomerLoginOrNot(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm==1){
        $customers=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                        left join (
                        SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                        on a.PSN=b.CustomerSn )c
                        left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                        )d
                        on d.customerId=c.PSN )e
                            join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                        where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.returnState=0 and h.CompanyNo=5 and h.PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
                        and h.PSN in(SELECT customerId FROM NewStarfood.dbo.star_customerSession1)");
            
        }
        if($searchTerm==2){
            $customers=DB::select("SELECT * FROM (
                SELECT * FROM (
                SELECT * FROM (
                SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                left join (
                SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                on a.PSN=b.CustomerSn )c
                left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                )d
                on d.customerId=c.PSN )e
                    join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.returnState=0 and h.CompanyNo=5 and h.PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
                and h.PSN not in(SELECT customerId FROM NewStarfood.dbo.star_customerSession1)");
        }
        if($searchTerm==3){
            $customers=DB::select("SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (
                            SELECT * FROM (SELECT * FROM Shop.dbo.Peopels) a
                            left join (
                            SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn) b
                            on a.PSN=b.CustomerSn )c
                            left join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS group by FactorHDS.CustomerSn
                            )d
                            on d.customerId=c.PSN )e
                             join(SELECT name as adminName,lastName,customer_id,returnState FROM CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id )g on e.PSN=g.customer_id)h
                            where h.GroupCode IN ( ".implode(",",Session::get("groups")).") and h.returnState=0 and h.CompanyNo=5 and h.PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
                            ");
        }
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n";   
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }
        return Response::json($customers);
    }

    public function searchKalaNameCode(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $kalas=DB::select("SELECT * FROM(
                    SELECT * FROM(
                    SELECT * FROM(
                    SELECT * FROM(
                    SELECT * FROM(
                    SELECT * FROM (
                    SELECT * FROM (
                    SELECT MAX(Shop.dbo.FactorHDS.FactDate) AS maxFactDate,SnGood FROM Shop.dbo.FactorHDS 
                    JOIN (SELECT Shop.dbo.FactorBYS.SnFact,SnGood FROM Shop.dbo.FactorBYS)a
                    ON a.SnFact=FactorHDS.SerialNoHDS GROUP BY a.SnGood )b
                    JOIN (SELECT GoodSn,GoodName,GoodCde,GoodGroupSn FROM Shop.dbo.PubGoods)c on b.SnGood=c.GoodSn)d
                    JOIN (SELECT firstGroupId,product_id FROM NewStarfood.dbo.star_add_prod_group)e on e.product_id=d.GoodSn)f
                    JOIN (SELECT id,title FROM NewStarfood.dbo.Star_Group_Def)g on f.firstGroupId=g.id)h
                    JOIN (SELECT productId,hideKala FROM NewStarfood.dbo.star_GoodsSaleRestriction)i
                    on i.productId=h.GoodSn)j
                    JOIN (SELECT Amount,SnGood as GSN,CompanyNo,FiscalYear FROM Shop.dbo.ViewGoodExists)k on k.GSN=j.GoodSn)l
                    where l.CompanyNo=5 and l.FiscalYear=1399 and l.GoodGroupSn>49)M WHERE M.GoodName like '%$searchTerm%' OR M.GoodCde LIKE '%$searchTerm%'");
            return Response::json($kalas);
    }

    public function searchKalaByStock(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $kalas=DB::select("SELECT  PubGoods.GoodName,PubGoods.GoodCde,PubGoods.GoodSn,star_GoodsSaleRestriction.hideKala,ViewGoodExists.Amount,a.maxFactDate FROM
        Shop.dbo.PubGoods 
        JOIN NewStarfood.dbo.star_GoodsSaleRestriction ON PubGoods.GoodSn=star_GoodsSaleRestriction.productId
        JOIN Shop.dbo.ViewGoodExists ON PubGoods.GoodSn=ViewGoodExists.SnGood
        JOIN(
        Select MAX(Shop.dbo.FactorHDS.FactDate) as maxFactDate,FactorBYS.SnGood
        FROM Shop.dbo.FactorHDS JOIN Shop.dbo.FactorBYS ON FactorBYS.SnFact=FactorHDS.SerialNoHDS
        GROUP BY    FactorBYS.SnGood)a
        ON a.SnGood=PubGoods.GoodSn
        JOIN (SELECT Amount,SnGood as GSN,CompanyNo,FiscalYear,SnStock FROM Shop.dbo.ViewGoodExistsInStock)k on k.GSN=GoodSn
        WHERE SnStock=".$searchTerm."
        and  ViewGoodExists.CompanyNo=5 and ViewGoodExists.FiscalYear=1399 and PubGoods.GoodGroupSn>49");
        return Response::json($kalas);
    }
    public function searchKalaByActiveOrNot(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm==1){
            $kalas=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT MAX(Shop.dbo.FactorHDS.FactDate) AS maxFactDate,SnGood FROM Shop.dbo.FactorHDS 
                        JOIN (SELECT Shop.dbo.FactorBYS.SnFact,SnGood FROM Shop.dbo.FactorBYS)a
                        ON a.SnFact=FactorHDS.SerialNoHDS GROUP BY a.SnGood )b
                        JOIN (SELECT GoodSn,GoodName,GoodCde,GoodGroupSn FROM Shop.dbo.PubGoods)c on b.SnGood=c.GoodSn)d
                        JOIN (SELECT productId,hideKala FROM NewStarfood.dbo.star_GoodsSaleRestriction)i
                        on i.productId=d.GoodSn)j
                        JOIN (SELECT Amount,SnGood as GSN,CompanyNo,FiscalYear,SnStock FROM Shop.dbo.ViewGoodExistsInStock)k on k.GSN=j.GoodSn)l
                        where l.CompanyNo=5 and l.FiscalYear=1399 and l.GoodGroupSn>49 and l.SnStock=23)M WHERE M.hideKala=0");
                return Response::json($kalas);}
        if($searchTerm==2){
            $kalas=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT MAX(Shop.dbo.FactorHDS.FactDate) AS maxFactDate,SnGood FROM Shop.dbo.FactorHDS 
                        JOIN (SELECT Shop.dbo.FactorBYS.SnFact,SnGood FROM Shop.dbo.FactorBYS)a
                        ON a.SnFact=FactorHDS.SerialNoHDS GROUP BY a.SnGood )b
                        JOIN (SELECT GoodSn,GoodName,GoodCde,GoodGroupSn FROM Shop.dbo.PubGoods)c on b.SnGood=c.GoodSn)d
                        JOIN (SELECT productId,hideKala FROM NewStarfood.dbo.star_GoodsSaleRestriction)i
                        on i.productId=d.GoodSn)j
                        JOIN (SELECT Amount,SnGood as GSN,CompanyNo,FiscalYear,SnStock FROM Shop.dbo.ViewGoodExistsInStock)k on k.GSN=j.GoodSn)l
                        where l.CompanyNo=5 and l.FiscalYear=1399 and l.GoodGroupSn>49 and l.SnStock=23)M WHERE M.hideKala=1");
                return Response::json($kalas);}
        if($searchTerm==3){
            $kalas=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT MAX(Shop.dbo.FactorHDS.FactDate) AS maxFactDate,SnGood FROM Shop.dbo.FactorHDS 
                        JOIN (SELECT Shop.dbo.FactorBYS.SnFact,SnGood FROM Shop.dbo.FactorBYS)a
                        ON a.SnFact=FactorHDS.SerialNoHDS GROUP BY a.SnGood )b
                        JOIN (SELECT GoodSn,GoodName,GoodCde,GoodGroupSn FROM Shop.dbo.PubGoods)c on b.SnGood=c.GoodSn)d
                        JOIN (SELECT productId,hideKala FROM NewStarfood.dbo.star_GoodsSaleRestriction)i
                        on i.productId=d.GoodSn)j
                        JOIN (SELECT Amount,SnGood as GSN,CompanyNo,FiscalYear,SnStock FROM Shop.dbo.ViewGoodExistsInStock)k on k.GSN=j.GoodSn)l
                        where l.CompanyNo=5 and l.FiscalYear=1399 and l.GoodGroupSn>49 and l.SnStock=23)M WHERE M.hideKala=0");
                return Response::json($kalas);}
    }
    public function searchKalaByZeroOrNot(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm==1){
            $kalas=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT MAX(Shop.dbo.FactorHDS.FactDate) AS maxFactDate,SnGood FROM Shop.dbo.FactorHDS 
                        JOIN (SELECT Shop.dbo.FactorBYS.SnFact,SnGood FROM Shop.dbo.FactorBYS)a
                        ON a.SnFact=FactorHDS.SerialNoHDS GROUP BY a.SnGood )b
                        JOIN (SELECT GoodSn,GoodName,GoodCde,GoodGroupSn FROM Shop.dbo.PubGoods)c on b.SnGood=c.GoodSn)d
                        JOIN (SELECT productId,hideKala FROM NewStarfood.dbo.star_GoodsSaleRestriction)i
                        on i.productId=d.GoodSn)j
                        JOIN (SELECT Amount,SnGood as GSN,CompanyNo,FiscalYear,SnStock FROM Shop.dbo.ViewGoodExistsInStock)k on k.GSN=j.GoodSn)l
                        where l.CompanyNo=5 and l.FiscalYear=1399 and l.GoodGroupSn>49 and l.SnStock=23)M WHERE M.Amount=0");
                return Response::json($kalas);}
        if($searchTerm==2){
            $kalas=DB::select("SELECT * FROM (
                SELECT * FROM (
                SELECT * FROM (
                SELECT * FROM (
                SELECT * FROM (
                SELECT MAX(Shop.dbo.FactorHDS.FactDate) AS maxFactDate,SnGood FROM Shop.dbo.FactorHDS 
                JOIN (SELECT Shop.dbo.FactorBYS.SnFact,SnGood FROM Shop.dbo.FactorBYS)a
                ON a.SnFact=FactorHDS.SerialNoHDS GROUP BY a.SnGood )b
                JOIN (SELECT GoodSn,GoodName,GoodCde,GoodGroupSn FROM Shop.dbo.PubGoods)c on b.SnGood=c.GoodSn)d
                JOIN (SELECT productId,hideKala FROM NewStarfood.dbo.star_GoodsSaleRestriction)i
                on i.productId=d.GoodSn)j
                JOIN (SELECT Amount,SnGood as GSN,CompanyNo,FiscalYear,SnStock FROM Shop.dbo.ViewGoodExistsInStock)k on k.GSN=j.GoodSn)l
                where l.CompanyNo=5 and l.FiscalYear=1399 and l.GoodGroupSn>49 and l.SnStock=23)M WHERE M.Amount>0");
                return Response::json($kalas);}
        if($searchTerm==3){
            $kalas=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT MAX(Shop.dbo.FactorHDS.FactDate) AS maxFactDate,SnGood FROM Shop.dbo.FactorHDS 
                        JOIN (SELECT Shop.dbo.FactorBYS.SnFact,SnGood FROM Shop.dbo.FactorBYS)a
                        ON a.SnFact=FactorHDS.SerialNoHDS GROUP BY a.SnGood )b
                        JOIN (SELECT GoodSn,GoodName,GoodCde,GoodGroupSn FROM Shop.dbo.PubGoods)c on b.SnGood=c.GoodSn)d
                        JOIN (SELECT productId,hideKala FROM NewStarfood.dbo.star_GoodsSaleRestriction)i
                        on i.productId=d.GoodSn)j
                        JOIN (SELECT Amount,SnGood as GSN,CompanyNo,FiscalYear,SnStock FROM Shop.dbo.ViewGoodExistsInStock)k on k.GSN=j.GoodSn)l
                        where l.CompanyNo=5 and l.FiscalYear=1399 and l.GoodGroupSn>49 and l.SnStock=23)M");
                return Response::json($kalas);}
    }
    public function searchSubGroupKala(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $subGroups=DB::select("SELECT title,id FROM NewStarfood.dbo.Star_Group_Def where selfGroupId=".$searchTerm);
        return Response::json($subGroups);
    }
    public function searchBySubGroupKala(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm>0){
        $kalas=DB::select("SELECT * FROM (
                    SELECT * FROM (
                    SELECT * FROM (
                    SELECT * FROM (
                    SELECT * FROM (
                    SELECT * FROM (
                    SELECT * FROM (
                    SELECT MAX(Shop.dbo.FactorHDS.FactDate) AS maxFactDate,SnGood FROM Shop.dbo.FactorHDS 
                    JOIN (SELECT Shop.dbo.FactorBYS.SnFact,SnGood FROM Shop.dbo.FactorBYS)a
                    ON a.SnFact=FactorHDS.SerialNoHDS GROUP BY a.SnGood )b
                    JOIN (SELECT GoodSn,GoodName,GoodCde,GoodGroupSn FROM Shop.dbo.PubGoods)c on b.SnGood=c.GoodSn)d
                    JOIN (SELECT firstGroupId,product_id,secondGroupId FROM NewStarfood.dbo.star_add_prod_group)e on e.product_id=d.GoodSn)f
                    JOIN (SELECT id,title FROM NewStarfood.dbo.Star_Group_Def)g on f.firstGroupId=g.id)h
                    JOIN (SELECT productId,hideKala FROM NewStarfood.dbo.star_GoodsSaleRestriction)i
                    on i.productId=h.GoodSn)j
                    JOIN (SELECT Amount,SnGood as GSN,CompanyNo,FiscalYear,SnStock FROM Shop.dbo.ViewGoodExistsInStock)k on k.GSN=j.GoodSn)l
                    where l.CompanyNo=5 and l.FiscalYear=1399 and l.GoodGroupSn>49 and l.SnStock=23)M WHERE M.secondGroupId=".$searchTerm);
                return Response::json($kalas);
        }else{
            $kalas=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT MAX(Shop.dbo.FactorHDS.FactDate) AS maxFactDate,SnGood FROM Shop.dbo.FactorHDS 
                        JOIN (SELECT Shop.dbo.FactorBYS.SnFact,SnGood FROM Shop.dbo.FactorBYS)a
                        ON a.SnFact=FactorHDS.SerialNoHDS GROUP BY a.SnGood )b
                        JOIN (SELECT GoodSn,GoodName,GoodCde,GoodGroupSn FROM Shop.dbo.PubGoods)c on b.SnGood=c.GoodSn)d
                        JOIN (SELECT firstGroupId,product_id,secondGroupId FROM NewStarfood.dbo.star_add_prod_group)e on e.product_id=d.GoodSn)f
                        JOIN (SELECT id,title FROM NewStarfood.dbo.Star_Group_Def)g on f.firstGroupId=g.id)h
                        JOIN (SELECT productId,hideKala FROM NewStarfood.dbo.star_GoodsSaleRestriction)i
                        on i.productId=h.GoodSn)j
                        JOIN (SELECT Amount,SnGood as GSN,CompanyNo,FiscalYear,SnStock FROM Shop.dbo.ViewGoodExistsInStock)k on k.GSN=j.GoodSn)l
                        where l.CompanyNo=5 and l.FiscalYear=1399 and l.GoodGroupSn>49 and l.SnStock=23)M WHERE M.firstGroupId=".$searchTerm);
                        return Response::json($kalas);
        }
    }

    public function searchPastAssesByDate(Request $request)
    {
        $fristDate=$request->get("firstDate");
        $secondDate=$request->get("secondDate");
        $customers=DB::select("SELECT NetPriceHDS as TotalPriceHDS,* FROM (
                        SELECT maxFactorId as SerialNoHDS,CustomerSn,NetPriceHDS,FactNo from
                        (SELECT MAX(SerialNoHDS) as maxFactorId FROM Shop.dbo.FactorHDS where FactorHDS.FactDate<='".$secondDate."' and FactorHDS.FactDate>='".$fristDate."' group by FactorHDS.CustomerSn)a
                        JOIN Shop.dbo.FactorHDS on a.maxFactorId=FactorHDS.SerialNoHDS)d
                        JOIN Shop.dbo.Peopels on d.CustomerSn=Peopels.PSN
                        where d.SerialNoHDS not in (SELECT factorId FROM CRM.dbo.crm_alarm)
                        and CustomerSn in (SELECT customer_id FROM CRM.dbo.crm_customer_added) ");
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n";   
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }    
        return Response::json($customers);
    }
    public function searchReturnedByDate(Request $request)
    {
        $fristDate=Jalalian::fromFormat('Y/m/d', $request->get("firstDate"))->toCarbon()->format('Y-m-d H:i:s');
        $secondDate=Jalalian::fromFormat('Y/m/d', $request->get("secondDate"))->toCarbon()->format('Y-m-d H:i:s');
        $customers=DB::select("SELECT * FROM Shop.dbo.Peopels 
                        JOIN CRM.dbo.crm_returnCustomer on Peopels.PSN=CRM.dbo.crm_returnCustomer.customerId
                        JOIN Shop.dbo.PhoneDetail on Peopels.PSN=PhoneDetail.SnPeopel
                        JOIN CRM.dbo.crm_admin on CRM.dbo.crm_returnCustomer.adminId=CRM.dbo.crm_admin.id
                        where CRM.dbo.crm_returnCustomer.returnState=1
                        and CRM.dbo.crm_returnCustomer.returnDate >='".$fristDate."' and CRM.dbo.crm_returnCustomer.returnDate <='".$secondDate."'");
        return Response::json($customers);
    }
    public function searchEmptyByDate(Request $request)
    {
        $fristDate=Jalalian::fromFormat('Y/m/d', $request->get("firstDate"))->toCarbon()->format('Y-m-d H:i:s');
        $secondDate=Jalalian::fromFormat('Y/m/d', $request->get("secondDate"))->toCarbon()->format('Y-m-d H:i:s');
        $customers=DB::select("SELECT * from(
                        SELECT distinct * from(
                        SELECT CRM.dbo.crm_customer_added.customer_id FROM CRM.dbo.crm_customer_added where gotEmpty=1 and customer_id not in (SELECT CRM.dbo.crm_returnCustomer.customerId FROM CRM.dbo.crm_returnCustomer where returnState=1))d
                        JOIN (SELECT * FROM Shop.dbo.Peopels)c
                        on c.PSN=d.customer_id
                        JOIN (SELECT PhoneStr,SnPeopel FROM Shop.dbo.PhoneDetail)b on d.customer_id=b.SnPeopel
                        where PSN not in (SELECT customerId FROM CRM.dbo.crm_inactiveCustomer where state=1))e
                        JOIN (SELECT customerId,removedDate from(
                        SELECT distinct customer_id as customerId FROM CRM.dbo.crm_customer_added where  gotEmpty=1 and customer_id not in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0))a
                        JOIN (SELECT MAX(removedTime)as removedDate,customer_id FROM CRM.dbo.crm_customer_added group by customer_id)b on a.customerId=b.customer_id)f ON f.customerId=e.PSN
                        where f.removedDate >='".$fristDate."' and f.removedDate <='".$secondDate."'");
            return Response::json($customers);
    }
    public function searchByReturner(Request $request)
    {
        $rerturnerId=$request->get("searchTerm");

        $customers=DB::table("Shop.dbo.Peopels")
                ->join("CRM.dbo.crm_returnCustomer","Peopels.PSN","=","crm_returnCustomer.customerId")
                ->join("CRM.dbo.crm_admin","crm_returnCustomer.adminId","=","crm_admin.id")
                    ->where("crm_returnCustomer.returnState",1)->where("crm_returnCustomer.adminId",$rerturnerId)
                    ->select("Peopels.PSN","Peopels.PCode","Peopels.Name","crm_returnCustomer.returnDate",
                            "crm_admin.name as adminName","crm_admin.lastName as adminLastName","Peopels.peopeladdress","crm_returnCustomer.adminId")
                    ->get();
    
            foreach ($customers as $customer) {
                $phones=DB::select("SELECT * FROM Shop.dbo.PhoneDetail WHERE  SnPeopel=".$customer->PSN);
                $hamrah="";
                $sabit="";
                foreach ($phones as $phone) {
                    if($phone->PhoneType==2){
                        $hamrah.=$phone->PhoneStr."\n";
                    }else{
                        $sabit.=$phone->PhoneStr."\n";
                    }
                }
                $customer->hamrah=$hamrah;
            }
        return Response::json($customers);
    }
    public function searchEmptyByName(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $customers=DB::select("SELECT * from(
                        SELECT distinct * from(
                        SELECT CRM.dbo.crm_customer_added.customer_id FROM CRM.dbo.crm_customer_added where gotEmpty=1 and customer_id not in (SELECT CRM.dbo.crm_returnCustomer.customerId FROM CRM.dbo.crm_returnCustomer where returnState=1))d
                        JOIN (SELECT * FROM Shop.dbo.Peopels)c
                        on c.PSN=d.customer_id
                        JOIN (SELECT PhoneStr,SnPeopel FROM Shop.dbo.PhoneDetail)b on d.customer_id=b.SnPeopel
                        where PSN not in (SELECT customerId FROM CRM.dbo.crm_inactiveCustomer where state=1))e
                        JOIN (SELECT customerId,removedDate from(
                        SELECT distinct customer_id as customerId FROM CRM.dbo.crm_customer_added where  gotEmpty=1 and customer_id not in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0))a
                        JOIN (SELECT MAX(removedTime)as removedDate,customer_id FROM CRM.dbo.crm_customer_added group by customer_id)b on a.customerId=b.customer_id)f ON f.customerId=e.PSN
                        where e.Name like '%$searchTerm%' or e.peopeladdress like '%$searchTerm%'");
            return Response::json($customers);
    }
    public function searchEmptyByPCode(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $customers=DB::select("SELECT * from(
                        SELECT distinct * from(
                        SELECT CRM.dbo.crm_customer_added.customer_id FROM CRM.dbo.crm_customer_added where gotEmpty=1 and customer_id not in (SELECT CRM.dbo.crm_returnCustomer.customerId FROM CRM.dbo.crm_returnCustomer where returnState=1))d
                        JOIN (SELECT * FROM Shop.dbo.Peopels)c
                        on c.PSN=d.customer_id
                        JOIN (SELECT PhoneStr,SnPeopel FROM Shop.dbo.PhoneDetail)b on d.customer_id=b.SnPeopel
                        where PSN not in (SELECT customerId FROM CRM.dbo.crm_inactiveCustomer where state=1))e
                        JOIN (SELECT customerId,removedDate from(
                        SELECT distinct customer_id as customerId FROM CRM.dbo.crm_customer_added where  gotEmpty=1 and customer_id not in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0))a
                        JOIN (SELECT MAX(removedTime)as removedDate,customer_id FROM CRM.dbo.crm_customer_added group by customer_id)b on a.customerId=b.customer_id)f ON f.customerId=e.PSN
                        where e.PCode like '%$searchTerm%' ");
            return Response::json($customers);
    }

    public function doneComment(){
        $customers=DB::select("SELECT * from(
                        SELECT * from(
                        SELECT * from(
                        SELECT distinct crm_alarm.factorId,state,a.comment,a.TimeStamp,assesId,adminId FROM CRM.dbo.crm_alarm
                        JOIN (SELECT comment,factorId,TimeStamp,id as assesId FROM CRM.dbo.crm_assesment)a on crm_alarm.factorId=a.factorId)b
                        JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHDS)c on c.SerialNoHDS=b.factorId)d
                        JOIN (SELECT PSN,Name FROM Shop.dbo.Peopels)e on e.PSN=d.CustomerSn)f
                        JOIN (select id,name as AdminName,lastName from CRM.dbo.crm_admin)h on f.adminId=h.id
                        where factorId not in (SELECT factorId FROM CRM.dbo.crm_alarm where state=0)");
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n";   
                    }
                }
                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;
            }
        return view ("customer.doneComment",['customers'=>$customers]);
    }
    public function searchDoneAssesByDate(Request $request)
    {
        $fristDate=Jalalian::fromFormat('Y/m/d', $request->get("firstDate"))->toCarbon();
        $secondDate=Jalalian::fromFormat('Y/m/d', $request->get("secondDate"))->toCarbon();
        $customers=DB::select("SELECT * from(
                        SELECT * from(
                        SELECT * from(
                        SELECT distinct crm_alarm.factorId,state,a.comment,a.TimeStamp FROM CRM.dbo.crm_alarm
                        JOIN (SELECT comment,factorId,TimeStamp FROM CRM.dbo.crm_assesment)a on crm_alarm.factorId=a.factorId)b
                        JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHDS)c on c.SerialNoHDS=b.factorId)d
                        JOIN (SELECT PSN,Name FROM Shop.dbo.Peopels)e on e.PSN=d.CustomerSn)f
                        JOIN (SELECT PhoneStr,SnPeopel FROM Shop.dbo.PhoneDetail)g on g.SnPeopel=f.CustomerSn
                        where factorId not in (SELECT factorId FROM CRM.dbo.crm_alarm where state=0) and TimeStamp>='".$fristDate."' and TimeStamp<='".$secondDate."'");

        return Response::json($customers);
    }
    public function setCommentProperty(Request $request)
    {
        $csn=$request->get('csn');
        $comment=$request->get("comment");
        DB::table("CRM.dbo.crm_customerProperties")->where('customerId',$csn)->update(['comment'=>"".$comment.""]);
        $comment=DB::table("CRM.dbo.crm_customerProperties")->where('customerId',$csn)->select("comment")->get();
        return Response::json($comment);
    }
    public function customerDashboard(Request $request)
    {
        $psn=$request->get("csn");
        $adminId=Session::get('asn');
        $customers=DB::select("SELECT * from(
            SELECT * from(         
            SELECT COUNT(Shop.dbo.FactorHDS.SerialNoHDS)as countFactor,CustomerSn FROM Shop.dbo.FactorHDS where FactorHDS.FactType=3  group by CustomerSn)a
            right join (SELECT comment,customerId FROM CRM.dbo.crm_customerProperties)b on a.CustomerSn=b.customerId)c
            right join (SELECT PSN,Name,GroupCode,CompanyNo,peopeladdress FROM Shop.dbo.Peopels)f on c.customerId=f.PSN
            where f.CompanyNo=5 AND f.GroupCode IN ( ".implode(",",Session::get("groups")).") AND f.PSN=".$psn);
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n"; 
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }
        $exactCustomer=$customers[0];
        $factors=DB::select("SELECT * FROM Shop.dbo.FactorHDS WHERE FactType=3 AND CustomerSn=".$psn." order by FactDate desc");
        $returnedFactors=DB::select("SELECT * FROM Shop.dbo.FactorHDS WHERE FactType=4 AND CustomerSn=".$psn);
        $GoodsDetail=DB::select("SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood from(
            SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood FROM Shop.dbo.FactorHDS
            JOIN Shop.dbo.FactorBYS on FactorHDS.SerialNoHDS=FactorBYS.SnFact
            where FactorHDS.CustomerSn=".$psn.")g group by SnGood)c
            JOIN (SELECT * FROM Shop.dbo.PubGoods)d on d.GoodSn=c.SnGood");
        $basketOrders=DB::select("SELECT orderStar.TimeStamp,PubGoods.GoodName,orderStar.Amount,orderStar.Fi FROM newStarfood.dbo.FactorStar join newStarfood.dbo.orderStar on FactorStar.SnOrder=orderStar.SnHDS
                                    join Shop.dbo.PubGoods on orderStar.SnGood=PubGoods.GoodSn  where orderStatus=0 and CustomerSn=".$psn);
        $comments=DB::select("SELECT  crm_comment.newComment,crm_comment.nexComment,crm_comment.TimeStamp,customerId,adminId,specifiedDate,doneState,crm_comment.id FROM CRM.dbo.crm_comment JOIN CRM.dbo.crm_workList ON crm_comment.id=crm_workList.commentId  WHERE customerId=".$psn);
        $specialComment=DB::table("CRM.dbo.crm_customerProperties")->where("customerId",$psn)->select("comment")->get();
        $assesments=DB::select("SELECT crm_assesment.comment,crm_assesment.factorId,crm_assesment.TimeStamp,crm_assesment.shipmentProblem,crm_assesment.driverBehavior FROM CRM.dbo.crm_assesment
        join Shop.dbo.FactorHDS on crm_assesment.factorId=FactorHDS.SerialNoHDS join Shop.dbo.Peopels on Peopels.PSN=FactorHDS.CustomerSn where PSN=".$psn);
        $loginInfo=DB::table("NewStarfood.dbo.star_customerTrack")->where("customerId",$psn)->get();
        return Response::json([$exactCustomer,$factors,$GoodsDetail,$basketOrders,$comments,$specialComment,$assesments,$returnedFactors,$loginInfo]);
    }
    public function viewReturnComment(Request $request)
    {
        $customerId=$request->get("csn");
        $comments=DB::select("SELECT * FROM CRM.dbo.crm_returnCustomer where returnState=1 and customerId=".$customerId);
        $comment=$comments[0]->returnWhy;
        return Response::json($comment);
    }
    public function addComment(Request $request)
    {
        $adminId=Session::get("asn");
        $todayDate=Carbon::now()->format('Y-m-d');
        $firstComment=$request->get("firstComment");
        $secondComment=$request->get("secondComment");
        $nextDate=$request->get("nextDate");
        $callType=$request->get("callType");
        $MNMID=$request->get("mantagheh");
        $customerId=$request->get("customerIdForComment");
        $doneCommentId=0;
        $doneComments=DB::table("CRM.dbo.crm_comment")->join('CRM.dbo.crm_workList',"crm_workList.commentId","=","crm_comment.id")->where('customerId',$customerId)->where('crm_workList.doneState',0)->select('commentId')->get();
        foreach ($doneComments as $ids) {
            $doneCommentId=$ids->commentId;
        }
        $result=DB::table("CRM.dbo.crm_comment")->insert(['newComment'=>"".$firstComment."",'nexComment'=>"".$secondComment."",'customerId'=>$customerId,'adminId'=>$adminId,'callType'=>$callType]);
        $maxCommentId=DB::table("CRM.dbo.crm_comment")->where('customerId',$customerId)->max('id');
        if($result){
            $specifiedDate=\Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $nextDate)->toCarbon();
            $resultToWorkList=DB::table('CRM.dbo.crm_workList')->insert(['commentId'=>$maxCommentId,'specifiedDate'=>$specifiedDate,'doneState'=>0]);
            DB::table('CRM.dbo.crm_workList')->where('commentId',$doneCommentId)->update(['doneState'=>1]);
        }
        $comments=DB::select("SELECT * FROM CRM.dbo.crm_comment JOIN CRM.dbo.crm_workList ON CRM.dbo.crm_comment.id=CRM.dbo.crm_workList.commentId  WHERE customerId=".$customerId);
        $customers;
        if($MNMID!=0){
            $customers=DB::select("SELECT * FROM(
                            SELECT * FROM(SELECT * FROM(
                            SELECT * FROM(
                            SELECT PSN,Name,GroupCode,PCode,admin_id,peopeladdress,returnState,SnMantagheh FROM Shop.dbo.Peopels JOIN (SELECT * FROM CRM.dbo.crm_customer_added)a ON Peopels.PSN=a.customer_id)b
                            where  b.admin_id=".$adminId." AND b.returnState=0)e
                            JOIN(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f on e.SnMantagheh=f.SnMNM)g
                            left JOIN (SELECT  maxTime,customerId FROM(
                            SELECT customerId,Max(TimeStamp) as maxTime FROM(
                            SELECT crm_comment.TimeStamp,customerId FROM CRM.dbo.crm_comment
                            JOIN CRM.dbo.crm_workList 
                            on crm_comment.id=crm_workList.commentId where doneState=0 and crm_workList.specifiedDate>'".$todayDate."'
                            )a group by customerId)b)h on g.PSN=h.customerId)i WHERE i.SnMantagheh=".$MNMID."  order by maxTime asc");
           }else{
            $customers=DB::select("SELECT * FROM(
                            SELECT * FROM(SELECT * FROM(
                            SELECT * FROM(
                            SELECT PSN,Name,GroupCode,PCode,admin_id,peopeladdress,returnState,SnMantagheh FROM Shop.dbo.Peopels 
                            JOIN (SELECT * FROM CRM.dbo.crm_customer_added)a ON Peopels.PSN=a.customer_id)b
                            where  b.admin_id=".$adminId." AND b.returnState=0)e
                            JOIN(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f on e.SnMantagheh=f.SnMNM)g
                            left JOIN (SELECT  maxTime,customerId FROM(
                            SELECT customerId,Max(TimeStamp) as maxTime FROM(
                            SELECT crm_comment.TimeStamp,customerId FROM CRM.dbo.crm_comment
                            JOIN CRM.dbo.crm_workList 
                            on crm_comment.id=crm_workList.commentId where doneState=0 and crm_workList.specifiedDate>'".$todayDate."'
                            )a group by customerId)b)h on g.PSN=h.customerId)i order by maxTime asc");
          
           }
            foreach ($customers as $customer) {
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            $hamrah="";
            $sabit="";
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.="\n".$phone->PhoneStr;
                }else{
                    $hamrah.="\n".$phone->PhoneStr;
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }

        return Response::json([$comments,$customers]);
    }
    public function getFirstComment(Request $request)
    {
        $id=$request->get('commentId');
        $comment=DB::table("CRM.dbo.crm_comment")->where("id",$id)->select("newComment","nexComment")->first();
        return Response::json($comment);
    }
    public function getCustomerForTimeTable(Request $request)
    {
        $adminId=Session::get('asn');
        $dayDate=$request->get("dayDate");
        $customers=DB::select("SELECT DISTINCT Peopels.PSN,Peopels.PCode,Peopels.Name,Peopels.peopeladdress,SnMantagheh,NameRec
                        FROM Shop.dbo.Peopels 
                        JOIN CRM.dbo.crm_customer_added ON Shop.dbo.Peopels.PSN=CRM.dbo.crm_customer_added.customer_id
                        JOIN CRM.dbo.crm_comment ON Shop.dbo.Peopels.PSN=CRM.dbo.crm_comment.customerId 
                        JOIN CRM.dbo.crm_workList ON CRM.dbo.crm_comment.id=CRM.dbo.crm_workList.commentId
                        JOIN Shop.dbo.MNM ON SnMantagheh=MNM.SnMNM
                        where CRM.dbo.crm_customer_added.admin_id=".$adminId." and CRM.dbo.crm_customer_added.returnState=0 
                        and CRM.dbo.crm_workList.doneState=0 and CRM.dbo.crm_workList.specifiedDate='".$dayDate."'");
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n"; 
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }
        return Response::json($customers);
    }

    public function returnCustomer(Request $request)
    {
        $comment=$request->get("returnComment");
        $customerId=$request->get("returnCustomerId");
        $adminId=Session::get('asn');
        $result=DB::table("CRM.dbo.crm_returnCustomer")->insert(['returnState'=>1,'returnWhy'=>"".$comment."",'adminId'=>$adminId,'customerId'=>$customerId]);
        if($result){

            DB::update("UPDATE CRM.dbo.crm_customer_added set returnState=1,removedTime='".Carbon::now()."' where customer_id=".$customerId." and returnState=0 and admin_id=".$adminId);
            $countCustomers=DB::table("CRM.dbo.crm_customer_added")->where("admin_id",$adminId)->where("returnState",0)->count();
            if($countCustomers==0){
                DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(['emptyState'=>1]);
            }
        }
        $todayDate=Carbon::now()->format('Y-m-d');
        $customers=DB::select("SELECT * FROM(
                        SELECT * FROM(SELECT * FROM(
                        SELECT * FROM(
                        SELECT * FROM Shop.dbo.Peopels 
                        JOIN (SELECT * FROM CRM.dbo.crm_customer_added)a ON Peopels.PSN=a.customer_id)b
                        where  b.admin_id=".$adminId." AND b.returnState=0)e
                        JOIN(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f on e.SnMantagheh=f.SnMNM)g
                        left JOIN (SELECT countComment,customerId 
                        FROM(SELECT customerId,count(id) as countComment 
                        FROM(SELECT crm_comment.id,customerId FROM CRM.dbo.crm_comment
                        JOIN CRM.dbo.crm_workList 
                        on crm_comment.id=crm_workList.commentId where doneState=0 and crm_workList.specifiedDate>'".$todayDate."'
                        )a group by customerId)b)h on g.PSN=h.customerId)i order by countComment asc");
        foreach ($customers as $customer) {
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            $hamrah="";
            $sabit="";
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.="\n".$phone->PhoneStr;
                }else{
                    $hamrah.="\n".$phone->PhoneStr;
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }
        return Response::json($customers);
    }
    public function getFactorDetail(Request $request)
    {
        $fsn=$request->get("FactorSn");
        $orders=DB::select("SELECT FactorBYS.Price AS goodPrice, *  FROM Shop.dbo.FactorHDS
                            JOIN Shop.dbo.FactorBYS ON FactorHDS.SerialNoHDS=FactorBYS.SnFact 
                            JOIN Shop.dbo.Peopels ON FactorHDS.CustomerSn=Peopels.PSN
                            JOIN Shop.dbo.PubGoods ON FactorBYS.SnGood=PubGoods.GoodSn 
                            JOIN Shop.dbo.PUBGoodUnits ON PUBGoodUnits.USN=PubGoods.DefaultUnit
                            JOIN CRM.dbo.crm_customer_added on Peopels.PSN=customer_id join CRM.dbo.crm_admin on admin_id=crm_admin.id
                            where returnState=0 and FactorHDS.SerialNoHDS=".$fsn);
        
        foreach ($orders as $order) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$order->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n"; 
                }
            }
            $order->sabit=$sabit;
            $order->hamrah=$hamrah;
        }
        return Response::json($orders);
    }
    public function addAssessment(Request $request){
        $yesterdayOfWeek = Jalalian::fromCarbon(Carbon::yesterday())->getDayOfWeek();
        $yesterday;
        if($yesterdayOfWeek==6){
            $yesterday = Jalalian::fromCarbon(Carbon::yesterday()->subDays(1))->format('Y/m/d');
        }else{
            $yesterday = Jalalian::fromCarbon(Carbon::yesterday())->format('Y/m/d');
        }
        $adminId=Session::get('asn');
        $shipmentProblem=$request->get("shipmentProblem");
        $behavior=$request->get("behavior");
        $customerId=$request->get("customerId");
        $adminId=Session::get("asn");
        $fsn=$request->get('factorId');
        $comment=$request->get("comment");
        $alarmDate=$request->get("alarmDate");
        $result=DB::table("CRM.dbo.crm_assesment")->insert(['adminId'=>$adminId,'shipmentProblem'=>$shipmentProblem,'driverBehavior'=>"".$behavior."",'comment'=>"".$comment."",'factorId'=>$fsn]);
        DB::table("CRM.dbo.crm_alarm")->insert(['comment'=>"".$comment."",'adminId'=>$adminId,'state'=>0,'alarmDate'=>"".$alarmDate."",'factorId'=>$fsn]);
        $customers = DB::select("SELECT * from(
                        SELECT NetPriceHDS as TotalPriceHDS,* FROM (
                        SELECT maxFactorId as SerialNoHDS,CustomerSn,NetPriceHDS,FactNo from
                        (SELECT MAX(SerialNoHDS) as maxFactorId FROM Shop.dbo.FactorHDS where FactType=3 and FactorHDS.FactDate='".$yesterday."' group by FactorHDS.CustomerSn)a
                        JOIN Shop.dbo.FactorHDS on a.maxFactorId=FactorHDS.SerialNoHDS)d
                        JOIN (SELECT Name,PSN,PCode FROM Shop.dbo.Peopels)e on d.CustomerSn=e.PSN)f
                         where f.SerialNoHDS not in (SELECT factorId FROM CRM.dbo.crm_alarm) ");
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n";   
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }
        return Response::json($customers);
    }

    public function newCustomer(){
        $adminId=Session::get('asn');
        $customers=DB::select("SELECT DISTINCT Peopels.PSN,Peopels.PCode,Peopels.Name,Peopels.GroupCode,Peopels.TimeStamp,Peopels.peopeladdress,SnMantagheh,NameRec,crm_admin.name as adminName ,crm_admin.lastName as adminLastName
        FROM Shop.dbo.Peopels
        JOIN Shop.dbo.MNM ON SnMantagheh=MNM.SnMNM
        JOIN CRM.dbo.crm_inserted_customers on Peopels.PSN=crm_inserted_customers.customerId
        join CRM.dbo.crm_admin on crm_admin.id=crm_inserted_customers.adminId
        where GroupCode=314  and PSN NOT IN(SELECT customer_id from CRM.dbo.crm_customer_added)");
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n";   
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }
        $cities=DB::table("Shop.dbo.MNM")->where("FatherMNM",79)->get();
        $admins=DB::table("CRM.dbo.crm_admin")->where('adminType',2)->get();
        $mantagheh=DB::table("Shop.dbo.MNM")->where("FatherMNM",80)->get();
        return View('customer.newCustomer',['customers'=>$customers,'cities'=>$cities,'admins'=>$admins,'mantagheh'=>$mantagheh]);
    }

    public function myCustomers(){
        $adminId=Session::get('asn');
        $customers=DB::select("SELECT DISTINCT Peopels.PSN,Peopels.PCode,Peopels.Name,Peopels.GroupCode,Peopels.TimeStamp,Peopels.peopeladdress,SnMantagheh,NameRec,crm_admin.name as adminName ,crm_admin.lastName as adminLastName
                            FROM Shop.dbo.Peopels
                            JOIN Shop.dbo.MNM ON SnMantagheh=MNM.SnMNM
                            JOIN CRM.dbo.crm_inserted_customers on Peopels.PSN=crm_inserted_customers.customerId
                            JOIN CRM.dbo.crm_admin on crm_admin.id=crm_inserted_customers.adminId
                            WHERE GroupCode=314 and crm_inserted_customers.adminId=".Session::get("asn"));
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n";   
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }
        $cities=DB::table("Shop.dbo.MNM")->where("FatherMNM",79)->get();
        $admins=DB::table("CRM.dbo.crm_admin")->where('adminType',2)->orwhere('adminType',3)->get();
        $mantagheh=DB::table("Shop.dbo.MNM")->where("FatherMNM",80)->get();
        return View('customer.newCustomer',['customers'=>$customers,'cities'=>$cities,'admins'=>$admins,'mantagheh'=>$mantagheh]);
    }

    public function getCustomerInfo(Request $request)
    {
        $csn=$request->get("csn");
        $exactCustomer=DB::select("SELECT * FROM Shop.dbo.Peopels JOIN Shop.dbo.MNM on Peopels.SnMantagheh=MNM.SnMNM 
        join NewStarfood.dbo.star_CustomerPass on star_CustomerPass.customerId=Peopels.PSN where Peopels.PSN=".$csn);

        $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$csn)->get();
        $hamrah="";
        $sabit="";
        foreach ($phones as $phone) {

            if($phone->PhoneType==1){
                $hamrah.=$phone->PhoneStr;   
            }

            if($phone->PhoneType==2){
                $sabit.=$phone->PhoneStr;   
            }
        }
        $phones[0]->hamrah=$hamrah;
        $phones[0]->sabit=$sabit;
        $mantagheh=DB::table("Shop.dbo.MNM")->where("FatherMNM",$exactCustomer[0]->SnNahiyeh)->get();
        $cities=DB::table("Shop.dbo.MNM")->where("FatherMNM",79)->get();
        return Response::json([$exactCustomer[0],$phones,$cities,$mantagheh]);
    }
    public function editCustomer(Request $request)
    {
        $customerID=$request->post("customerId");
        $password=$request->post("password");
        $hamrah=$request->post("mobilePhone");
        $sabit=$request->post("sabitPhone");
        $picture=$request->file('picture');

        $groupCode=314;
        $pCode=$request->post("PCode");
        $name=$request->post("name");
        $description="";
        // $timeStamp=$request->post("timeStamp");
        $peopeladdress=$request->post("peopeladdress");
        $peopelEghtesadiCode="";
        $sabtNoOrMeliCode=" ";
        $companyCustName="";
        $printName="";
        $snMasir=79;
        $snNahiyeh=$request->post("snNahiyeh");
        $snMantagheh=$request->post("snMantagheh");
        $latPers=0;
        $lonPers=0;

        DB::table("Shop.dbo.Peopels")->where('PSN',$customerID)->update(
        [
        'GroupCode'=>$groupCode
        ,'PCode'=>$pCode
        ,'Name'=>"$name"
        ,'Description'=>"$description"
        ,'CustomerIs'=>1
        ,'FiscalYear'=>1399
        ,'peopeladdress'=>"$peopeladdress"
        ,'PeopelEghtesadiCode'=>"$peopelEghtesadiCode"
        ,'SabtNoOrMeliCode'=>"$sabtNoOrMeliCode"
        ,'CompanyCustName'=>"$companyCustName"
        ,'PrintName'=>"$printName"
        ,'SnMasir'=>$snMasir
        ,'SnNahiyeh'=>$snNahiyeh
        ,'SnMantagheh'=>$snMantagheh
        ,'LatPers'=>$latPers
        ,'LonPers'=>$lonPers]);

        

        if($hamrah){
        DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customerID)->where('PhoneType',2)->update
           (['PhoneStr'=>"$hamrah"]);
        }

        if($sabit){
            DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customerID)->where('PhoneType',1)->update
           (['PhoneStr'=>"$sabit"]); 
        }
        if($hamrah){
            DB::table("NewStarfood.dbo.star_CustomerPass")->where('customerId',$customerID)->update
            (['customerPss'=>"$password"
            ,'userName'=>"$hamrah"]);
        }else{
            DB::table("NewStarfood.dbo.star_CustomerPass")->where('customerId',$customerID)->update
            (['customerPss'=>"$password"
            ,'userName'=>"$sabit"]);
        }
        if($picture){
        $fileName=$customerID.".jpg";
        $picture->move("resources/assets/images/customers/",$fileName);
        }
        return redirect("/newCustomer");
    }

    public function addCustomer(Request $request)
    {
        $password=$request->post("password");
        $hamrah=$request->post("mobilePhone");
        $sabit=$request->post("sabitPhone");
        $picture=$request->file('picture');

        $groupCode=314;
        $pCode=$request->post("PCode");
        $name=$request->post("name");
        $description="";
        // $timeStamp=$request->post("timeStamp");
        $peopeladdress=$request->post("peopeladdress");
        $peopelEghtesadiCode="";
        $sabtNoOrMeliCode=" ";
        $companyCustName="";
        $printName="";
        $snMasir=79;
        $snNahiyeh=$request->post("snNahiyeh");
        $snMantagheh=$request->post("snMantagheh");
        $latPers=0;
        $lonPers=0;

        DB::table("Shop.dbo.Peopels")->insert(
        ['CompanyNo'=>5
        ,'GroupCode'=>$groupCode
        ,'PCode'=>$pCode
        ,'Name'=>"$name"
        ,'Description'=>"$description"
        ,'CustomerIs'=>1
        ,'SellerIs'=>0
        ,'Status'=>0
        ,'Worker'=>0
        ,'FirstExistans'=>0
        ,'FirstStatus'=>0
        ,'FiscalYear'=>1399
        ,'Tel'=>""
        ,'peopeladdress'=>"$peopeladdress"
        ,'IsActive'=>1
        ,'PayKind'=>1
        ,'SharePercent'=>0
        ,'SaleLevel'=>0
        ,'AccBankNo'=>""
        ,'AccBankType'=>0
        ,'PeopelEghtesadiCode'=>"$peopelEghtesadiCode"
        ,'SabtNoOrMeliCode'=>"$sabtNoOrMeliCode"
        ,'SnProvince'=>0
        ,'SnCity1'=>0
        ,'SnCity2'=>0
        ,'PeopelPostalCode'=>""
        ,'FaxNo'=>""
        ,'Tel2'=>""
        ,'CompanyCustName'=>"$companyCustName"
        ,'E_MailCust'=>""
        ,'WebSiteCust'=>""
        ,'MobileCust'=>""
        ,'SnGoodGroup'=>0
        ,'TypePorsant'=>0
        ,'PercentPorsant'=>0
        ,'ColorStatus'=>0
        ,'EtebarCheque'=>0
        ,'EtebarNaghd'=>0
        ,'BarBari'=>""
        ,'Moarref'=>""
        ,'ShirFi'=>0
        ,'MalekiyatType'=>0
        ,'IsJavaz'=>0
        ,'JavazNo'=>""
        ,'JavazDate'=>"00/00/00"
        ,'JavazAddress'=>""
        ,'SabegheFaaliyat'=>""
        ,'HamkaryType'=>0
        ,'SherakatType'=>""
        ,'PrintName'=>"$printName"
        ,'IsExport'=>0
        ,'LastTimeTasviyeh'=>0
        ,'SnGroupSecond'=>0
        ,'BirthDate2'=>"00/00/00"
        ,'SexType2'=>0
        ,'Marriage'=>0
        ,'BloodGroup'=>0
        ,'PeopelLevel'=>0
        ,'Add_Update'=>0
        ,'PeriodVisit'=>0
        ,'SnMasir'=>$snMasir
        ,'SnNahiyeh'=>$snNahiyeh
        ,'SnMantagheh'=>$snMantagheh
        ,'LatPers'=>$latPers
        ,'LonPers'=>$lonPers
        ,'LastDateFact'=>"00/00/00"
        ,'NextDateFact'=>"00/00/00"
        ,'SnSeller'=>0
        ,'PeopelType'=>0
        ,'ControlEtebarType'=>3
        ,'SupportType'=>0
        ,'SupportPrice'=>0
        ,'SupportFiscalYear'=>0]);

        $lastCustomerID=DB::table("Shop.dbo.Peopels")->where("GroupCode",314)->max("PSN");

        if($hamrah){
        DB::table("Shop.dbo.PhoneDetail")->insert
           (['CompanyNo'=>0
           ,'SnPeopel'=>$lastCustomerID
           ,'RecType'=>2
           ,'PhoneStr'=>"$hamrah"
           ,'PhoneType'=>2
           ,'IsExport'=>0]);
        }

        if($sabit){
            DB::table("Shop.dbo.PhoneDetail")->insert
            (['CompanyNo'=>0
            ,'SnPeopel'=>$lastCustomerID
            ,'RecType'=>2
            ,'PhoneStr'=>"$sabit"
            ,'PhoneType'=>1
            ,'IsExport'=>0]); 
        }
        if($hamrah){
            DB::table("NewStarfood.dbo.star_CustomerPass")->insert
            (['customerId'=>$lastCustomerID
            ,'customerPss'=>"$password"
            ,'userName'=>"$hamrah"]);
        }else{
            DB::table("NewStarfood.dbo.star_CustomerPass")->insert
            (['customerId'=>$lastCustomerID
            ,'customerPss'=>"$password"
            ,'userName'=>"$hamrah"]);
        }
        if($picture){
            $fileName=$lastCustomerID.".jpg";
            $picture->move("resources/assets/images/customers/",$fileName);
        }
        DB::table("CRM.dbo.crm_inserted_customers")->insert(
        ['adminId'=>Session::get('asn')
        ,'customerId'=>$lastCustomerID]);
        return redirect("/myCustomers");
    }

    public function searchMap(Request $request)
    {
        $locations=DB::select("SELECT * FROM Shop.dbo.Peopels where GroupCode IN ( ".implode(",",Session::get("groups")).")
                                and PSN in(SELECT distinct customer_id FROM CRM.dbo.crm_customer_added)");
        return Response::json($locations);
    }
    public function searchMapByFactor(Request $request)
    {
        $fsn=$request->get("fsn");
        $locations=DB::select("SELECT LatPers,LonPers FROM Shop.dbo.Peopels where Peopels.PSN in(".$fsn.")");
        return Response::json($locations);
    }

    public function customerLocation(Request $request) {
        $customers=DB::select("SELECT * from(
                        SELECT * from(
                        SELECT Name,PSN,PCode,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)b 
                        JOIN (SELECT * FROM Shop.dbo.MNM)c on b.SnMantagheh=c.SnMNM)d  where PSN in (
                        SELECT distinct customer_id FROM CRM.dbo.crm_customer_added)");
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n"; 
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }

        return view("customer.customerLocation", ["allCustomers"=>$customers]);
    }
    
public function searchCustomerByName(Request $request)
{
    $searchTerm=trim($request->get("searchTerm"));
    $adminId=Session::get("asn");
    $todayDate=Carbon::now()->format("Y-m-d");
    $customers=DB::select("SELECT * FROM(
                    SELECT * FROM(SELECT * FROM(
                    SELECT * FROM(
                    SELECT * FROM Shop.dbo.Peopels JOIN (SELECT * FROM CRM.dbo.crm_customer_added)a ON Peopels.PSN=a.customer_id)b
                    where  b.admin_id=".$adminId." AND b.returnState=0)e
                    JOIN(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f on e.SnMantagheh=f.SnMNM)g
                    left JOIN (SELECT countComment,customerId FROM(
                    SELECT customerId,count(id) as countComment FROM(
                    SELECT crm_comment.id,customerId FROM CRM.dbo.crm_comment
                    JOIN CRM.dbo.crm_workList 
                    on crm_comment.id=crm_workList.commentId where doneState=0 and crm_workList.specifiedDate>'".$todayDate."'
                    )a group by customerId)b)h on g.PSN=h.customerId)i
                    where peopeladdress like '%".$searchTerm."%' or Name like '%".$searchTerm."%' order by countComment asc");
    foreach ($customers as $customer) {
        $sabit="";
        $hamrah="";
        $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
        foreach ($phones as $phone) {
            if($phone->PhoneType==1){
                $sabit.=$phone->PhoneStr."\n";
            }else{
                $hamrah.=$phone->PhoneStr."\n"; 
            }
        }
        $customer->sabit=$sabit;
        $customer->hamrah=$hamrah;
    }
    return Response::json($customers);
}
public function searchCustomerByMantagheh(Request $request)
{
    $searchTerm=trim($request->get("searchTerm"));
    $adminId=Session::get("asn");
    $todayDate=Carbon::now()->format("Y-m-d");
    $customers=DB::select("SELECT * FROM(
                    SELECT * FROM(SELECT * FROM(
                    SELECT * FROM(
                    SELECT * FROM Shop.dbo.Peopels JOIN (SELECT * FROM CRM.dbo.crm_customer_added)a ON Peopels.PSN=a.customer_id)b
                    where  b.admin_id=".$adminId." AND b.returnState=0)e
                    JOIN(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f on e.SnMantagheh=f.SnMNM)g
                    left JOIN (SELECT  maxTime,customerId FROM(
                    SELECT customerId,Max(TimeStamp) as maxTime FROM(
                    SELECT crm_comment.TimeStamp,customerId FROM CRM.dbo.crm_comment
                    JOIN CRM.dbo.crm_workList 
                    on crm_comment.id=crm_workList.commentId where doneState=0 and crm_workList.specifiedDate>'".$todayDate."'
                    )a group by customerId)b)h on g.PSN=h.customerId)i
                    where SnMNM=".$searchTerm." order by maxTime asc");
    foreach ($customers as $customer) {
        $sabit="";
        $hamrah="";
        $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
        foreach ($phones as $phone) {
            if($phone->PhoneType==1){
                $sabit.=$phone->PhoneStr."\n";
            }else{
                $hamrah.=$phone->PhoneStr."\n"; 
            }
        }
        $customer->sabit=$sabit;
        $customer->hamrah=$hamrah;
    }
    return Response::json($customers);
}
public function searchCustomerByCode(Request $request)
{
    $searchTerm=trim($request->get("searchTerm"));
    $adminId=Session::get("asn");
    $todayDate=Carbon::now()->format("Y-m-d");
    $customers=DB::select("SELECT * FROM(
                    SELECT * FROM(SELECT * FROM(
                    SELECT * FROM(
                    SELECT * FROM Shop.dbo.Peopels JOIN (SELECT * FROM CRM.dbo.crm_customer_added)a ON Peopels.PSN=a.customer_id)b
                    where  b.admin_id=".$adminId." AND b.returnState=0)e
                    JOIN(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f on e.SnMantagheh=f.SnMNM)g
                    left JOIN (SELECT countComment,customerId FROM(
                    SELECT customerId,count(id) as countComment FROM(
                    SELECT crm_comment.id,customerId FROM CRM.dbo.crm_comment
                    JOIN CRM.dbo.crm_workList 
                    on crm_comment.id=crm_workList.commentId where doneState=0 and crm_workList.specifiedDate>'".$todayDate."'
                    )a group by customerId)b)h on g.PSN=h.customerId)i
                            where PCode like '%".$searchTerm."%' order by countComment asc");
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n"; 
                }
            }
            $customer->sabit=$sabit;
            $customer->hamrah=$hamrah;
        }
    return Response::json($customers);
}
public function searchReferedPCode(Request $request)
{
    $searchTerm=$request->get("searchTerm");
    $customers=DB::table("Shop.dbo.Peopels")
                    ->join("CRM.dbo.crm_returnCustomer","Peopels.PSN","=","crm_returnCustomer.customerId")
                    ->join("CRM.dbo.crm_admin","crm_returnCustomer.adminId","=","crm_admin.id")
                    ->where("crm_returnCustomer.returnState",1)
                    ->where("Peopels.PCode", "like","%$searchTerm%")
                    ->select("Peopels.PSN","Peopels.PCode","Peopels.Name","crm_admin.name as adminName","crm_admin.lastName as adminLastName","Peopels.peopeladdress","crm_returnCustomer.adminId")
                    ->get();
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                        $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n"; 
                    }
                }
                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;
            }

    return Response::json($customers);
}
public function orderByNameCode(Request $request)
{
    $adminId=Session::get('asn');
    $todayDate=Carbon::now()->format("Y-m-d");
    $orederType=$request->get("searchTerm");
    if($orederType==1){
        $customers=DB::select("SELECT * FROM(
                        SELECT * FROM(SELECT * FROM(
                        SELECT * FROM(
                        SELECT * FROM Shop.dbo.Peopels JOIN (SELECT * FROM CRM.dbo.crm_customer_added)a ON Peopels.PSN=a.customer_id)b
                        where  b.admin_id=".$adminId." AND b.returnState=0)e
                        JOIN(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f on e.SnMantagheh=f.SnMNM)g
                        left JOIN (SELECT countComment,customerId FROM(
                        SELECT customerId,count(id) as countComment FROM(
                        SELECT crm_comment.id,customerId FROM CRM.dbo.crm_comment
                        JOIN CRM.dbo.crm_workList 
                        on crm_comment.id=crm_workList.commentId where doneState=0 and crm_workList.specifiedDate>'".$todayDate."'
                        )a group by customerId)b)h on g.PSN=h.customerId)i
                        order by Name asc");
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                        $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n"; 
                    }
                }
                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;
            }
        return Response::json($customers);    
    }else{
        $customers=DB::select("SELECT * FROM(
                        SELECT * FROM(SELECT * FROM(
                        SELECT * FROM(
                        SELECT * FROM Shop.dbo.Peopels JOIN (SELECT * FROM CRM.dbo.crm_customer_added)a ON Peopels.PSN=a.customer_id)b
                        where  b.admin_id=".$adminId." AND b.returnState=0)e
                        JOIN(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f on e.SnMantagheh=f.SnMNM)g
                        left JOIN (SELECT countComment,customerId FROM(
                        SELECT customerId,count(id) as countComment FROM(
                        SELECT crm_comment.id,customerId FROM CRM.dbo.crm_comment
                        JOIN CRM.dbo.crm_workList 
                        on crm_comment.id=crm_workList.commentId where doneState=0 and crm_workList.specifiedDate>'".$todayDate."'
                        )a group by customerId)b)h on g.PSN=h.customerId)i
                        ORDER BY PCode ASC");
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                        $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n"; 
                    }
                }
                $customer->sabit=$sabit;
                $customer->hamrah=$hamrah;
            }
        return Response::json($customers); 
    }

}

public function searchCustomerAalarmName(Request $request)
{
    $searchTerm=$request->get("searchTerm");
    $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
    $customers=DB::select("SELECT * FROM (
        SELECT * FROM (
        SELECT * FROM (
        SELECT * FROM (
        SELECT * FROM (
        SELECT DISTINCT * FROM (
        SELECT * FROM CRM.dbo.crm_alarm)a
        JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
        JOIN (SELECT id AS admin_Id,name AS AdminName,lastName FROM CRM.dbo.crm_admin)d ON c.adminId=d.Admin_Id)e
        JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=e.factorId)g
        JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
        JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
        WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0 and Name like '%".$searchTerm."%'
        and PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
        ");
        
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n"; 
                }
            }
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::parse($customer->TimeStamp));
            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
            $customer->hamrah=$hamrah;
            $customer->sabit=$sabit;
        }
    return Response::json($customers);
}

public function searchCustomerAalarmCode(Request $request)
{
    $searchTerm=$request->get("searchTerm");
    $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
    $customers=DB::select("SELECT * FROM (
        SELECT * FROM (
        SELECT * FROM (
        SELECT * FROM (
        SELECT * FROM (
        SELECT DISTINCT * FROM (
        SELECT * FROM CRM.dbo.crm_alarm)a
        JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
        JOIN (SELECT id AS admin_Id,name AS AdminName,lastName FROM CRM.dbo.crm_admin)d ON c.adminId=d.Admin_Id)e
        JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=e.factorId)g
        JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,PCode FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
        JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
        WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0 and PCode like '%".$searchTerm."%'
        and PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
        ");
        
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n"; 
                }
            }
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::parse($customer->TimeStamp));
            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
            $customer->hamrah=$hamrah;
            $customer->sabit=$sabit;
        }

    return Response::json($customers);
}

public function searchCustomerAalarmActive(Request $request)
{
    $searchTerm=$request->get("searchTerm");
    $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
    if($searchTerm==1){
        $customers=DB::select("SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT DISTINCT * FROM (
            SELECT * FROM CRM.dbo.crm_alarm)a
            JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
            JOIN (SELECT id AS admin_Id,name AS AdminName,lastName FROM CRM.dbo.crm_admin)d ON c.adminId=d.Admin_Id)e
            JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=e.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,PCode FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
            WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0 and PSN not in(
            SELECT customerId FROM CRM.dbo.crm_inactiveCustomer where state=1
            )
            and PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
            ");
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                        $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n"; 
                    }
                }
                $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::parse($customer->TimeStamp));
                $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
                $customer->hamrah=$hamrah;
                $customer->sabit=$sabit;
            }
        return Response::json($customers);
        }
    if($searchTerm==2){
        $customers=DB::select("SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT DISTINCT * FROM (
            SELECT * FROM CRM.dbo.crm_alarm)a
            JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
            JOIN (SELECT id AS admin_Id,name AS AdminName,lastName FROM CRM.dbo.crm_admin)d ON c.adminId=d.Admin_Id)e
            JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=e.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,PCode FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
            WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0 and PSN in(
            SELECT customerId FROM CRM.dbo.crm_inactiveCustomer where state=1
            )
            and PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)");
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                        $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n"; 
                    }
                }
                $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::parse($customer->TimeStamp));
                $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
                $customer->hamrah=$hamrah;
                $customer->sabit=$sabit;
            }
        return Response::json($customers);
            }
    if($searchTerm==0){
        $customers=DB::select("SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT DISTINCT * FROM (
            SELECT * FROM CRM.dbo.crm_alarm)a
            JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
            JOIN (SELECT id AS admin_Id,name AS AdminName,lastName FROM CRM.dbo.crm_admin)d ON c.adminId=d.Admin_Id)e
            JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=e.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,PCode FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
            WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0
            and PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
            ");
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                        $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n"; 
                    }
                }
                $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::parse($customer->TimeStamp));
                $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
                $customer->hamrah=$hamrah;
                $customer->sabit=$sabit;
            }
        return Response::json($customers);
            }
}
public function searchCustomerAalarmLocation(Request $request)
{
    $searchTerm=$request->get("searchTerm");
    $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
    if($searchTerm==1){
        $customers=DB::select("SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT DISTINCT * FROM (
            SELECT * FROM CRM.dbo.crm_alarm)a
            JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
            JOIN (SELECT id AS admin_Id,name AS AdminName,lastName FROM CRM.dbo.crm_admin)d ON c.adminId=d.Admin_Id)e
            JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=e.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,PCode,LatPers,LonPers FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
            WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0 and LatPers>0 and LonPers>0
            and PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
            ");
        
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n"; 
                }
            }
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::parse($customer->TimeStamp));
            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
            $customer->hamrah=$hamrah;
            $customer->sabit=$sabit;
        }
        return Response::json($customers);
    }
    if($searchTerm==2){
        $customers=DB::select("SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT DISTINCT * FROM (
            SELECT * FROM CRM.dbo.crm_alarm)a
            JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
            JOIN (SELECT id AS admin_Id,name AS AdminName,lastName FROM CRM.dbo.crm_admin)d ON c.adminId=d.Admin_Id)e
            JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=e.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,PCode,LatPers,LonPers FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
            WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0 and LatPers=0 and LonPers=0
            and PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
            ");
        
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n"; 
                }
            }
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::parse($customer->TimeStamp));
            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
            $customer->hamrah=$hamrah;
            $customer->sabit=$sabit;
        }
        return Response::json($customers);
    }
    if($searchTerm==0){
        $customers=DB::select("SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT DISTINCT * FROM (
            SELECT * FROM CRM.dbo.crm_alarm)a
            JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
            JOIN (SELECT id AS admin_Id,name AS AdminName,lastName FROM CRM.dbo.crm_admin)d ON c.adminId=d.Admin_Id)e
            JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=e.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,PCode,LatPers,LonPers FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
            WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0
            and PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)");
        
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n"; 
                }
            }
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::parse($customer->TimeStamp));
            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
            $customer->hamrah=$hamrah;
            $customer->sabit=$sabit;
        }
        return Response::json($customers);
    }
}
public function searchCustomerAalarmOrder(Request $request)
{
    $searchTerm=$request->get("searchTerm");
    $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
    if($searchTerm==0){
        $customers=DB::select("SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT DISTINCT * FROM (
            SELECT * FROM CRM.dbo.crm_alarm)a
            JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
            JOIN (SELECT id AS admin_Id,name AS AdminName,lastName FROM CRM.dbo.crm_admin)d ON c.adminId=d.Admin_Id)e
            JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=e.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,PCode,LatPers,LonPers FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
            WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0 
            and PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
            order by Name asc
            ");
                
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                        $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n"; 
                    }
                }
                $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::parse($customer->TimeStamp));
                $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
                $customer->hamrah=$hamrah;
                $customer->sabit=$sabit;
            }
        return Response::json($customers);
    }
    if($searchTerm==1){
        $customers=DB::select("SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT * FROM (
            SELECT DISTINCT * FROM (
            SELECT * FROM CRM.dbo.crm_alarm)a
            JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
            JOIN (SELECT id AS admin_Id,name AS AdminName,lastName FROM CRM.dbo.crm_admin)d ON c.adminId=d.Admin_Id)e
            JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=e.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,PCode,LatPers,LonPers FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
            WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0
            and PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0) order by PCode asc");
                
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::table("Shop.dbo.PhoneDetail")->where("SnPeopel",$customer->PSN)->get();
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                        $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n"; 
                    }
                }
                $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::parse($customer->TimeStamp));
                $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
                $customer->hamrah=$hamrah;
                $customer->sabit=$sabit;
            }
        return Response::json($customers);
    }
}
public function searchRegion(Request $request)
{
    $cityId=$request->get("cityId");
    $regions=DB::table("Shop.dbo.MNM")->where("FatherMNM",$cityId)->get();
    $regions=DB::select("SELECT * FROM Shop.dbo.MNM where FatherMNM=".$cityId." and SnMNM in(SELECT distinct SnMantagheh from(
                    SELECT PSN,SnMantagheh,returnState,admin_id from Shop.dbo.Peopels
                    JOIN (SELECT * from CRM.dbo.crm_customer_added)b on PSN=b.customer_id)c where returnState=0 and admin_id=".Session::get("asn").")");
    return Response::json($regions);
}

public function searchAssignRegion(Request $request)
{
    $cityId=$request->get("cityId");
    $regions=DB::table("Shop.dbo.MNM")->where("FatherMNM",$cityId)->get();
    return Response::json($regions);
}

public function tempRoute(Request $request)
{
    // $customers=DB::select("SELECT * FROM Shop.dbo.Peopels where GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5");
    // foreach ($customers as $customer) {
    //     DB::table("CRM.dbo.crm_customerProperties")->insert(['location'=>"","comment"=>"","customerId"=>$customer->PSN]);
        
    // }
    return "good";
}
public function searchAddedCustomerByNameMNM(Request $request)
{
    $asn=$request->get("asn");
    $name=$request->get("name");
    $customers=DB::select("SELECT * FROM (
                            SELECT * FROM Shop.dbo.Peopels where CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).")
                            and PSN in(SELECT distinct customer_id FROM CRM.dbo.crm_customer_added where admin_id=".$asn." and returnState=0))a
                            where Name like '%".$name."%'");
    return Response::json($customers);
}
public function searchCustomerByNameMNM(Request $request)
{
    $name=$request->get("name");
    $customers=DB::select("SELECT * FROM Shop.dbo.Peopels where Peopels.PSN 
                            not in (SELECT distinct customer_id FROM CRM.dbo.crm_customer_added
                            where customer_id not in(SELECT customerId FROM CRM.dbo.crm_inactiveCustomer where state=0) 
                            and customer_id not in(SELECT customerId
                            FROM CRM.dbo.crm_returnCustomer where returnState=0) and returnState=0)  
                            AND CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).") And Name!=''
                            and Name like '%".$name."%'");
    return Response::json($customers);
}

}
