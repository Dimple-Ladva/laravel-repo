<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Common;
use App\Http\Controllers\Controller;
use App\Models\CategoryMaster;
use App\Models\EmailTemplate;
use App\Models\ErrorLog;
use App\Models\GeoState;
use App\Models\Membership;
use App\Models\OrderMaster;
use App\Models\Payment;
use App\Models\PcaMember;
use App\Models\Subscription;
use App\Models\TrackLog;
use App\Models\User;
use Dompdf\Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Stripe;
use App\Models\email_log;
use function PHPUnit\Framework\throwException;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\OrderInvoice;
use App\Models\OrderItem;
use App\Models\ProductMaster;
use App\Models\captcha;
use App\Models\SignupLog;

class MemberApplicationController extends Controller
{
    //
    public function __construct()
    {
        Common::main();
    }

    public function index(Request $request)
    {
        $_SESSION['member_session_id']=uniqid();
        $arrState       =   GeoState::obj()->getStates('us');

        return view('layouts.frontend_default_layout',
            [
                'T_Body'            =>   'front.membership-application.membership-application',

                'title'             =>   'Member Application',
                'JavaScript'        =>    array('membership-front','inputmask'),
                'Css'               =>    array('membership-application'),
                'arrState'          =>    $arrState,
                'is_clariety'       =>      true,
                'member_session_id'        =>     $_SESSION['member_session_id'],

            ])->render();
    }
    public function index2(Request $request)
    {

        $_SESSION['member_session_id']=uniqid();
        $arrState       =   GeoState::obj()->getStates('us');

        return view('layouts.frontend_default_layout',
            [
                'T_Body'            =>   'front.membership-application.new-membership-application',
                'title'             =>   'Member Application',
                'JavaScript'        =>    array('membership-front','inputmask'),
                'Css'               =>    array('membership-application'),
                'arrState'          =>    $arrState,
                'is_clariety'       =>      true,
                'member_session_id'        =>     $_SESSION['member_session_id'],

            ])->render();
    }
    public function reloadCaptcha()
    {
        return response()->json(['captcha'=> captcha_img()]);
    }
    public function store(Request $request,$type)
    {
        global $db;
        $POST = $request->all();

        if($type == 'proceed-to-payment')
        {
            if (isset($POST['pm_stripe_price_id']) && $POST['pm_stripe_price_id'] != '') {
                $IsMemberExist  = PcaMember::obj()->IsPcaMemberExist($POST['pcam_email']);
                $IsUserExist =  User::obj()->ExistEmail($POST['pcam_email']);

                $POST['pcam_member_since_date'] = date('Y-m-d H:i:s');

                if ($IsMemberExist != true && $IsUserExist != true)
                {
                    try {

                        $isValidCaptcha = captcha::obj()->isValid();
                        $isValidCaptcha = true;
                        if(!$isValidCaptcha)
                        {
                            return redirect()->back()->with('error_captcha_msg', 'Please enter valid captcha.')->withInput();
                        }
                        else {

                            $ret_val = PcaMember::obj()->Insert($POST);

                            if (!is_object($ret_val) && $ret_val > 0 && is_numeric($ret_val)) {

                                $POSTTRACK=$POST;
                                $POSTTRACK['step'] = 1;
                                $POSTTRACK['user_id']=$ret_val;
                                $POSTTRACK['status']= 'Success';

                                TrackLog::obj()->Insert($POSTTRACK);

                                $arrState           =   GeoState::obj()->getStates('us');
                                $arrPcaMemberData   =   PcaMember::obj()->getPcaMemberInfoById($ret_val);
                                $arrOrderData       =   OrderMaster::obj()->getOrderItemByUserId($ret_val);

                                $Adminemail          = config('constant.ADMIN_EMAIL'); //Client's Admin mail
                                $email   =  $request['pcam_email'];

                                $username = $request['pcam_fname'].' '.$request['pcam_lname'] ;
                                $companyname = $request['pcam_company_name'];
                                $companyWebsite = $request['pcam_company_website'];
                                $title = $request['pcam_title'];
                                $membership_level = CategoryMaster::obj()->getInfoById($request['pcam_level']);
                                $membership_level = $membership_level['cm_name'];
                                $EmailTemplateData  =   EmailTemplate::obj()->EmailTemplateByName('Submit User')->toArray();

                                $category = array();
                                if (isset($arrPcaMemberData['pcam_level']))
                                    $category = CategoryMaster::obj()->getInfoById($arrPcaMemberData['pcam_level']);

                                $subjectCompanyName = $companyname;
                                if (isset($category['cm_name']) && $category['cm_name'] == 'Individual')
                                    $subjectCompanyName = $username;

                                $emailSubject = 'New Member Alert: '.$subjectCompanyName;

                                $data2 = [
                                    'email_body'            => 'emails.send_user_mail',
                                    'useremail'             => $email,
                                    'username'              => $username,
                                    'company_name'          => $companyname,
                                    'companyWebsite'          => $companyWebsite,
                                    'title'                 => $title,
                                    'membership_level'       => $membership_level,
                                    'desc'                  => $EmailTemplateData[0]['etemp_desc'],
                                    'sub'                   => $emailSubject,
                                ];

                                $view2 = view('emails.email_layout',$data2);
                                $content=$view2->render();
                                file_put_contents('New_Member_Alert.html',$view2);

                                if(config('constant.EMAIL_SEND') == 'Yes') {
                                    Mail::send('emails.email_layout', $data2, function ($message) use ($Adminemail,$emailSubject) {
                                        $message->to($Adminemail, 'myName')->subject($emailSubject);
                                    });
                                    if(Mail::failures()) {
                                        file_put_contents('Fail_email.html',print_r(Mail::failures(),true));
                                        $post['elog_result']            =  0;
                                        $post['elog_error_message']     =  'error';
                                    }
                                    else
                                    {
                                        $post['elog_result']            =  1;
                                        $post['elog_error_message']     =  '';
                                    }
                                    $post['elog_to_email']          =  $Adminemail;
                                    $post['elog_subject']           =  $emailSubject;
                                    $post['elog_message']           =  $content;
                                    $post['elog_header']            =  '';
                                    $post['elog_attachments']       =  '';
                                    $post['elog_sent_date']         =  date('Y-m-d');
                                    $post['elog_attachment_name']   =  '';
                                    $post['elog_ip_address']        =  '';
                                    $elog=email_log::obj()->Insert($post);
                                }


                                $username   =  $request['pcam_email'];
                                $password   =  $request['pcam_fname'].$request['pcam_lname'];
                                $name       =  $request['pcam_fname'];

                                $EmailTemplateData  =   EmailTemplate::obj()->EmailTemplateByName('Submit User')->toArray();

                                $data2 = [
                                    'email_body'            => 'emails.send_user_information_mail',
                                    'username'              => $username,
                                    'password'              => $password,
                                    'name'                  => $name,
                                    'desc'                  => $EmailTemplateData[0]['etemp_desc'],
                                    'sub'                   => 'Welcome to PCA',
                                ];

                                $view2 = view('emails.email_layout',$data2);
                                $content=$view2->render();

                                if(config('constant.EMAIL_SEND') == 'Yes') {
                                    Mail::send('emails.email_layout', $data2, function ($message) use ($email) {
                                        $message->to($email, 'myName')->subject('Welcome to PCA');
                                    });
                                    if(Mail::failures()) {
                                        file_put_contents('Fail_email1.html',print_r(Mail::failures(),true));
                                        $post['elog_result']            =  0;
                                        $post['elog_error_message']     =  'error';
                                    }
                                    else
                                    {
                                        $post['elog_result']            =  1;
                                        $post['elog_error_message']     =  '';
                                    }
                                    $post['elog_to_email']          =  $email;
                                    $post['elog_subject']           =  'Welcome to PCA';
                                    $post['elog_message']           =  $content;
                                    $post['elog_header']            =  '';
                                    $post['elog_attachments']       =  '';
                                    $post['elog_sent_date']         =  date('Y-m-d');
                                    $post['elog_attachment_name']   =  '';
                                    $post['elog_ip_address']        =  '';
                                    $elog=email_log::obj()->Insert($post);
                                }

                                return view('layouts.frontend_default_layout',
                                    [
                                        'T_Body'            =>   'front.membership-application.membership-billing-details',
                                        'title'             =>   'Member Application',
                                        'JavaScript'        =>    array('membership-front'),
                                        'Css'               =>    array('membership-application'),
                                        'arrState'          =>    $arrState,
                                        'arrPcaMemberData'  =>    $arrPcaMemberData,
                                        'arrOrderData'      =>    $arrOrderData,
                                        'price_id'          =>    $POST['pm_stripe_price_id'],
                                        'prod_id'           =>    $POST['pm_stripe_prod_id'],
                                        'is_clariety'       =>    true,
                                        'is_browser_backBtn_disable'       =>    true,
                                    ])->render();
                            }
                        }
                        throw new Exception($ret_val);
                    }
                    catch (Exception $e){
                        $post_err['user_id'] = 0;
                        $post_err['user_email'] = $POST['pcam_email'];
                        $post_err['error_log'] = $e->getMessage();
                        $post_err['url']       = '';
                        $post_err['form_name'] = 'Membership-Form';
                        ErrorLog::obj()->Insert($post_err);

                        $POSTTRACK['step'] = 1;
                        $POSTTRACK['pcam_email']= $POST['pcam_email'];
                        $POSTTRACK['status']= 'Failed';
                        TrackLog::obj()->Insert($POSTTRACK);

                        return redirect()->back()->withErrors($ret_val)->withInput();
                    }
                }
                else
                {
                    return redirect()->back()->with('error_msg', 'Member already exists.')->withInput();
                }
            }
            else{
                return redirect()->back()->with('error_msg', 'Please select the Company Membership Level.')->withInput();
            }
            $arrData = SignupLog::obj()->SignupProcess($POST);
            unset($_SESSION['member_session_id']);
        }
        elseif($type == 'join-pca')
        {

            if ($POST['so_orders_payment_method'] == 'Credit Card'){
                $_POST = $request->all();

                if( $POST['stripeToken'] != '')
                {
                    Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    $result = \Stripe\Customer::create(array(
                        "email"  => $POST['pcam_email'],
                        "source" => $POST['stripeToken']
                    ));
                    file_put_contents('custcreate.txt',print_r($result,true));
                    if (is_object($result) && $result->id != '') {

                        $StripeData = User::obj()->StripeData($result, $_POST['stripeToken']);
                    }

                    $user_id    = isset($_POST['so_user_id']) && $_POST['so_user_id'] != '' ? $_POST['so_user_id']: '';
                    $Order_Id   =  isset($_POST['so_user_id']) && $_POST['so_user_id'] != '' ? $_POST['payment_order_id'] : '';

                    $subscription= \Stripe\Subscription::create([
                        'customer' => $result->id,
                        'items' => [[
                            'price' => $POST['pm_stripe_price_id'],
                        ]],
                        'metadata' => [
                                        'first_payment_pending' => true
                        ],
                        'expand' => ['latest_invoice.payment_intent'],
                    ]);
                    file_put_contents('custsub.txt',print_r($subscription,true));

                    $serviceChargePercentage = 0.03;
                    $additionalCharge = (int)($_POST['order_item_total'] * $serviceChargePercentage);

                    // Create the invoice item for the additional charge
                    /*$invItem = \Stripe\InvoiceItem::create([
                        'customer' => $result->id,
                        'amount' => $additionalCharge,
                        'currency' => 'usd',
                        'description' => '3% service charge',
                    ]);
                    $newInvoice = \Stripe\Invoice::create([
                        'customer' => $result->id,
                        'auto_advance' => true,
                    ]);
                    $newInvoice->finalizeInvoice();*/

//                    file_put_contents('custInv.txt',print_r($newInvoice,true));
                    $_POST['so_start_date'] = date('Y-m-d H:i:s',$subscription->current_period_start);
                    $_POST['so_next_payment'] = date('Y-m-d H:i:s',$subscription->current_period_end);
                    $_POST['so_next_payment_date'] = date('Y-m-d H:i:s',$subscription->current_period_end);
                    $_POST['so_end_date'] = date('Y-m-d H:i:s',$subscription->current_period_end);
                    $_POST['cust_id']=$subscription->customer;
                    $_POST['sub_id']=$subscription->id;


                    // charge customer with your amount

                    /*Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    $retinfo = \Stripe\Charge::create ([
                        "amount"        => $_POST['so_total'] * 100,
                        "currency"      => "usd",
                        //"source"        => $user['stripe_token'],
                        "customer"      => $result->id,
                        "description"   => $POST['pcam_email'],
                        "metadata"      => array("User_ID"  => $user_id,
                                                 "Order_id" => $Order_Id),
                        //"customer"      => $customer->stripe_id,
                    ]);*/
                    // store transaction info for logs
                }

                $POSTTRACK=$_POST;
                $POSTTRACK['step'] = 2;
                $POSTTRACK['user_id']=$_POST['so_user_id'];
                $POSTTRACK['order_id']= $_POST['so_order_id'];
                $POSTTRACK['product_name']= $_POST['oi_product_name'];
                $POSTTRACK['total_price']= $_POST['order_item_total'];
                $POSTTRACK['payment_method']= $_POST['so_orders_payment_method'];
                $POSTTRACK['status']= 'Success';
                $_POST['so_orders_payment_method'] = 'stripe';

                if($subscription->status == 'active')
                {
                    try{
                        $ret_val = Subscription::obj()->Insert($_POST);

                        $POSTTRACK['payment_status']= 'Payment Success';

                        $POST['order_status']   =   'Completed';
                        $POST['order_payment_method']    = $_POST['so_orders_payment_method'];
                        $POST['order_payment_status']    = 'Success';
                        $POST['order_company_street2']   =  isset($_POST['pcam_company_street2']) && $_POST['pcam_company_street2'] !=''? $_POST['pcam_company_street2']:'' ;
                        $POST['order_company_city']      =  isset($_POST['pcam_company_city']) && $_POST['pcam_company_city'] !=''? $_POST['pcam_company_city']:'' ;
                        $POST['order_company_state']     =  isset($_POST['pcam_company_state']) && $_POST['pcam_company_state'] !=''? $_POST['pcam_company_state']:'' ;
                        $POST['order_company_zip ']      =  isset($_POST['pcam_company_zip']) && $_POST['pcam_company_zip'] !=''? $_POST['pcam_company_zip']:'' ;

                        $OrderId                =   $request['payment_order_id'];
                        $ret_val1 = OrderMaster::obj()->Updates($POST,$OrderId);

                        if (!is_object($ret_val) && is_numeric($ret_val) && $ret_val > 0)
                        {
                            TrackLog::obj()->Insert($POSTTRACK);
                            #insert in payment table
                            $PaymentStatus = Payment::obj()->Insert($subscription);

                            $Adminemail          = config('constant.ADMIN_EMAIL'); //Client's Admin mail
                            $email = $_POST['order_email'];

                            $name           =  $request['pcam_fname'].' '. $request['pcam_lname'];
                            $order_id       =  $request['payment_order_id'];

                            $arrOrderData   =   $request->all();
                            $arrPcaMemberData   =   PcaMember::obj()->getPcaMemberInfoById($_POST['so_user_id']);
                            $arrOrderData['order_company_name'] = isset($arrPcaMemberData['pcam_company_name'])?$arrPcaMemberData['pcam_company_name']:'';

                            $arrSubscriptionData    =   Subscription::obj()->getSubscriptionByUserId($request['so_user_id']);
                            $EmailTemplateData  =   EmailTemplate::obj()->EmailTemplateByName('New Customer Order')->toArray();

                            $newMemberEmailSubject = 'New PCA Member: '.(isset($arrPcaMemberData['pcam_company_name'])?$arrPcaMemberData['pcam_company_name']:$name);

                            $data = [
                                'email_body'            =>  'emails.send_new_cust_order_mail',
                                'name'                  =>  $name,
                                'email'                 =>  $email,
                                'desc'                  =>  $EmailTemplateData[0]['etemp_desc'],
                                'sub'                   =>  $newMemberEmailSubject,
                                'order_id'              =>  $order_id,
                                'arrOrderData'          =>  $arrOrderData,
                                'arrPcaMemberData'      =>  $arrPcaMemberData,
                                'arrSubscriptionData'   =>  $arrSubscriptionData,
                                'profileStatus'         =>  'Active',
                                'paymentStatus'         =>  'PAID '.date('F d, Y'),
                                'companyWebsite'        =>  isset($arrPcaMemberData['pcam_company_website'])?$arrPcaMemberData['pcam_company_website']:'',
                            ];

                            $view = view('emails.email_layout',$data);
                            $content=$view->render();

                            file_put_contents('send_new_cust_order_mailCREDIRCARD.html',$view);

                            if(config('constant.EMAIL_SEND') == 'Yes'){
                                Mail::send('emails.email_layout', $data, function($message) use ($Adminemail,$newMemberEmailSubject)
                                {
                                    $message->to($Adminemail, 'myName')->subject($newMemberEmailSubject);
                                });
                                if(Mail::failures()) {
                                    $post['elog_result']            =  0;
                                    $post['elog_error_message']     =  'error';
                                }
                                else
                                {
                                    $post['elog_result']            =  1;
                                    $post['elog_error_message']     =  '';
                                }

                                $post['elog_to_email']          =  $Adminemail;
                                $post['elog_subject']           =  $newMemberEmailSubject;
                                $post['elog_message']           =  $content;
                                $post['elog_header']            =  '';
                                $post['elog_attachments']       =  '';
                                $post['elog_sent_date']         =  date('Y-m-d');
                                $post['elog_attachment_name']   =  '';
                                $post['elog_ip_address']        =  '';
                                $elog=email_log::obj()->Insert($post);
                            }

                            $EmailTemplateData  =   EmailTemplate::obj()->EmailTemplateByName('Thank You For Your Order')->toArray();
                            $data1 = [
                                'email_body'            =>  'emails.send_order_mail',
                                'name'                  =>  $name,
                                'desc'                  =>  $EmailTemplateData[0]['etemp_desc'],
                                'sub'                   =>  'Order Received',
                                'order_id'              =>  $order_id,
                                'arrOrderData'          =>  $arrOrderData,
                                'arrSubscriptionData'   =>  $arrSubscriptionData,
                            ];

                            $view1 = view('emails.email_layout',$data1);
                            $content=$view1->render();
                            if(config('constant.EMAIL_SEND') == 'Yes') {
                                Mail::send('emails.email_layout', $data1, function ($message) use ($email) {
                                    $message->to($email, 'myName')->subject('Order Received');

                                });
                                if(Mail::failures()) {
                                    $post['elog_result']            =  0;
                                    $post['elog_error_message']     =  'error';
                                }
                                else
                                {
                                    $post['elog_result']            =  1;
                                    $post['elog_error_message']     =  '';
                                }
                                $post['elog_to_email']          =  $email;
                                $post['elog_subject']           =  'Order Received';
                                $post['elog_message']           =  $content;
                                $post['elog_header']            =  '';
                                $post['elog_attachments']       =  '';
                                $post['elog_sent_date']         =  date('Y-m-d');
                                $post['elog_attachment_name']   =  '';
                                $post['elog_ip_address']        =  '';
                                $elog=email_log::obj()->Insert($post);
                            }

                            $username   =  $request['pcam_email'];
                            $password   =  $request['pcam_fname'].$request['pcam_lname'];
                            $name       =  $request['pcam_fname'];

                            /*$EmailTemplateData  =   EmailTemplate::obj()->EmailTemplateByName('Submit User')->toArray();

                            $data2 = [
                                'email_body'            => 'emails.send_submit_user_email',
                                'username'              => $username,
                                'password'              => $password,
                                'name'                  => $name,
                                'desc'                  => $EmailTemplateData[0]['etemp_desc'],
                                'sub'                   => 'PCA New Member Registration',
                            ];

                            $view2 = view('emails.email_layout',$data2);
                            $content=$view2->render();
                            if(config('constant.EMAIL_SEND') == 'Yes') {
                                Mail::send('emails.email_layout', $data2, function ($message) use ($email) {
                                    $message->to($email, 'myName')->subject('PCA New Member Registration');
                                });
                                if(Mail::failures()) {
                                    $post['elog_result']            =  0;
                                    $post['elog_error_message']     =  'error';
                                }
                                else
                                {
                                    $post['elog_result']            =  1;
                                    $post['elog_error_message']     =  '';
                                }
                                $post['elog_to_email']          =  $email;
                                $post['elog_subject']           =  'PCA New Member Registration';
                                $post['elog_message']           =  $content;
                                $post['elog_header']            =  '';
                                $post['elog_attachments']       =  '';
                                $post['elog_sent_date']         =  date('Y-m-d');
                                $post['elog_attachment_name']   =  '';
                                $post['elog_ip_address']        =  '';
                                $elog=email_log::obj()->Insert($post);
                            }*/
                            //Donation Letter
                            $data = [
                                'email_body'            =>  'emails.donation_letter',
                                'sub'                   =>  'Your Membership Contribution Donation Letter',
                                'date'                  =>  date('Y-m-d'),
                                'arrOrderData'          =>  $arrOrderData,
                            ];

                            $view = view('emails.email_layout',$data);
                            $content=$view->render();
                            if(config('constant.EMAIL_SEND') == 'Yes'){
                                Mail::send('emails.email_layout', $data, function($message) use ($email)
                                {
                                    $message->to($email, 'myName')->subject('Your Membership Contribution Donation Letter');
                                });
                                if(Mail::failures()) {
                                    $post['elog_result']            =  0;
                                    $post['elog_error_message']     =  'error';
                                }
                                else
                                {
                                    $post['elog_result']            =  1;
                                    $post['elog_error_message']     =  '';
                                }

                                $post['elog_to_email']          =  $email;
                                $post['elog_subject']           =  'Your Membership Contribution Donation Letter';
                                $post['elog_message']           =  $content;
                                $post['elog_header']            =  '';
                                $post['elog_attachments']       =  '';
                                $post['elog_sent_date']         =  date('Y-m-d');
                                $post['elog_attachment_name']   =  '';
                                $post['elog_ip_address']        =  '';
                                $elog=email_log::obj()->Insert($post);
                            }
                            //order Invoice mail
                            $email = $request['pcam_email'];
                            $name  = $request['pcam_fname'].' '.$request['pcam_lname'];
                            $orderdata = OrderMaster::obj()->getOrderByUserId($ret_val);
                            $EmailTemplateData  =   EmailTemplate::obj()->EmailTemplateByName('Submit Order')->toArray();

                            $data = [
                                'name'          =>  $name,
                                'desc'          =>  $EmailTemplateData[0]['etemp_desc'],
                                'order_id'      =>  $orderdata['order_id'],
                                'arrOrderData'  =>  $arrOrderData,
                                'order_email'   =>  $arrOrderData['order_email'],
                                'payment_failed'          =>  'No',
                                'Email'         =>  $email,
                                'temp_name'     =>  $EmailTemplateData[0]['etemp_name'],
                                'Uid'           =>  $orderdata['order_user_id'],
                            ];
                            $arrOrderData   =   OrderMaster::obj()->getOrderById($orderdata['order_id']);
                            $arrOrderItemData   =   OrderItem::obj()->getOrderItemById($orderdata['order_id']);
                            $arrProductData =   ProductMaster::obj()->getAllProduct($request);
                            $POST['oin_order_id']   =   $arrOrderData['order_id'];
                            $ret_val    =   OrderInvoice::obj()->Insert($POST);

                            $dataformail = [
                                'arrOrderData'      => $arrOrderData,
                                'arrOrderItemData'  => $arrOrderItemData,
                                'arrProductData'    => $arrProductData,
                                'invoice_number'    => $ret_val,
                            ];

                            $pdf = PDF::loadView('front.order.order_invoice', $dataformail);

                            $attachment =   $pdf->output();

                            $view = View::make('front.order.sendmail',$data);

                            $contents = $view->render();


                            file_put_contents('invoice_mail.html',$view);

                            if(config('constant.EMAIL_SEND') == 'Yes'){
                                Mail::send('front.order.sendmail', $data, function($message) use ($email,$attachment)
                                {
                                    $message->to($email, 'myName')->subject('Payment Successful');
                                    $message->attachData($attachment, "invoice.pdf");
                                });
                                if(Mail::failures()) {
                                    $post['elog_result']            =  0;
                                    $post['elog_error_message']     =  'error';
                                }
                                else
                                {
                                    $post['elog_result']            =  1;
                                    $post['elog_error_message']     =  '';
                                }
                                $post['elog_to_email']          =   $email;
                                $post['elog_subject']           =  'Payment Successful';
                                $post['elog_header']            =  '';
                                $post['elog_attachments']       =  '';
                                $post['elog_sent_date']         =  date('Y-m-d');
                                $post['elog_attachment_name']   =  '';
                                $post['elog_ip_address']        =  '';
                                $post['elog_message']           =  $contents;
                                $elog   =   email_log::obj()->Insert($post);
                            }


                            Auth::loginUsingId($user_id);
                            return redirect()->route('checkout',$orderdata['order_id']);
                        }
                        throw new Exception($ret_val);
                    }
                    catch (Exception $e){
                        echo 'Message: '. $e->getMessage();
                        $post_err['user_id'] = $_POST['so_user_id'];
                        $post_err['user_email'] = $_POST['pcam_email'];
                        $post_err['error_log'] = $e->getMessage();
                        $post_err['url']       = '';
                        $post_err['form_name'] = 'Payment-Form';
                        ErrorLog::obj()->Insert($post_err);
                        $POSTTRACK['user_id']= $_POST['so_user_id'];
                        $POSTTRACK['step'] = 2;
                        $POSTTRACK['status']= 'Failed';
                        TrackLog::obj()->Insert($POSTTRACK);
                        return redirect()->back()->withErrors($ret_val)->withInput();
                    }
                }
                elseif ($subscription->status == 'incomplete'){
                    $POSTTRACK['payment_status']= 'Payment Failed';
                    TrackLog::obj()->Insert($POSTTRACK);

                    $POST['order_status']   =   'Pending';
                    $POST['order_payment_method']=$_POST['so_orders_payment_method'];
                    $POST['order_payment_status']='Failed';
                    $OrderId                =   $request['payment_order_id'];
                    $ret_val1 = OrderMaster::obj()->Updates($POST,$OrderId);

                    $email = $request['pcam_email'];
                    $name  = $request['pcam_fname'].' '.$request['pcam_lname'];
                    $user_id = $request['so_user_id'];

                    $orderdata = OrderMaster::obj()->getOrderByUserId($user_id);

                    $arrOrderData       =   OrderMaster::obj()->getOrderItemForInvoiceByUserId($user_id);
                    $arrOrderData['order_payment_method']= $request['so_orders_payment_method'];

                    $EmailTemplateData  =   EmailTemplate::obj()->EmailTemplateByName('Submit Order')->toArray();
                    $data = [
                        'name'          =>  $name,
                        'desc'          =>  $EmailTemplateData[0]['etemp_desc'],
                        'order_id'      =>  $orderdata['order_id'],
                        'arrOrderData'  =>  $arrOrderData,
                        'order_email'   =>  $arrOrderData['order_email'],
                        'payment_failed'=>  'Yes',
                        'Email'         =>  $email,
                        'temp_name'     =>  $EmailTemplateData[0]['etemp_name'],
                        'Uid'           =>  $orderdata['order_user_id'],
                    ];

                    $arrOrderData   =   OrderMaster::obj()->getOrderById($orderdata['order_id']);
                    $arrOrderItemData   =   OrderItem::obj()->getOrderItemById($orderdata['order_id']);
                    $arrProductData =   ProductMaster::obj()->getAllProduct($request);

                    $POST['oin_order_id']   =   $arrOrderData['order_id'];
                    $ret_val    =   OrderInvoice::obj()->Insert($POST);

                    $dataformail = [
                        'arrOrderData'      => $arrOrderData,
                        'arrOrderItemData'  => $arrOrderItemData,
                        'arrProductData'    => $arrProductData,
                        'invoice_number'    => $ret_val,
                    ];

                    $pdf = PDF::loadView('front.order.order_invoice', $dataformail);

                    $attachment =   $pdf->output();

                    $view = View::make('front.order.sendmail',$data);

                    $contents = $view->render();


                    file_put_contents('Failed_invoice_mail.html',$view);

                    if(config('constant.EMAIL_SEND') == 'Yes'){
                        Mail::send('front.order.sendmail', $data, function($message) use ($email,$attachment)
                        {
                            $message->to($email, 'myName')->subject('PCA Payment Failed');

                            $message->attachData($attachment, "invoice.pdf");
                        });
                        if(Mail::failures()) {
                            $post['elog_result']            =  0;
                            $post['elog_error_message']     =  'error';
                        }
                        else
                        {
                            $post['elog_result']            =  1;
                            $post['elog_error_message']     =  '';
                        }
                        $post['elog_to_email']          =   $email;
                        $post['elog_subject']           =  'PCA Payment Failed';
                        $post['elog_header']            =  '';
                        $post['elog_attachments']       =  '';
                        $post['elog_sent_date']         =  date('Y-m-d');
                        $post['elog_attachment_name']   =  '';
                        $post['elog_ip_address']        =  '';
                        $post['elog_message']           =  $contents;
                        $elog   =   email_log::obj()->Insert($post);
                    }
                    return redirect()->back()->with('Err_Message','Your Payment is not Successfully done. Please try later');
                }
            }
            elseif ( $POST['so_orders_payment_method'] == 'Invoice Payment'){
                 try{

                    $_POST['so_orders_payment_method'] = 'Invoice Payment';
                    $_POST['so_payment_status'] = 'Pending';
                    $ret_val = Subscription::obj()->Insert($_POST);

                    $user_id    = $_POST['so_user_id'];

                    $POST['order_status']   =   'Pending';
                    $POST['order_payment_status']='Pending';
                    $POST['order_payment_method']=$_POST['so_orders_payment_method'];
                    $POST['order_company_street']    =  isset($POST['pcam_company_street']) && $POST['pcam_company_street'] !='' ?$POST['pcam_company_street']:'';
                    $POST['order_company_street2']   = isset($POST['pcam_company_street2']) && $POST['pcam_company_street2'] !=''? $POST['pcam_company_street2']:'' ;
                    $POST['order_company_city']      =  isset($POST['pcam_company_city']) && $POST['pcam_company_city'] !=''? $POST['pcam_company_city']:'' ;
                    $POST['order_company_state']     =  isset($POST['pcam_company_state']) && $POST['pcam_company_state'] !=''? $POST['pcam_company_state']:'' ;
                    $POST['order_company_zip ']      =  isset($POST['pcam_company_zip']) && $POST['pcam_company_zip'] !=''? $POST['pcam_company_zip']:'' ;

                    $OrderId                =   $request['payment_order_id'];

                    $ret_val1 = OrderMaster::obj()->Updates($POST,$OrderId);

                    if (!is_object($ret_val) && is_numeric($ret_val) && $ret_val > 0)
                    {
                        $POSTTRACK=$_POST;
                        $POSTTRACK['step'] = 2;
                        $POSTTRACK['user_id']=$_POST['so_user_id'];
                        $POSTTRACK['order_id']= $_POST['so_order_id'];
                        $POSTTRACK['product_name']= $_POST['oi_product_name'];
                        $POSTTRACK['total_price']= $_POST['order_item_total'];
                        $POSTTRACK['payment_method']= $_POST['so_orders_payment_method'];
                        $POSTTRACK['status']= 'Success';
                        TrackLog::obj()->Insert($POSTTRACK);

                        $Adminemail          = config('constant.ADMIN_EMAIL'); //Client's Admin mail

                        $email = $_POST['order_email'];

                        $name           =  $request['pcam_fname'].' '. $request['pcam_lname'];
                        $order_id       =  $request['payment_order_id'];

                        $arrOrderData   =   $request->all();
                        $arrPcaMemberData   =   PcaMember::obj()->getPcaMemberInfoById($request['so_user_id']);
                        $arrOrderData['order_company_name'] = isset($arrPcaMemberData['pcam_company_name'])?$arrPcaMemberData['pcam_company_name']:'';

                        $arrSubscriptionData    =   Subscription::obj()->getSubscriptionByUserId($request['so_user_id']);
                        $EmailTemplateData  =   EmailTemplate::obj()->EmailTemplateByName('New Customer Order')->toArray();

                        $newMemberEmailSubject = 'New PCA Member: '.(isset($arrPcaMemberData['pcam_company_name'])?$arrPcaMemberData['pcam_company_name']:$name);

                        $data = [
                            'email_body'            =>  'emails.send_new_cust_order_mail',
                            'name'                  =>  $name,
                            'email'                 =>  $email,
                            'desc'                  =>  $EmailTemplateData[0]['etemp_desc'],
                            'sub'                   =>  $newMemberEmailSubject,
                            'order_id'              =>  $order_id,
                            'arrOrderData'          =>  $arrOrderData,
                            'arrPcaMemberData'      =>  $arrPcaMemberData,
                            'arrSubscriptionData'   =>  $arrSubscriptionData,
                            'profileStatus'         =>  'Pending',
                            'paymentStatus'         =>  'INVOICE REQUESTED',
                            'companyWebsite'        =>  isset($arrPcaMemberData['pcam_company_website'])?$arrPcaMemberData['pcam_company_website']:'',
                            'is_invoice_payemnt'    =>  true
                        ];

                         $view = view('emails.email_layout',$data);
                         $content=$view->render();

                        file_put_contents('send_new_cust_order_mailINVOICE.html',$view);

                        if(config('constant.EMAIL_SEND') == 'Yes'){
                            Mail::send('emails.email_layout', $data, function($message) use ($Adminemail,$newMemberEmailSubject)
                            {
                                $message->to($Adminemail, 'myName')->subject($newMemberEmailSubject);
                            });
                            if(Mail::failures()) {
                                $post['elog_result']            =  0;
                                $post['elog_error_message']     =  'error';
                            }
                            else
                            {
                                $post['elog_result']            =  1;
                                $post['elog_error_message']     =  '';
                            }

                            $post['elog_to_email']          =  $Adminemail;
                            $post['elog_subject']           =  $newMemberEmailSubject;
                            $post['elog_message']           =  $content;
                            $post['elog_header']            =  '';
                            $post['elog_attachments']       =  '';
                            $post['elog_sent_date']         =  date('Y-m-d');
                            $post['elog_attachment_name']   =  '';
                            $post['elog_ip_address']        =  '';
                            $elog=email_log::obj()->Insert($post);
                        }

                        $EmailTemplateData  =   EmailTemplate::obj()->EmailTemplateByName('Thank You For Your Order')->toArray();
                        $data1 = [
                            'email_body'            =>  'emails.send_order_mail',
                            'name'                  =>  $name,
                            'desc'                  =>  $EmailTemplateData[0]['etemp_desc'],
                            'sub'                   =>  'Order Received',
                            'order_id'              =>  $order_id,
                            'arrOrderData'          =>  $arrOrderData,
                            'arrSubscriptionData'   =>  $arrSubscriptionData,
                            'is_invoice_payemnt'    =>  true
                            /*'type'                  =>  'Yes',*/
                        ];

                         $view1 = view('emails.email_layout',$data1);
                         $content=$view1->render();
                        if(config('constant.EMAIL_SEND') == 'Yes') {
                            Mail::send('emails.email_layout', $data1, function ($message) use ($email) {
                                $message->to($email, 'myName')->subject('Order Received');

                            });
                            if(Mail::failures()) {
                                $post['elog_result']            =  0;
                                $post['elog_error_message']     =  'error';
                            }
                            else
                            {
                                $post['elog_result']            =  1;
                                $post['elog_error_message']     =  '';
                            }
                            $post['elog_to_email']          =  $email;
                            $post['elog_subject']           =  'Order Received';
                            $post['elog_message']           =  $content;
                            $post['elog_header']            =  '';
                            $post['elog_attachments']       =  '';
                            $post['elog_sent_date']         =  date('Y-m-d');
                            $post['elog_attachment_name']   =  '';
                            $post['elog_ip_address']        =  '';
                            $elog=email_log::obj()->Insert($post);
                        }

                        /*$username   =  $request['pcam_email'];
                        $password   =  $request['pcam_fname'].$request['pcam_lname'];
                        $name       =  $request['pcam_fname'];

                        $EmailTemplateData  =   EmailTemplate::obj()->EmailTemplateByName('Submit User')->toArray();

                        $data2 = [
                            'email_body'            => 'emails.send_submit_user_email',
                            'username'              => $username,
                            'password'              => $password,
                            'name'                  => $name,
                            'desc'                  => $EmailTemplateData[0]['etemp_desc'],
                            'sub'                   => 'PCA New Member Registration',
                        ];

                         $view2 = view('emails.email_layout',$data2);
                         $content=$view2->render();
                        if(config('constant.EMAIL_SEND') == 'Yes') {
                            Mail::send('emails.email_layout', $data2, function ($message) use ($email) {
                                $message->to($email, 'myName')->subject('PCA New Member Registration');
                            });
                            if(Mail::failures()) {
                                $post['elog_result']            =  0;
                                $post['elog_error_message']     =  'error';
                            }
                            else
                            {
                                $post['elog_result']            =  1;
                                $post['elog_error_message']     =  '';
                            }
                            $post['elog_to_email']          =  $email;
                            $post['elog_subject']           =  'PCA New Member Registration';
                            $post['elog_message']           =  $content;
                            $post['elog_header']            =  '';
                            $post['elog_attachments']       =  '';
                            $post['elog_sent_date']         =  date('Y-m-d');
                            $post['elog_attachment_name']   =  '';
                            $post['elog_ip_address']        =  '';
                            $elog=email_log::obj()->Insert($post);
                        }*/

                        Auth::loginUsingId($user_id);
                        return redirect()->route('checkout',$ret_val);
                    }
                    throw new Exception($ret_val);
                }
                catch (Exception $e){
                    echo 'Message: '. $e->getMessage();
                    $post_err['user_id'] = $_POST['so_user_id'];
                    $post_err['user_email'] = $_POST['pcam_email'];
                    $post_err['error_log'] = $e->getMessage();
                    $post_err['url']       = '';
                    $post_err['form_name'] = 'Payment-Form';
                    ErrorLog::obj()->Insert($post_err);

                    $POSTTRACK['user_id'] = $_POST['so_user_id'];
                    $POSTTRACK['status']= 'Failed';
                    TrackLog::obj()->Insert($POSTTRACK);

                    return redirect()->back()->withErrors($ret_val)->withInput();
                }
            }
            /*else
            {
                return redirect()->back()->with('Err_Message','Your Payment is not Successfully done. Please try later');
            }*/
        }
    }
    public function captchaImage()
    {
        return captcha::obj()->getImage();
    }

}
