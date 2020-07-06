<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\PromotionCode;
use App\Form\AddItemType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Cart;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/cart", name="cart.")
 */

class CartController extends AbstractController
{
    protected $cart;
    protected $urlGenerator;
    private $session;

    public function __construct(SessionInterface $session , UrlGeneratorInterface $urlGenerator , Cart $cart)
    {
        $this->cart = $cart;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Book $book
     */
//    public function addItemForm(Book $book)
//    {
//        $form = $this->createForm(AddItemType::class, $book);
//
//        return $this->render('cart/addItem_form.html.twig', [
//            'form' => $form->createView()
//        ]);
//    }

    /**
     * @Route("/addItem/{id}", name="addItem", methods={"POST"})
     */
    public function addItem(Request $request, Book $book)
    {
        $this->cart->add($book , $request->get('qty') , $request->get('qty-type'));
        $this->addFlash('cart.add.success.'.$request->get('route'), 'Your Book has Been Added to cart Successfully');
        return $this->redirect($this->buildRoute($request->get('route'),$book->getId()));
    }

    /**
     * @Route("/show", name="show")
     */
    public function show()
    {
        return $this->render('cart/index.html.twig',[
            'cart_count'=>$this->cart->getQtyCount(),
            'route'=>'cart.show',
            'order_books'=>$this->cart->getOrderBooks(),
            'order_total'=>$this->cart->getOrderTotal(),
            'order_discount'=>$this->cart->getOrderDiscount(),
        ]);
    }
    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove(Request $request,Book $book)
    {
        $this->cart->remove($book->getCategory()->getId(),$book->getId());
        $this->addFlash('cart_remove_success', 'Your Book has Been Removed Successfully');
        return $this->redirect($this->buildRoute($request->get('route')));
    }

    /**
     * @Route("/clear", name="clear")
     */
    public function clear(Request $request){
        $this->cart->clear();
        $this->addFlash('cart_clear_success', 'Your Cart has Been Cleared Successfully');
        return $this->redirect($this->buildRoute($request->get('route')));
    }

    /**
     * @Route("/promostion_code", name="promostion_code")
     */
    public function promostionCode(Request $request){
        $this->cart->setPromostionCode($request->get('code'));
        return $this->redirect($this->buildRoute($request->get('route')));
    }

    /**
     * @Route("/checkout", name="checkout")
     */
    public function checkout(Request $request){
        return $this->render('cart/checkout.html.twig',[
            'cart_count'=>$this->cart->getQtyCount(),
            'order_books'=>$this->cart->getOrderBooks(),
            'order_total'=>$this->cart->getOrderTotal(),
            'order_discount'=>$this->cart->getOrderDiscount(),

        ]);
    }

    /**
     * @param $route
     * @param null $bookId
     * @return string
     */
    private function buildRoute($route , $bookId=null):string {
        switch ($route){
                case "book.view":
                    return $this->urlGenerator->generate($route,['id'=>$bookId]);break;
                Default:
                    return $this->urlGenerator->generate($route);break;

        }
    }
}
