<?php

namespace App\Tests\controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerVideoTest extends WebTestCase
{
    public function testEmptySearchResult(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('Search video')->form([
            'query' => 'XYZ',
        ]);
        $client->submit($form);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'No results were found');
    }

    public function testSearchResultsFound(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('Search video')->form([
            'query' => '1',
        ]);
        $crawler = $client->submit($form);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Search results');
        self::assertEquals(2, $crawler->filter('h2')->count());
        self::assertSelectorTextContains('h2', 'Movie 1');
    }
}
