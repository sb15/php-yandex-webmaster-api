<?php

namespace Sb\YandexWebmaster;

use Sb\Browser\Console as Browser;

class Api
{
    const END_POINT = 'https://webmaster.yandex.ru';

    protected $siteId = null;
    protected $oauthToken = null;
    protected $browser = null;

    public function __construct($oauthToken)
    {
        $this->oauthToken = $oauthToken;
        $this->browser = new Browser();
        $this->browser->addHeader("Authorization", "OAuth " . $oauthToken);
    }

    public function getHosts()
    {
        $response = $this->browser->get(self::END_POINT . '/api/v2/hosts');
        return xmlstr_to_array($response);
    }

    public function getHost($siteId)
    {
        $response = $this->browser->get(self::END_POINT . '/api/v2/hosts/' . $siteId);
        return xmlstr_to_array($response);
    }

    public function addOriginalText($siteId, $text)
    {
        $text = "<original-text>
                    <content>{$text}</content>
                </original-text>";

        $response = $this->browser->post(self::END_POINT . '/api/v2/hosts/'.$siteId.'/original-texts/', $text);
        $response = xmlstr_to_array($response);
        if (isset($response['@root']) && $response['@root'] == 'error') {
            throw new Exception\Exception($response['message']);
        }
        return $response['id'];
    }
}