<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $allowedFields = ['atbi_id', 'name', 'description', 'price', 'stock', 'image_url', 'is_active'];
    protected $returnType = 'object';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getByAtbi($atbiId)
    {
        return $this->where('atbi_id', $atbiId)
                    ->where('is_active', 1)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getAllActive()
    {
        return $this->where('is_active', 1)->findAll();
    }
}