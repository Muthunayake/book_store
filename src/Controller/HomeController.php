<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Service\Cart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    protected $cart;

    function __construct(Cart $cart ,SessionInterface $session)
    {
        $this->session = $session;
        $this->session->start();
        $this->cart = $cart;
    }
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request,CategoryRepository $categoryRepository,BookRepository $bookRepository)
    {
        $params['active'] = 1;

        if($request->get('category')){
            $params['category']  =  $request->get('category');
            $this->session->set('selected_category' , $params['category']);
        }else{
            if($request->request->has('category')){
                $this->session->remove('selected_category');
            }else{
                if($this->session->get('selected_category'))
                    $params['category']  =  $this->session->get('selected_category');
            }
        }

        return $this->render('home/index.html.twig', [
            'categories' => $categoryRepository->findBy(['active'=>1]),
            'books' => $bookRepository->findBy($params),
            'selected_category'=> isset($params['category']) ? $params['category'] :'',
            'cart_count'=>$this->cart->getQtyCount(),
            'order_total'=>$this->cart->getOrderTotal(),
            'route'=>'home'
        ]);
    }
}
