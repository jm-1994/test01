<?php

namespace src\controllers;

use inc\Controller;
use src\lib\Router;
use src\lib\mailClass;
use inc\Raise;
/**
 * To handle the users data models
 
 */
class MailController extends Controller {

    /**
     * 
     * @return Mixed
     */
    public function __construct(){
        global $mail;
        $this->mail=$mail;
        $this->mobj= (new mailClass); 
        global $mpdf;
        $this->mpdf=$mpdf;
    }

    public function actionGetMailContent() {

        $orderid = $_POST['id'];
       
        $orderdata = $this->mobj->getAdminEmail($orderid);
        $this->sendMail($orderdata);
        
        
    }

    public function sendMail($orderdata) {

        $payment=array(1=>'Cash',2=>'Paypal');

        if($orderdata['row1']['type'] == '1'){
            $ordertype = "Pickup";
            $time = $orderdata['row1']['pickup_date_time'];

            $foodpreparedby = explode("-",$time);
            $foodpreparedby = date("h:i A", strtotime("-30 minutes", strtotime($foodpreparedby[0])));

            $time=str_replace('-','to',$time);
            $address = "Pick up at ".$orderdata['row1']['pickup_address1'];

            $billngaddress = " ";


        }else{
            $ordertype = "Delivery";
            $time = $orderdata['row1']['delivery_date_time'];

            $foodpreparedby = explode("-",$time);
            $foodpreparedby = date("h:i A", strtotime("-30 minutes", strtotime($foodpreparedby[0])));

            $time=str_replace('-','to',$time);
            $address = "Delivery to ".$orderdata['row1']['delivery_address1'];

            $billngaddress = $orderdata['row1']['delivery_address1'];
        }
        $message = '<html>
                            <head>
                                <meta charset="utf-8">
                                <title>A simple, clean, and responsive HTML invoice template</title>
                                
                                <style>
                                .button {
                                    display: block;
                                    width: 115px;
                                    height: 25px;
                                    background: #4E9CAF;
                                    padding: 10px;
                                    text-align: center;
                                    border-radius: 5px;
                                    color: white;
                                    font-weight: bold;
                                }
                                .invoice-box {
                                    max-width: 800px;
                                    margin: auto;
                                    padding: 30px;
                                    border: 1px solid #eee;
                                    box-shadow: 0 0 10px rgba(0, 0, 0, .15);
                                    font-size: 16px;
                                    line-height: 24px;
                                    font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif;
                                    color: #555;
                                }
                                
                                .invoice-box table {
                                    width: 100%;
                                    line-height: inherit;
                                    text-align: left;
                                }
                                
                                .invoice-box table td {
                                    padding: 5px;
                                    vertical-align: top;
                                }
                                
                                .invoice-box table tr td:nth-child(2) {
                                    text-align: right;
                                }
                                
                                .invoice-box table tr.top table td {
                                    padding-bottom: 20px;
                                }
                                
                                .invoice-box table tr.top table td.title {
                                    font-size: 45px;
                                    line-height: 45px;
                                    color: #333;
                                }
                                
                                .invoice-box table tr.information table td {
                                    padding-bottom: 40px;
                                }
                                
                                .invoice-box table tr.heading td {
                                    background: #eee;
                                    border-bottom: 1px solid #ddd;
                                    font-weight: bold;
                                }
                                
                                .invoice-box table tr.details td {
                                    padding-bottom: 20px;
                                }
                                
                                .invoice-box table tr.item td{
                                    border-bottom: 1px solid #eee;
                                }
                                
                                .invoice-box table tr.item.last td {
                                    border-bottom: none;
                                }
                                
                                .invoice-box table tr.total td:nth-child(2) {
                                    border-top: 2px solid #eee;
                                    font-weight: bold;
                                }
                                
                                @media only screen and (max-width: 600px) {
                                    .invoice-box table tr.top table td {
                                        width: 100%;
                                        display: block;
                                        text-align: left;
                                    }
                                    
                                    .invoice-box table tr.information table td {
                                        width: 100%;
                                        display: block;
                                        text-align: left;
                                    }
                                }
                                
                                /** RTL **/
                                .rtl {
                                    direction: rtl;
                                    font-family: Tahoma, Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif;
                                }
                                
                                .rtl table {
                                    text-align: right;
                                }
                                
                                .rtl table tr td:nth-child(2) {
                                    text-align: left;
                                }
                                </style>
                            </head>

                            <body>
                                <div class="invoice-box">
                                    <table cellpadding="0" cellspacing="0">
                                        
                                        <tr class="information">
                                            <td colspan="2">
                                                <table style="word-wrap:break-word;table-layout: fixed;">
                                                    <tr>
                                                        <td>
                                                            Dear Merchant ,
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Pending order on '.date("d M Y \\,\\ h:i a",$orderdata['row1']["date"])." for ".ucwords($orderdata['row3']["first_name"]).'
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <b>Store : </b> '.$orderdata['row4']["name"]." , ".$orderdata['row4']["address1"]." , ".$orderdata['row4']["address2"]." , ".$orderdata['row4']["postalcode"].'
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <b>Contact Detail : </b> '.$orderdata['row3']["phone1"]." , ".$orderdata['row3']["email"].'
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <b>Organization Name : </b> '.$orderdata['row3']["organisation_name"].'
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <b>Food to be ready by : </b> '.date("d M Y",$orderdata['row1']["pickup_delivery_date"])." ".$foodpreparedby.'
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Customer wants food by '.date("d M Y",$orderdata['row1']["pickup_delivery_date"])." ".$time.'
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <b>Collection Method : </b> '.$address.'
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <b>Payment Method : </b> '.$payment[$orderdata['row1']["payment_type"]].'
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <b>Billing Address : </b> '.$billngaddress.'
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="2">
                                            <table>
                                                <tr class="heading">
                                                    <td>Item</td>
                                                    <td>Qty</td>
                                                    <td>Unit(SGD)</td>
                                                    <td>Amount(SGD)</td>
                                                </tr>';
                                                foreach ($orderdata['row2'] as $value) {
                                                    $message .= '<tr class="item"><td>'.$value["item_name"].'</td><td>'.$value["quantity"].'</td><td>$ '.$value["unit_price"].'</td><td>$ '.$value["total_price"].'</td></tr>';
                                                }
                                            $message .= '<tr><td colspan="2"></td><td><b>Subtotal</b></td><td>$ '.$orderdata['row1']["subtotal"].'</td><tr>';

                                            if ($orderdata['row1']["delivery_charge"] != 0){
                                                $message .= '<td colspan="2"></td><td><b>Delivery Charge</b></td><td>$ '.$orderdata['row1']["delivery_charge"].'</td><tr>';
                                            }
                                            if ($orderdata['row1']["promo_amt"] != 0){
                                                $message .= '<td colspan="2"></td><td><b>Promotion(Discount)</b></td><td>$ '.$orderdata['row1']["promo_amt"].'</td><tr>';
                                            }

                                            $message .= '<tr><td colspan="2"></td><td><b>Total</b></td><td>$ '.$orderdata['row1']["total"].'</td><tr>';

                                            $message .= '<tr></tr><tr></tr><tr><td colspan="2"></td><td><a href="'.ADMINURL.'ConfirmMail/GetMailContent?order_status=2&id='.$orderdata['row1']["id"].'" class="button" style="text-decoration: none;color:white;float:right;">Confirm Order</a></td><td><a href="'.ADMINURL.'Mail/GetMailContent?order_status=4&id='.$orderdata['row1']["id"].'" class="button" style="text-decoration: none;color:white;">Cancel Order</a></td></tr></table>
                                        </td>
                                            
                                        </tr>
                                        
                                    </table>
                                </div>
                            </body>
                            </html>';

        //$mail = new PHPMailer();
        $this->mail->CharSet  =  "utf-8";
        $this->mail->SMTPDebug = 1;                               // Enable verbose debug output

        $this->mail->isSMTP(); 
                                             // Set mailer to use SMTP
        $this->mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;                               // Enable SMTP authentication
        $this->mail->Username = 'zim262zim@gmail.com';                 // SMTP username
        $this->mail->Password = 'zim123456';                           // SMTP password
        $this->mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = 587;  
                                          // TCP port to connect to

        $this->mail->From = 'zim262zim@gmail.com';
        $this->mail->FromName = 'Popiah';
        $this->mail->addAddress('chuachuachua123123@gmail.com', 'Admin');     // Add a recipient
        if($_GET['id']){
            header("Location: ".FRONTEND);
        }
        $this->mail->isHTML(true); 

        $this->mail->Subject = 'Pending #Orderno: '.$orderdata['row1'][orderno];
        
        $this->mail->Body    = $message;
        $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!$this->mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {

           echo 'Message has been sent';
        }

    }
    
    public function actionExport(){
        $id = base64_decode($_GET['id']);
        $orderdata = $this->mobj->getAdminEmail($id);
        $payment=array(1=>'Cash',2=>'Paypal');
        
        if($orderdata['row1']['type'] == '1'){
            $ordertype = "Pickup";
            $time = $orderdata['row1']['pickup_date_time'];
            $address = $orderdata['row1']['pickup_address1'];
        }else{
            $ordertype = "Delivery";
            $time = $orderdata['row1']['delivery_date_time'];
            $address = $orderdata['row1']['delivery_address1'];
        }
         $receipt = '<html>
                            <head>
                                <meta charset="utf-8">
                                <title>A simple, clean, and responsive HTML invoice template</title>
                                
                                <style>
                                .button {
                                    display: block;
                                    width: 50%;
                                    height: 25px;
                                    background: #4E9CAF;
                                    padding: 10px;
                                    text-align: center;
                                    border-radius: 5px;
                                    color: white;
                                    font-weight: bold;
                                }
                                .invoice-box {
                                    max-width: 800px;
                                    margin: auto;
                                    padding: 30px;
                                    border: 1px solid #eee;
                                    box-shadow: 0 0 10px rgba(0, 0, 0, .15);
                                    font-size: 16px;
                                    line-height: 24px;
                                    font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif;
                                    color: #555;
                                }
                                
                                .invoice-box table {
                                    width: 100%;
                                    line-height: inherit;
                                    text-align: left;
                                }
                                
                                .invoice-box table td {
                                    padding: 5px;
                                    vertical-align: top;
                                }
                                
                                .invoice-box table tr td:nth-child(2) {
                                    text-align: right;
                                }
                                
                                .invoice-box table tr.top table td {
                                    padding-bottom: 20px;
                                }
                                
                                .invoice-box table tr.top table td.title {
                                    font-size: 45px;
                                    line-height: 45px;
                                    color: #333;
                                }
                                
                                .invoice-box table tr.information table td {
                                    padding-bottom: 40px;
                                }
                                
                                .invoice-box table tr.heading td {
                                    background: #eee;
                                    border-bottom: 1px solid #ddd;
                                    font-weight: bold;
                                }
                                
                                .invoice-box table tr.details td {
                                    padding-bottom: 20px;
                                }
                                
                                .invoice-box table tr.item td{
                                    border-bottom: 1px solid #eee;
                                }
                                
                                .invoice-box table tr.item.last td {
                                    border-bottom: none;
                                }
                                
                                .invoice-box table tr.total td:nth-child(2) {
                                    border-top: 2px solid #eee;
                                    font-weight: bold;
                                }
                                
                                @media only screen and (max-width: 600px) {
                                    .invoice-box table tr.top table td {
                                        width: 100%;
                                        display: block;
                                        text-align: center;
                                    }
                                    
                                    .invoice-box table tr.information table td {
                                        width: 100%;
                                        display: block;
                                        text-align: center;
                                    }
                                }
                                
                                /** RTL **/
                                .rtl {
                                    direction: rtl;
                                    font-family: Tahoma, Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif;
                                }
                                
                                .rtl table {
                                    text-align: right;
                                }
                                
                                .rtl table tr td:nth-child(2) {
                                    text-align: left;
                                }
                                </style>
                            </head>

                            <body>
                                <div class="invoice-box">
                                    <table cellpadding="0" cellspacing="0">
                                        <tr class="top">
                                            <td colspan="2">
                                                <table>
                                                <tr>
                                                        <td style="text-align: center;">
                                                            <img src="'.BASEURL.'web/images/logomain.png" style="width:100%; max-width:100px;">
                                                            <div style="text-align: center;"><label>&nbsp;<b>Ann Chin Company Pte Ltd<br>16B Lornie Road S(29703)<br>Reg No. 2008053349C</b></label></div>
                                                            
                                                        </td>
                                                    </tr>
                                                    
                                                </table>
                                            </td>
                                        </tr>
                                        
                                        <tr class="information">
                                            <td colspan="2">
                                                <table style="word-wrap:break-word;table-layout: fixed;">
                                                    <tr>
                                                        <td colspan="2">
                                                            <b>Order No #:</b> '.$orderdata['row1']["orderno"].'<br>
                                                            <b>Name: </b>'.$orderdata['row3']["first_name"].'<br>
                                                            <b>Email: </b>'.$orderdata['row3']["email"].'<br>
                                                            <b>Payment type:</b>'.$payment[$orderdata['row1']["payment_type"]].'<br>';
                                                            

                                        $receipt .= '</td>
                                                        
                                                        <td colspan="2" style="padding-left:15%;">
                                                            <b>'.$ordertype.' Date:</b> '.date("d-m-Y",$orderdata['row1']["pickup_delivery_date"]).'<br>
                                                            <b>Time:</b> '.$time.'<br>
                                                            <b>Address: </b>'.$address.'
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="2">
                                            <table>
                                                <tr class="heading">
                                                    <td>Item</td>
                                                    <td>Qty</td>
                                                    <td>Unit(SGD)</td>
                                                    <td>Amount(SGD)</td>
                                                </tr>';
                                                foreach ($orderdata['row2'] as $value) {
                                                    $receipt .= '<tr class="item"><td>'.$value["item_name"].'</td><td>'.$value["quantity"].'</td><td>$ '.$value["unit_price"].'</td><td>$ '.$value["total_price"].'</td></tr>';
                                                }
                                            $receipt .= '<tr><td colspan="2"></td><td><b>Subtotal</b></td><td>$ '.$orderdata['row1']["subtotal"].'</td><tr>';

                                            if ($orderdata['row1']["delivery_charge"] != 0){
                                                $receipt .= '<td colspan="2"></td><td><b>Delivery Charge</b></td><td>$ '.$orderdata['row1']["delivery_charge"].'</td><tr>';
                                            }
                                            if ($orderdata['row1']["promo_amt"] != 0){
                                                $receipt .= '<td colspan="2"></td><td><b>Promotion(Discount)</b></td><td>$ '.$orderdata['row1']["promo_amt"].'</td><tr>';
                                            }

                                            $receipt .= '<tr><td colspan="2"></td><td><b>Total</b></td><td>$ '.$orderdata['row1']["total"].'</td><tr>';
                                            

                                            $receipt .= '</table>
                                        </td>
                                            
                                        </tr>
                                        
                                    </table>
                                </div>
                            </body>
                            </html>';

        $file_name=$orderdata['row1']["orderno"]." Receipt ";
        $this->mpdf->SetTitle('Ann Chin Popiah Receipt');
        $this->mpdf->WriteHTML($receipt);
        $this->mpdf->Output($file_name.'.pdf', 'I');

    }


}