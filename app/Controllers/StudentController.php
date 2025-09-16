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

        try {
            $data['students'] = $this->userModel->getStudents();
            return view('students', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error loading students: ' . $e->getMessage());
            return redirect()->to('/dashboard')->with('error', 'Error loading student data');
        }
    }

    // CREATE - Tambah mahasiswa (Admin only)
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
            $errors = $validation->getErrors();
            $errorMessage = implode(', ', $errors);
            return redirect()->back()->with('error', 'Validation failed: ' . $errorMessage)->withInput();
        }

        try {
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'), 
                'role' => 'student',
                'full_name' => $this->request->getPost('full_name')
            ];

            $studentData = [
                'entry_year' => $this->request->getPost('entry_year')
            ];

            $userId = $this->userModel->createStudent($userData, $studentData);
            
            if ($userId) {
                session()->setFlashdata('success', 'Mahasiswa berhasil ditambahkan');
                log_message('debug', 'New student created with ID: ' . $userId);
            } else {
                session()->setFlashdata('error', 'Gagal menambahkan mahasiswa');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error creating student: ' . $e->getMessage());
            session()->setFlashdata('error', 'System error: ' . $e->getMessage());
        }

        return redirect()->to('/students');
    }

    // UPDATE - Edit mahasiswa (Admin only)
    public function update()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $userId = $this->request->getPost('user_id');
        
        if (!$userId || !is_numeric($userId)) {
            return redirect()->back()->with('error', 'Invalid user ID');
        }

        $validation = \Config\Services::validation();
      $validation->setRules([
    'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
    'email' => 'required|valid_email|is_unique[users.email]',
    'password' => 'required|min_length[6]',
    'full_name' => 'required|min_length[2]|max_length[100]',
    'entry_year' => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[' . date('Y') . ']'  // Sudah benar di dokumen, tapi pastikan konsisten
]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();
            $errorMessage = implode(', ', $errors);
            return redirect()->back()->with('error', 'Validation failed: ' . $errorMessage)->withInput();
        }

        try {
          
            $existingStudent = $this->userModel->find($userId);
            if (!$existingStudent || $existingStudent['role'] !== 'student') {
                return redirect()->back()->with('error', 'Student not found');
            }

            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'full_name' => $this->request->getPost('full_name')
            ];


            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $userData['password'] = $password;
            }

            $studentData = [
                'entry_year' => $this->request->getPost('entry_year')
            ];

            if ($this->userModel->updateStudent($userId, $userData, $studentData)) {
                session()->setFlashdata('success', 'Data mahasiswa berhasil diperbarui');
                log_message('debug', 'Student updated with ID: ' . $userId);
            } else {
                session()->setFlashdata('error', 'Gagal memperbarui data mahasiswa');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating student: ' . $e->getMessage());
            session()->setFlashdata('error', 'System error: ' . $e->getMessage());
        }

        return redirect()->to('/students');
    }

    // DELETE - Hapus Mahasiswa (Admin only)
    public function delete($userId)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        if (!$userId || !is_numeric($userId)) {
            return redirect()->to('/students')->with('error', 'Invalid user ID');
        }

        try {
       
            $student = $this->userModel->find($userId);
            if (!$student || $student['role'] !== 'student') {
                return redirect()->to('/students')->with('error', 'Mahasiswa tidak ditemukan');
            }

            if ($userId == session()->get('user_id')) {
                return redirect()->to('/students')->with('error', 'Tidak dapat menghapus akun yang sedang aktif');
            }

            if ($this->userModel->deleteStudent($userId)) {
                session()->setFlashdata('success', 'Mahasiswa berhasil dihapus');
                log_message('debug', 'Student deleted with ID: ' . $userId);
            } else {
                session()->setFlashdata('error', 'Gagal menghapus mahasiswa');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting student: ' . $e->getMessage());
            session()->setFlashdata('error', 'System error occurred');
        }

        return redirect()->to('/students');
    }

    // VIEW - Lihat detail mahasiswa dengan enrolled courses (Admin only)
    public function view($userId)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        if (!$userId || !is_numeric($userId)) {
            return redirect()->to('/students')->with('error', 'Invalid user ID');
        }

        try {

            $student = $this->userModel->getStudentById($userId);
            if (!$student) {
                return redirect()->to('/students')->with('error', 'Mahasiswa tidak ditemukan');
            }

            $courseModel = new \App\Models\CourseModel();
            $enrolledCourses = $courseModel->getEnrolledCourses($userId);

            $data = [
                'student' => $student,
                'enrolled_courses' => $enrolledCourses
            ];

            return view('student_detail', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error viewing student: ' . $e->getMessage());
            return redirect()->to('/students')->with('error', 'Error loading student data');
        }
    }

    // BULK DELETE - Hapus multiple mahasiswa (Admin only)
    public function bulkDelete()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $studentIds = $this->request->getPost('student_ids');
        
        if (!$studentIds || !is_array($studentIds)) {
            return redirect()->back()->with('error', 'No students selected');
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
                session()->setFlashdata('success', "$deletedCount mahasiswa berhasil dihapus");
            } else {
                session()->setFlashdata('error', 'Tidak ada mahasiswa yang dihapus');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error bulk deleting students: ' . $e->getMessage());
            session()->setFlashdata('error', 'System error occurred');
        }

        return redirect()->to('/students');
    }
}