<?php

namespace App\Http\Controllers;

use App\Engine\SMS\Contracts\Services\Messaging;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CallHandler extends Controller
{

    private $required_fields = [
        'card' => 'required|int|min:10|max:16',
        'name' => 'required|string',
        'email' => 'required|email',
        'dob' => 'required|date'
    ];

    private $messaging;

    /**
     * CallHandler constructor.
     * @param array $required_fields
     */
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }


    public function missedCall(Request $request){
        $number = $request->get('VerifiedNumber');
        $is_dnd = $request->get('DNDStatus');
        $operator = $request->get('Operator');
        $circle = $request->get('TelcoCircle');

        /*
         * Sanitize the number
         */
        $number = $this->sanitizeNumber($number);

        $meta = app('codemojo.meta');

        $random = str_random(7);
        $session['call.details'] = [
            'dnd' => $is_dnd,
            'operator' => $operator,
            'circle' => $circle
        ];

        $meta->add($random, $number);
        $meta->add('session'.$number, $session);

        $message = "Welcome to ShoppersStop. To register your First Citizen Card, click on the link http://shoppersstop.codemojo.io/r/{$random} or reply to 9212356765 in the below format"
        . "\n\nACTIVATE CARD_NUMBER, NAME, EMAIL, DOB (in MM/DD/YYYY)"
        . "\n\nActivate your card and get 50 points credited to your First Citizen Card";

        $this->messaging->to($number)->message($message)->send();

        return response('OK');
    }

    public function processForm($code, Request $request){
        $meta = app('codemojo.meta');
        $number = $meta->get($code)['value'];
        if(empty($number)){
            return view('invalid');
        }

        $number = $this->sanitizeNumber($number);

        $session = $meta->get('session'.$number)['value'];

        $details['card'] = $request->get('card_no');
        $details['name'] = $request->get('name');
        $details['email'] = $request->get('email');
        $details['dob'] = $request->get('dob');

        $details['number'] = $number;
        if($this->processFinalStep($details)){
            $meta->delete($code);
        }

        return view('thank-you');
    }

    public function processSMS(Request $request){
        $number = $request->get('smsFrom');
        $action = $request->get('SmsKeyword');
        $content = $request->get('smsMsg');

        $meta = app('codemojo.meta');

        $number = $this->sanitizeNumber($number);

        $session = (array) @$meta->get('session'.$number)['value'];
        $details = $session_details = (array) @$session['user.details'];
        $pending = (array) @$session['pending'];

        if(strtolower(trim($action)) == 'activate'){
        } else {
            $this->updateRequiredFieldsBasedOnPendingFields($pending);
        }

        $processed = explode(",", $content);

        foreach ($this->required_fields as $field => $dummy){
            $details[$field] = trim(array_shift($processed));
        }

        /*
         * Validate the inputs and get the invalid fields
         */
        $pending = $this->validateGetPendingFields($details);

        /*
         * Populate available details from the session for invalid fields
         */
        foreach ($pending as $field){
            $details[$field] = @$session_details[$field];
        }

        $session['user.details'] = $details;
        $session['pending'] = $pending;

        $meta->add('session'.$number, $session);

        $details['number'] = $number;
        $this->processFinalStep($details, $pending);

        return response('OK');
    }

    private function validateGetPendingFields($details){
        $status = Validator::make($details, $this->required_fields);
        return array_keys($status->invalid());
    }

    private function updateRequiredFieldsBasedOnPendingFields($pending_fields){
        $keys = array_keys($this->required_fields);
        foreach ($keys as $field){
            if(!in_array($field, $pending_fields)){
                unset($this->required_fields[$field]);
            }
        }
    }

    private function processFinalStep($details, $pending_fields = null) {

        if(!empty($pending_fields)){
            $error = [];
            foreach ($pending_fields as $field){
                $error[] = ucfirst($field);
            }

            $message = "Dear Patron, Please make sure that you have entered the following field(s) correctly - " . implode(", ", $error);
            $message .= "\n\nSMS the remaining details to 9212356765 in the below format";
            $message .= "\n\n" . strtoupper(implode(", ", $error));
            $this->messaging->to($details['number'])->message($message)->send();
            return false;
        } else {
            app('codemojo.wallet')->addBalance($details['card'], 50, 1, 0, sha1(time() . $details['number']. $details['card']), 'Card activation bonus', 'ONBOARDING_BONUS');

            $balance = app('codemojo.wallet')->getBalance($details['card']);
            $message = "Congratulations, your First Citizen Card has been successfully activated. We have credited 50 points to your account & your current balance is {$balance['total']} points"
                    . "\n\nPlease download our app from http://bit.ly/2btk8YU and get additional 50 points on signup";
            $this->messaging->to($details['number'])->message($message)->send();
            return true;
        }
    }

    private function sanitizeNumber($number){
        $number = str_replace('+91','', $number);
        if(strlen($number) > 10){
            $number = substr($number, strlen($number)-10);
        }

        return $number;
    }
}
