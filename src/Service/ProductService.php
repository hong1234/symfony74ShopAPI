<?php
namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Product;
use App\Entity\Category;

class ProductService {
    private $validator;
    private $entityManager;
    
    function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager) {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    public function newProduct(array $data) : Product 
    {
        $product = new Product();
        $product->setTitle($data['title']);
        $product->setPrice($data['price']);

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            throw new \Exception((string) $errors);
        }

        $category = $this->entityManager->getRepository(Category::class)->find($data['category']);
        if(is_null($category)){
            throw new \Exception('category with id = '.$data['category'].' is not found');
        }
        $product->setCategory($category);
        
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    public function getProduct(int $productId) : Product 
    {
        $product = $this->entityManager->getRepository(Product::class)->find($productId);
        if(is_null($product)){
            throw new \Exception('product with id = '.$productId.' is not found');
        }
        return $product;
    }
}