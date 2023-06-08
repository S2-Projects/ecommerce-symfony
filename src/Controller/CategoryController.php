<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/api", name="api_")
 */

class CategoryController extends AbstractController
{

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine->getManager();
    }

    /**
     * @Route("/category", name="category_index", methods={"GET"})
     */

    public function index(): JsonResponse
    {

        //get all categories from database
        $cat = $this->doctrine
            ->getRepository(Category::class)
            ->findAll();

        //array to store data
        $data = [];

        //add every category that we get to data array
        foreach ($cat as $category) {
            $data[] = [
                'id' => $category->getId(),
                'nom' => $category->getName(),
            ];
        }

        //return this data as json
        return $this->json($data);
    }

    //add new category

    /**
     * @Route("/category", name="category_new", methods={"POST"})
     */

    public function new(Request $request): JsonResponse
    {
        // transform data to json format
        $data = json_decode($request->getContent());

        // new Category object
        $cat = new Category();
        $cat->setName($data->name);

        // add to database
        $this->doctrine->persist($cat);
        $this->doctrine->flush();

        return $this->json('Created new category successfully with id ' . $cat->getId(), 201);
    }

    //update a category

    /**
     * @Route("/category/{id}", name="category_edit", methods={"PUT"})
     */
    public function edit(Request $request, int $id): JsonResponse
    {

        //find category by id
        $cat = $this->doctrine->getRepository(Category::class)->find($id);

        //if category with id not found
        if (!$cat) {
            return $this->json('No category found for id ' . $id, 404);
        }

        //transform data to json format
        $newData = json_decode($request->getContent());

        //update name
        $cat->setName($newData->name);

        //update in database
        $this->doctrine->flush();

        //get new data
        $data = [
            'id' => $cat->getId(),
            'name' => $cat->getName(),
        ];

        return $this->json($data, 201);
    }


    //delete category

    /**
     * @Route("/category/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {

        //find category by id
        $cat = $this->doctrine->getRepository(Category::class)->find($id);

        //if category with id not found
        if (!$cat) {
            return $this->json('No Category found for id ' . $id, 404);
        }

        //delete object and row in database
        $this->doctrine->remove($cat);
        $this->doctrine->flush();

        return $this->json('Deleted a Category successfully with id ' . $id, 200);
    }

    /**
     * @Route("/categories", name="categories_list", methods={"GET"})
     */
    public function showCat(): JsonResponse
    {
        $categories =  $this->doctrine->getRepository(Category::class)->findAll();


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

    //get products of a category

    /**
     * @Route("/category/{id}/product", name="products_category", methods={"GET"})
     */
    public function getProducts(int $id): JsonResponse
    {
        $cat =  $this->doctrine->getRepository(Category::class)->find($id);

        $data = [];

        foreach ($cat->getProducts() as $product) {
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
