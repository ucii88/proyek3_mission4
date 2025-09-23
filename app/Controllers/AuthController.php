<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class AuthController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function loginForm()
    {
        if (session()->get('isLoggedIn')) {
            $role = session()->get('role');
            return redirect()->to($role === 'admin' ? '/dashboard' : '/courses');
        }
        return view('login');
    }

    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (empty($email) || empty($password)) {
            return redirect()->back()->with('error', 'Email dan password wajib diisi.');
        }

        try {
            $user = $this->userModel->getUserByEmail($email);
            if (!$user || !password_verify($password, $user['password'])) {
                return redirect()->back()->with('error', 'Email atau password salah.');
            }

            $sessionData = [
                'isLoggedIn' => true,
                'user_id' => $user['user_id'],
                'full_name' => $user['full_name'],
                'role' => $user['role'],
                'email' => $user['email']
            ];

            // Tambahkan entry_year untuk student
            if ($user['role'] === 'student') {
                $student = $this->userModel->getStudentById($user['user_id']);
                $sessionData['entry_year'] = $student['entry_year'] ?? date('Y');
            }

            session()->set($sessionData);
            return redirect()->to($user['role'] === 'admin' ? '/dashboard' : '/courses');
        } catch (\Exception $e) {
            log_message('error', 'Error during login: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat login. Silakan coba lagi.');
        }
    }

    public function logout()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Anda belum login');
        }

        $confirm = $this->request->getPost('confirm_logout');
        if (!$confirm || $confirm !== 'yes') {
            return redirect()->back()->with('error', 'Konfirmasi logout diperlukan. Silakan coba lagi.');
        }

        try {
            session()->destroy();
            return redirect()->to('/login')->with('success', 'Anda telah logout');
        } catch (\Exception $e) {
            log_message('error', 'Error during logout: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal logout. Silakan coba lagi.');
        }
    }
}