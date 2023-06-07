<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */

class CartController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine->getManager();
    }


    // add item to cart

    /**
     * @Route("/cart", name="add_to_cart", methods={"POST"})
     */

    public function add(Request $request): JsonResponse
    {

        $decoded = json_decode($request->getContent());


        //get user 
        $user = $this->doctrine
            ->getRepository(User::class)
            ->find($decoded->user_id);

        //create new cart object

        $cart = new Cart();

        //set user to this cart item

        $cart->setUser($user);

        //find product

        $product = $this->doctrine
            ->getRepository(Product::class)
            ->find($decoded->product_id);


        //verifie if this product hasn't associated to another user cart

        if ($product->getCart() != null)
            if ($product->getCart()->getUser() != $user)
                return $this->json('This product not in stock with id ' . $product->getId(), 202);
            else
                return $this->json('This product is already in your cart with id ' . $product->getId(), 203);



        //associate this product to this cart

        $product->setCart($cart);

        //applied this in database

        $this->doctrine->persist($cart, $product);
        $this->doctrine->flush();

        $data = [
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'cart_id' => $product->getCart()->getId(),
            'user' => $product->getCart()->getUser()->getName()
        ];

        //return this data as json
        return $this->json($data);
    }

    //get user cart

    /**
     * @Route("/cart/{id}", name="cart_user", methods={"GET"})
     */

    public function index(int $id): JsonResponse
    {

        //get user 
        $user = $this->doctrine
            ->getRepository(User::class)
            ->find($id);

        //array to store data
        $data = [];

        //if user not exist
        if (!$user)
            return $this->json('No user found for id ' . $id, 404);

        //if cart is empty
        if (count($user->getCarts()) == 0)
            return $this->json('Cart empty for user with id = ' . $id, 202);


        //get all products
        $products = $this->doctrine
            ->getRepository(Product::class)
            ->findAll();

        //store items of cart
        $items = [];

        //add product to data if has same cart id with cart associated with user
        foreach ($user->getCarts() as $cart) {
            foreach ($products as $product) {
                if ($product->getCart() === $cart) {
                    $data[] = [
                        "name" => $product->getName(),
                        "price" => $product->getPrice(),

                    ];
                }
            }
        }

        //return this data as json
        return $this->json($data);
    }

    // delete item from cart

    /**
     * @Route("/cart", name="delete_from_cart", methods={"DELETE"})
     */

    public function delete(Request $request): JsonResponse
    {

        $decoded = json_decode($request->getContent());

        //find product by id
        $product = $this->doctrine->getRepository(Product::class)->find($decoded->product_id);

        //if product with id not found
        if (!$product)
            return $this->json('No Product found for id ' . $decoded->product_id, 404);

        //if product not associate to any cart
        if ($product->getCart() == null)
            return $this->json('This Product not associate to any cart ' . $decoded->product_id, 405);

        //if product associate to another user cart

        if ($product->getCart()->getUser()->getId() != $decoded->user_id)
            return $this->json('This Product not found in cart of user id ' . $decoded->user_id, 406);

        //get current cart id to remove it
        $cart_id = $product->getCart()->getId();

        //set cart null
        $product->setCart(null);

        //find cart by id
        $cart = $this->doctrine->getRepository(Cart::class)->find($cart_id);

        //delete object and row in database
        $this->doctrine->remove($cart);
        $this->doctrine->flush();

        return $this->json('Deleted a cart successfully with id ' . $cart_id, 200);
    }
}
