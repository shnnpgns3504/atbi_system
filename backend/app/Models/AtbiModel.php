<?php

namespace App\Models;

use CodeIgniter\Model;

class AtbiModel extends Model
{
    protected $table = 'atbis';
    protected $primaryKey = 'atbi_id';
    protected $allowedFields = ['slug', 'name', 'description', 'location', 'contact_person', 'contact_email', 'contact_phone', 'image_url', 'is_active'];
    protected $returnType = 'object';
    protected $useTimestamps = false;

    public function getActive()
    {
        return $this->where('is_active', 1)->orderBy('atbi_id', 'ASC')->findAll();
    }

    public function getBySlug($slug)
    {
        return $this->where('slug', $slug)->where('is_active', 1)->first();
    }
}