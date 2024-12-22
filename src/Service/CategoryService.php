<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    private $categoryRepository;
    private $entityManager;

    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }

    // Obtenir toutes les catégories
    public function getAllCategories()
    {
        return $this->categoryRepository->findAll();
    }

    // Créer une nouvelle catégorie
    public function createCategory($name)
    {
        $category = new Category();
        $category->setName($name);

        // Utilisation de l'entityManager pour persister l'entité
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    // Mettre à jour une catégorie
    public function updateCategory(Category $category, $name)
    {
        $category->setName($name);
        $this->entityManager->flush();

        return $category;
    }

    // Supprimer une catégorie
    public function deleteCategory(Category $category)
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }

    // Récupérer une catégorie avec ses produits
    public function getCategoryWithProducts($categoryId)
    {
        return $this->categoryRepository->find($categoryId);
    }
}
