<?php namespace App\Controllers;

use App\Models\ContactModel;
use CodeIgniter\API\ResponseTrait;

class Contactlist extends BaseController
{
    use ResponseTrait;

    // Return an array of all contacts
    public function index($userId) {
        $contactModel = new ContactModel();
        // Filter contacts based on user ID (assuming a 'user_id' field in the contacts table)
        $contacts = $contactModel->where('user_id', $userId)->findAll();
        return $this->respond($contacts);
    }

    // Return a single contact by ID
    public function show($id = null)
    {
        $contactModel = new ContactModel();
        $contact = $contactModel->find($id);

        if ($contact) {
            return $this->respond($contact);
        } else {
            return $this->failNotFound('Contact not found');
        }
    }

    // Create a new contact
    public function create()
    {
        $model = new ContactModel();

        $rules = [
          
            'name' => [
                'rules' => 'required|min_length[3]|max_length[20]',
                'errors' => [
                    'required' => 'Last name is required.',
                    'min_length' => 'Last name must be at least 3 characters long.',
                    'max_length' => 'Last name cannot exceed 20 characters.'
                ]
            ],
            'email' => [
                'rules' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email is required.',
                    'min_length' => 'Email must be at least 6 characters long.',
                    'max_length' => 'Email cannot exceed 50 characters.',
                    'valid_email' => 'Please provide a valid email address.',
                    'is_unique' => 'Email is already registered.'
                ]
            ]
        ];
        
        if ($this->validate($rules)) {
            $newUserData = [
                'name' => $this->request->getVar('name'),
                'user_id' => $this->request->getVar('user_id'),
                'email' => $this->request->getVar('email'),
               
            ];
        
            $model->save($newUserData);
            $userId = $model->getInsertID();
        
            return $this->respond(['message' => 'User created successfully.', 'userId' => $userId], 200);
        } else {
            $response = [
                'errors' => $this->validator->getErrors(),
                'message' => 'Invalid Inputs'
            ];
            return $this->fail($response, 409);}
    }

    // // Update a contact by ID
    // public function update($id = null)
    // {
    //     $contactModel = new ContactModel();

    //     $data = [
            
    //         'name' => $this->request->getVar('name'),
    //         'email' => $this->request->getVar('email')
    //     ];

    //     if ($contactModel->update($id, $data)) {
    //         return $this->respond([
    //             'status' => 'success',
    //             'message' => 'Contact has been updated.',
    //             'data' => $data
    //         ]);
    //     } else {
    //         return $this->fail('Failed to update contact.');
    //     }
    // }

    // // Delete a contact by ID
    // public function delete($id = null)
    // {
    //     $contactModel = new ContactModel();

    //     if ($contactModel->delete($id)) {
    //         return $this->respondDeleted([
    //             'status' => 'success',
    //             'message' => 'Contact has been deleted.'
    //         ]);
    //     } else {
    //         return $this->fail('Failed to delete contact.');
    //     }
    // }
}
