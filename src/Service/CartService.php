<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    private $entityManager;
    private $cartRepository;
    private $productRepository;
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CartRepository $cartRepository,
        ProductRepository $productRepository,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }

    // Ajouter un produit au panier
    public function addProductToCart(User $user, Product $product)
    {
        // Créer un nouveau panier
        $cart = new Cart();
        $cart->setUser($user);
        $cart->addProduct($product);

        // Persister le panier
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $cart;
    }

    // Obtenir le panier d'un utilisateur
    public function getUserCart(User $user)
    {
        // Rechercher les paniers associés à cet utilisateur
        return $this->cartRepository->findBy(['user' => $user]);
    }

    // Supprimer un produit du panier
    public function deleteProductFromCart(User $user, Product $product)
    {
        // Vérifier si le produit est associé à un panier
        $cart = $product->getCarts()->first();

        if ($cart && $cart->getUser() === $user) {
            // Retirer le produit du panier
            $cart->removeProduct($product);

            // Supprimer le panier si le produit est le dernier
            if ($cart->getProducts()->isEmpty()) {
                $this->entityManager->remove($cart);
            }

            // Sauvegarder les modifications
            $this->entityManager->flush();
            return $cart;
        }

        return null;
    }
}
