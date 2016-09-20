<?php

namespace Sb\YandexWebmaster;

use Sb\Browser\Console as Browser;
use Sb\YandexWebmaster\Exception\Exception;

class Api
{
    const END_POINT = 'https://api.webmaster.yandex.net';

    protected $siteId = null;
    protected $oauthToken = null;
    protected $browser = null;

    private $userId = null;

    public function __construct($oauthToken)
    {
        $this->oauthToken = $oauthToken;
        $this->browser = new Browser();
        $this->browser->addHeader("Authorization", "OAuth " . $oauthToken);
        $this->browser->addHeader("Accept", "application/json");
        $this->browser->addHeader("Content-type", "application/json");
    }

    public function getUserId()
    {
        if (!$this->userId) {
            $response = $this->browser->get(self::END_POINT . '/v3/user/');
            $response = json_decode($response);
            $this->userId = $response->user_id;
        }

        return $this->userId;
    }

    public function getHosts()
    {
        $response = $this->browser->get(self::END_POINT . '/v3/user/' . $this->getUserId() . '/hosts/');
        return json_decode($response);
    }

    public function getHost($hostId)
    {
        $response = $this->browser->get(self::END_POINT . '/v3/user/' . $this->getUserId() . '/hosts/' . $hostId .'/');
        return json_decode($response);
    }

    public function addOriginalText($hostId, $text)
    {
        $content = json_encode(['content' => $text]);

        $response = $this->browser->post(self::END_POINT . '/v3/user/' . $this->getUserId() . '/hosts/' . $hostId . '/original-texts/', $content);
        $response = json_decode($response);

        if (isset($response->error_message)) {
            throw new Exception($response->error_message);
        }

        return $response->text_id;
    }
}
