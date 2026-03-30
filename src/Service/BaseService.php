<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Category;

class BaseService {

    protected $validator;
    protected $entityManager;
    
    function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager) {
        $this->validator = $validator;
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

    public function getCustomer(int $customerId) : Customer {
        $customer = $this->entityManager->getRepository(Customer::class)->find($customerId);
        if(is_null($customer)){
            throw new \Exception('customer with id = '.$customerId.' is not found');
        }
        return $customer;
    }

    public function getCart(int $customerId) : Cart 
    {
        $customer = $this->getCustomer($customerId);
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

    public function getCategory(int $categoryId) : Category {
        $category = $this->entityManager->getRepository(Category::class)->find($categoryId);
        if(is_null($category)){
            throw new \Exception('category with id = '.$categoryId.' is not found');
        }
        return $category;
    }

    public function getOrder(int $orderId) : Order {
        $order = $this->entityManager->getRepository(Order::class)->find($orderId);
        if(is_null($order)){
            throw new \Exception('order with id = '.$orderId.' is not found');
        }
        return $order;
    }

}