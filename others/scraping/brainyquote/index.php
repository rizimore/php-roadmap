<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

require_once "../../../vendor/autoload.php";

$urls = [
    "topics/motivational-quotes",
    "topics/motivational-quotes_2",
    "topics/motivational-quotes_3",
    "topics/motivational-quotes_4",
    "topics/motivational-quotes_5",
    "topics/motivational-quotes_6",
    "topics/motivational-quotes_7",
];

$client = new Client(["base_uri" => "https://www.brainyquote.com/"]);

$quotes = [];

foreach ($urls as $url) {
    try {
        $response = $client->get($url);
        $body = (string) $response->getBody();

        // parsing
        $crawler = new Crawler($body);
        $crawler->filter(".bqQt")->each(function ($node) use (&$quotes) {
            try {
                $quote = [];

                $quote_crawler = new Crawler($node->html());
                $quote["text"] = $quote_crawler->filter(".b-qt")->first()->text();
                $quote["author"] = $quote_crawler->filter(".bq-aut")->first()->text();

                $keywords = [];
                $quote_crawler->filter(".qkw-btn")->each(function ($keyword_node) use (&$keywords) {
                    $keywords[] = $keyword_node->text();
                });

                $quote["keywords"] = $keywords;

                $quotes[] = $quote;
            } catch (Exception $e) {}
        });
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

$data = json_encode($quotes);
var_dump($data);



