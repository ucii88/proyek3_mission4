<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'));
        }
        $data['role'] = session()->get('role');
        return view('dashboard', $data);
    }
}