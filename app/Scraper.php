<?php

namespace App;

use Clue\React\Buzz\Browser;
use React\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class Scraper
{
    private $brower;
    private $scraped;

    public function __construct(Browser $brower)
    {
        $this->brower = $brower;
    }

    public function scrape(string $url): PromiseInterface
    {
        return $this->extractFromUrl($url);
    }

    private function extractFromUrl(string $url): PromiseInterface
    {
        return $this->brower
            ->get($url)
            ->then(function (ResponseInterface $response) {
                return $this->extractFromHtml((string) $response->getBody());
            });
    }

    private function extractFromHtml(string $html): array
    {
        $crawler = new Crawler($html);

        $moviesList = [];

        $crawler
            ->filter('.schedule_item')
            ->each(function (Crawler $node) use (&$moviesList) {
                $date   = $node->filter('h3')->text();
                $movies = $node->filter('.list_item_p2v li a')->extract(['_text']);

                $moviesList[] = ['date' => $date, 'movies' => $movies];
            });

        return $moviesList;
    }
}
