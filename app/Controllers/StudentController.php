<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel;
use CodeIgniter\Controller;

class StudentController extends Controller
{
    protected $userModel;
    protected $courseModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->courseModel = new CourseModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Admin only.');
        }
        
        $data['students'] = $this->userModel->getStudents();
        return view('students/students_common', $data);
    }

    public function getAll()
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Access denied']);
        }
        
        try {
            $students = $this->userModel->getStudents();
            return $this->response->setJSON($students);
        } catch (\Exception $e) {
            log_message('error', 'Error getting students: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'System error']);
        }
    }

    public function create()
    {
        // Log semua informasi untuk debugging
        log_message('info', '=== CREATE STUDENT START ===');
        log_message('info', 'Request Method: ' . $this->request->getMethod());
        log_message('info', 'Content Type: ' . $this->request->getHeaderLine('Content-Type'));
        log_message('info', 'Is AJAX: ' . ($this->request->isAJAX() ? 'yes' : 'no'));
        log_message('info', 'Session Role: ' . session()->get('role'));

        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak. Hanya admin.']);
        }

        // Debug: Cek semua input yang masuk
        $rawInput = file_get_contents('php://input');
        log_message('info', 'Raw Input: ' . $rawInput);
        
        $postData = $this->request->getPost();
        log_message('info', 'POST data: ' . json_encode($postData));
        
        $jsonData = $this->request->getJSON(true);
        log_message('info', 'JSON data: ' . json_encode($jsonData));

        // Ambil input dengan prioritas JSON, fallback ke POST
        $input = [];
        if (!empty($jsonData)) {
            $input = $jsonData;
            log_message('info', 'Using JSON data');
        } elseif (!empty($postData)) {
            $input = $postData;
            log_message('info', 'Using POST data');
        } else {
            log_message('error', 'No input data found');
            return $this->response->setStatusCode(400)->setJSON(['error' => 'No input data']);
        }

        log_message('info', 'Final input: ' . json_encode($input));

        // Cek password secara eksplisit
        if (!isset($input['password'])) {
            log_message('error', 'Password key not found in input');
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Password key not found']);
        }

        if (empty($input['password'])) {
            log_message('error', 'Password is empty');
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Password cannot be empty']);
        }

        log_message('info', 'Password found: ' . (strlen($input['password']) . ' characters'));

        // Validasi sederhana tanpa rules kompleks dulu
        $requiredFields = ['username', 'email', 'password', 'full_name', 'entry_year'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            log_message('error', 'Missing fields: ' . implode(', ', $missingFields));
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Missing required fields: ' . implode(', ', $missingFields)
            ]);
        }

       
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid email format']);
        }

       
        if (!is_numeric($input['entry_year']) || $input['entry_year'] < 2000 || $input['entry_year'] > date('Y')) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid entry year']);
        }

        $userData = [
            'username' => trim($input['username']),
            'email' => trim($input['email']),
            'password' => $input['password'], 
            'role' => 'student',
            'full_name' => trim($input['full_name'])
        ];

        $studentData = [
            'entry_year' => (int)$input['entry_year']
        ];

        log_message('info', 'User data prepared: ' . json_encode([
            'username' => $userData['username'],
            'email' => $userData['email'],
            'password_length' => strlen($userData['password']),
            'role' => $userData['role'],
            'full_name' => $userData['full_name']
        ]));

        log_message('info', 'Student data prepared: ' . json_encode($studentData));

        try {
            $userId = $this->userModel->createStudent($userData, $studentData);
            
            if ($userId) {
                log_message('info', 'Student created successfully with ID: ' . $userId);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Mahasiswa berhasil ditambahkan',
                    'student_id' => $userId
                ]);
            } else {
                log_message('error', 'createStudent returned false/null');
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal menambahkan mahasiswa']);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Exception in create: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Error sistem: ' . $e->getMessage(),
                'debug' => $e->getTraceAsString()
            ]);
        } finally {
            log_message('info', '=== CREATE STUDENT END ===');
        }
    }

    
    public function update()
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Access denied']);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $userId = $input['user_id'] ?? null;
        
        if (!$userId || !is_numeric($userId)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid user ID']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,user_id,{$userId}]",
            'email' => "required|valid_email|is_unique[users.email,user_id,{$userId}]",
            'full_name' => 'required|min_length[2]|max_length[100]',
            'entry_year' => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[' . date('Y') . ']'
        ]);

        if (!empty($input['password'])) {
            $validation->setRule('password', 'min_length[6]');
        }

        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        try {
            $existingStudent = $this->userModel->find($userId);
            if (!$existingStudent || $existingStudent['role'] !== 'student') {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Student not found']);
            }

            $userData = [
                'username' => $input['username'],
                'email' => $input['email'],
                'full_name' => $input['full_name']
            ];

            if (!empty($input['password'])) {
                $userData['password'] = $input['password'];
            }

            $studentData = [
                'entry_year' => $input['entry_year']
            ];

            if ($this->userModel->updateStudent($userId, $userData, $studentData)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data mahasiswa berhasil diperbarui'
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal memperbarui data mahasiswa']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating student: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'System error: ' . $e->getMessage()]);
        }
    }

    public function delete($userId)
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Access denied']);
        }

        if (!$userId || !is_numeric($userId)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid user ID']);
        }

        try {
            $student = $this->userModel->find($userId);
            if (!$student || $student['role'] !== 'student') {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Mahasiswa tidak ditemukan']);
            }

            if ($userId == session()->get('user_id')) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Tidak dapat menghapus akun yang sedang aktif']);
            }

            if ($this->userModel->deleteStudent($userId)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Mahasiswa berhasil dihapus'
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal menghapus mahasiswa']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting student: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'System error occurred']);
        }
    }

    public function view($userId)
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Access denied']);
        }

        if (!$userId || !is_numeric($userId)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid user ID']);
        }

        try {
            $student = $this->userModel->getStudentById($userId);
            if (!$student) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Mahasiswa tidak ditemukan']);
            }

            $enrolledCourses = $this->courseModel->getEnrolledCourses($userId);

            $data = [
                'student' => $student,
                'enrolled_courses' => $enrolledCourses
            ];

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error viewing student: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error loading student data']);
        }
    }

    public function bulkDelete()
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Access denied']);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $studentIds = $input['student_ids'] ?? [];
        
        if (!$studentIds || !is_array($studentIds)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'No students selected']);
        }

        try {
            $deletedCount = 0;
            $currentUserId = session()->get('user_id');
            $errors = [];

            foreach ($studentIds as $userId) {
                if ($userId == $currentUserId) {
                    $errors[] = "Cannot delete active account (ID: {$userId})";
                    continue;
                }

                if (is_numeric($userId) && $this->userModel->deleteStudent($userId)) {
                    $deletedCount++;
                } else {
                    $errors[] = "Failed to delete student ID: {$userId}";
                }
            }

            $response = ['deleted_count' => $deletedCount];
            
            if ($deletedCount > 0) {
                $response['message'] = "{$deletedCount} mahasiswa berhasil dihapus";
            }
            
            if (!empty($errors)) {
                $response['errors'] = $errors;
            }
            
            if ($deletedCount === 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Tidak ada mahasiswa yang dihapus',
                    'errors' => $errors
                ]);
            }

            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'Error bulk deleting students: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'System error occurred']);
        }
    }
}