<?php

namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\API\ResponseTrait;

class Products extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $productModel = new ProductModel();
        return $this->respond(['success' => true, 'data' => $productModel->getAllActive()]);
    }

    public function create()
    {
        // Use getPost() for FormData (since we are uploading a file now)
        $data = $this->request->getPost();

        $rules = [
            'atbi_id' => 'required|integer',
            'name' => 'required|min_length[2]',
            'price' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return $this->respond(['success' => false, 'errors' => $this->validator->getErrors()], 400);
        }

        $imagePath = null;
        $file = $this->request->getFile('image');
        
        // Handle Image Upload
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            // Ensure you have a folder at: public/uploads/products/
            $file->move(FCPATH . 'uploads/products', $newName);
            $imagePath = '/uploads/products/' . $newName;
        }

        $productModel = new ProductModel();
        $id = $productModel->insert([
            'atbi_id' => $data['atbi_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock' => $data['stock'] ?? 0,
            'weight_kg' => $data['weight_kg'] ?? 1,
            'image_url' => $imagePath,
            'is_active' => 1
        ]);

        return $this->respond(['success' => true, 'message' => 'Product added', 'product_id' => $id], 201);
    }

    public function update($id)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $productModel = new ProductModel();

        if (!$productModel->find($id)) {
            return $this->respond(['success' => false, 'message' => 'Product not found'], 404);
        }

        $productModel->update($id, [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock' => $data['stock'] ?? 0,
            'weight_kg' => $data['weight_kg'] ?? 1,
            'image_url' => $data['image_url'] ?? null,
            'is_active' => $data['is_active'] ?? 1
        ]);

        return $this->respond(['success' => true, 'message' => 'Product updated']);
    }

    public function delete($id)
    {
        $productModel = new ProductModel();
        if (!$productModel->find($id)) {
            return $this->respond(['success' => false, 'message' => 'Product not found'], 404);
        }
        $productModel->update($id, ['is_active' => 0]);
        return $this->respond(['success' => true, 'message' => 'Product deleted']);
    }
}