<?php

namespace App\Engine\SMS\Services;

use App\Engine\SMS\Contracts\Services\Messaging as MessagingContract;
use App\Engine\SMS\Exceptions\DestinationSMSNumberIsEmptyException;
use App\Engine\SMS\Exceptions\MessageIsEmptyException;
use App\Engine\SMS\Exceptions\SourceSMSNumberIsEmptyException;
use App\Engine\SMS\Services\Dial2Verify;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class Messaging implements MessagingContract{

    private $provider;

    public function __construct()
    {
        switch (config('sms.provider')){
            case 'dial2verify':
                $this->provider = new Dial2Verify();
                break;
            case 'plivo':
                $this->provider = new Plivo();
                break;
        }
        $this->from(config('sms.sender'));
    }

    public function from($sender_id){
        return $this->provider->from($sender_id);
    }

    public function to($number){
        return $this->provider->to($number);
    }

    public function message($message){
        return $this->provider->message($message);
    }

    public function send(){
        return $this->provider->send();
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
        return $this->provider->send();
    }

    /**
     * Return Formatted Message Data
     *
     * @return array
     */
    public function getMessageData()
    {
        return $this->provider->getMessageData();
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
        return $this->message($message);
    }

    /**
     * Source Number Setter
     *
     * @param mixed $sourceNumber
     * @return Messaging
     */
    public function setSourceNumber($sourceNumber)
    {
        return $this->from($sourceNumber);
    }

    /**
     * Destination Number(s) Setter
     *
     * @param array|string $destinationNumber
     * @return Messaging
     */
    public function setDestinationNumber($destinationNumber)
    {
        return $this->to($destinationNumber);
    }

    /**
     * Get destination sms number(s)
     */
    public function getDestinationNumber()
    {
        return $this->to;
    }
}