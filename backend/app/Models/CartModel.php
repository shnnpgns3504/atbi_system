<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table = 'cart_items';
    protected $primaryKey = 'cart_id';
    protected $allowedFields = ['user_id', 'product_id', 'quantity'];
    protected $returnType = 'object';
    protected $useTimestamps = false;

    public function getByUser($userId)
    {
        return $this->select('cart_items.*, products.name, products.price, products.image_url, products.atbi_id, atbis.name as atbi_name')
                    ->join('products', 'products.product_id = cart_items.product_id')
                    ->join('atbis', 'atbis.atbi_id = products.atbi_id')
                    ->where('cart_items.user_id', $userId)
                    ->findAll();
    }

    public function getItem($userId, $productId)
    {
        return $this->where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->first();
    }

    public function clearCart($userId)
    {
        return $this->where('user_id', $userId)->delete();
    }
}