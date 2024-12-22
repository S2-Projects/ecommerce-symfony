<?php

namespace App\Controller;

use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v1", name="api_")
 */
class CategoryController extends AbstractController
{
    private $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @Route("/category", name="category_index", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();

        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ];
        }

        return $this->json($data);
    }

    /**
     * @Route("/category", name="category_new", methods={"POST"})
     */
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());

        try {
            $category = $this->categoryService->createCategory($data->name);
            return $this->json('Created new category successfully with id ' . $category->getId(), 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/category/{id}", name="category_edit", methods={"PUT"})
     */
    public function edit(Request $request, int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryWithProducts($id);

        if (!$category) {
            return $this->json('No category found for id ' . $id, 404);
        }

        $data = json_decode($request->getContent());
        $this->categoryService->updateCategory($category, $data->name);

        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
        ], 201);
    }

    /**
     * @Route("/category/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryWithProducts($id);

        if (!$category) {
            return $this->json('No category found for id ' . $id, 404);
        }

        $this->categoryService->deleteCategory($category);

        return $this->json('Deleted category with id ' . $id, 200);
    }

    /**
     * @Route("/category/product", name="categories_list", methods={"GET"})
     */
    public function getWithProducts(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();

        $data = [];
        foreach ($categories as $category) {
            $categoryData = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'products' => []
            ];

            foreach ($category->getProducts() as $product) {
                $categoryData['products'][] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'description' => $product->getDescription(),
                    'image' => $product->getImage()
                ];
            }

            $data[] = $categoryData;
        }

        return $this->json($data);
    }

    /**
     * @Route("/category/{id}/product", name="products_category", methods={"GET"})
     */
    public function getProductsOfCategoryById(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryWithProducts($id);

        if (!$category) {
            return $this->json('No category found for id ' . $id, 404);
        }

        $data = [];
        foreach ($category->getProducts() as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
                'image' => $product->getImage()
            ];
        }

        return $this->json($data);
    }
}