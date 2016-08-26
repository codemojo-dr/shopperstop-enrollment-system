<?php

namespace App\Engine\SMS\Services;

use App\Engine\SMS\Contracts\Services\Messaging as MessagingContract;
use App\Engine\SMS\Exceptions\DestinationSMSNumberIsEmptyException;
use App\Engine\SMS\Exceptions\MessageIsEmptyException;
use App\Engine\SMS\Exceptions\SourceSMSNumberIsEmptyException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class Dial2Verify implements MessagingContract {

    protected $to, $from, $message;

    /**
     * Dial2Verify constructor.
     */
    public function __construct()
    {
    }

    public function from($sender_id){
        $this->from = $sender_id;
        return $this;
    }

    public function to($number){
        $this->to = $number;
        return $this;
    }

    public function message($message){
        $message = str_replace("\'","'",$message);
        $message = str_replace('\"','"',$message);
        $this->message = $message;
        return $this;
    }

    public function send(){
        if(config('sms.test')){
            Log::info($this->message);
            return;
        }

        $url = 'http://2factor.in/API/V1/' . config('sms.dial2verify.key') . '/ADDON_SERVICES/SEND/TSMS';

        $data = [
            'From' => $this->from,
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

    /**
     * Sends SMS message
     *
     * @return array
     * @throws DestinationSMSNumberIsEmptyException
     * @throws MessageIsEmptyException
     * @throws SourceSMSNumberIsEmptyException
     */
    public function sendMessage()
    {
        return $this->send();
    }

    /**
     * Return Formatted Message Data
     *
     * @return array
     */
    public function getMessageData()
    {
        return $this->message;
    }

    /**
     * Get Message details
     */
    public function getMessages()
    {
        throw new \Exception("Method not implemented");
    }

    /**
     * Get Specific Message Details
     *
     * @param $uuid
     * @return array
     */
    public function getMessage($uuid)
    {
        throw new \Exception("Method not implemented");
    }

    /**
     * Message Setter
     *
     * @param string $message
     * @return Messaging
     */
    public function setMessage($message)
    {
        $this->message($message);
    }

    /**
     * Source Number Setter
     *
     * @param mixed $sourceNumber
     * @return Messaging
     */
    public function setSourceNumber($sourceNumber)
    {
        $this->from($sourceNumber);
    }

    /**
     * Destination Number(s) Setter
     *
     * @param array|string $destinationNumber
     * @return Messaging
     */
    public function setDestinationNumber($destinationNumber)
    {
        $this->to($destinationNumber);
    }

    /**
     * Get destination sms number(s)
     */
    public function getDestinationNumber()
    {
        return $this->to;
    }
}