<?php

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

require_once "../../../vendor/autoload.php";

$areas = [
    "Oxford",
    // "NW3",
    // "York"
];

$client = new Client();
foreach ($areas as $area) {
    $crawler = $client->request("GET", "https://www.rightmove.co.uk");

    $form = $crawler->selectButton("For sale")->form();
    $crawler = $client->submit($form, ["searchLocation" => $area]);

    $form = $crawler->selectButton("Find properties")->form();
    $crawler = $client->submit($form);

    $url = $crawler->getUri();
    $urls = [];
    for ($i=0; $i<100; $i++) {
        $urls[] = $url . "&index=" . 24 * $i ;
    }

    foreach ($urls as $url) {
        $crawler = $client->request("GET", $url);
        if ($crawler->filter(".errorCard")->count()) {
            break;
        }

        $crawler->filter(".l-searchResult")->each(function ($property) use (&$client) {
            $property_crawler = new Crawler($property->html());

            $property_link = $property_crawler->filter(".propertyCard-link")->first()->attr("href");
            $property_link = "https://www.rightmove.co.uk" . $property_link;

            echo $property_link . "<br>";
        });
    }
}