<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book", name="book.")
 */
class BookController extends AbstractController
{

    protected $cart;

    function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }
    /**
     * @Route("/view/{id}", name="view")
     */
    public function view(Book $book)
    {
        return $this->render('book/index.html.twig', [
            'book' => $book,
            'cart_count'=>$this->cart->getQtyCount(),
            'route'=>'book.view',
            'order_total'=>$this->cart->getOrderTotal()
        ]);
    }
}
