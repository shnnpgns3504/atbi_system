<?php

namespace App\Controllers;

use App\Models\CalendarModel;
use CodeIgniter\API\ResponseTrait;

class Calendar extends BaseController
{
    use ResponseTrait;

    // User Calendar & Manage Calendar Data
    public function index()
    {
        $calendarModel = new CalendarModel();
        // Returns all active (both holidays and custom)
        return $this->respond(['success' => true, 'data' => $calendarModel->getActiveEvents()]);
    }

    public function archived()
    {
        $calendarModel = new CalendarModel();
        return $this->respond(['success' => true, 'data' => $calendarModel->getArchivedEvents()]);
    }

    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $rules = [
            'title' => 'required|min_length[2]',
            'event_date' => 'required|valid_date[Y-m-d]',
            'event_type' => 'required|min_length[2]',
            'status' => 'required|in_list[upcoming,finished,cancelled]'
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
            'is_holiday' => 0, // Admin added events are never holidays
            'status' => $data['status'],
            'created_by' => $data['user_id'] ?? null,
            'is_active' => 1,
            'is_archived' => 0
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
            'status' => $data['status']
        ]);

        return $this->respond(['success' => true, 'message' => 'Event updated']);
    }

    public function archive($id)
    {
        $calendarModel = new CalendarModel();
        if (!$calendarModel->find($id)) {
            return $this->respond(['success' => false, 'message' => 'Event not found'], 404);
        }
        $calendarModel->update($id, ['is_archived' => 1]);
        return $this->respond(['success' => true, 'message' => 'Event archived successfully']);
    }

    public function restore($id)
    {
        $calendarModel = new CalendarModel();
        if (!$calendarModel->find($id)) {
            return $this->respond(['success' => false, 'message' => 'Event not found'], 404);
        }
        $calendarModel->update($id, ['is_archived' => 0]);
        return $this->respond(['success' => true, 'message' => 'Event restored successfully']);
    }

    public function deletePermanent($id)
    {
        $calendarModel = new CalendarModel();
        if (!$calendarModel->find($id)) {
            return $this->respond(['success' => false, 'message' => 'Event not found'], 404);
        }
        $calendarModel->delete($id);
        return $this->respond(['success' => true, 'message' => 'Event permanently deleted']);
    }
}