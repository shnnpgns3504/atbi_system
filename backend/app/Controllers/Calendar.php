<?php

namespace App\Controllers;

use App\Models\CalendarModel;
use CodeIgniter\API\ResponseTrait;

class Calendar extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $calendarModel = new CalendarModel();
        return $this->respond(['success' => true, 'data' => $calendarModel->getAllActive()]);
    }

    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $rules = [
            'title' => 'required|min_length[2]',
            'event_date' => 'required|valid_date[Y-m-d]',
            'event_type' => 'required|in_list[event,announcement,holiday]',
        ];

        if (!$this->validate($rules)) {
            return $this->respond(['success' => false, 'errors' => $this->validator->getErrors()], 400);
        }

        $calendarModel = new CalendarModel();
        $id = $calendarModel->insert([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'event_date' => $data['event_date'],
            'event_end_date' => $data['event_end_date'] ?? null,
            'event_type' => $data['event_type'],
            'atbi_id' => $data['atbi_id'] ?? null,
            'is_holiday' => $data['is_holiday'] ?? 0,
            'created_by' => $data['user_id'] ?? null,
            'is_active' => 1
        ]);

        return $this->respond(['success' => true, 'message' => 'Event created', 'event_id' => $id], 201);
    }

    public function update($id)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $calendarModel = new CalendarModel();

        if (!$calendarModel->find($id)) {
            return $this->respond(['success' => false, 'message' => 'Event not found'], 404);
        }

        $calendarModel->update($id, [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'event_date' => $data['event_date'],
            'event_end_date' => $data['event_end_date'] ?? null,
            'event_type' => $data['event_type'],
            'atbi_id' => $data['atbi_id'] ?? null,
            'is_holiday' => $data['is_holiday'] ?? 0
        ]);

        return $this->respond(['success' => true, 'message' => 'Event updated']);
    }

    public function delete($id)
    {
        $calendarModel = new CalendarModel();
        if (!$calendarModel->find($id)) {
            return $this->respond(['success' => false, 'message' => 'Event not found'], 404);
        }
        $calendarModel->update($id, ['is_active' => 0]);
        return $this->respond(['success' => true, 'message' => 'Event deleted']);
    }
}