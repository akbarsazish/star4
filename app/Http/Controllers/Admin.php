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
class Admin extends Controller
{
    public function index(Request $request)
    {
        $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where("crm_admin.adminType","!=",4)->where("crm_admin.adminType","!=",1)->where("crm_admin.adminType","!=",5)->where("crm_admin.adminType","!=",3)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","crm_admin.discription")->orderby("admintype")->get();
        $regions=DB::select("SELECT * FROM Shop.dbo.MNM WHERE CompanyNo=5 and SnMNM>82");
        $cities=DB::select("Select * FROM Shop.dbo.MNM WHERE  CompanyNo=5 and RecType=1 AND FatherMNM=79");
        return View('admin.assignCustomer',['admins'=>$admins,'regions'=>$regions,'cities'=>$cities]);
    }
    public function listKarbaran(Request $request)
    {
        $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","crm_admin.discription")->orderby("admintype")->get();
        $regions=DB::select("SELECT * FROM Shop.dbo.MNM WHERE CompanyNo=5 and SnMNM>82");
        $cities=DB::select("Select * FROM Shop.dbo.MNM WHERE  CompanyNo=5 and RecType=1 AND FatherMNM=79");
        return View('admin.listKarbaran',['admins'=>$admins,'regions'=>$regions,'cities'=>$cities]);
    }
    public function allCustomers(Request $request)
    {
        $customers=DB::select("SELECT TOP 20 * FROM(
                            SELECT * FROM(
                            SELECT * FROM(
                            SELECT * FROM(
                            SELECT PCode,PSN,Name,peopeladdress,SnMantagheh FROM Shop.dbo.Peopels
                            WHERE  PSN IN  (SELECT DISTINCT customer_id FROM CRM.dbo.crm_customer_added))b 
                            JOIN   (SELECT * FROM CRM.dbo.crm_customer_added)c ON b.PSN=c.customer_id)d )e
                            join(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f ON e.SnMantagheh=f.SnMNM)g");
        
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
        return View('admin.allCustomerList',['customers'=>$customers]);
    }

    public function searchAllCustomerByName(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $customers=DB::select("SELECT * FROM(
                            SELECT * FROM(
                            SELECT * FROM(
                            SELECT * FROM(
                            SELECT PCode,PSN,Name,peopeladdress,SnMantagheh FROM Shop.dbo.Peopels
                            WHERE  PSN IN  (SELECT DISTINCT customer_id FROM CRM.dbo.crm_customer_added))b 
                            JOIN   (SELECT * FROM CRM.dbo.crm_customer_added)c ON b.PSN=c.customer_id)d )e
                            join(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f ON e.SnMantagheh=f.SnMNM)g WHERE  Name LIKE '%".$searchTerm."%'");
        
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
    public function searchAllCustomerByCode(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $customers=DB::select("SELECT * FROM(
                        SELECT * FROM(
                        SELECT * FROM(
                        SELECT * FROM(
                        SELECT PCode,PSN,Name,peopeladdress,SnMantagheh FROM Shop.dbo.Peopels
                        WHERE  PSN IN  (SELECT DISTINCT customer_id FROM CRM.dbo.crm_customer_added))b 
                        JOIN   (SELECT * FROM CRM.dbo.crm_customer_added)c ON b.PSN=c.customer_id)d )e
                        join(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f ON e.SnMantagheh=f.SnMNM)g WHERE  PCode LIKE '%".$searchTerm."%'");
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
    public function orderAllCustomerByCName(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm==1){
            $customers=DB::select("SELECT * FROM(
                                SELECT * FROM(
                                SELECT * FROM(
                                SELECT * FROM(
                                SELECT PCode,PSN,Name,peopeladdress,SnMantagheh FROM Shop.dbo.Peopels
                                WHERE  PSN IN  (SELECT DISTINCT customer_id FROM CRM.dbo.crm_customer_added))b 
                                JOIN   (SELECT * FROM CRM.dbo.crm_customer_added)c ON b.PSN=c.customer_id)d )e
                                join(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f ON e.SnMantagheh=f.SnMNM)g order By Name asc");
        
        }else{
            $customers=DB::select("SELECT * FROM(
                                SELECT * FROM(
                                SELECT * FROM(
                                SELECT * FROM(
                                SELECT PCode,PSN,Name,peopeladdress,SnMantagheh FROM Shop.dbo.Peopels
                                WHERE  PSN IN  (SELECT DISTINCT customer_id FROM CRM.dbo.crm_customer_added))b 
                                JOIN   (SELECT * FROM CRM.dbo.crm_customer_added)c ON b.PSN=c.customer_id)d )e
                                join(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f ON e.SnMantagheh=f.SnMNM)g order By PCode asc");
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
        return Response::json($customers);
    }
    public function dashboard() {
        $allCustomerCount=DB::select("SELECT COUNT(PSN) as countAllCustomers FROM Shop.dbo.Peopels WHERE  Peopels.CompanyNo=5 and Peopels.GroupCode IN ( ".implode(",",Session::get("groups")).")");
        
        $allCustomers=$allCustomerCount[0]->countAllCustomers;
        
        $allActiveCustomerCount=DB::select("SELECT COUNT(PSN) as countActiveCustomers FROM Shop.dbo.Peopels WHERE  PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE  returnState=0) and Peopels.CompanyNo=5 and Peopels.GroupCode IN ( ".implode(",",Session::get("groups")).")");
        
        $allActiveCustomers=$allActiveCustomerCount[0]->countActiveCustomers;
        
        $allInActiveCustomerCount=DB::select("SELECT COUNT(PSN) as countInActiveCustomers FROM Shop.dbo.Peopels WHERE  PSN in(SELECT customerId FROM CRM.dbo.crm_inactiveCustomer WHERE  state=1) AND  Peopels.CompanyNo=5 and Peopels.GroupCode IN ( ".implode(",",Session::get("groups")).")");
        
        $allInActiveCustomers=$allInActiveCustomerCount[0]->countInActiveCustomers;
        
        $allEmptyCustomerCount=DB::select("SELECT COUNT(customer_id) as countCustomer FROM(
                                        SELECT DISTINCT * FROM(
                                        SELECT CRM.dbo.crm_customer_added.customer_id FROM CRM.dbo.crm_customer_added WHERE  gotEmpty=1 and customer_id not IN  (SELECT CRM.dbo.crm_returnCustomer.customerId FROM CRM.dbo.crm_returnCustomer WHERE  returnState=1)
                                        )d
                                        JOIN   (SELECT * FROM Shop.dbo.Peopels)c
                                        ON c.PSN=d.customer_id
                                        JOIN   (SELECT PhoneStr,SnPeopel FROM Shop.dbo.PhoneDetail)b ON d.customer_id=b.SnPeopel
                                        WHERE  PSN not IN  (SELECT customerId FROM CRM.dbo.crm_inactiveCustomer WHERE  state=1))e
                                        JOIN   (SELECT customerId,removedDate FROM(
                                        SELECT DISTINCT customer_id as customerId FROM CRM.dbo.crm_customer_added WHERE   gotEmpty=1 and customer_id not in(SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE  returnState=0))a
                                        JOIN   (SELECT MAX(removedTime)as removedDate,customer_id FROM CRM.dbo.crm_customer_added GROUP BY    customer_id)b ON a.customerId=b.customer_id)f ON f.customerId=e.PSN");
       
        $allEmptyCustomers=$allEmptyCustomerCount[0]->countCustomer;
        
        $allGoodsCount=DB::select("SELECT COUNT(GoodSn) countAllGoods FROM Shop.dbo.PubGoods WHERE  PubGoods.GoodGroupSn>49 and PubGoods.CompanyNo=5");
        
        $allGoods=$allGoodsCount[0]->countAllGoods;
        
        $allPrebuyableCount=DB::select("SELECT COUNT(GoodSn) countPrepbuyables FROM Shop.dbo.PubGoods WHERE  GoodSn in(SELECT productId FROM NewStarfood.dbo.star_GoodsSaleRestriction WHERE  activePishKharid=1) and PubGoods.GoodGroupSn>49 and PubGoods.CompanyNo=5");
        
        $allPrebuyableGoods=$allPrebuyableCount[0]->countPrepbuyables;
        
        $allBoughtGoodsCount=DB::select("SELECT COUNT(GoodSn) countBoughtGoods FROM Shop.dbo.PubGoods WHERE  GoodSn in(SELECT DISTINCT SnGood FROM Shop.dbo.FactorBYS JOIN   Shop.dbo.PubGoods ON FactorBYS.SnGood=PubGoods.GoodSn WHERE  PubGoods.GoodGroupSn>49 and PubGoods.CompanyNo=5) and PubGoods.GoodGroupSn>49 and PubGoods.CompanyNo=5 
         ");
        
        $boughtGoods=$allBoughtGoodsCount[0]->countBoughtGoods;
        
        $allGoodsInBrandsCount=DB::select("SELECT COUNT(productId) as countBrandProducts FROM(
                                    SELECT DISTINCT productId FROM NewStarfood.dbo.star_add_prod_brands WHERE  brandId>6)a");
        
        $allBrandGoods=$allGoodsInBrandsCount[0]->countBrandProducts;
        
        $allBrandsCount=DB::select("SELECT COUNT(id) as countBrands FROM NewStarfood.dbo.star_brands");
        
        $allBrands=$allBrandsCount[0]->countBrands;
        
        $allMainGroupCount=DB::select("SELECT COUNT(id) as countMainGroups FROM NewStarfood.dbo.Star_Group_Def WHERE  selfGroupId=0");
        
        $allmainGroup=$allMainGroupCount[0]->countMainGroups;
        
        $allSubGroupCount=DB::select("SELECT COUNT(id) as countSubGroups FROM NewStarfood.dbo.Star_Group_Def WHERE  selfGroupId>0");
        
        $allSubGroups=$allSubGroupCount[0]->countSubGroups;
        
        $allReturnedCustomers=DB::select("SELECT COUNT(id) as countReturnedCustomers FROM CRM.dbo.crm_returnCustomer WHERE  returnState=1");
        
        $allReturnedCustomer=$allReturnedCustomers[0]->countReturnedCustomers;
        
        $admins=DB::select("SELECT * FROM(
                        SELECT crm_admin.id as adminId,crm_admin.name,crm_admin.lastName,crm_admin.phone,crm_admin.address,crm_adminType.adminType FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id)a
                        left JOIN   (SELECT COUNT(id) as countCustomer,admin_id FROM CRM.dbo.crm_customer_added WHERE  returnState=0 GROUP BY    admin_id)b ON a.adminId=b.admin_id
                        ORDER BY     a.adminType asc");
        
        return view('admin.dashboard',['allCustomers'=>$allCustomers,'allActiveCustomers'=>$allActiveCustomers,'allInActiveCustomers'=>$allInActiveCustomers,
                'allEmptyCustomers'=>$allEmptyCustomers,'allGoods'=>$allGoods,'prebuyableGoods'=>$allPrebuyableGoods,'allboughtGoods'=>$boughtGoods,'allBrandGoods'=>$allBrandGoods
                ,'allBrands'=>$allBrands,'allmainGroup'=>$allmainGroup,'allSubGroups'=>$allSubGroups,'allReturnedCustomer'=>$allReturnedCustomer,'admins'=>$admins]);
    }
    public function AddAdmin(Request $request)
    {
        $name=$request->post("name");
        $userName=$request->post("userName");
        $lastName=$request->post("lastName");
        $password=$request->post("password");
        $adminType=$request->post("adminType");
        $phone=$request->post("phone");
        $address=$request->post("address");
        $sex=$request->post("sex");
        $discription=$request->post("discription");
        $hasAsses=$request->post("hasAsses");
        $hasAllCustomer=$request->post("hasAllCustomer");
        $picture=$request->file('picture');
        $fileName=$picture->getClientOriginalName();
        list($a,$b)=explode(".",$fileName);
        $maxId=0;
        $maxId=DB::table("CRM.dbo.crm_admin")->max('id');
        if($maxId>1){
            $maxId=$maxId+1;
        }else{
            $maxId=1;
        }
        $fileName=$maxId.".jpg";
        $picture->move("resources/assets/images/admins/",$fileName);
        DB::table("CRM.dbo.crm_admin")->insert(['username'=>"".$userName."",'name'=>"".$name."",'lastName'=>"".$lastName."",'adminType'=>$adminType,'password'=>"".$password."",'activeState'=>1,'phone'=>$phone,'address'=>$address,'sex'=>"".$sex."",'discription'=>"".$discription."",'hasAsses'=>$hasAsses,'driverId'=>0,'hasAllCustomer'=>$hasAllCustomer]);
        return redirect("/assignCustomer");
    }
    public function addAdminFromList(Request $request)
    {
        $name=$request->post("name");
        $userName=$request->post("userName");
        $lastName=$request->post("lastName");
        $password=$request->post("password");
        $adminType=$request->post("adminType");
        $phone=$request->post("phone");
        $address=$request->post("address");
        $sex=$request->post("sex");
        $discription=$request->post("discription");
        $hasAsses=$request->post("hasAsses");
        $hasAllCustomer=$request->post("hasAllCustomer");
        $picture=$request->file('picture');
        $fileName=$picture->getClientOriginalName();
        list($a,$b)=explode(".",$fileName);
        $maxId=0;
        $maxId=DB::table("CRM.dbo.crm_admin")->max('id');
        if($maxId>1){
            $maxId=$maxId+1;
        }else{
            $maxId=1;
        }

        $fileName=$maxId.".jpg";
        $picture->move("resources/assets/images/admins/",$fileName);
        DB::table("CRM.dbo.crm_admin")->insert(['username'=>"".$userName."",'name'=>"".$name."",'lastName'=>"".$lastName."",'adminType'=>$adminType,'password'=>"".$password."",'activeState'=>1,'phone'=>$phone,'address'=>$address,'sex'=>"".$sex."",'discription'=>"".$discription."",'hasAsses'=>$hasAsses,'driverId'=>0,'hasAllCustomer'=>$hasAllCustomer]);
        return redirect("/listKarbaran"); 
    }
    public function AddCustomerToAdmin(Request $request)
    {
        $adminId=$request->get("adminId");
        $customerIDs=$request->get("customerIDs");
        foreach ($customerIDs as $customerId) {
            DB::table("CRM.dbo.crm_customer_added")->insert(['admin_id'=>$adminId,'customer_id'=>$customerId,'returnState'=>0]);
        }
        $customers=DB::select("SELECT * FROM Shop.dbo.Peopels WHERE  Peopels.PSN IN  (SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE  admin_id=".$adminId." and returnState!=1)  AND CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).")");
        DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(["emptyState"=>0]);
       return Response::json($customers);
    }
    public function RemoveCustomerFromAdmin(Request $request)
    {
        $customerIds=$request->get("customerIDs");
        $adminId=$request->get("adminId");
        foreach ($customerIds as $customerId) {
           DB::table("CRM.dbo.crm_customer_added")->where("customer_id",$customerId)->update(['returnState'=>1,'gotEmpty'=>1,'removedTime'=>"".Carbon::now().""]);
        }
        $customers=DB::select("SELECT * FROM Shop.dbo.Peopels WHERE  Peopels.PSN 
        not IN  (SELECT DISTINCT customer_id FROM CRM.dbo.crm_customer_added WHERE  customer_id not in(SELECT customerId FROM CRM.dbo.crm_inactiveCustomer WHERE  state=0) and customer_id not in(SELECT customerId
        FROM CRM.dbo.crm_returnCustomer WHERE  returnState=1) and returnState=0)  AND CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).") And Name!=''");
        return Response::json($customers);
    }

    public function myCalendar(){
        $adminId=Session::get('asn');
        $now = Jalalian::fromCarbon(Carbon::now());
        $month= $now->getMonth();
        $year= $now->getYear();
        $workList=DB::select("SELECT count(a.workId) as count,a.specifiedDate FROM (SELECT crm_workList.id as workId, crm_workList.specifiedDate FROM CRM.dbo.crm_workList Join CRM.dbo.crm_comment ON crm_workList.commentId=crm_comment.id
        JOIN   CRM.dbo.crm_customer_added ON crm_comment.customerId=crm_customer_added.customer_id WHERE  crm_customer_added.admin_id=".$adminId." and crm_workList.doneState=0 and crm_customer_added.returnState=0)a GROUP BY    a.specifiedDate");
        return view ("admin.calendar",['commenDates'=>$workList,'month'=>$month,'year'=>$year]);
    }

    public function changeDate(Request $request)
    {
        $month=$request->post("month");
        $year=$request->post("year");
        $adminId=Session::get('asn');
        $workList=DB::select("SELECT count(a.workId) as count,a.specifiedDate FROM (SELECT crm_workList.id as workId, crm_workList.specifiedDate FROM CRM.dbo.crm_workList Join CRM.dbo.crm_comment ON crm_workList.commentId=crm_comment.id
        JOIN   CRM.dbo.crm_customer_added ON crm_comment.customerId=crm_customer_added.customer_id WHERE  crm_customer_added.admin_id=".$adminId." and crm_workList.doneState=0 and crm_customer_added.returnState=0 and crm_comment.customerId not IN  (SELECT customerId FROM CRM.dbo.crm_returnCustomer WHERE  crm_returnCustomer.returnState=1))a GROUP BY    a.specifiedDate");
        return view ("admin.calendar",['commenDates'=>$workList,'month'=>$month,'year'=>$year]);
    }
    public function takhsisCustomer(Request $request)
    {
        $customerId=$request->get("csn");
        $adminId=$request->get("asn");
        // add to customer update two places
        $admin=DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->first();
        if($admin->emptyState==1){
            DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(["emptyState"=>0]);
        }
        DB::table("CRM.dbo.crm_customer_added")->where('customer_id',$customerId)->where('returnState',0)->update(['removedTime'=>"".Carbon::now()."",'returnState'=>1]);
        DB::table("CRM.dbo.crm_customer_added")->insert(['admin_id'=>$adminId,'customer_id'=>$customerId,'returnState'=>0]);
        DB::table("CRM.dbo.crm_customer_added")->where('customer_id',$customerId)->update(['gotEmpty'=>0]);
        DB::update("UPDATE CRM.dbo.crm_returnCustomer SET returnState=0 WHERE  customerId=".$customerId." and returnState=1");
        DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->where('emptyState',1)->update(['emptyState'=>0]);
        $customers=DB::table("Shop.dbo.Peopels")->join("CRM.dbo.crm_returnCustomer","Peopels.PSN","=","crm_returnCustomer.customerId")->join("Shop.dbo.PhoneDetail","Peopels.PSN","=","PhoneDetail.SnPeopel")->where("crm_returnCustomer.returnState",1)->select("Peopels.PSN","Peopels.PCode","Peopels.Name","PhoneDetail.PhoneStr","Peopels.peopeladdress")->get();
        return Response::json($customers);

    }
    public function takhsisNewCustomer(Request $request)
    {
        $customerId=$request->get("csn");
        $adminId=$request->get("asn");
        // add to customer update two places
        $admin=DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->first();
        if($admin->emptyState==1){
            DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(["emptyState"=>0]);
        }
        DB::table("CRM.dbo.crm_customer_added")->where('customer_id',$customerId)->where('returnState',0)->update(['removedTime'=>"".Carbon::now()."",'returnState'=>1]);
        DB::table("CRM.dbo.crm_customer_added")->insert(['admin_id'=>$adminId,'customer_id'=>$customerId,'returnState'=>0]);
        DB::table("CRM.dbo.crm_customer_added")->where('customer_id',$customerId)->update(['gotEmpty'=>0]);
        DB::update("UPDATE CRM.dbo.crm_returnCustomer SET returnState=0 WHERE  customerId=".$customerId." and returnState=1");
        DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->where('emptyState',1)->update(['emptyState'=>0]);
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
        return Response::json($customers);

    }
    public function takhsisCustomerFromEmpty(Request $request)
    {
        $customerId=$request->get("csn");
        $adminId=$request->get("asn");
        // add to customer update two places
        $admin=DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->first();
        if($admin->emptyState==1){
            DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(["emptyState"=>0]);
        }
        DB::table("CRM.dbo.crm_customer_added")->where('customer_id',$customerId)->where('returnState',0)->update(['removedTime'=>"".Carbon::now().""]);
        DB::table("CRM.dbo.crm_customer_added")->insert(['admin_id'=>$adminId,'customer_id'=>$customerId,'returnState'=>0]);
        DB::table("CRM.dbo.crm_customer_added")->where('customer_id',$customerId)->update(['gotEmpty'=>0]);
        DB::update("UPDATE CRM.dbo.crm_returnCustomer SET returnState=0 WHERE  customerId=".$customerId." and returnState=1");
        DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->where('emptyState',1)->update(['emptyState'=>0]);
        $customers=DB::select("SELECT DISTINCT * FROM(
                        SELECT CRM.dbo.crm_customer_added.customer_id FROM CRM.dbo.crm_customer_added WHERE  gotEmpty=1 and customer_id not IN  (SELECT CRM.dbo.crm_returnCustomer.customerId FROM CRM.dbo.crm_returnCustomer WHERE  returnState=1)
                        )d
                        JOIN(SELECT * FROM Shop.dbo.Peopels)c
                        ON c.PSN=d.customer_id
                        JOIN (SELECT * FROM Shop.dbo.PhoneDetail)b ON d.customer_id=b.SnPeopel");
        return Response::json($customers);
    }

    public function activateCustomer(Request $request)
    {
        $customerId=$request->get("csn");
        $adminId=$request->get("asn");
        // add to customer update two places
        // $result1=DB::table("CRM.dbo.crm_customer_added")->where('customer_id',$customerId)->where("returnState",1)->update(['admin_id'=>$adminId,'customer_id'=>$customerId,'returnState'=>0]);
        DB::table("CRM.dbo.crm_customer_added")->insert(['admin_id'=>$adminId,'customer_id'=>$customerId,'returnState'=>0]);
        DB::update("UPDATE CRM.dbo.crm_inactiveCustomer SET state=0 WHERE  customerId=".$customerId." and state=1");

        $customers=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM CRM.dbo.crm_inactiveCustomer
                        join(SELECT name,lastName,id as admin_id FROM CRM.dbo.crm_admin)a ON a.admin_id=adminId)b
                        JOIN   (SELECT Name as CustomerName,PSN,PCode FROM Shop.dbo.Peopels)c ON c.PSN=b.customerId)d
                        JOIN   (SELECT SnPeopel,PhoneStr FROM Shop.dbo.PhoneDetail)e ON e.SnPeopel=d.PSN
                        WHERE  state=1");
        return Response::json($customers);
    }

    public function report(){
        $amdins=DB::select("Select * FROM CRM.dbo.crm_admin WHERE  adminType=2 or adminType=3");
        
        $customers=DB::select("SELECT TOP 20 * FROM(
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (SELECT PSN,Name,peopeladdress,CompanyNo,GroupCode FROM Shop.dbo.Peopels) a
                        left JOIN   (
                        SELECT COUNT(SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS GROUP BY    FactorHDS.CustomerSn) b ON a.PSN=b.CustomerSn )c
                        join(SELECT MAX(FactorHDS.FactDate)as lastDate,CustomerSn as customerId FROM Shop.dbo.FactorHDS GROUP BY    FactorHDS.CustomerSn
                        )d
                        ON d.customerId=c.PSN )e
                        JOIN   (SELECT customer_id,admin_id,name as adminName,lastName,returnState FROM CRM.dbo.crm_customer_added JOIN   CRM.dbo.crm_admin ON CRM.dbo.crm_customer_added.admin_id=crm_admin.id)f ON f.customer_id=e.PSN)g
                        WHERE  g.returnState=0 and g.GroupCode IN ( ".implode(",",Session::get("groups")).") and g.CompanyNo=5 and g.PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added) ORDER BY countFactor desc");
        
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
        $cities=DB::table("Shop.dbo.MNM")->where("Rectype",1)->where("FatherMNM",79)->get();
        return view ("reports.listReport",['customers'=>$customers,'cities'=>$cities, 'amdins'=>$amdins]);
    }

    public function referedCustomer(){
        $admins=DB::table("CRM.dbo.crm_admin")
            ->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')
                ->where('crm_admin.adminType','!=',1)
                ->where('crm_admin.adminType','!=',4)
                ->select("crm_admin.id","crm_admin.name","crm_admin.lastName",
                "crm_admin.adminType as adminTypeId","crm_adminType.adminType")->get();

        $customers=DB::table("Shop.dbo.Peopels")
            ->join("CRM.dbo.crm_returnCustomer","Peopels.PSN","=","crm_returnCustomer.customerId")
            ->join("CRM.dbo.crm_admin","crm_returnCustomer.adminId","=","crm_admin.id")
                ->where("crm_returnCustomer.returnState",1)
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
        $returnerAdmins=DB::select("SELECT * FROM CRM.dbo.crm_admin 
                        JOIN(SELECT DISTINCT CRM.dbo.crm_returnCustomer.adminId
                        FROM CRM.dbo.crm_returnCustomer WHERE returnState=1)b ON CRM.dbo.crm_admin.id=b.adminId");

        return view ("admin.referedCustomer",['customers'=>$customers, 'admins'=>$admins,'returners'=>$returnerAdmins]);
    }

    public function searchReferedCustomerName(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        
        // $customers=DB::select("SELECT PSN,PCode,Name,peopeladdress,adminId FROM(
        //                 SELECT * FROM(SELECT * FROM Shop.dbo.Peopels)a
        //                 JOIN (SELECT * FROM CRM.dbo.crm_returnCustomer)b ON a.PSN=b.customerId)c
        //                 WHERE c.returnState=1 AND Name LIKE '%$searchTerm%'");

        $customers=DB::table("Shop.dbo.Peopels")
                    ->join("CRM.dbo.crm_returnCustomer","Peopels.PSN","=","crm_returnCustomer.customerId")
                    ->join("CRM.dbo.crm_admin","crm_returnCustomer.adminId","=","crm_admin.id")
                    ->where("crm_returnCustomer.returnState",1)
                    ->where("Peopels.Name","LIKE",'%'.$searchTerm.'%')
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

    public function login(){
       return view ("admin.login");

    }


    public function alarm(){
        $maxFacors=DB::select("SELECT max(SerialNoHDS) AS MaxFactorId,CustomerSn FROM (
                               SELECT * FROM Shop.dbo.FactorHDS WHERE CustomerSn in(SELECT customer_id FROM CRM.dbo.crm_customer_added)
                               )a GROUP BY CustomerSn");

        $inAlarmFactors=DB::select("SELECT factorId,CustomerSn FROM CRM.dbo.crm_alarm 
                                    JOIN Shop.dbo.FactorHDS ON crm_alarm.factorId=FactorHDS.SerialNoHDS");

        foreach ($maxFacors as $factor) {
            foreach ($inAlarmFactors as $alarm) {
                if($factor->CustomerSn==$alarm->CustomerSn and ($factor->MaxFactorId>$alarm->factorId)){
                    DB::table("CRM.dbo.crm_alarm")->where("factorId",$alarm->factorId)->update(["state"=>1]);
                }
            }
        }
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
                        WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0 
                        and PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0)
                        " );
        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::select("SELECT * FROM Shop.dbo.PhoneDetail WHERE  SnPeopel=".$customer->PSN);
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
        return view ("admin.alarm",['customers'=>$customers]);
    }
    public function customerDashboardForAdmin(Request $request)
    {
        $psn=$request->get("csn");
        $customers=DB::select("SELECT * FROM(
                            SELECT * FROM(
                            SELECT * FROM
                            (SELECT Peopels.CompanyNo,Peopels.Name,Peopels.PSN,Peopels.GroupCode,Peopels.peopeladdress,Peopels.PCode,SnMantagheh FROM Shop.dbo.Peopels)a
                            LEFT JOIN (SELECT COUNT(Shop.dbo.FactorHDS.SerialNoHDS)AS countFactor,CustomerSn 
                            FROM Shop.dbo.FactorHDS WHERE FactType=3 GROUP BY CustomerSn)b ON b.CustomerSn=a.PSN)c
                            JOIN (SELECT customer_id,crm_admin.id,name AS adminName,lastName,returnState
                            FROM CRM.dbo.crm_customer_added JOIN CRM.dbo.crm_admin ON crm_customer_added.admin_id=crm_admin.id)d ON d.customer_id=c.PSN)e
                            WHERE e.CompanyNo=5 AND e.GroupCode IN ( ".implode(",",Session::get("groups")).") AND e.returnState=0 AND e.PSN=".$psn);
        $exactCustomer=0;

        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::select("SELECT * FROM Shop.dbo.PhoneDetail WHERE  SnPeopel=".$customer->PSN);
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n";    
                }
            }
            $customer->hamrah=$hamrah;
            $customer->sabit=$sabit;
        }
        $exactCustomer=$customers[0];
        $factors=DB::select("SELECT * FROM Shop.dbo.FactorHDS WHERE  FactType=3 and CustomerSn=".$psn." ORDER BY     FactDate desc");
        $returnedFactors=DB::select("SELECT * FROM Shop.dbo.FactorHDS WHERE  FactType=4 and CustomerSn=".$psn);

        $GoodsDetail=DB::select("SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood FROM(
                                SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood FROM Shop.dbo.FactorHDS
                                JOIN Shop.dbo.FactorBYS ON FactorHDS.SerialNoHDS=FactorBYS.SnFact
                                WHERE  FactorHDS.CustomerSn=".$psn.")g GROUP BY    SnGood)c
                                JOIN (SELECT * FROM Shop.dbo.PubGoods)d ON d.GoodSn=c.SnGood");

        $basketOrders=DB::select("SELECT orderStar.TimeStamp,PubGoods.GoodName,orderStar.Amount,orderStar.Fi 
                                FROM newStarfood.dbo.FactorStar 
                                JOIN newStarfood.dbo.orderStar ON FactorStar.SnOrder=orderStar.SnHDS
                                JOIN Shop.dbo.PubGoods ON orderStar.SnGood=PubGoods.GoodSn  
                                WHERE  orderStatus=0 and CustomerSn=".$psn);

        $comments=DB::select("SELECT  crm_comment.id,newComment,nexComment,TimeStamp,customerId,adminId,specifiedDate FROM CRM.dbo.crm_comment 
                                JOIN CRM.dbo.crm_workList ON crm_comment.id=crm_workList.commentId WHERE customerId=".$psn);

        $specialComment=DB::table("CRM.dbo.crm_customerProperties")->where("customerId",$psn)->select("comment")->get();
        
        $assesments=DB::select("SELECT crm_assesment.comment,crm_assesment.factorId,crm_assesment.TimeStamp,crm_assesment.shipmentProblem,crm_assesment.driverBehavior FROM CRM.dbo.crm_assesment
                                JOIN   Shop.dbo.FactorHDS ON crm_assesment.factorId=FactorHDS.SerialNoHDS JOIN   Shop.dbo.Peopels ON Peopels.PSN=FactorHDS.CustomerSn WHERE  PSN=".$psn);
        
        return Response::json([$exactCustomer,$factors,$GoodsDetail,$basketOrders,$comments,$specialComment,$assesments,$returnedFactors]);
    }

    // ======================
    public function customerDashboardForAlarm(Request $request)
    {
        $psn=$request->get("csn");
        $asn=$request->get("asn");
        $customer=DB::select("SELECT * FROM(
                        SELECT * FROM (
                        SELECT COUNT(Shop.dbo.FactorHDS.SerialNoHDS)as countFactor,CustomerSn FROM Shop.dbo.FactorHDS GROUP BY    CustomerSn
                        )a
                        JOIN   (SELECT customer_id,returnState,admin_id FROM CRM.dbo.crm_customer_added)b ON a.CustomerSn=b.customer_id
                        )c
                        JOIN   (SELECT Name,PSN,PCode,CompanyNo FROM Shop.dbo.Peopels)d ON c.customer_id=d.PSN
                        WHERE   PSN=".$psn);
        $exactCustomer=0;
        foreach ($customer as $cust) {
            $exactCustomer=$cust;
        }
        $factors=DB::select("SELECT * FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$psn." ORDER BY     FactDate desc");
        
        $GoodsDetail=DB::select("SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood FROM(
                            SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood FROM Shop.dbo.FactorHDS
                            JOIN Shop.dbo.FactorBYS ON FactorHDS.SerialNoHDS=FactorBYS.SnFact
                            WHERE FactorHDS.CustomerSn=".$psn.")g GROUP BY    SnGood)c
                            JOIN (SELECT * FROM Shop.dbo.PubGoods)d ON d.GoodSn=c.SnGood");

        $basketOrders=DB::select("SELECT orderStar.TimeStamp,PubGoods.GoodName,orderStar.Amount,orderStar.Fi FROM newStarfood.dbo.FactorStar 
                            JOIN newStarfood.dbo.orderStar ON FactorStar.SnOrder=orderStar.SnHDS
                            JOIN Shop.dbo.PubGoods ON orderStar.SnGood=PubGoods.GoodSn  WHERE  orderStatus=0 and CustomerSn=".$psn);
        
        $comments=DB::select("SELECT  crm_comment.newComment,crm_comment.nexComment,crm_comment.TimeStamp,customerId,adminId,specifiedDate,doneState,crm_comment.id 
                            FROM CRM.dbo.crm_comment JOIN CRM.dbo.crm_workList ON crm_comment.id=crm_workList.id 
                            WHERE customerId=".$psn);
        
        $specialComment=DB::table("CRM.dbo.crm_customerProperties")->where("customerId",$psn)->select("comment")->get();
        
        $assesments=DB::select("SELECT crm_assesment.comment,crm_assesment.factorId,crm_assesment.TimeStamp,crm_assesment.shipmentProblem,crm_assesment.driverBehavior FROM CRM.dbo.crm_assesment
                                JOIN Shop.dbo.FactorHDS ON crm_assesment.factorId=FactorHDS.SerialNoHDS JOIN   Shop.dbo.Peopels ON Peopels.PSN=FactorHDS.CustomerSn WHERE  PSN=".$psn);
        
        return Response::json([$exactCustomer,$factors,$GoodsDetail,$basketOrders,$comments,$specialComment,$assesments]);
    }
        
    // ======================

    public function getAssesComment(Request $request)
    {
        $id=$request->get("assesId");
        $comment=DB::table("CRM.dbo.crm_assesment")->where("id",$id)->first();
        return Response::json($comment);
    }
    
    // ======================

    public function message(){
         $admins=DB::select("SELECT crm_admin.id,crm_admin.name,crm_admin.lastName,crm_admin.adminType as adminTypeId,crm_adminType.adminType FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_adminType.id=crm_admin.adminType WHERE  crm_admin.id!=".Session::get('asn'));
         
         $messages=DB::select("SELECT * FROM (
                        SELECT * FROM(
                        SELECT MAX(id) maxID,senderId FROM CRM.dbo.crm_message WHERE  getterId=".Session::get('asn')." GROUP BY    crm_message.senderId)a
                        JOIN   CRM.dbo.crm_admin ON a.senderId=crm_admin.id )b
                        JOIN   CRM.dbo.crm_message ON b.maxID=crm_message.id WHERE  crm_message.senderId!=".Session::get('asn')."  and crm_message.getterId=".Session::get('asn'));
        return view("admin.message",['admins'=>$admins,'messages'=>$messages]);
    }
        
    // ======================

    public function inactivCustomer(){
        $admins=DB::table("CRM.dbo.crm_admin")
                    ->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')
                    ->where('crm_admin.adminType','!=',1)->where('crm_admin.adminType','!=',4)
                    ->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType")
                    ->get();

        $customers=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM CRM.dbo.crm_inactiveCustomer
                        JOIN(SELECT name,lastName,id as admin_id FROM CRM.dbo.crm_admin)a ON a.admin_id=adminId)b
                        JOIN (SELECT Name as CustomerName,PSN,PCode,SnMantagheh FROM Shop.dbo.Peopels)c ON c.PSN=b.customerId)d
                        JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM)e ON d.SnMantagheh=e.SnMNM)f
                        WHERE  state=1");
            foreach ($customers as $customer) {
                $sabit="";
                $hamrah="";
                $phones=DB::select("SELECT * FROM Shop.dbo.PhoneDetail WHERE  SnPeopel=".$customer->PSN);
                foreach ($phones as $phone) {
                    if($phone->PhoneType==1){
                        $sabit.=$phone->PhoneStr."\n";
                    }else{
                        $hamrah.=$phone->PhoneStr."\n";    
                    }
                }
                $customer->hamrah=$hamrah;
            }
        return view ("customer.inactiveCustomer",['customers'=>$customers,'admins'=>$admins]);
    }
        
    // ======================

    public function loginUser(Request $request)
    {
        
        $userName=$request->post("userName");
        $password=$request->post("password");
        $result=DB::table("CRM.dbo.crm_admin")->where("username",$userName)->where("password",$password)->count();
        if($result>0){
            $admin=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE username='".$userName."' and password='".$password."'");
            $exactAdmin;
            foreach ($admin as $ad) {
                $exactAdmin=$ad;
            }
            
             $isLogedIn=DB::table("CRM.dbo.crm_loginTrack")->where('adminId',$exactAdmin->id)->where('loginDate',Carbon::now()->format('Y-m-d'))->count();
            if($isLogedIn>0){
                DB::table('CRM.dbo.crm_loginTrack')->where('adminId',$exactAdmin->id)->update(['loginTime'=>Carbon::now()]);
            }else{
                DB::table('CRM.dbo.crm_loginTrack')->insert(['adminId'=>$exactAdmin->id,'loginDate'=>Carbon::now()->format('Y-m-d'),'loginTime'=>Carbon::now()]);
            }
            Session::put("username",$exactAdmin->name.' '.$exactAdmin->lastName);
            Session::put("asn",$exactAdmin->id);
            Session::put("dsn",$exactAdmin->driverId);
            Session::put("adminType",$exactAdmin->adminType);
            Session::put("activeState",$exactAdmin->activeState);
            Session::put("hasAsses",$exactAdmin->hasAsses);
            Session::put("hasAllCustomer",$exactAdmin->hasAllCustomer);
            Session::put('groups',[291,297,299,312,313,314]);
            switch (Session::get("adminType")) {
                case 1:
                    return redirect('/home');
                    break;
                case 2:
                    return redirect('/customers');
                    break;
                case 3:
                    return redirect('/myCustomers');
                    break; 
                case 4:
                    return redirect('/crmDriver');
                    break;   
                case 5:
                    return redirect('/home');
                    break;             
                default:
                    return redirect('/notfound');
                    break;
            }
            
            
        }else{
            return view('admin.login',['loginError'=>"نام کاربری و یا رمز ورود اشتباه است"]);
        }
    }
        
    // ======================

    public function logoutUser(Request $request)
    {
        Session::forget("username");
        Session::forget("asn");
        Session::forget("adminType");
        Session::forget("hasAsses");
        return view('admin.login');
    }
        
    // ======================

    public function kalaAction(){
                            
        $products=DB::select("SELECT TOP 20  PubGoods.GoodName,PubGoods.GoodCde,PubGoods.GoodSn,star_GoodsSaleRestriction.hideKala,ViewGoodExists.Amount,a.maxFactDate FROM
                        Shop.dbo.PubGoods 
                        JOIN NewStarfood.dbo.star_GoodsSaleRestriction ON PubGoods.GoodSn=star_GoodsSaleRestriction.productId
                        JOIN Shop.dbo.ViewGoodExists ON PubGoods.GoodSn=ViewGoodExists.SnGood
                        JOIN(
                        Select MAX(Shop.dbo.FactorHDS.FactDate) as maxFactDate,FactorBYS.SnGood
                        FROM Shop.dbo.FactorHDS JOIN Shop.dbo.FactorBYS ON FactorBYS.SnFact=FactorHDS.SerialNoHDS
                        GROUP BY    FactorBYS.SnGood)a
                        ON a.SnGood=PubGoods.GoodSn
                        WHERE  ViewGoodExists.CompanyNo=5 and ViewGoodExists.FiscalYear=1399 and PubGoods.GoodGroupSn>49");
        
        $stocks=DB::select("SELECT * FROM Shop.dbo.Stocks WHERE  CompanyNo=5");
        $mainGroups=DB::select("SELECT * FROM NewStarfood.dbo.Star_Group_Def WHERE  selfGroupId=0");
        return view ("admin.kalaAction",['products'=>$products,'stocks'=>$stocks,'mainGroups'=>$mainGroups]);
    }

    
    // ======================

    public function userProfile(){
        $adminId=Session::get("asn");
        $admin=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE  id=".$adminId);
        $exactAdmin;
        foreach ($admin as $admin) {
            $exactAdmin=$admin;
        }
        return view ("admin.userProfile",['admin'=>$exactAdmin]);
    }
    
    // ======================

    public function editProfile(){
        return view ("admin.editProfile");
    }
        
    // ======================

    public function editOwnAdmin(Request $request)
    {
        $adminId=Session::get("asn");
        $userName=$request->post("userName");
        $picture=$request->file("picture");
        $phone=$request->post("phone");
        $address=$request->post("address");
        $password=$request->post("password");
        $fileName=$picture->getClientOriginalName();
        $fileName=$adminId.".jpg";
        $picture->move("resources/assets/images/admins/",$fileName);
        $result=DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(['username'=>"".$userName."",'password'=>"".$password."",'address'=>"".$address."",'phone'=>"".$phone.""]);
        return redirect("/userProfile");
    }
    
    // ======================

    public function crmSetting(){
        return view ("admin.crmSetting");
    }
        
    // ======================

    public function karbarAction(Request $request)
    {
        $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where("crm_admin.adminType",2)->orWhere("crm_admin.adminType",3)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType")->get();
        $adminTypes=DB::select("SELECT * FROM CRM.dbo.crm_adminType WHERE  id=2 or id=3");
        return view("admin.karbarAction",['admins'=>$admins,'adminTypes'=>$adminTypes]);
    }
        
    // ======================

    public function adminDashboard(Request $request)
    {
        $adminId=$request->get("asn");
        $admin=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->first();
        if($admin->emptyState==0){
            
            $admins=DB::select("SELECT id, minDate,countPeopel,adminId,discription,name,lastName FROM(
                            SELECT MIN(addedTime) as minDate,COUNT(customer_id) as countPeopel,adminId 
                            FROM(SELECT crm_admin.id as adminId,crm_customer_added.addedTime,crm_customer_added.customer_id 
                            FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_customer_added ON crm_admin.id=crm_customer_added.admin_id 
                            WHERE  crm_customer_added.returnState=0)d  GROUP BY    adminId)d
                            LEFT JOIN   (SELECT * FROM CRM.dbo.crm_admin)a ON a.id=d.adminId WHERE  adminId=".$adminId);
            
            $info=DB::select("SELECT COUNT(customer_id)as countPeopels,crm_customer_added.admin_id 
                            FROM CRM.dbo.crm_customer_added where returnState=0 and crm_customer_added.admin_id=$adminId GROUP BY admin_id");
            
            $customers=DB::select("SELECT customer_id,returnState,addedTime,removedTime 
                            FROM CRM.dbo.crm_customer_added WHERE  returnState=0 and crm_customer_added.admin_id=".$adminId);
            
            $countAllFactor=0;
            $sumAllFactor=0;
            $countAllReturnedFactor=0;
            $sumAllReturnedFactor=0;
            $seenCustomers=array();
            foreach ($customers as $customer) {
                $removedDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
                $addedDate=Jalalian::fromCarbon(Carbon::parse($customer->addedTime))->format('Y/m/d');
                $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=3 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."'  GROUP BY    FactorHDS.SerialNoHDS");
                $returendFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=4 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."'  GROUP BY    FactorHDS.SerialNoHDS");
                foreach ($factors as $factor) {
                    if($factor->countFactors>0 and !in_array($customer->customer_id, $seenCustomers)){
                    array_push($seenCustomers,$customer->customer_id);
                    }
                    $countAllFactor+=$factor->countFactors;
                    $sumAllFactor+=$factor->sumFactors;
                }
                foreach ($returendFactors as $factor) {
                    $countAllReturnedFactor+=$factor->countFactors;
                    $sumAllReturnedFactor+=$factor->sumFactors;
                }
            }
            $boughtPeopelsCount=count($seenCustomers);
            $lastFactorAllMoney=DB::select("SELECT factorAllMoney,lastMonthReturnedAllMoney FROM CRM.dbo.crm_adminHistory WHERE   timeStamp=(
                SELECT MAX(timeStamp)from CRM.dbo.crm_adminHistory WHERE  adminId=".$adminId." GROUP BY    crm_adminHistory.adminId)");
            $lastFactorAllMoney1=0;
            $lastMonthReturnAllMoney1=0;
            foreach ($lastFactorAllMoney as $last) {
                $lastFactorAllMoney1=$last->factorAllMoney;
                $lastMonthReturnAllMoney1=$last->lastMonthReturnedAllMoney;
            }

            foreach ($info as $infor) {
                $infor->countFactor=$countAllFactor;
                $infor->totalMoneyHds=$sumAllFactor;
                $infor->countReturnFactor=$countAllReturnedFactor;
                $infor->totalReturnMoneyHds=$sumAllReturnedFactor;
                $infor->boughtPeopelsCount=$boughtPeopelsCount;
                $infor->lastMonthFactorAllMoney=$lastFactorAllMoney1;
                $infor->lastMonthReturnedAllMoney=$lastMonthReturnAllMoney1;
            }

            $history=DB::table("CRM.dbo.crm_adminHistory")->where("adminId",$adminId)->get();
            $minDate=DB::table("CRM.dbo.crm_customer_added")->where("returnState",0)->where("admin_id",$adminId)->min("addedTime");
            $countAllFactor=0;
            $sumAllFactor=0;
            $countAllReturnedFactor=0;
            $sumAllReturnedFactor=0;
            $customers=DB::select("SELECT customer_id,addedTime FROM CRM.dbo.crm_customer_added WHERE  admin_id=".$adminId." and returnState=0");
            $endDate=Jalalian::fromCarbon(Carbon::parse($minDate))->format('Y/m/d');
            $startDate=Jalalian::fromCarbon(Carbon::parse($minDate)->subdays(30))->format('Y/m/d');

            foreach ($customers as $customer) {
                $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactType=3 and FactorHDS.FactDate>='".$startDate."' and FactorHDS.FactDate<='".$endDate."'  GROUP BY    FactorHDS.CustomerSn");
                foreach ($factors as $factor) {
                    $countAllFactor+=$factor->countFactors;
                    $sumAllFactor+=$factor->sumFactors;
                }

                $returnFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactType=4 and FactorHDS.FactDate>='".$startDate."' and FactorHDS.FactDate<='".$endDate."'  GROUP BY    FactorHDS.CustomerSn");
                foreach ($returnFactors as $factor) {
                    $countAllReturnedFactor+=$factor->countFactors;
                    $sumAllReturnedFactor+=$factor->sumFactors;
                }
            }
            // DB::table("CRM.dbo.crm_adminHistory")->where("adminId",$adminId)->update(['noCommentCust'=>0,'noDoneWork'=>0]);
            $customers=array(array('countCustomers'=>count($customers),'countAllFactor'=>$countAllFactor,'sumAllFactor'=>$sumAllFactor,'countAllReturnedFactor'=>$countAllReturnedFactor,'sumAllReturnedFactor'=>$sumAllReturnedFactor));

            return Response::json([$admins,$info,$history,$customers]);
        }else{
            $admins1=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->get();
            foreach ($admins1 as $admin) {
                $admin->minDate=0;
                $admin->countPeopel=0;
            }

            $lastFactorAllMoney=DB::select("SELECT factorAllMoney FROM CRM.dbo.crm_adminHistory WHERE   timeStamp=(
                SELECT MAX(timeStamp)from CRM.dbo.crm_adminHistory WHERE  adminId=".$adminId." GROUP BY    crm_adminHistory.adminId)");
            $lastFactorAllMoney1=0;
            foreach ($lastFactorAllMoney as $last) {
                $lastFactorAllMoney1=$last->factorAllMoney;
            }
            $info=array(array('countFactor'=>0,'totalMoneyHds'=>0,'boughtPeopelsCount'=>0,'lastMonthFactorAllMoney'=>$lastFactorAllMoney1));
            $history=DB::table("CRM.dbo.crm_adminHistory")->where("adminId",$adminId)->get();
            $minDate=DB::table("CRM.dbo.crm_customer_added")->where("returnState",0)->where("admin_id",$adminId)->min("addedTime");
            $countAllFactor=0;
            $sumAllFactor=0;
            $countAllReturnedFactor=0;
            $sumAllReturnedFactor=0;
            $customers=DB::select("SELECT customer_id,addedTime FROM CRM.dbo.crm_customer_added WHERE  admin_id=".$adminId." and returnState=0");
            foreach ($customers as $customer) {
                $removedDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
                $addedDate=Jalalian::fromCarbon(Carbon::parse($customer->addedTime))->format('Y/m/d');
                $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=3 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."'  GROUP BY    FactorHDS.CustomerSn");
                foreach ($factors as $factor) {
                    $countAllFactor+=$factor->countFactors;
                    $sumAllFactor+=$factor->sumFactors;
                }

                $returnFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=4 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."'  GROUP BY    FactorHDS.CustomerSn");
                foreach ($returnFactors as $factor) {
                    $countAllReturnedFactor+=$factor->countFactors;
                    $sumAllReturnedFactor+=$factor->sumFactors;
                }
            }
            $customers=array(array('countCustomers'=>count($customers),'countAllFactor'=>$countAllFactor,'sumAllFactor'=>$sumAllFactor,'countAllReturnedFactor'=>$countAllReturnedFactor,'sumAllReturnedFactor'=>$sumAllReturnedFactor));

            return Response::json([$admins1,$info,$history,$customers]);
        }
    }

    public function getAdminHistoryComment(Request $request)
    {
        $adminId=$request->get("id");
        $timeStamp=$request->get("timeStamp");
        $info=DB::table("CRM.dbo.crm_adminHistory")->where("adminId",$adminId)->where("timeStamp",$timeStamp)->first();
        return Response::json($info);
    }
    
    // ======================

    public function searchAdminByNameCode(Request $request)
    {
        $searchTerm=$request->get("searchTerm");

        $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType,crm_admin.id AS id 
                        FROM CRM.dbo.crm_admin
                        JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id
                        WHERE ( crm_admin.adminType=2 OR crm_admin.adminType=3) AND (crm_admin.name LIKE '%$searchTerm%' OR crm_admin.lastName LIKE '%$searchTerm%')");
        return Response::json($admins);
    }
        
    // ======================

    public function searchAdminByType(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm!=0){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin
                            JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id
                            WHERE  crm_admin.adminType=".$searchTerm);
            return Response::json($admins);
        }else{
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin
                            JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id");
            return Response::json($admins);
        }
    }
        
    // ======================

    public function searchAdminByActivation(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm==0){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin
                            JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id WHERE  crm_admin.adminType=2 or crm_admin.adminType=3");
            return Response::json($admins);
        }
        if($searchTerm==1){
            $searchTerm=$request->get("searchTerm");
            
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin
                            JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id
                            WHERE  crm_admin.activeState=1 AND  (crm_admin.adminType=2 or crm_admin.adminType=3)");
            
            return Response::json($admins);
        }
        if($searchTerm==2){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin
                            JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id
                            WHERE  crm_admin.activeState=0 AND  (crm_admin.adminType=2 or crm_admin.adminType=3)");
            
            return Response::json($admins);
        }
    }
    
    // ======================

    public function searchAdminFactorOrNot(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        
        if($searchTerm==1){
            $admins=DB::select("SELECT * FROM (
                            SELECT DISTINCT admin_id FROM(
                            SELECT * FROM (
                            SELECT * FROM CRM.dbo.crm_customer_added WHERE  admin_id not IN  (
                            SELECT DISTINCT admin_id FROM (
                            SELECT COUNT(SerialNoHDS)countFactor,CustomerSn FROM Shop.dbo.FactorHDS WHERE  FactDate='1401/05/6' GROUP BY    CustomerSn)a JOIN   (SELECT * FROM CRM.dbo.crm_customer_added WHERE  returnState=0)l ON a.CustomerSn=l.customer_id
                            ))b
                            WHERE  returnState=0)c)d
                            JOIN   (SELECT crm_admin.adminType as adminKind,name,lastName,crm_adminType.adminType,crm_admin.id FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id)e ON d.admin_id=e.id");
            return Response::json($admins);
        }

        if($searchTerm==2){
            $admins=DB::select("SELECT * FROM (
                            SELECT DISTINCT admin_id FROM(
                            SELECT * FROM (
                            SELECT * FROM CRM.dbo.crm_customer_added WHERE  admin_id IN  (
                            SELECT DISTINCT admin_id FROM (
                            SELECT COUNT(SerialNoHDS)countFactor,CustomerSn FROM Shop.dbo.FactorHDS WHERE  FactDate='1401/05/6' GROUP BY    CustomerSn)a JOIN   (SELECT * FROM CRM.dbo.crm_customer_added WHERE  returnState=0)l ON a.CustomerSn=l.customer_id
                            ))b
                            WHERE  returnState=0)c)d
                            JOIN   (SELECT crm_admin.adminType as adminKind,name,lastName,crm_adminType.adminType,crm_admin.id FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id)e ON d.admin_id=e.id");
            return Response::json($admins);
        }

        if($searchTerm==0){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin
            JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id WHERE  crm_admin.adminType=2 or crm_admin.adminType=3");
                return Response::json($admins);
                }
    }
        
    // ======================

    public function searchAdminLoginOrNot(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm==2){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin JOIN CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id WHERE crm_admin.id NOT IN(
                            SELECT DISTINCT adminId FROM CRM.dbo.crm_loginTrack) and (crm_admin.adminType=2 or crm_admin.adminType=3)");
            return Response::json($admins);
        }
        if($searchTerm==1){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin JOIN CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id WHERE crm_admin.id IN(
                            SELECT DISTINCT adminId FROM CRM.dbo.crm_loginTrack) and (crm_admin.adminType=2 or crm_admin.adminType=3)");
            return Response::json($admins);
        }
        if($searchTerm==0){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin JOIN CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id  and (crm_admin.adminType=2 or crm_admin.adminType=3)");
            return Response::json($admins);
        }
    }
    
    // ======================

    public function searchAdminCustomerLoginOrNot(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        
        if($searchTerm==2){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id
                            WHERE  crm_admin.id not in(
                            SELECT * FROM(
                            SELECT DISTINCT admin_id FROM(
                            SELECT * FROM (
                            SELECT * FROM(
                            SELECT PSN FROM Shop.dbo.Peopels WHERE  PSN in(
                            SELECT customerId FROM NewStarfood.dbo.star_customerSession1))a
                            JOIN   (SELECT customer_id,admin_id FROM CRM.dbo.crm_customer_added WHERE  returnState=0 )b ON a.PSN=b.customer_id)c)f)g
                            )  and (crm_admin.adminType=2 or crm_admin.adminType=3)");
            return Response::json($admins);
        }
        
        if($searchTerm==1){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id
                            WHERE  crm_admin.id in(
                            SELECT * FROM(
                            SELECT DISTINCT admin_id FROM(
                            SELECT * FROM (
                            SELECT * FROM(
                            SELECT PSN FROM Shop.dbo.Peopels WHERE  PSN in(
                            SELECT customerId FROM NewStarfood.dbo.star_customerSession1))a
                            JOIN   (SELECT customer_id,admin_id FROM CRM.dbo.crm_customer_added WHERE  returnState=0 )b ON a.PSN=b.customer_id)c)f)g
                            )  and (crm_admin.adminType=2 or crm_admin.adminType=3)");
            return Response::json($admins);
        }
        if($searchTerm==0){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,
            sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id 
            FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id  WHERE  crm_admin.adminType=2 or crm_admin.adminType=3");
            return Response::json($admins);
        }
    }
    
    // ======================

    public function getAdminTodayInfo(Request $request)
    {
        $adminId=$request->get("asn");
        $todayDate=Carbon::now()->format('Y-m-d');
        $today=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        
        $todayInfo=DB::select("SELECT * FROM (SELECT countPeopel,countFactor,countComment,a.admin_id FROM(
                            SELECT COUNT(crm_customer_added.id)as countPeopel,crm_customer_added.admin_id FROM CRM.dbo.crm_customer_added WHERE  crm_customer_added.returnState=0 GROUP BY    admin_id)
                            a
                            left JOIN   (SELECT COUNT(FactorHDS.SerialNoHDS) as countFactor,admin_id FROM Shop.dbo.FactorHDS JOIN   CRM.dbo.crm_customer_added ON FactorHDS.CustomerSn=crm_customer_added.customer_id  WHERE  FactorHDS.FactDate='".$today."'  GROUP BY    admin_id)
                            b
                            ON a.admin_id=b.admin_id
                            left JOIN   (SELECT COUNT(id) as countComment,crm_comment.adminId FROM CRM.dbo.crm_comment WHERE  crm_comment.TimeStamp='".$todayDate."' GROUP BY    adminId)
                            c
                            ON a.admin_id=c.adminId)d
                            JOIN   CRM.dbo.crm_admin ON d.admin_id=crm_admin.id
                            WHERE  d.admin_id=".$adminId);
        
        $todayAdminInfo=DB::select("SELECT count(customerId) countComment FROM(
                                SELECT * FROM (SELECT * FROM (
                                SELECT Peopels.Name,Peopels.PSN,admin_id,returnState FROM Shop.dbo.Peopels
                                JOIN   CRM.dbo.crm_customer_added ON Peopels.PSN=CRM.dbo.crm_customer_added.customer_id)
                                a
                                left JOIN   (SELECT COUNT(FactorHDS.SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS WHERE  FactorHDS.FactDate='".$today."'  GROUP BY    FactorHDS.CustomerSn)
                                b
                                ON a.PSN=b.CustomerSn
                                WHERE  returnState=0 AND  admin_id=".$adminId.")c
                                JOIN   (SELECT MAX(timeStamp) as maxHour,customerId FROM CRM.dbo.crm_comment WHERE  CAST(timeStamp as DATE)='".$todayDate."' GROUP BY    customerId)d
                                ON d.customerId=c.PSN)d");
        
                                $countFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactor FROM Shop.dbo.FactorHDS WHERE  FactorHDS.CustomerSn in(SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE  admin_id=".$adminId." and returnState=0) and FactorHDS.FactDate='".$today."'");
        $countCustomers=DB::select("SELECT COUNT(id) as countCustomer FROM CRM.dbo.crm_customer_added WHERE  returnState=0 and admin_id=".$adminId);
        $countComment=0;
        $countFactor=0;
        $countCustomer=0;
        
        foreach ($todayAdminInfo as $info) {
            $countComment=$info->countComment;
        }
        
        foreach ($countFactors as $fact) {
            $countFactor=$fact->countFactor;
        }
        
        foreach ($countCustomers as $cust) {
            $countCustomer=$cust->countCustomer;
        }
        $loginTime=DB::table("CRM.dbo.crm_loginTrack")->where('adminId',$adminId)->select('loginTime')->first();
        $loginTime1=0;
        
        if($loginTime){
        $loginTime1=$loginTime->loginTime;
        }
        
        $adminTodayInfo=array(array('countComments'=>$countComment,'countFctors'=>$countFactor,'countCustomers'=>$countCustomer,'loginTime'=>$loginTime1));
        
        $customers=DB::select("SELECT * FROM (SELECT * FROM (
                        SELECT Peopels.Name,Peopels.PSN,admin_id,returnState FROM Shop.dbo.Peopels
                        JOIN   CRM.dbo.crm_customer_added ON Peopels.PSN=CRM.dbo.crm_customer_added.customer_id)
                        a
                        left JOIN   (SELECT COUNT(FactorHDS.SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS WHERE  FactorHDS.FactDate='".$today."'  GROUP BY    FactorHDS.CustomerSn)
                        b
                        ON a.PSN=b.CustomerSn
                        WHERE  returnState=0 and admin_id=".$adminId.")c
                        JOIN   (SELECT MAX(timeStamp) as maxHour,customerId FROM CRM.dbo.crm_comment WHERE  CAST(timeStamp as DATE)='".$todayDate."' GROUP BY    customerId)d
                        ON d.customerId=c.PSN");
        return Response::json([$todayInfo,$customers,$adminTodayInfo]);
    }
        
    // ======================

    public function checkUserNameExistance(Request $request)
    {
        $username=$request->get("username");
        $countExistance=DB::table("CRM.dbo.crm_admin")->where('username',$username)->count();
        return Response::json($countExistance);
    }
        
    // ======================

    public function getAdminForEmpty(Request $request)
    {
        $adminId=$request->get("asn");
        $admin=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->get();
        return Response::json($admin);
    }
        
    // ======================

    public function getAdminForMove(Request $request)
    {
        $adminId=$request->get("asn");
        $admin=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->get();
        $otherAdmins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE id not in(SELECT DISTINCT admin_id FROM CRM.dbo.crm_customer_added WHERE  returnState=0) and id !=".$adminId." and (adminType=3 or adminType=2)");
        return Response::json([$admin,$otherAdmins]);
    }
    
    // ======================

    public function emptyAdmin(Request $request)
    {
        $adminId=$request->get("asn");
        $admins=DB::select("SELECT minDate,countPeopel,adminId,discription,name,lastName FROM(
                        SELECT MIN(addedTime) as minDate,COUNT(customer_id) as countPeopel,adminId FROM(
                        SELECT crm_admin.id as adminId,crm_customer_added.addedTime,crm_customer_added.customer_id FROM CRM.dbo.crm_admin 
                        JOIN   CRM.dbo.crm_customer_added ON crm_admin.id=crm_customer_added.admin_id WHERE  crm_customer_added.returnState=0)d  GROUP BY    adminId)d
                        left JOIN   (SELECT * FROM CRM.dbo.crm_admin)a ON a.id=d.adminId WHERE  adminId=".$adminId);
        
        $info=DB::select("SELECT COUNT(customer_id)as countPeopels,crm_customer_added.admin_id FROM CRM.dbo.crm_customer_added 
                        JOIN   CRM.dbo.crm_admin ON crm_customer_added.admin_id=crm_admin.id 
                        WHERE  returnState=0 and crm_customer_added.admin_id=".$adminId." GROUP BY    admin_id");
        
        $customers=DB::select("SELECT customer_id,returnState,addedTime FROM CRM.dbo.crm_customer_added 
        WHERE  returnState=0 and crm_customer_added.admin_id=".$adminId);

        $countAllFactor=0;
        $sumAllFactor=0;
        $countAllReturnedFactor=0;
        $sumAllReturnedFactor=0;
        $seenCustomers=array();
        
        foreach ($customers as $customer) {
            $removedDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
            $addedDate=Jalalian::fromCarbon(Carbon::parse($customer->addedTime))->format('Y/m/d');
            $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=3 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."' GROUP BY    FactorHDS.SerialNoHDS");
            $returnedFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=4 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."' GROUP BY    FactorHDS.SerialNoHDS");

            foreach ($factors as $factor) {
                if($factor->countFactors>0 and !in_array($customer->customer_id, $seenCustomers)){
                array_push($seenCustomers,$customer->customer_id);
                }
                $countAllFactor+=$factor->countFactors;
                $sumAllFactor+=$factor->sumFactors;
            }
            
            foreach ($returnedFactors as $factor) {
                $countAllReturnedFactor+=$factor->countFactors;
                $sumAllReturnedFactor+=$factor->sumFactors;
            }
        }
        $boughtPeopelsCount=count($seenCustomers);
        
        foreach ($info as $infor) {
            $infor->countFactor=$countAllFactor;
            $infor->totalMoneyHds=$sumAllFactor;
            $infor->boughtPeopelsCount=$boughtPeopelsCount;
            $infor->sumAllReturnedFactor=$sumAllReturnedFactor;
        }
        $exactAdmin;
        
        foreach ($admins as $admin) {
            $exactAdmin=$admin;
        }
        $exactInfo;
        
        foreach ($info as $infor) {
            $exactInfo=$infor;
        }
        
        $lastHistoryInfo=DB::select("SELECT * FROM CRM.DBO.crm_adminHistory WHERE crm_adminHistory.timeStamp=(SELECT MAX(timeStamp) as MaxDate FROM CRM.dbo.crm_adminHistory WHERE adminId=".$adminId." GROUP BY    adminId)");
        $lastMonthMoney=0;
        
        foreach ($lastHistoryInfo as $history) {
            $lastMonthMoney=$history->factorAllMoney;
        }



        $history=DB::table("CRM.dbo.crm_adminHistory")->where("adminId",$adminId)->get();
        $minDate=DB::table("CRM.dbo.crm_customer_added")->where("returnState",0)->where("admin_id",$adminId)->min("addedTime");
        $countAllFactor=0;
        $sumAllFactor=0;
        $countAllReturnedFactor=0;
        $sumAllReturnedFactor=0;
        $customers=DB::select("SELECT customer_id,addedTime FROM CRM.dbo.crm_customer_added WHERE  admin_id=".$adminId." and returnState=0");
        $endDate=Jalalian::fromCarbon(Carbon::parse($minDate))->format('Y/m/d');
        $startDate=Jalalian::fromCarbon(Carbon::parse($minDate)->subdays(30))->format('Y/m/d');

        foreach ($customers as $customer) {
            $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactType=3 and FactorHDS.FactDate>='".$startDate."' and FactorHDS.FactDate<='".$endDate."'  GROUP BY    FactorHDS.CustomerSn");
            foreach ($factors as $factor) {
                $countAllFactor+=$factor->countFactors;
                $sumAllFactor+=$factor->sumFactors;
            }

            $returnFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactType=4 and FactorHDS.FactDate>='".$startDate."' and FactorHDS.FactDate<='".$endDate."'  GROUP BY    FactorHDS.CustomerSn");
            foreach ($returnFactors as $factor) {
                $countAllReturnedFactor+=$factor->countFactors;
                $sumAllReturnedFactor+=$factor->sumFactors;
            }
        }

        $meanIncreas=($lastMonthMoney - $sumAllFactor)/$sumAllFactor;
        $comment=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->first();

        $countAllCommentedCustomers=DB::select("select COUNT(customerId) AS countComment from(
                                                select distinct customerId from CRM.dbo.crm_comment where adminId=$adminId and TimeStamp>=(select min(addedTime) from CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId and customer_id not in(select customerId from CRM.dbo.crm_returnCustomer where returnState=1)
                                                and customer_id not in(select customerId from CRM.dbo.crm_inactiveCustomer where state=1)
                                                )
                                                and TimeStamp<=(select max(removedTime) from CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId and customer_id not in(select customerId from CRM.dbo.crm_returnCustomer where returnState=1)
                                                and customer_id not in(select customerId from CRM.dbo.crm_inactiveCustomer where state=1)
                                                )
                                                )a ");
        $allActiveCustomerCount=DB::select("SELECT COUNT(PSN) as countActiveCustomers FROM Shop.dbo.Peopels WHERE  PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE  returnState=0) and Peopels.CompanyNo=5 and Peopels.GroupCode IN ( ".implode(",",Session::get("groups")).")");
        $nocommentedCustomers=$allActiveCustomerCount[0]->countActiveCustomers - $countAllCommentedCustomers[0]->countComment;
        if($nocommentedCustomers<0){
            $nocommentedCustomers=0; 
        }

        $todayDate=Carbon::now()->format('Y-m-d');
        $countNoDoneWork=\DB::select("select sum(countJob) as countJob from(
            select COUNT(id) as countJob,specifiedDate from (
                            select * from (
                            select crm_workList.commentId,crm_workList.id,crm_workList.doneState,crm_workList.specifiedDate,crm_comment.customerId from CRM.dbo.crm_workList join CRM.dbo.crm_comment on crm_workList.commentId=crm_comment.id where doneState=0)a
                            join (select customer_id,returnState,admin_id from CRM.dbo.crm_customer_added )c on a.customerId =c.customer_id where c.returnState=0 and admin_id=".\Session::get('asn').")b
                            where specifiedDate<='".$todayDate."'
                            group by specifiedDate)a");

        DB::table("CRM.dbo.crm_adminHistory")->insert(['adminId'=>$adminId,'countPeople'=>$exactAdmin->countPeopel,'countFactor'=>$exactInfo->countFactor,'countBuyPeople'=>$exactInfo->boughtPeopelsCount,'factorAllMoney'=>$exactInfo->totalMoneyHds
        ,'lastMonthAllMoney'=>$sumAllFactor,'lastMonthReturnedAllMoney'=>$exactInfo->sumAllReturnedFactor,'meanIncrease'=>$meanIncreas,'comment'=>"".$comment->comment."",'noCommentCust'=>$nocommentedCustomers,'noDoneWork'=>$countNoDoneWork[0]->countJob]);
        DB::update("UPDATE CRM.dbo.crm_customer_added set removedTime='".Carbon::now()."' WHERE  returnState=0 and admin_id=".$adminId);
        DB::update("UPDATE CRM.dbo.crm_customer_added set returnState=1, gotEmpty=1 WHERE  returnState=0 and admin_id=".$adminId);
        
        DB::update("UPDATE CRM.dbo.crm_admin set emptyState=1 WHERE  id=".$adminId);
        return Response::json(1);
    }
        
    // ======================

    public function moveCustomerToAdmin(Request $request)
    {
        $holderAdmin=$request->get("holderID");
        $giverAdmin=$request->get("giverID");
        $adminId=$giverAdmin;

        $admins=DB::select("SELECT minDate,countPeopel,adminId,discription,name,lastName FROM(
                        SELECT MIN(addedTime) as minDate,COUNT(customer_id) as countPeopel,adminId FROM(
                        SELECT crm_admin.id as adminId,crm_customer_added.addedTime,crm_customer_added.customer_id 
                        FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_customer_added ON crm_admin.id=crm_customer_added.admin_id 
                        WHERE  crm_customer_added.returnState=0)d  GROUP BY    adminId)d
                        left JOIN   (SELECT * FROM CRM.dbo.crm_admin)a ON a.id=d.adminId WHERE  adminId=".$adminId);
                        
        $info=DB::select("SELECT COUNT(customer_id)as countPeopels,crm_customer_added.admin_id FROM CRM.dbo.crm_customer_added JOIN   CRM.dbo.crm_admin ON crm_customer_added.admin_id=crm_admin.id WHERE  returnState=0 and crm_customer_added.admin_id=".$adminId." GROUP BY    admin_id");
        $customers=DB::select("SELECT customer_id,returnState,addedTime FROM CRM.dbo.crm_customer_added WHERE  returnState=0 and crm_customer_added.admin_id=".$adminId);
        $countAllFactor=0;
        $sumAllFactor=0;
        $countAllReturnedFactor=0;
        $sumAllReturnedFactor=0;
        $seenCustomers=array();
        
        foreach ($customers as $customer) {
            $removedDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
            $addedDate=Jalalian::fromCarbon(Carbon::parse($customer->addedTime))->format('Y/m/d');
            $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."' and FactType=3  GROUP BY    FactorHDS.SerialNoHDS");
            $returnedFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."' and FactType=4  GROUP BY    FactorHDS.SerialNoHDS");

            foreach ($factors as $factor) {
                if($factor->countFactors>0 and !in_array($customer->customer_id, $seenCustomers)){
                array_push($seenCustomers,$customer->customer_id);
                }
                $countAllFactor+=$factor->countFactors;
                $sumAllFactor+=$factor->sumFactors;
            }
            
            foreach ($returnedFactors as $factor) {
                $countAllReturnedFactor+=$factor->countFactors;
                $sumAllReturnedFactor+=$factor->sumFactors;
            }
        }
        
        $boughtPeopelsCount=count($seenCustomers);
        
        foreach ($info as $infor) {
            $infor->countFactor=$countAllFactor;
            $infor->totalMoneyHds=$sumAllFactor;
            $infor->boughtPeopelsCount=$boughtPeopelsCount;
            $infor->countAllReturnedFactor=$countAllReturnedFactor;
            $infor->sumAllReturnedFactor=$sumAllReturnedFactor;
            $infor->countAllReturnedFactor=$countAllReturnedFactor;
            $infor->sumAllReturnedFactor=$sumAllReturnedFactor;
        }
        $exactAdmin;
        
        foreach ($admins as $admin) {
            $exactAdmin=$admin;
        }
        
        $exactInfo;
        
        foreach ($info as $infor) {
            $exactInfo=$infor;
        }
        
        $customers=DB::select("SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE  returnState=0 and admin_id=".$adminId);
        $lastHistoryInfo=DB::select("SELECT * FROM CRM.dbo.crm_adminHistory WHERE crm_adminHistory.timeStamp=(SELECT MAX(timeStamp) as MaxDate FROM CRM.dbo.crm_adminHistory WHERE adminId=".$adminId." GROUP BY    adminId)");
        $lastMonthMoney=0;
        
        foreach ($lastHistoryInfo as $history) {
            $lastMonthMoney=$history->factorAllMoney;
        }
        
        DB::table("CRM.dbo.crm_adminHistory")->insert(['adminId'=>$adminId,'countPeople'=>$exactAdmin->countPeopel,'countFactor'=>$exactInfo->countFactor,'countBuyPeople'=>$exactInfo->boughtPeopelsCount,'factorAllMoney'=>$exactInfo->totalMoneyHds,'lastMonthAllMoney'=>$lastMonthMoney,'lastMonthReturnedAllMoney'=>$exactInfo->sumAllReturnedFactor]);
        
        $customersToMove=DB::select("SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE admin_id=".$giverAdmin." and returnState!=1");
        
        foreach ($customersToMove as $customer) {
            DB::table("CRM.dbo.crm_customer_added")->insert(['admin_id'=>$holderAdmin,'customer_id'=>$customer->customer_id,'returnState'=>0]);
        }
        
        DB::table("CRM.dbo.crm_customer_added")->where('admin_id',$giverAdmin)->update(['returnState'=>1,'removedTime'=>Carbon::now()]);
        DB::table("CRM.dbo.crm_admin")->where('id',$giverAdmin)->update(['emptyState'=>1]);
        DB::table("CRM.dbo.crm_admin")->where('id',$holderAdmin)->update(['emptyState'=>0]);
        return Response::json(1);
    }
        
    // ======================

    public function editAdmintStuff(Request $request)
    {
        $name=$request->post("name");
        $userName=$request->post("userName");
        $lastName=$request->post("lastName");
        $password=$request->post("password");
        $adminType=$request->post("adminType");
        $phone=$request->post("phone");
        $address=$request->post("address");
        $sex=$request->post("sex");
        $discription=$request->post("discription");
        $hasAsses=$request->post("hasAsses");
        $hasAllCustomer=$request->post("hasAllCustomer");
        $adminId=$request->get('adminId');
        
        if($request->file('picture')){
            $picture=$request->file('picture');
            $fileName=$adminId.".jpg";
            $picture->move("resources/assets/images/admins/",$fileName);
        }

        DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(['username'=>"".$userName."",'name'=>"".$name."",'lastName'=>"".$lastName."",'adminType'=>$adminType,'password'=>"".$password."",'activeState'=>1,'phone'=>$phone,'address'=>$address,'sex'=>"".$sex."",'discription'=>"".$discription."",'hasAsses'=>$hasAsses,'driverId'=>0,'hasAllCustomer'=>$hasAllCustomer]);
        
        return redirect("/assignCustomer");
    }
        
    // ======================

    public function editAdmintListStuff(Request $request)
    {
        $name=$request->post("name");
        $userName=$request->post("userName");
        $lastName=$request->post("lastName");
        $password=$request->post("password");
        $adminType=$request->post("adminType");
        $phone=$request->post("phone");
        $address=$request->post("address");
        $sex=$request->post("sex");
        $discription=$request->post("discription");
        $hasAsses=$request->post("hasAsses");
        $hasAllCustomer=$request->post("hasAllCustomer");
        $adminId=$request->get('adminId');
        
        if($request->file('picture')){
            $picture=$request->file('picture');
            $fileName=$adminId.".jpg";
            $picture->move("resources/assets/images/admins/",$fileName);
        }
        
        DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(['username'=>"".$userName."",'name'=>"".$name."",'lastName'=>"".$lastName."",'adminType'=>$adminType,'password'=>"".$password."",'activeState'=>1,'phone'=>$phone,'address'=>$address,'sex'=>"".$sex."",'discription'=>"".$discription."",'hasAsses'=>$hasAsses,'driverId'=>0,'hasAllCustomer'=>$hasAllCustomer]);
        return redirect("/listKarbaran");
    }

    
    // ======================

    public function deleteAdmin(Request $request)
    {
        $asn=$request->get("asn");
        DB::table("CRM.dbo.crm_returnCustomer")->where("adminId",$asn)->delete();
        DB::table("CRM.dbo.crm_alarm")->where("adminId",$asn)->delete();
        DB::table("CRM.dbo.crm_assesment")->where("adminId",$asn)->delete();
        DB::table("CRM.dbo.crm_comment")->where("adminId",$asn)->delete();
        DB::table("CRM.dbo.crm_customer_added")->where("admin_id",$asn)->delete();
        DB::table("CRM.dbo.crm_admin")->where("id",$asn)->delete();
        $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","crm_admin.discription")->orderby("admintype")->get();
        return Response::json($admins);
    }
        
    // ======================

    public function changeAlarm(Request $request){
        $comment=$request->get("comment");
        $adminId=Session::get("asn");
        $alarmDate=$request->get("alarmDate");
        $factorId=$request->get("factorId");
        DB::table("CRM.dbo.crm_alarm")->where('factorId',$factorId)->update(['state'=>1]);
        DB::table("CRM.dbo.crm_alarm")->insert(['comment'=>"".$comment."",'adminId'=>$adminId,'state'=>0,'alarmDate'=>"".$alarmDate."",'factorId'=>$factorId]);
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        
        $customers=DB::select("SELECT * FROM Shop.dbo.FactorHDS JOIN   Shop.dbo.Peopels ON FactorHDS.CustomerSn=Peopels.PSN
                        JOIN   CRM.dbo.crm_customer_added ON Peopels.PSN=crm_customer_added.customer_id JOIN   CRM.dbo.crm_admin ON crm_customer_added.admin_id=crm_admin.id
                        JOIN   CRM.dbo.crm_assesment ON crm_assesment.factorid=FactorHDS.SerialNoHDS
                        JOIN   Shop.dbo.PhoneDetail ON Peopels.PSN=PhoneDetail.SnPeopel
                        JOIN   CRM.dbo.crm_alarm ON crm_assesment.factorid=crm_alarm.factorid
                        WHERE  Peopels.GroupCode IN ( ".implode(",",Session::get("groups")).") and Peopels.CompanyNo=5  and crm_alarm.alarmDate<='".$todayDate."' and state=0");
        
        return Response::json($customers);
    }
        
    // ======================

    public function getAlarmHistory(Request $request)
    {
        $fsn=$request->get("fsn");
        $history=DB::table("CRM.dbo.crm_alarm")->where("factorId",$fsn)->get();
        return Response::json($history);
    }
        
    // ======================

    public function gotEmpty(Request $request)
    {
        $customers=DB::select("SELECT * FROM(
                        SELECT DISTINCT * FROM(
                        SELECT CRM.dbo.crm_customer_added.customer_id FROM CRM.dbo.crm_customer_added WHERE  gotEmpty=1 and customer_id not IN  (SELECT CRM.dbo.crm_returnCustomer.customerId FROM CRM.dbo.crm_returnCustomer WHERE  returnState=1)
                        )d
                        JOIN   (SELECT * FROM Shop.dbo.Peopels)c
                        ON c.PSN=d.customer_id
                        JOIN   (SELECT PhoneStr,SnPeopel FROM Shop.dbo.PhoneDetail)b ON d.customer_id=b.SnPeopel
                        WHERE  PSN not IN  (SELECT customerId FROM CRM.dbo.crm_inactiveCustomer WHERE  state=1))e
                        JOIN   (SELECT customerId,removedDate FROM(
                        SELECT DISTINCT customer_id as customerId FROM CRM.dbo.crm_customer_added WHERE   gotEmpty=1 and customer_id not in(SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE  returnState=0))a
                        JOIN   (SELECT MAX(removedTime)as removedDate,customer_id FROM CRM.dbo.crm_customer_added GROUP BY    customer_id)b ON a.customerId=b.customer_id)f ON f.customerId=e.PSN");
            
        $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where('crm_admin.adminType','!=',1)->where('crm_admin.adminType','!=',4)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType")->get();
        
        return view('admin.gotEmpty',['customers'=>$customers,'admins'=>$admins]);
    }
        
    // ======================
    
    public function getAdminInfo(Request $request)
    {
        $id=$request->get("id");
        $appositId=$id;
        $myId=Session::get('asn');
        $admin=DB::table("CRM.dbo.crm_admin")->where('id',$id)->first();
        $sendedMessages=DB::select("SELECT * FROM (
                                SELECT * FROM(
                                SELECT * FROM CRM.dbo.crm_message)a
                                JOIN   (SELECT name,id as adminId FROM CRM.dbo.crm_admin)c ON a.senderId=c.adminId )b
                                WHERE  (senderId=".$myId." and getterId=".$appositId.") or (senderId=".$appositId." and getterId=".$myId.") ORDER BY     messageDate desc");
        
        DB::update("UPDATE CRM.dbo.crm_message set readState=1 WHERE senderId=".$appositId." and getterId=".$myId);
        
        return Response::json([$sendedMessages,$appositId,$myId,$admin]);
    }
    
    // ======================

    public function getDiscusstion(Request $request)
    {
        $appositId=$request->get("sendId");
        $myId=Session::get('asn');
        $sendedMessages=DB::select("SELECT * FROM (
                                SELECT * FROM(
                                SELECT * FROM CRM.dbo.crm_message)a
                                JOIN   (SELECT name,id as adminId FROM CRM.dbo.crm_admin)c ON a.senderId=c.adminId )b
                                WHERE  (senderId=".$myId." and getterId=".$appositId.") or (senderId=".$appositId." and getterId=".$myId.") ORDER BY     messageDate desc");
              
        DB::update("UPDATE CRM.dbo.crm_message set readState=1 WHERE senderId=".$appositId." and getterId=".$myId);
              
        return Response::json([$sendedMessages,$appositId,$myId]);
    }
    
    // ======================

    public function addMessage(Request $request)
    {
        $senderId=Session::get("asn");
        $getterId=$request->get("getterId");
        $messageContent=$request->get("messageContent");
        DB::table("CRM.dbo.crm_message")->insert(['messageContent'=>"".$messageContent."",'readState'=>0,'senderId'=>$senderId,'getterId'=>$getterId]);
        $sendedMessages=DB::select("SELECT * FROM (
                                SELECT * FROM(
                                SELECT * FROM CRM.dbo.crm_message)a
                                JOIN   (SELECT name,id as adminId FROM CRM.dbo.crm_admin)c ON a.senderId=c.adminId )b
                                WHERE  (senderId=".$senderId." and getterId=".$getterId.") or (senderId=".$getterId." and getterId=".$senderId.") ORDER BY     messageDate desc");
        return Response::json([$sendedMessages,$getterId,$senderId]);
    }
    
    // ======================

    public function addDiscussion(Request $request)
    {
        $senderId=Session::get("asn");
        $getterId=$request->get("getterId");
        $messageContent=$request->get("messageArea");
        DB::table("CRM.dbo.crm_message")->insert(['messageContent'=>"".$messageContent."",'readState'=>0,'senderId'=>$senderId,'getterId'=>$getterId]);
        $sendedMessages=DB::select("SELECT * FROM (
                                SELECT * FROM(
                                SELECT * FROM CRM.dbo.crm_message)a
                                JOIN   (SELECT name,id as adminId FROM CRM.dbo.crm_admin)c ON a.senderId=c.adminId )b
                                WHERE  (senderId=".$senderId." and getterId=".$getterId.") or (senderId=".$getterId." and getterId=".$senderId.") ORDER BY     messageDate desc");
        return Response::json([$sendedMessages,$getterId,$senderId]);
    }
    
    // ======================

    public function addAlarmClock(Request $request)
    {
        $comment=$request->get("comment");
        $adminId=Session::get("asn");
        $dateTime=$request->get("dateTime");
        DB::table("CRM.dbo.crm_alarmClock")->insert(['comment'=>"".$comment."",'TimeStamp'=>"".$dateTime."",'adminId'=>$adminId,'doneState'=>0]);
        return Response::json("1");
    }
        
    // ======================

    public function getAlarmTime(Request $request)
    {
        $adminId=Session::get("asn");
        $nowTime=Carbon::now();
        $alarmTime=DB::table("CRM.dbo.crm_alarmClock")->where('doneState',0)->where('adminId',$adminId)->first();
        $alarmTime1=list($t,$d)=explode(" ",$alarmTime->TimeStamp);
        $newTime=$d.' '.$t;
        $alarmTime2=Jalalian::fromFormat('Y/m/d H:i:s', $newTime)->toCarbon();
        $result=$nowTime->gte($alarmTime2);
        $diff = $nowTime->diffInSeconds($alarmTime2);
        return Response::json([$result,$diff,$newTime,$alarmTime]);
    }
        
    // ======================

    public function offAlarmClock(Request $request)
    {
        $adminId=Session::get('asn');
        DB::table("CRM.dbo.crm_alarmClock")->where("adminId",$adminId)->update(["doneState"=>1]);
        return Response::json(1);
    }

    // ======================

    public function visitorReport(){
        $visitors=DB::select("SELECT lastVisit,PSN,countLogin,Name,platform,browser,firstVisit FROM(
                        SELECT * FROM(
                        SELECT * FROM(
                        SELECT * FROM(
                        SELECT * FROM(
                        SELECT MAX(visitDate) as lastVisit,customerId FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)a
                        JOIN   (SELECT Name,PSN,GroupCode FROM Shop.dbo.Peopels)b
                        ON a.customerId=b.PSN)c
                        JOIN   (SELECT COUNT(id) as countLogin,customerId as csn FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)d ON c.customerId=d.csn)e
                        JOIN   (SELECT visitDate,browser,platform FROM NewStarfood.dbo.star_customerTrack)f ON e.lastVisit=f.visitDate)g
                        JOIN   (SELECT MIN(visitDate) as firstVisit,customerId as CUSTOMERID2 FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)h ON g.PSN=h.CUSTOMERID2)i");
        
        return view("admin.visitorReport",['visitors'=>$visitors]);
    }
        
    // ======================

    public function getCustomerLoginInfo(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $visitors=DB::select("SELECT lastVisit,PSN,countLogin,Name,platform,browser,firstVisit FROM(
                        SELECT * FROM(
                        SELECT * FROM(
                        SELECT * FROM(
                        SELECT * FROM(
                        SELECT MAX(visitDate) as lastVisit,customerId FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)a
                        JOIN   (SELECT Name,PSN,GroupCode FROM Shop.dbo.Peopels)b
                        ON a.customerId=b.PSN)c
                        JOIN   (SELECT COUNT(id) as countLogin,customerId as csn FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)d ON c.customerId=d.csn)e
                        JOIN   (SELECT visitDate,browser,platform FROM NewStarfood.dbo.star_customerTrack)f ON e.lastVisit=f.visitDate)g
                        JOIN   (SELECT MIN(visitDate) as firstVisit,customerId as CUSTOMERID2 FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)h ON g.PSN=h.CUSTOMERID2)i
                        WHERE Name LIKE '%".$searchTerm."%'");
        
        return Response::json($visitors);
    }

    public function tempRoute(Request $request)
    {
        $adminId=19;
        $countAllCommentedCustomers=DB::select("select COUNT(customerId) AS countComment from(
            select distinct customerId from CRM.dbo.crm_comment where adminId=$adminId and TimeStamp>=(select min(addedTime) from CRM.dbo.crm_customer_added where returnState=1 and admin_id=$adminId and customer_id not in(select customerId from CRM.dbo.crm_returnCustomer where returnState=1)
            and customer_id not in(select customerId from CRM.dbo.crm_inactiveCustomer where state=1)
            )
            and TimeStamp<=(select max(removedTime) from CRM.dbo.crm_customer_added where returnState=1 and admin_id=$adminId and customer_id not in(select customerId from CRM.dbo.crm_returnCustomer where returnState=1)
            and customer_id not in(select customerId from CRM.dbo.crm_inactiveCustomer where state=1)
            )
            )a ");
        $allActiveCustomerCount=DB::select("SELECT COUNT(PSN) as countActiveCustomers FROM Shop.dbo.Peopels WHERE  PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE  returnState=1  and admin_id=$adminId
        and PSN not in(select customerId from CRM.dbo.crm_returnCustomer where returnState=1)
            and PSN not in(select customerId from CRM.dbo.crm_inactiveCustomer where state=1)
        ) and Peopels.CompanyNo=5 and Peopels.GroupCode IN ( ".implode(",",Session::get("groups")).")");
        $nocommentedCustomers=$allActiveCustomerCount[0]->countActiveCustomers - $countAllCommentedCustomers[0]->countComment;
        if($nocommentedCustomers<0){
            $nocommentedCustomers=0; 
        }
        $todayDate=Carbon::now()->format('Y-m-d');
        $countNoDoneWork=\DB::select("select sum(countJob) as countJob from(
                                    select COUNT(id) as countJob,specifiedDate from (
                                    select * from (
                                    select crm_workList.commentId,crm_workList.id,crm_workList.doneState,crm_workList.specifiedDate,crm_comment.customerId from CRM.dbo.crm_workList join CRM.dbo.crm_comment on crm_workList.commentId=crm_comment.id where doneState=0)a
                                    join (select customer_id,returnState,admin_id from CRM.dbo.crm_customer_added )c on a.customerId =c.customer_id where c.returnState=0 and admin_id=".$adminId.")b
                                    where specifiedDate<='2022-09-24'
                                    group by specifiedDate)a");
        return $countNoDoneWork[0]->countJob;
// 'noCommentCust'=>$nocommentedCustomers,
        DB::table("CRM.dbo.crm_adminHistory")->where('adminId',$adminId)->update(['noDoneWork'=>$countNoDoneWork[0]->countJob]);
    }
}
