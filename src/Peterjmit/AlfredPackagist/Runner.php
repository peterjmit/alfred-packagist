<?php

namespace Peterjmit\AlfredPackagist;

use Peterjmit\Alfred;

class Runner
{
    const PACKAGIST_SEARCH_ENDPOINT = 'https://packagist.org/search.json?q=';

    public $lastQuery;
    public $lastResponse;

    private $workflow;

    public function __construct()
    {
        $this->workflow = new Alfred\Workflow();
    }

    public function search($query)
    {
        $result = $this->workflow->jsonRequest(self::PACKAGIST_SEARCH_ENDPOINT . urlencode($query));

        $mappedResult = $this->mapSearchToAlfredXML($result['results']);

        return $this->workflow->feedbackXML($mappedResult);
    }

    protected function mapSearchToAlfredXML(array $data)
    {
        $workflow = $this->workflow;

        return array_map(function ($r) use ($workflow) {
            return $workflow->alfredXMLFormat(
                $r['url'],
                $r['url'],
                $r['name'],
                sprintf('%s (%d Downloads, %d Favourites)', $r['description'], $r['downloads'], $r['favers'])
            );
        }, $data);
    }
}