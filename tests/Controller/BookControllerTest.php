<?php


namespace App\Tests\Controller;


use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    protected $book;
    public function setUp()
    {
        $this->book = new Book();
    }

    public function testBookView()
    {
        $client = static::createClient();

        $client->request('GET', '/book/view/50');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}