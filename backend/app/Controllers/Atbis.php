<?php

namespace App\Controllers;

use App\Models\AtbiModel;
use App\Models\ProductModel;
use CodeIgniter\API\ResponseTrait;

class Atbis extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $atbiModel = new AtbiModel();
        return $this->respond(['success' => true, 'data' => $atbiModel->getActive()]);
    }

    public function show($slug)
    {
        $atbiModel = new AtbiModel();
        $productModel = new ProductModel();

        $atbi = $atbiModel->getBySlug($slug);
        if (!$atbi) {
            return $this->respond(['success' => false, 'message' => 'ATBI not found'], 404);
        }

        $atbi->products = $productModel->getByAtbi($atbi->atbi_id);

        return $this->respond(['success' => true, 'data' => $atbi]);
    }
}