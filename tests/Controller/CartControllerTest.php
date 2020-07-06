<?php


namespace App\Tests\Controller;


use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    public function testAddItemToCartFromHomePage(){

        $client = static::createClient();

        $client->request(
            'POST',
            '/cart/addItem/50',
            ['qty' => '1', 'qty-type'=>'IN' ,'route'=>'home']
        );

        $this->assertTrue($client->getResponse()->isRedirect('/'));
    }

    public function testAddItemToCartFromBookPage(){

        $client = static::createClient();

        $client->request(
            'POST',
            '/cart/addItem/50',
            ['qty' => '5', 'qty-type'=>'VAL' ,'route'=>'book.view']
        );

        $this->assertTrue($client->getResponse()->isRedirect('/book/view/50'));
    }

    public function testAddItemToCartFromCartPage(){

        $client = static::createClient();

        $client->request(
            'POST',
            '/cart/addItem/50',
            ['qty' => '5', 'qty-type'=>'VAL' ,'route'=>'cart.show']
        );

        $this->assertTrue($client->getResponse()->isRedirect('/cart/show'));
    }

    public function testRemoveItemFromCart(){
        $client = static::createClient();

        $client->request(
            'POST',
            '/cart/remove/50',
            ['route'=>'cart.show']
        );

        $this->assertTrue($client->getResponse()->isRedirect('/cart/show'));
    }

    public function testClearCart(){
        $client = static::createClient();

        $client->request(
            'POST',
            '/cart/clear',
            ['route'=>'cart.show']
        );

        $this->assertTrue($client->getResponse()->isRedirect('/cart/show'));
    }

    public function testSetPromotionCode(){
        $client = static::createClient();

        $client->request(
            'POST',
            '/cart/promostion_code',
            ['route'=>'cart.show']
        );

        $this->assertTrue($client->getResponse()->isRedirect('/cart/show'));
    }

    public function testCartCheckout(){
        $client = static::createClient();

        $client->request('GET', '/cart/checkout');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}