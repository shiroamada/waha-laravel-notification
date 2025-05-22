<?php

namespace NotificationChannels\Waha;

use DomainException;
use GuzzleHttp\Client as HttpClient;
use NotificationChannels\Waha\Exceptions\CouldNotSendNotification;

class WahaApi
{
    const FORMAT_JSON = 3;

    /** @var string */
    protected $apiUrl;

    /** @var HttpClient */
    protected $httpClient;

    /** @var string */
    protected $sessionId;

    /** @var string */
    protected $token;

    /** @var integer */
    public $isMalaysiaMode;

    /** @var integer */
    public $isEnable;

    /** @var integer */
    public $isDebug;

    /** @var string */
    public $debugReceiveNumber;

    /** @var string */
    protected $action = 'sendText';

    /** @var string */
    protected $priority = '10';


    public function __construct($config)
    {
        $this->apiUrl = $config['apiUrl'];
        $this->sessionId = $config['sessionId'];
        $this->token = $config['token'];

        $this->isMalaysiaMode = $config['isMalaysiaMode'];
        $this->isEnable = (isset($config['isEnable']) ? $config['isEnable'] : 0);
        $this->isDebug = $config['isDebug'];
        $this->debugReceiveNumber = $config['debugReceiveNumber'];


        $this->httpClient = new HttpClient([
            'base_uri' =>  $this->apiUrl.'/api/'.$this->action,
            'timeout' => 8.0,
        ]);
    }

    /**
     * @param  array  $params
     *
     * @return array
     *
     * @throws CouldNotSendNotification
     */
    public function send($params)
    {
        if($this->isEnable)
        {
            try {
                $response = $this->httpClient->request('POST', $this->apiUrl.'/api/'.$this->action, [
                    'form_params' => [
                        'session' => $this->sessionId,
                        'chatId' => $params['to'].'@c.us',
                        'text' => $params['mesg'],
                        "linkPreview" => true,
                        "linkPreviewHighQuality" => true
                    ],
                ]);

                $stream = $response->getBody();

                $content = $stream->getContents();

                $response = json_decode((string) $response->getBody(), true);

                return $response;
            } catch (DomainException $exception) {
                throw CouldNotSendNotification::exceptionWahaRespondedWithAnError($exception);
            } catch (\Exception $exception) {
                throw CouldNotSendNotification::couldNotCommunicateWithWaha($exception);
            }
        }
    }
}
