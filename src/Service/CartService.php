<?php
namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderItem;

class CartService extends BaseService {

    public function checkoutCart(int $customerId) : Order
    {
        $customer = $this->getCustomer($customerId);
        $cart     = $this->getCart($customerId);

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

    public function plusCart(int $customerId, int $productId) : bool 
    {
        $product = $this->getProduct($productId);
        $cart    = $this->getCart($customerId);

        $add = true;
        foreach ($cart->getItems() as $item) {
            if($item->getProduct()->getId() == $productId){
                $item->setQty($item->getQty() + 1);
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
        }

        $this->entityManager->flush();
        
        return true;
    }

    public function minusCart(int $customerId, int $productId) : bool 
    {
        $product = $this->getProduct($productId);
        $cart    = $this->getCart($customerId);

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