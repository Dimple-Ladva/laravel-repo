<?php

namespace App\Models;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class Membership extends Model
{
    use HasFactory;
    public static $Instance;
    public static function obj()
    {
        if(!is_object(self::$Instance))
            self::$Instance = new self();

        return self::$Instance;
    }

    use Notifiable;

    protected  $table        = 'memberships';
    protected  $primaryKey   = 'mem_id';
    public     $field_prefix = 'mem_';
    protected  $fillable     = ['mem_pcam_id','mem_pm_id','mem_total','mem_status','mem_since','mem_expires','mem_adt','mem_udt','mem_level', 'mem_is_deleted','is_mem_expiry'];
    // public     $timestamps   =  false;
    const CREATED_AT = 'mem_adt';
    const UPDATED_AT = 'mem_udt';

    /*public static $rulesOnAddMembership = [
        'mem_level' => 'required',
        'mem_pm_id' => 'required',

    ];
    public static $messages = [
        'mem_level.required'        => 'Please Select member level',
        'mem_pm_id.required'        => 'Please Select product',

    ];*/

    public function Insert($POST)
    {
        try{
            //echo '<pre>'; print_r($POST);die;
            if (isset($POST['mem_since']) && $POST['mem_since'] != '') {
                $POST['mem_since'] = date('Y-m-d', strtotime($POST['mem_since']));
            }
            if (isset($POST['mem_expires']) && $POST['mem_expires'] != '') {
                $POST['mem_expires'] = date('Y-m-d', strtotime($POST['mem_expires']));
            }

            $objMemberships = new Membership();
            $objMemberships->fill($POST);
            $objMemberships->save();
            //echo 123; die;
            //echo"<pre>";print_r($a);die;
            if (!$objMemberships->save()) {
                //echo 123; die;
                return false;
            } else {
                //echo 123457; die;
                return true;
            }
        }
        catch(\Illuminate\Database\QueryException $ex){
            return $ex->getMessage();
        }
    }
    public function getMembershipDataById($id)
    {
        //return $this->where('mem_pcam_id',$id)->first();

        $sql = $this->select('*')
            ->leftjoin("product_master","product_master.pm_id","=","$this->table.mem_pm_id")->where('mem_pcam_id',$id)->get();
        //->leftjoin("category_master","category_master.cm_id","=","$this->table.mem_level")
        /*echo '<pre>';print_r($sql->toSql());
        echo '<pre>';print_r($sql->getBindings());die();*/
        return $sql;
    }
    public function getQueryParameter($sql,$POST)
    {
        //echo '<pre>';print_r($POST['search_all']);die;
        if(isset($POST['search_all']) && $POST['search_all'] != '')
        {
            $sql->where(function ($query) use ($POST){
                $query->whereraw("LOWER(pcam_fname) LIKE '%".strtolower($POST['search_all'])."%'")
                      ->orWhereraw("LOWER(pcam_email) LIKE '%".strtolower($POST['search_all'])."%'");

            });
        }

        /*echo print_r($sql->toSql());
        echo print_r($sql->getBindings());
        die;*/
        return $sql;
    }
    public function getAllMembershipData($request)
    {
        //echo '<pre>'; print_r($request->all); die;
        $sort_order = ($request->get('so') != '')?$request->get('so'):'mem_adt';
        $sort_dir   = ($request->get('sd') != '')?$request->get('sd'):'DESC';
        $limit      = ($request->post('limit') > 0)?$request->post('limit'):config('constant.PAGE_SIZE');

        $sql =$this->select('*')
        ->leftjoin("pca_member","pca_member.pcam_id","=","$this->table.mem_pcam_id")
        ->leftjoin("product_master","product_master.pm_id","=","$this->table.mem_pm_id");
        if(count($request->post()) > 0)
        {
            // echo 12356567;die;
            $this->getQueryParameter($sql,$request->post());
        }

        $MembershipInfo =  $sql->orderBy($sort_order,$sort_dir)->paginate($limit);
        //echo print_r($sql->toSql());die;
        // echo'<pre>'; print_r($categoryInfo);die;
        return $MembershipInfo;
    }
    public function getAllMembershipDataByUserId($id)
    {
//        echo $id;die;
        $sort_order = 'mem_adt';
        $sort_dir   = 'DESC';
        $limit      = 15;

        $sql =$this->select('*') ->where('pca_member.pcam_user_id',$id)
            ->leftjoin("pca_member","pca_member.pcam_id","=","$this->table.mem_pcam_id")
            ->leftjoin("product_master","product_master.pm_id","=","$this->table.mem_pm_id");

//        echo print_r($sql->toSql());
//        print_r($sql->getBindings());die;
        $MembershipInfo =  $sql->orderBy($sort_order,$sort_dir)->paginate($limit);

        return $MembershipInfo;
    }
    public function deletes($id)
    {
        $objMembership = Membership::where("mem_pcam_id","=",$id)->delete();
        if(!$objMembership){
            return false;
        }
        else
        {
            Subscription::obj()->deletes($id);
            return true;
        }
    }
    public function Updates($POST,$id)
    {
       // echo '<pre>'; print_r($POST); die;
        if(isset($POST['mem_since']) && $POST['mem_since'] != '')
        {
            $POST['mem_since']     =  str_replace(',','',$POST['mem_since']);
            $POST['mem_since']     =   date('Y-m-d h:i:sa',strtotime($POST['mem_since']));
        }
        if(isset($POST['mem_expires']) && $POST['mem_expires'] != '')
        {
            $POST['mem_expires']     =  str_replace(',','',$POST['mem_expires']);
            $POST['mem_expires']     =   date('Y-m-d H:i:sa',strtotime($POST['mem_expires']));
            $POST['is_mem_expiry']   = 0;
        }
        else{
            /*$memData = Membership::find($id);
            $POST['mem_expires'] = isset($memData['mem_expires'])?$memData['mem_expires']:null;*/
            $POST['is_mem_expiry']   = 1;
        }

       // $objMembership=Membership::find($id)->update($POST);
        if(isset($POST['mem_status']))
        {
//            $objMembership=$this->updateOrCreate(['mem_pcam_id' => $id], $POST);
                $objMembership = Membership::find($id)->update($POST);
        }
        else{
            $objMembership=$this->updateOrCreate(['mem_id' => $id], $POST);
        }

        if(!$objMembership){
            return false;
        }
        else{
            return true;
        }

    }
    public function getPcaMember($id)
    {
        $sql    =   $this->select('*')->where('mem_id',$id)
            ->leftjoin("pca_member","pca_member.pcam_id","=","$this->table.mem_pcam_id")->get();
        return $sql;
    }
    public function UpdateStatus($POST,$id)
    {
        $objMembership  =   $this->find($id)->update($POST);
        if(!$objMembership){
            return false;
        }
        else{
            return true;
        }
    }
    public function getMembershipDataByPcaId($id)
    {
        $sql    =   $this->select('*')->where('mem_pcam_id', $id)->first();
        return $sql;
    }

    public function getMembershipDataByUserId($id)
    {
        //echo $id;die();
        $sql = $this->select('*')
                    ->leftjoin("pca_member","pca_member.pcam_id","=","$this->table.mem_pcam_id")
                    ->leftjoin("product_master","product_master.pm_id","=","$this->table.mem_pm_id")
                    //->leftjoin("order_master as OM","OM.order_user_id","=","pca_member.pcam_user_id")
                    ->leftjoin("subscription_order as SO",'SO.so_pcam_id','=','pca_member.pcam_id')
                    //->leftjoin("subscription_order as SO",'SO.so_user_id','=','pca_member.pcam_user_id')
                    ->where('pcam_user_id','=',$id)
                    ->get();
       // echo '<pre>';print_r($sql);die();
        /*echo '<pre>';print_r($sql->toSql());
        echo '<pre>';print_r($sql->getBindings());die();*/
        return $sql;
    }
    public function getSubDataById($Sub_id)
    {
        //echo $Sub_id;die();
        $sql = $this->select('*')
            ->leftjoin("pca_member","pca_member.pcam_id","=","$this->table.mem_pcam_id")
            ->leftjoin("product_master","product_master.pm_id","=","$this->table.mem_pm_id")
            //->leftjoin("order_master as OM","OM.order_user_id","=","pca_member.pcam_user_id")
            ->leftjoin("subscription_order","subscription_order.so_pcam_id","=","pca_member.pcam_id");
            //"OM.order_id","=","subscription_order.so_order_id")
        $sql = $sql->leftjoin("order_master as OM", function($join)
            {
                $join->on('OM.order_id', '=', 'subscription_order.so_order_id');
                $join->on('OM.order_user_id','=', 'pca_member.pcam_user_id');
            });
        $sql = $sql->where('so_id','=',$Sub_id)
                   ->first();
        /*echo '<pre>';print_r($sql->toSql());
        echo '<pre>';print_r($sql->getBindings());
        die();*/
        return $sql;
    }
    public function GetDataForChangeStatusPaused()
    {
        $date = date('Y-m-d');
        $arrPausedStatusData = $this->select('*')
                                    ->leftjoin("pca_member","pca_member.pcam_id","=","$this->table.mem_pcam_id")
                                    ->leftjoin("email_log","email_log.elog_user_id","=","pca_member.pcam_user_id")
                                    ->where("mem_expires","like","$date%")
                                    ->where("is_mem_expiry","=",0)
                                    ->get();

        return $arrPausedStatusData;
    }
    public function UpdateStatusPaused($id,$exp_date)
    {
        $exp_date = Carbon::parse($exp_date)->addMinute(1)->format('Y-m-d h:i');
        echo Carbon::now()->format('Y-m-d h:i');
        echo $exp_date;
        /*if($exp_date == (Carbon::now()->format('Y-m-d h:i')))
        {*/
            $sql = Membership::where('mem_pcam_id',$id)->update(['mem_status' => 'Paused']);
            return $sql;
      /*  }*/
    }
    public function GetId($post){
        $sql = $this->select('mem_id')->where('mem_pcam_id',$post['pcam_id']) ->first();
        return $sql;
    }

    public function getAllMembershiplevelData()
    {
        $arrData = array();
        $arrData['year'] =$this->select(DB::raw('count(mem_level) as count, mem_level, category_master.cm_name'))
          ->leftjoin("category_master","category_master.cm_id","=","$this->table.mem_level")
          ->where('mem_level', '!=', '')
          ->whereBetween('mem_adt', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ])->groupBy('mem_level','category_master.cm_name')
          ->get()->toArray();

        $arrData['lastmonth'] =$this->select(DB::raw('count(mem_level) as count, mem_level, category_master.cm_name'))
            ->leftjoin("category_master","category_master.cm_id","=","$this->table.mem_level")
            ->where('mem_level', '!=', '')
            ->whereBetween('mem_adt', [\Illuminate\Support\Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
            ->groupBy('mem_level','category_master.cm_name')
            ->get()->toArray();

        $arrData['currmonth'] =$this->select(DB::raw('count(mem_level) as count, mem_level, category_master.cm_name'))
            ->leftjoin("category_master","category_master.cm_id","=","$this->table.mem_level")
            ->where('mem_level', '!=', '')
            ->whereBetween('mem_adt', [\Illuminate\Support\Carbon::now()->firstOfMonth(),Carbon::now()])
            ->groupBy('mem_level','category_master.cm_name')
            ->get()->toArray();

        $arrData['week'] =$this->select(DB::raw('count(mem_level) as count, mem_level, category_master.cm_name'))
            ->leftjoin("category_master","category_master.cm_id","=","$this->table.mem_level")
            ->where('mem_level', '!=', '')
            ->where('mem_adt', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 1 WEEk)'))
            ->groupBy('mem_level','category_master.cm_name')
            ->get()->toArray();

        return $arrData;
    }
    public function getMembershipDataByCriteria($limit = false){

        $latestNextPaymentQuery = Subscription::select('so_pcam_id', DB::raw('MAX(so_next_payment) as max_next_payment'))
            ->groupBy('so_pcam_id');

        $subscriptionOrdersQuery = Subscription::select('so_user_id', 'subscription_order.so_pcam_id', 'so_orders_status', 'so_next_payment', 'so_orders_payment_method')
            ->fromSub($latestNextPaymentQuery, 'latest_next_payment')
            ->join('subscription_order', function ($join) {
                $join->on('subscription_order.so_pcam_id', '=', 'latest_next_payment.so_pcam_id')
                    ->on('subscription_order.so_next_payment', '=', 'latest_next_payment.max_next_payment');
            });

        $sql = Membership::query()
            ->leftJoinSub($subscriptionOrdersQuery, 'so', 'memberships.mem_pcam_id', '=', 'so.so_pcam_id')
            ->leftJoin('users','users.id','=','so.so_user_id')
            ->whereNotNull('mem_expires')
            ->whereRaw("DATE_FORMAT(so.so_next_payment, '%d') != DATE_FORMAT(memberships.mem_expires, '%d')")
            ->where('so.so_orders_payment_method', 'like', '%stripe')
//            ->whereDate('so.so_next_payment', '>=', now())
            ->whereIn('so.so_orders_status', ['Active', 'Renewal'])
//            ->where('so.so_pcam_id','=',142)
            ->orderBy('so.so_next_payment', 'asc');

//        echo '<pre>';print_r($sql->toSql());die;
            if ($limit && $limit > 0){
                $sql->limit($limit);
            }
            return $sql->get([
                'so.so_user_id',
                'so.so_pcam_id as so_pcam_id',
                'so.so_orders_status',
                'so.so_next_payment',
                'so.so_orders_payment_method',
                'memberships.mem_expires',
                'users.stripe_id as stripe_id'
            ]);

    }
    public function getAllActiveMembershipData(){
        $sql =  $this->select('*')
                ->leftjoin("pca_member","pca_member.pcam_id","=","$this->table.mem_pcam_id")
                ->leftjoin("product_master","product_master.pm_id","=","$this->table.mem_pm_id")
                ->where("$this->table.mem_status","=","Active")
                ->orWhere("$this->table.mem_status","=","Approved")
                ->orWhere("$this->table.mem_status","=","Pending")
                ->orWhere("$this->table.mem_status","=","Renewal")
                ->where("$this->table.mem_is_deleted","=","No")
                ->get();

        return $sql;
    }
}
