<?php

namespace App\Engine\SMS;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class Dial2Verify {

    private $to, $sender, $message;

    /**
     * Dial2Verify constructor.
     */
    public function __construct()
    {
    }

    public function from($sender_id){
        $this->sender = $sender_id;
        return $this;
    }

    public function to($number){
        $this->to = $number;
        return $this;
    }

    public function message($message){
        $this->message = str_replace("\'","'",$message);
        $this->message = str_replace('\"','"',$message);
        return $this;
    }

    public function send(){
        $url = 'http://2factor.in/API/V1/' . env('DIAL2VERIFY_KEY') . '/ADDON_SERVICES/SEND/TSMS';

        $data = [
            'From' => $this->sender,
            'To' => $this->to,
            'Msg' => $this->message,
            'SendAt' => ''
        ];

        $guzzle = new Client();
        try {
            return $guzzle->post($url, [
                'form_params' => $data,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ])->getBody();
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
            return $e;
        } catch (ClientException $e) {
            Log::error($e->getMessage());
            return $e;
        }
    }

}