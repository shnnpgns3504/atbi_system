<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ProductModel;
use CodeIgniter\API\ResponseTrait;

class Cart extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $userId = (int)($this->request->getGet('user_id') ?? 0);
        if (!$userId) {
            return $this->respond(['success' => false, 'message' => 'User ID required'], 401);
        }

        $cartModel = new CartModel();
        $items = $cartModel->getByUser($userId);

        $total = 0;
        foreach ($items as $item) {
            $item->subtotal = $item->price * $item->quantity;
            $total += $item->subtotal;
        }

        return $this->respond(['success' => true, 'data' => $items, 'total' => $total]);
    }

    public function add()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $userId = (int)($data['user_id'] ?? 0);
        $productId = (int)($data['product_id'] ?? 0);
        $quantity = (int)($data['quantity'] ?? 1);

        if (!$userId || !$productId) {
            return $this->respond(['success' => false, 'message' => 'Missing required fields'], 400);
        }

        $productModel = new ProductModel();
        if (!$productModel->find($productId)) {
            return $this->respond(['success' => false, 'message' => 'Product not found'], 404);
        }

        $cartModel = new CartModel();
        $existing = $cartModel->getItem($userId, $productId);

        if ($existing) {
            $cartModel->update($existing->cart_id, [
                'quantity' => $existing->quantity + $quantity
            ]);
        } else {
            $cartModel->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return $this->respond(['success' => true, 'message' => 'Added to cart']);
    }

    public function updateItem($cartId)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $cartModel = new CartModel();

        $item = $cartModel->find($cartId);
        if (!$item) {
            return $this->respond(['success' => false, 'message' => 'Cart item not found'], 404);
        }

        $qty = (int)($data['quantity'] ?? 1);
        if ($qty <= 0) {
            $cartModel->delete($cartId);
            return $this->respond(['success' => true, 'message' => 'Item removed']);
        }

        $cartModel->update($cartId, ['quantity' => $qty]);
        return $this->respond(['success' => true, 'message' => 'Cart updated']);
    }

    public function remove($cartId)
    {
        $cartModel = new CartModel();
        $cartModel->delete($cartId);
        return $this->respond(['success' => true, 'message' => 'Item removed']);
    }

    public function clear()
    {
        $userId = (int)($this->request->getGet('user_id') ?? 0);
        if (!$userId) {
            return $this->respond(['success' => false, 'message' => 'User ID required'], 401);
        }
        $cartModel = new CartModel();
        $cartModel->clearCart($userId);
        return $this->respond(['success' => true, 'message' => 'Cart cleared']);
    }
}