<?php

use App\Scraper;
use React\EventLoop\Factory;
use Clue\React\Buzz\Browser;
use React\ChildProcess\Process;

require __DIR__ . '/vendor/autoload.php';

$loop   = Factory::create();
$client = new Browser($loop);

$url = 'http://www.adorocinema.com/filmes/agenda/mes/';

$scraper = new Scraper($client);
$scraper->scrape($url)
    ->then(function ($movies) use ($loop) {
        $timestamp = time();
        foreach ($movies as $movie) {
            $movieInJson = json_encode($movie, JSON_UNESCAPED_UNICODE);
            $process = new Process("echo '${movieInJson}' >> {$timestamp}_movies.json", __DIR__);
            $process->start($loop);
        }
    });

$loop->run();
