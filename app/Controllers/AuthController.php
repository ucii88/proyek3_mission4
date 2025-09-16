<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class AuthController extends Controller
{
    public function loginForm()
    {
        // Redirect ke dashboard jika sudah login
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/dashboard'));
        }
        
        helper('url');
        return view('login');
    }

    public function login()
    {
        $session = session();
        $model = new UserModel();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $session->setFlashdata('errors', $validation->getErrors());
            return redirect()->back()->withInput();
        }

        log_message('debug', "Login attempt at " . Time::now() . ": Email = " . $email);

        try {
            $user = $model->getUserByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                // Set session data
                $sessionData = [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'full_name' => $user['full_name'],
                    'isLoggedIn' => true
                ];
                
                // Jika mahasiswa, ambil student_id juga
                if ($user['role'] === 'student') {
                    $student = $model->getStudentByUserId($user['user_id']);
                    if ($student) {
                        $sessionData['student_id'] = $student['student_id'];
                        $sessionData['entry_year'] = $student['entry_year'];
                    }
                }
                
                $session->set($sessionData);
                
                log_message('debug', "Login successful for user: " . $user['full_name']);
                
                $session->setFlashdata('success', 'Welcome back, ' . $user['full_name'] . '!');
                
                return redirect()->to(base_url('/dashboard'));
            } else {
                log_message('debug', "Login failed for email: " . $email);
                $session->setFlashdata('error', 'Invalid email or password');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            log_message('error', "Database error during login: " . $e->getMessage());
            $session->setFlashdata('error', 'System error. Please try again.');
            return redirect()->back();
        }
    }

    public function logout()
    {
        $fullName = session()->get('full_name');
        session()->destroy();
        
        session()->setFlashdata('success', 'You have been logged out successfully.');
        log_message('debug', "User logged out: " . ($fullName ?? 'unknown'));
        
        return redirect()->to(base_url('/login'));
    }
}