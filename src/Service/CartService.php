<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Customer;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;

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