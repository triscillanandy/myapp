<?php namespace App\Controllers;

use App\Models\ContactModel;
use CodeIgniter\API\ResponseTrait;

class Contactlist extends BaseController
{
    use ResponseTrait;

    // // Return an array of all contacts
    // public function index($userId) {
    //     $contactModel = new ContactModel();
    //     // Filter contacts based on user ID (assuming a 'user_id' field in the contacts table)
    //     $contacts = $contactModel->where('user_id', $userId)->findAll();
    //     return $this->respond($contacts);
    // }
    // search peopple by name s email 

    // Return a single contact by ID
    // public function show($id = null)
    // {
    //     $contactModel = new ContactModel();
    //     $contact = $contactModel->find($id);

    //     if ($contact) {
    //         return $this->respond($contact);
    //     } else {
    //         return $this->failNotFound('Contact not found');
    //     }
    // }

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
            'rules' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[contacts.email]',
            'errors' => [
                'required' => 'Email is required.',
                'min_length' => 'Email must be at least 6 characters long.',
                'max_length' => 'Email cannot exceed 50 characters.',
                'valid_email' => 'Please provide a valid email address.',
                'is_unique' => 'Email contact is already registered.'
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
        // $userId = $model->getInsertID();
        return redirect()->to('/dashboard')->with('success', 'Contact added successfully.');
    } else {
        // Set flashdata to display the error messages
        return redirect()->to('/dashboard')->with('fail', $this->validator->getErrors())->withInput();
        // Redirect back to the same page and keep the input data
      
    }

    }


    public function edit($id)
    {
        // Fetch the contact data by ID
        $contactModel = new ContactModel();
        $contact = $contactModel->find($id);
        echo view('templates/header');
   
      
        // Pass the contact data to the view
        return view('edit_contact', ['contact' => $contact]);
        echo view('templates/footer');
    }

    // Update a contact by ID
    public function update($id)
    {
        $contactModel = new ContactModel();

        // Retrieve the updated data from the form
        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email')
        ];

        // Update the contact data in the database
        if ($contactModel->update($id, $data)) {
            // Redirect to the contact list page or display a success message
            return redirect()->to('dashboard')->with('success', 'Contact updated successfully.');
        } else {
            // Redirect back to the edit page with an error message
            return redirect()->back()->withInput()->with('error', 'Failed to update contact.');
        }
    }


    // Delete a contact by ID
    public function delete($id = null)
    {
        $contactModel = new ContactModel();

        if ($contactModel->delete($id)) {
            return redirect()->to('/dashboard')->with('success', 'Contact deleted successfully.');
            
        } else {
            return $this->fail('Failed to delete contact.');
        }
    }
}
