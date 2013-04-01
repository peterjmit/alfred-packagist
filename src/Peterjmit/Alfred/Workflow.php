<?php

namespace Peterjmit\Alfred;

use Buzz;
use SimpleXMLElement;

class Workflow
{
    const XML_ATTR = 1;
    const XML_EL = 2;

    private $browser;

    private $xmlMap = array(
        'uid' => self::XML_ATTR,
        'arg' => self::XML_ATTR,
        'type' => self::XML_ATTR,
        'valid' => self::XML_ATTR,
        'autocomplete' => self::XML_ATTR,
        'title' => self::XML_EL,
        'subtitle' => self::XML_EL,
        'icon' => self::XML_EL,
    );

    public function __construct()
    {
        $client = new Buzz\Client\Curl();
        $this->browser = new Buzz\Browser($client);
    }

    public function jsonRequest($url)
    {
        $response = $this->browser->get($url);

        return json_decode($response->getContent(), $assoc = true);
    }

    public function feedbackXML(array $data)
    {
        $items = new SimpleXMLElement("<items></items>");

        foreach ($data as $row) {
            $this->addItem($items, $row);
        }

        return $items->asXML();
    }

    public function alfredXMLFormat($uid, $arg, $title, $subtitle, $icon = 'icon.png', $valid = 'yes', $type = null, $autocomplete = null)
    {
        $data = array(
            'uid'          => $uid,
            'arg'          => $arg,
            'valid'        => $valid,
            'title'        => $title,
            'subtitle'     => $subtitle,
        );

        $icon && $data['icon'] = $icon;
        $autocomplete && $data['autocomplete'] = $autocomplete;
        $type && $data['type'] = $type;

        return $data;
    }

    protected function addItem(SimpleXMLElement $items, $data)
    {
        $item = $items->addChild('item');

        foreach ($data as $key => $value) {
            $this->mapXML($item, $key, $value);
        }
    }

    protected function mapXML(SimpleXMLElement $item, $key, $value)
    {
        if (!array_key_exists($key, $this->xmlMap)) {
            throw new \Exception(sprintf('Unrecognised xml key "%s" with value "%s"', $key, $value));
        }

        $type = $this->xmlMap[$key];

        switch ($type) {
            case self::XML_ATTR:
                $item->addAttribute($key, $value);
                break;

            case self::XML_EL:
                $item->$key = $value;
                break;
        }
    }

}
