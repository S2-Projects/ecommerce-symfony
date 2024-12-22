<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductService
{
    private $entityManager;
    private $fileUploader;
    private $productRepository;
    private $categoryRepository;
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager, 
        FileUploader $fileUploader,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->userRepository = $userRepository;
    }

    // Obtenir tous les produits
    public function getAllProducts()
    {
        return $this->productRepository->findAll();
    }

    // Créer un produit
    public function createProduct($name, $price, $description, $categoryId, $userId, UploadedFile $uploadedFile)
    {
        $category = $this->categoryRepository->find($categoryId);
        $user = $this->userRepository->find($userId);

        if (!$category) {
            throw new \Exception('Category not found');
        }

        if (!$user) {
            throw new \Exception('User not found');
        }

        $product = new Product();
        $product->setName($name);
        $product->setPrice((float)$price);
        $product->setDescription($description);
        $product->setCategory($category);
        $product->setUser($user);
        $product->setImage($this->fileUploader->upload($uploadedFile));

        // Persister le produit
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    // Mettre à jour un produit
    public function updateProduct(Product $product, $name, $price, $description)
    {
        $product->setName($name);
        $product->setPrice((float)$price);
        $product->setDescription($description);

        // Sauvegarder les modifications
        $this->entityManager->flush();
    }

    // Supprimer un produit
    public function deleteProduct(Product $product)
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }
}