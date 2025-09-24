<?php
namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class CourseController extends Controller
{
    protected $courseModel;
    protected $userModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'));
        }

        $role = session()->get('role');
        $data['courses'] = $this->courseModel->getAllCourses();

        if ($role === 'student') {
            $studentId = session()->get('user_id');
            $data['enrolled'] = $this->courseModel->getEnrolledCourses($studentId);
            $data['total_credits'] = $this->courseModel->getStudentTotalCredits($studentId);
        }

        return view('courses/courses_common', $data);
    }

    public function mycourses()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return redirect()->to(base_url('/dashboard'));
        }

        $studentId = session()->get('user_id');
        $data['enrolled_courses'] = $this->courseModel->getEnrolledCourses($studentId);
        $data['total_credits'] = $this->courseModel->getStudentTotalCredits($studentId);

        return view('mycourses', $data);
    }

    public function create()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'course_name' => 'required|min_length[3]|max_length[100]',
            'credits' => 'required|integer|greater_than[0]|less_than_equal_to[6]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        try {
            $data = [
                'course_name' => $this->request->getPost('course_name'),
                'credits' => $this->request->getPost('credits')
            ];

            if ($this->courseModel->insert($data)) {
                return redirect()->to('/courses')->with('success', 'Mata kuliah berhasil ditambahkan');
            } else {
                return redirect()->back()->with('error', 'Gagal menambahkan mata kuliah');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error creating course: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    public function update()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $courseId = $this->request->getPost('course_id');
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'course_name' => 'required|min_length[3]|max_length[100]',
            'credits' => 'required|integer|greater_than[0]|less_than_equal_to[6]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        try {
            $data = [
                'course_name' => $this->request->getPost('course_name'),
                'credits' => $this->request->getPost('credits')
            ];

            if ($this->courseModel->update($courseId, $data)) {
                return redirect()->to('/courses')->with('success', 'Mata kuliah berhasil diperbarui');
            } else {
                return redirect()->back()->with('error', 'Gagal memperbarui mata kuliah');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating course: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    public function delete($courseId)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        if (!$this->request->getPost('confirm_delete')) {
            return redirect()->back()->with('error', 'Konfirmasi penghapusan diperlukan');
        }

        try {
            $enrolledCount = $this->courseModel->getEnrolledStudentsCount($courseId);
            if ($enrolledCount > 0) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus mata kuliah yang masih memiliki mahasiswa terdaftar');
            }

            if ($this->courseModel->delete($courseId)) {
                return redirect()->to('/courses')->with('success', 'Mata kuliah berhasil dihapus');
            } else {
                return redirect()->back()->with('error', 'Gagal menghapus mata kuliah');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting course: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    public function enroll($courseId)
    {
        if (session()->get('role') !== 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $studentId = session()->get('user_id');

        try {
            if ($this->courseModel->isStudentEnrolled($studentId, $courseId)) {
                return redirect()->back()->with('error', 'Anda sudah terdaftar di mata kuliah ini');
            }

            $currentCredits = $this->courseModel->getStudentTotalCredits($studentId);
            $course = $this->courseModel->find($courseId);
            
            if (($currentCredits + $course['credits']) > 24) {
                return redirect()->back()->with('error', 'Total SKS akan melebihi batas maksimum 24 SKS');
            }

            if ($this->courseModel->enroll($studentId, $courseId)) {
                return redirect()->back()->with('success', 'Berhasil mendaftar mata kuliah');
            } else {
                return redirect()->back()->with('error', 'Gagal mendaftar mata kuliah');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error enrolling course: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    public function unenroll($courseId)
    {
        if (session()->get('role') !== 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $studentId = session()->get('user_id');

        try {
            if ($this->courseModel->unenroll($studentId, $courseId)) {
                return redirect()->back()->with('success', 'Berhasil membatalkan pendaftaran mata kuliah');
            } else {
                return redirect()->back()->with('error', 'Gagal membatalkan pendaftaran mata kuliah');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error unenrolling course: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    public function enrollMultiple()
    {
        if (session()->get('role') !== 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $studentId = session()->get('user_id');
        $courseIds = $this->request->getPost('course_ids');

        if (!$courseIds || !is_array($courseIds)) {
            return redirect()->back()->with('error', 'Pilih setidaknya satu mata kuliah');
        }
        
        try {
            $currentCredits = $this->courseModel->getStudentTotalCredits($studentId);
            $selectedCredits = 0;
            
            foreach ($courseIds as $courseId) {
                $course = $this->courseModel->find($courseId);
                
                if (!$course) {
                    return redirect()->back()->with('error', 'Salah satu mata kuliah tidak ditemukan');
                }
                
                if ($this->courseModel->isStudentEnrolled($studentId, $courseId)) {
                    return redirect()->back()->with('error', 'Anda sudah terdaftar di salah satu mata kuliah yang dipilih');
                }
                
                $selectedCredits += $course['credits'];
            }

            if (($currentCredits + $selectedCredits) > 24) {
                return redirect()->back()->with('error', 'Total SKS akan melebihi batas maksimum 24 SKS');
            }

            $successCount = $this->courseModel->enrollMultiple($studentId, $courseIds);
            
            if ($successCount > 0) {
                return redirect()->back()->with('success', "Berhasil mendaftar {$successCount} mata kuliah");
            } else {
                return redirect()->back()->with('error', 'Gagal mendaftar mata kuliah atau sudah terdaftar semua');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error enrolling multiple courses: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    public function unenrollMultiple()
    {
        if (session()->get('role') !== 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $studentId = session()->get('user_id');
        $courseIds = $this->request->getPost('course_ids');

        if (!$courseIds || !is_array($courseIds)) {
            return redirect()->back()->with('error', 'Pilih setidaknya satu mata kuliah');
        }

        try {
            $successCount = $this->courseModel->unenrollMultiple($studentId, $courseIds);
            
            if ($successCount > 0) {
                return redirect()->back()->with('success', "Berhasil membatalkan {$successCount} mata kuliah");
            } else {
                return redirect()->back()->with('error', 'Gagal membatalkan pendaftaran mata kuliah');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error unenrolling multiple courses: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    public function getAll()
    {
        try {
            $courses = $this->courseModel->getAllCourses();
            return $this->response->setJSON($courses);
        } catch (\Exception $e) {
            log_message('error', 'Error getting courses: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'System error']);
        }
    }
}