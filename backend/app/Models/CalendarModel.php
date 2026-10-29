<?php

namespace App\Models;

use CodeIgniter\Model;

class CalendarModel extends Model
{
    protected $table = 'calendar_events';
    protected $primaryKey = 'event_id';
    protected $allowedFields = ['title', 'description', 'event_date', 'event_end_date', 'event_type', 'atbi_id', 'is_holiday', 'created_by', 'is_active'];
    protected $returnType = 'object';
    protected $useTimestamps = false;

    public function getAllActive()
    {
        return $this->select('calendar_events.*, atbis.name as atbi_name')
                    ->join('atbis', 'atbis.atbi_id = calendar_events.atbi_id', 'left')
                    ->where('calendar_events.is_active', 1)
                    ->orderBy('calendar_events.event_date', 'ASC')
                    ->findAll();
    }
}