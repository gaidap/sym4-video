<?php

namespace App\Tests\controllers;

use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerCategoriesTest extends WebTestCase
{
    private KernelBrowser $client;

    /**
     * @var EntityManager|object|null
     */
    private $entityManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->entityManager = self::$container->get('doctrine.orm.entity_manager');
        $this->entityManager->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->rollback();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testCategoriesOnPage(): void
    {
        $this->client->request('GET', '/admin/categories');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h2', 'Categories list');
    }

    public function testCountOfCategories(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');
        $this->assertCount(17, $crawler->filter('option'));
    }

    public function testAddCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');
        $form = $crawler->selectButton('Add')->form([
            'category[parent]' => 1,
            'category[name]' => 'Paul',
        ]);
        $this->client->submit($form);
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Paul']);
        $this->assertNotNull($category);
    }

    public function testEditCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/edit-category/1');
        $form = $crawler->selectButton('Save')->form([
            'category[parent]' => 1,
            'category[name]' => 'Clever Paul',
        ]);
        $this->client->submit($form);
        $category = $this->entityManager->getRepository(Category::class)->find(1);
        $this->assertSame('Clever Paul', $category->getName());
    }

    public function testDeleteCategory(): void
    {
        $this->client->request('GET', '/admin/delete-category/1');
        $category = $this->entityManager->getRepository(Category::class)->find(1);
        $this->assertNull( $category);
    }
}
