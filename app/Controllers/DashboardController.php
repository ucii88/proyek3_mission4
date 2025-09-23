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

        $role = session()->get('role');
        $data = [
            'full_name' => session()->get('full_name'),
            'email' => session()->get('email'),
            'role' => $role,
        ];

        if ($role === 'admin') {
           
            $userModel = new \App\Models\UserModel();
            $data['total_students'] = $userModel->getTotalStudents();
            return view('admin_dashboard', $data);
        } elseif ($role === 'student') {
           
            $courseModel = new \App\Models\CourseModel();
            $data['total_credits'] = $courseModel->getStudentTotalCredits(session()->get('user_id'));
            $data['entry_year'] = session()->get('entry_year'); 
            return view('student_dashboard', $data);
        } else {
           
            session()->destroy();
            return redirect()->to(base_url('/login'))->with('error', 'Role tidak valid.');
        }
    }
}