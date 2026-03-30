<?php
namespace App\Service;

use App\Entity\Product;

class ProductService extends BaseService {

    public function newProduct(array $data) : Product 
    {
        $product = new Product();
        $product->setTitle($data['title']);
        $product->setPrice($data['price']);

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            throw new \Exception((string) $errors);
        }

        $category = $this->getCategory($data['category']);
        $product->setCategory($category);
        
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

}