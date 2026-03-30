<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Product;
use App\Entity\Customer;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderItem;

class CartService {
    
    private $entityManager;
    
    function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function getProduct(int $productId) : Product 
    {
        $product = $this->entityManager->getRepository(Product::class)->find($productId);
        if(is_null($product)){
            throw new \Exception('product with id = '.$productId.' is not found');
        }
        return $product;
    }

    public function checkoutCart(int $customerId) : Order
    {
        $customer = $this->entityManager->getRepository(Customer::class)->find($customerId);
        if(is_null($customer)){
            throw new \Exception('customer with id = '.$customerId.' is not found');
        }

        $cart = $this->entityManager->getRepository(Cart::class)->findCartForCustomer($customer);
        if(is_null($cart)){
            throw new \Exception('cart of customer with id = '.$customerId.' is not found');  
        }

        if($cart->getItems()->isEmpty()){
            throw new \Exception('cart of customer with id = '.$customerId.' is empty');  
        }

        $order = new Order();
        $order->setCustomer($customer);
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setItemsPrice(0.00);
        $order->setShipmentPrice(5.99);

        foreach ($cart->getItems() as $citem) {

            $oitem = new OrderItem();
            $oitem->setQty($citem->getQty());
            $oitem->setUnitPrice($citem->getUnitPrice());
            $oitem->setTitle($citem->getProduct()->getTitle());
            $oitem->setProduct($citem->getProduct());
            $oitem->setOrderr($order);

            $order->setItemsPrice($order->getItemsPrice() + $citem->getUnitPrice() * $citem->getQty());
            $order->addOrderItem($oitem);

            $this->entityManager->remove($citem);
        }

        $order->setTotalPrice($order->getItemsPrice() + $order->getShipmentPrice());

        $this->entityManager->persist($order);

        $this->entityManager->flush();
        return $order;
    }

    public function getCart(int $customerId) : Cart 
    {
        $customer = $this->entityManager->getRepository(Customer::class)->find($customerId);
        if(is_null($customer)){
            throw new \Exception('customer with id = '.$customerId.' is not found');
        }

        $cart = $this->entityManager->getRepository(Cart::class)->findCartForCustomer($customer);

        if(is_null($cart)){
            $cart = new Cart();
            $cart->setCustomer($customer);
            $cart->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }

        return $cart;
    }

    public function plusCart(int $customerId, int $productId) : bool 
    {
        $product = $this->getProduct($productId);
        $cart = $this->getCart($customerId);
        $add = true;
        foreach ($cart->getItems() as $item) {
            if($item->getProduct()->getId() == $productId){
                $item->setQty($item->getQty() + 1);
                // $this->entityManager->flush();
                $add = false;
                break;
            }
        }
        if($add){
            $item = new CartItem();
            $item->setQty(1);
            $item->setProduct($product);
            $item->setUnitPrice($product->getPrice());
            $cart->addItem($item);
            // $this->entityManager->persist($cart);
            // $this->entityManager->flush();
        }

        $this->entityManager->flush();
        
        return true;
    }

    public function minusCart(int $customerId, int $productId) : bool 
    {
        $product = $this->getProduct($productId);
        $cart = $this->getCart($customerId);

        foreach ($cart->getItems() as $item) {
            if($item->getProduct()->getId() == $productId){
                if($item->getQty()>1){
                    $item->setQty($item->getQty() - 1);
                    $this->entityManager->flush();
                }
                break;
            }
        }

        return true;
    }
}