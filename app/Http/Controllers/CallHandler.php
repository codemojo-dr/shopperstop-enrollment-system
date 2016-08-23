<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class CallHandler extends Controller
{

    public function missedCall(Request $request){
        $number = $request->get('VerifiedNumber');
        $is_dnd = $request->get('DNDStatus');
        $operator = $request->get('Operator');
        $circle = $request->get('TelcoCircle');

        /*
         * Sanitize the number
         */
        $number = $this->sanitizeNumber($number);

        $sms = app('dial2verify.sms');
        $message = "Welcome to ShoppersStop. To register your First Citizen Card, click on the link http://shpr.st/reg/ or reply to +919212356765 in the below format"
        . "\n\nACTIVATE <Card Number>, <Name>, <Email>, <DOB DD/MM/YY>"
        . "\n\nActivate your card and get 50 points credited to your First Citizen Card";

        $sms->from(env('SMS_SENDER_ID', 'CDMOJO'))->to($number)->message($message)->send();

        return response('OK');
    }

    public function processSMS(Request $request){
        $number = $request->get('smsFrom');
        $action = $request->get('SmsKeyword');
        $content = $request->get('smsMsg');

        if(strtolower(trim($action)) != 'activate'){
            return response('failed', 400);
        }
        $processed = explode(",", $content);

        $card_number = trim($processed[0]);
        $name = trim($processed[1]);
        $email = trim($processed[2]);
        $dob = trim($processed[3]);

        if(empty($card_number) || empty($name) || empty($email) || empty($dob)){
            $message = "Dear Patron, Please make sure that you have entered all the fields with valid data.\n\nPlease retry again or give us a call at 15221522";
        } else {
            app('codemojo.wallet')->addBalance($card_number, 50, 1, 0, sha1(time() . $number . $card_number), 'Card activation bonus');

            $balance = app('codemojo.wallet')->getBalance($card_number);

            $message = "Congratulations, your First Citizen Card has been successfully activated. We have credited 50 points to your account & your current balance is {$balance} points
                    \n\nPlease download our app from http://bit.ly/2btk8YU and get additional 50 points on signup";
        }

        $sms = app('dial2verify.sms');
        $sms->from(env('SMS_SENDER_ID', 'CDMOJO'))->to($number)->message($message)->send();

        return response('OK');
    }

    private function sanitizeNumber($number){
        $number = str_replace('+91','', $number);
        if(strlen($number) > 10){
            $number = substr($number, strlen($number)-10);
        }

        return $number;
    }
}
