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
use Plivo\RestAPI;

class Plivo implements MessagingContract
{
    protected $to, $from, $message;

    /**
     * Plivo constructor.
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
        $this->message = $message;
        return $this;
    }

    public function send(){
        if(env('SMS_TEST', false)){
            Log::info($this->message);
            return;
        }

        $data = [
            'src' => $this->from,
            'dst' => $this->to,
            'text' => $this->message,
            'type' => 'sms'
        ];

        $api = new RestAPI(env('PLIVO_AUTH_ID'), env('PLIVO_AUTH_TOKEN'));

        return $api->send_message($data);
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
        return $this;
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
        return $this;
    }

    /**
     * Get destination sms number(s)
     */
    public function getDestinationNumber()
    {
        return $this->to;
    }

}
