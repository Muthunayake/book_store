<?php

namespace App\Service;


use App\Entity\Book;
use App\Repository\CategoryRepository;
use App\Repository\PromotionCodeRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart{

    private $session;
    private $sessionId;
    private $promotionCodeRepository;
    private $categoryRepository;
    /**
     * @param SessionInterface $session
     */
    public function __construct(
        SessionInterface $session,
        PromotionCodeRepository $promotionCodeRepository,
        CategoryRepository $categoryRepository
    )
    {
        $this->session = $session;
        $this->session->start();
        $this->sessionId = $session->getId();
        $this->promotionCodeRepository = $promotionCodeRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Book $book
     * @param $qty
     * @param $qtyType
     * @return bool
     */
    function add(Book $book , $qty , $qtyType) : bool {
        $bookId = $book->getId();
        $categoryId = $book->getCategory()->getId();

        if($this->check($categoryId , $bookId)){
            $order = $this->getOne($categoryId , $bookId);
            if($qtyType=='VAL')
                $order['qty'] = $qty;
            else
                $order['qty'] = $order['qty'] + $qty;
        }else{
            $order =[
                'qty'=>$qty,
                'id'=>$book->getId(),
                'price'=>$book->getPrice(),
                'title'=>$book->getTitle(),
                'author'=>$book->getAuthor(),
                'category'=>$book->getCategory()->getCategory(),
                'image'=>$book->getImage(),
            ];
        }

        $this->session->set(
            $this->sessionId,
            $this->buildOrders($order,$categoryId , $bookId)
        );

        return true;
    }

    /**
     * @param $categoryId
     * @param $bookId
     */
    private function check($categoryId , $bookId) : bool {
        $orders = $this->getAll();
        return isset($orders[$categoryId]) && isset($orders[$categoryId][$bookId]);
    }

    /**
     * @return array
     */
    private function getAll() : array {
        return $this->session->get($this->sessionId,[]);
    }

    /**
     * @param $categoryId
     * @param $bookId
     * @return array
     */
    private function getOne($categoryId , $bookId) : array {
        return $this->session->get($this->sessionId)[$categoryId][$bookId];
    }


    /**
     * @param $order
     * @param $categoryId
     * @param $bookId
     * @return array
     */
    private function buildOrders($order, $categoryId , $bookId) : array {
        $orders = $this->getAll();
        $orders[$categoryId][$bookId] = $order;
        return $orders;
    }


    /**
     * @param $categoryId
     * @param $bookId
     * @return bool
     */
    public function remove($categoryId , $bookId) : bool {
        $orders = $this->getAll();
        unset($orders[$categoryId][$bookId]);

        $this->session->set(
            $this->sessionId,
            $orders
        );
        return true;
    }

    /**
     * @return int
     */
    function getQtyCount() : int {
        $orders = $this->getAll();
        $count = 0;
        foreach ($orders as $category){
            foreach ($category as $book){
                $count+=$book['qty'];
            }
        }
        return $count;
    }

    /**
     * @return array
     */
    function getOrderBooks() : array {
        $orders = $this->getAll();
        $books = [];
        foreach ($orders as $category){
            foreach ($category as $book){
                $books [] =$book;
            }
        }
        return $books;
    }

    /**
     * @return float
     */
    function getOrderTotal():float {
        $orders = $this->getAll();
        $total = 0;
        foreach ($orders as $category){
            foreach ($category as $book){
                $total+=$book['price'] * $book['qty'];
            }
        }
        return $total;
    }

    /**
     * @return array
     */
    function getOrderDiscount():array {
        $code = $this->getPromostionCode();
        $total = $this->getOrderTotal();
        $discountRate = $childCatCount = 0;

        $result = $this->promotionCodeRepository->findOneBy([
            'code'=> $code,
            'active'=>1
        ]);

        if($result){
            $discount['rate'] = $result->getDiscount();
            $discount['code'] = $result->getCode();
            $discount['discount'] = $total*$discount['rate'];
            $discount['total'] = $total;
            $discount['net_amount'] = $discount['total'] - $discount['discount'];
        }else{
            $orders = $this->getAll();
            $childCatId = $this->categoryRepository->findOneBy(['category'=>'Children'])->getId();
            $childCatData = isset($orders[$childCatId]) ? $orders[$childCatId] : [];
            #first logic
            foreach ($childCatData as $childCat){
                $childCatCount+=$childCat['qty'];
            }
            if($childCatCount>=5)
                $discountRate = 0.1;

            #secound logic
            foreach ($orders as $order){
                $catCount = 0;
                foreach ($order as $o){
                    $catCount+=$o['qty'];
                }
                if($catCount>=10)
                    $discountRate+= 0.05;
            }

            $discount['rate'] = $discountRate;
            $discount['code'] = '';
            $discount['discount'] = $total*$discount['rate'];
            $discount['total'] = $total;
            $discount['net_amount'] = $discount['total'] - $discount['discount'];
        }
       return $discount;
    }

    /**
     * @param $code
     * @return bool
     */
    function setPromostionCode($code):bool {
        $this->session->set($this->sessionId.'-promotions-code',$code);
        return true;
    }


    /**
     * @return mixed
     */
    function getPromostionCode() {
       return $this->session->get($this->sessionId.'-promotions-code');
    }

    /**
     * @return mixed
     */
    function clear(){
        return $this->session->remove($this->sessionId);
    }
}