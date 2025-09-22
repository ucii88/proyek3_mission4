<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class StudentController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Admin only.');
        }
        
        $data['students'] = $this->userModel->getStudents();
        return view('students', $data);
    }

    public function getAll()
    {
        if (session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Access denied');
        }
        
        $students = $this->userModel->getStudents();
        return $this->respond($students);
    }

    public function create()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'full_name' => 'required|min_length[2]|max_length[100]',
            'entry_year' => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[' . date('Y') . ']'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        try {
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'), // Will be hashed in model
                'role' => 'student',
                'full_name' => $this->request->getPost('full_name')
            ];

            $studentData = [
                'entry_year' => $this->request->getPost('entry_year')
            ];

            if ($this->userModel->createStudent($userData, $studentData)) {
                return redirect()->to('/students')->with('success', 'Mahasiswa berhasil ditambahkan');
            } else {
                return redirect()->back()->with('error', 'Gagal menambahkan mahasiswa');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error creating student: ' . $e->getMessage());
            return redirect()->back()->with('error', 'System error occurred: ' . $e->getMessage());
        }
    }

    public function update()
    {
        if (session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Access denied');
        }

        $userId = $this->request->getPost('user_id');
        
        if (!$userId || !is_numeric($userId)) {
            return $this->fail('Invalid user ID');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,user_id,' . $userId . ']',
            'email' => 'required|valid_email|is_unique[users.email,user_id,' . $userId . ']',
            'full_name' => 'required|min_length[2]|max_length[100]',
            'entry_year' => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[' . date('Y') . ']'
        ]);

        if ($this->request->getPost('password')) {
            $validation->setRule('password', 'min_length[6]');
        }

        if (!$validation->withRequest($this->request)->run()) {
            return $this->failValidationErrors($validation->getErrors());
        }

        try {
            $existingStudent = $this->userModel->find($userId);
            if (!$existingStudent || $existingStudent['role'] !== 'student') {
                return $this->failNotFound('Student not found');
            }

            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'full_name' => $this->request->getPost('full_name')
            ];

            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $userData['password'] = $password; // Will be hashed in model
            }

            $studentData = [
                'entry_year' => $this->request->getPost('entry_year')
            ];

            if ($this->userModel->updateStudent($userId, $userData, $studentData)) {
                return $this->respond(['message' => 'Data mahasiswa berhasil diperbarui']);
            } else {
                return $this->failServerError('Gagal memperbarui data mahasiswa');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating student: ' . $e->getMessage());
            return $this->failServerError('System error: ' . $e->getMessage());
        }
    }

    public function delete($userId)
    {
        if (session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Access denied');
        }

        if (!$userId || !is_numeric($userId)) {
            return $this->fail('Invalid user ID');
        }

        try {
            $student = $this->userModel->find($userId);
            if (!$student || $student['role'] !== 'student') {
                return $this->failNotFound('Mahasiswa tidak ditemukan');
            }

            if ($userId == session()->get('user_id')) {
                return $this->fail('Tidak dapat menghapus akun yang sedang aktif');
            }

            if ($this->userModel->deleteStudent($userId)) {
                return $this->respondDeleted(['message' => 'Mahasiswa berhasil dihapus']);
            } else {
                return $this->failServerError('Gagal menghapus mahasiswa');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting student: ' . $e->getMessage());
            return $this->failServerError('System error occurred');
        }
    }

    public function view($userId)
    {
        if (session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Access denied');
        }

        if (!$userId || !is_numeric($userId)) {
            return $this->fail('Invalid user ID');
        }

        try {
            $student = $this->userModel->getStudentById($userId);
            if (!$student) {
                return $this->failNotFound('Mahasiswa tidak ditemukan');
            }

            $courseModel = new \App\Models\CourseModel();
            $enrolledCourses = $courseModel->getEnrolledCourses($userId);

            $data = [
                'student' => $student,
                'enrolled_courses' => $enrolledCourses
            ];

            return $this->respond($data);
        } catch (\Exception $e) {
            log_message('error', 'Error viewing student: ' . $e->getMessage());
            return $this->failServerError('Error loading student data');
        }
    }

    public function bulkDelete()
    {
        if (session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Access denied');
        }

        $studentIds = $this->request->getPost('student_ids');
        
        if (!$studentIds || !is_array($studentIds)) {
            return $this->fail('No students selected');
        }

        try {
            $deletedCount = 0;
            $currentUserId = session()->get('user_id');

            foreach ($studentIds as $userId) {
                if ($userId == $currentUserId) {
                    continue;
                }

                if (is_numeric($userId) && $this->userModel->deleteStudent($userId)) {
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                return $this->respond(['message' => "$deletedCount mahasiswa berhasil dihapus"]);
            } else {
                return $this->fail('Tidak ada mahasiswa yang dihapus');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error bulk deleting students: ' . $e->getMessage());
            return $this->failServerError('System error occurred');
        }
    }
}