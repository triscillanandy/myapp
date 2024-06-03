<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PasswordResetModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;

class PasswordResetController extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        helper(['url', 'form', 'text']);
    }

    public function forgotPassword()
    {
        echo view('templates/header');
        echo view('forgot_password');
        echo view('templates/footer');
    }

    public function requestReset()
    {
        $userModel = new UserModel();
        $passwordResetModel = new PasswordResetModel();
        $emailService = \Config\Services::email();

        $email = $this->request->getPost('email');

        if (!$this->validate(['email' => 'required|valid_email'])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return $this->failNotFound('Email not found');
        }

        $token = bin2hex(random_bytes(32));

        $passwordResetModel->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Time::now()
        ]);

        $resetLink = base_url("passwordreset/reset/$token");
        $message = "Hi {$user['firstname']},<br><br>Click the link below to reset your password:<br><br><a href='$resetLink'>Reset Password</a><br><br>Thanks,<br>Team";
          
        $emailService->setFrom('uprint332@gmail.com', 'maria');
        $emailService->setTo($email);
        $emailService->setSubject('Password Reset Request');
        $emailService->setMessage($message);

        if ($emailService->send()) {
            return $this->respond(['message' => 'Reset password link sent to your email.']);
        } else {
            return $this->fail('Failed to send email.');
        }
    }

    public function resetPassword($token)
    {
        $passwordResetModel = new PasswordResetModel();

        $resetRequest = $passwordResetModel->where('token', $token)->first();

        if (!$resetRequest || (Time::now()->getTimestamp() - Time::parse($resetRequest['created_at'])->getTimestamp() > 3600)) {
            return $this->failNotFound('Invalid or expired token.');
        }

        $data = ['token' => $token];
        echo view('templates/header', $data);
        echo view('reset_password', $data);
        echo view('templates/footer', $data);
    }

    public function updatePassword()
    {
        $userModel = new UserModel();
        $passwordResetModel = new PasswordResetModel();

        $token = $this->request->getPost('token');
        $newPassword = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Ensure newPassword and confirmPassword are strings
        if (!is_string($newPassword) || !is_string($confirmPassword)) {
            return $this->failValidationErrors(['password' => 'Invalid password input.']);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->failValidationErrors(['password' => 'Passwords do not match.']);
        }

        $resetRequest = $passwordResetModel->where('token', $token)->first();

        if (!$resetRequest) {
            return $this->failNotFound('Invalid token.');
        }

        $user = $userModel->where('email', $resetRequest['email'])->first();
        
        // Ensure newPassword is defined and valid
        if (empty($newPassword)) {
            return $this->failValidationErrors(['password' => 'New password cannot be empty.']);
        }

        $user['password'] = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($userModel->update($user['id'], ['password' => $user['password']])) {
            $passwordResetModel->where('token', $token)->delete();
            return $this->respond(['message' => 'Password updated successfully.']);
        } else {
            return $this->fail('Failed to update password.');
        }
    }
}
