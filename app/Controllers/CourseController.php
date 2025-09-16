<?php
namespace App\Controllers;

use App\Models\CourseModel;
use CodeIgniter\Controller;

class CourseController extends Controller
{
    protected $courseModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
    }

   public function index()
{
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/login');
    }

    $data['courses'] = $this->courseModel->getAllCourses();
    $data['role'] = session()->get('role');  

    if (session()->get('role') === 'student') {
        $data['enrolled'] = $this->courseModel->getEnrolledCourses(session()->get('user_id'));
    }

    return view('courses', $data);
}

    // CREATE - Tambah Mata Kuliah (Admin only)
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
            return redirect()->back()->with('error', 'Invalid input data')->withInput();
        }

        try {
            $data = [
                'course_name' => $this->request->getPost('course_name'),
                'credits' => $this->request->getPost('credits')
            ];
            
            if ($this->courseModel->insert($data)) {
                session()->setFlashdata('success', 'Mata kuliah berhasil ditambahkan');
            } else {
                session()->setFlashdata('error', 'Gagal menambahkan mata kuliah');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error creating course: ' . $e->getMessage());
            session()->setFlashdata('error', 'System error occurred');
        }

        return redirect()->to('/courses');
    }

    // UPDATE - Edit Mata Kuliah (Admin only)
    public function update()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $courseId = $this->request->getPost('course_id');
        
        if (!$courseId) {
            return redirect()->back()->with('error', 'Invalid course ID');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'course_name' => 'required|min_length[3]|max_length[100]',
            'credits' => 'required|integer|greater_than[0]|less_than_equal_to[6]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', 'Invalid input data')->withInput();
        }

        try {
            $data = [
                'course_name' => $this->request->getPost('course_name'),
                'credits' => $this->request->getPost('credits')
            ];
            
            if ($this->courseModel->update($courseId, $data)) {
                session()->setFlashdata('success', 'Mata kuliah berhasil diperbarui');
            } else {
                session()->setFlashdata('error', 'Gagal memperbarui mata kuliah');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating course: ' . $e->getMessage());
            session()->setFlashdata('error', 'System error occurred');
        }

        return redirect()->to('/courses');
    }

    // DELETE - Hapus Mata Kuliah (Admin only)
    public function delete($courseId)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        if (!$courseId || !is_numeric($courseId)) {
            return redirect()->to('/courses')->with('error', 'Invalid course ID');
        }

        try {
   
            $course = $this->courseModel->find($courseId);
            if (!$course) {
                return redirect()->to('/courses')->with('error', 'Mata kuliah tidak ditemukan');
            }

            $enrolledCount = $this->courseModel->getEnrolledStudentsCount($courseId);
            if ($enrolledCount > 0) {
                return redirect()->to('/courses')->with('error', 
                    'Tidak dapat menghapus mata kuliah yang masih memiliki mahasiswa terdaftar');
            }

            if ($this->courseModel->delete($courseId)) {
                session()->setFlashdata('success', 'Mata kuliah berhasil dihapus');
            } else {
                session()->setFlashdata('error', 'Gagal menghapus mata kuliah');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting course: ' . $e->getMessage());
            session()->setFlashdata('error', 'System error occurred');
        }

        return redirect()->to('/courses');
    }

    // ENROLL - Mahasiswa enroll Mata Kuliah
    public function enroll($courseId)
    {
        if (session()->get('role') !== 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        if (!$courseId || !is_numeric($courseId)) {
            return redirect()->to('/courses')->with('error', 'Invalid course ID');
        }

        try {
            $studentId = session()->get('user_id');


            $course = $this->courseModel->find($courseId);
            if (!$course) {
                return redirect()->to('/courses')->with('error', 'Mata kuliah tidak ditemukan');
            }

            if ($this->courseModel->isStudentEnrolled($studentId, $courseId)) {
                return redirect()->to('/courses')->with('error', 'Anda sudah terdaftar di mata kuliah ini');
            }

            $currentCredits = $this->courseModel->getStudentTotalCredits($studentId);
            if (($currentCredits + $course['credits']) > 24) {
                return redirect()->to('/courses')->with('error', 
                    'Tidak dapat mendaftar. Total SKS akan melebihi batas maksimum (24 SKS)');
            }

            // Enroll mahasiswa 
            if ($this->courseModel->enroll($studentId, $courseId)) {
                session()->setFlashdata('success', 'Berhasil mendaftar mata kuliah: ' . $course['course_name']);
            } else {
                session()->setFlashdata('error', 'Gagal mendaftar mata kuliah');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error enrolling student: ' . $e->getMessage());
            session()->setFlashdata('error', 'System error occurred');
        }

        return redirect()->to('/courses');
    }

    // UNENROLL - Mahasiswa  batal enroll
    public function unenroll($courseId)
    {
        if (session()->get('role') !== 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        if (!$courseId || !is_numeric($courseId)) {
            return redirect()->to('/courses')->with('error', 'Invalid course ID');
        }

        try {
            $studentId = session()->get('user_id');

            $course = $this->courseModel->find($courseId);
            if (!$course) {
                return redirect()->to('/courses')->with('error', 'Mata kuliah tidak ditemukan');
            }

            if (!$this->courseModel->isStudentEnrolled($studentId, $courseId)) {
                return redirect()->to('/courses')->with('error', 'Anda tidak terdaftar di mata kuliah ini');
            }


            if ($this->courseModel->unenroll($studentId, $courseId)) {
                session()->setFlashdata('success', 'Berhasil membatalkan pendaftaran mata kuliah: ' . $course['course_name']);
            } else {
                session()->setFlashdata('error', 'Gagal membatalkan pendaftaran mata kuliah');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error unenrolling student: ' . $e->getMessage());
            session()->setFlashdata('error', 'System error occurred');
        }

        return redirect()->to('/courses');
    }

    // GET COURSE DETAIL - Tampilkan detail mata kuliah
    public function detail($courseId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        if (!$courseId || !is_numeric($courseId)) {
            return redirect()->to('/courses')->with('error', 'Invalid course ID');
        }

        try {
            $course = $this->courseModel->find($courseId);
            if (!$course) {
                return redirect()->to('/courses')->with('error', 'Mata kuliah tidak ditemukan');
            }

            $data['course'] = $course;
            $data['role'] = session()->get('role');
            
            if (session()->get('role') === 'admin') {
                $data['enrolled_students'] = $this->courseModel->getEnrolledStudentsByCourse($courseId);
            }
 
            if (session()->get('role') === 'student') {
                $data['is_enrolled'] = $this->courseModel->isStudentEnrolled(session()->get('user_id'), $courseId);
            }

            return view('course_detail', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error getting course detail: ' . $e->getMessage());
            return redirect()->to('/courses')->with('error', 'System error occurred');
        }
    }

    // SEARCH COURSES - Pencarian mata kuliah
    public function search()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $keyword = $this->request->getGet('keyword');
        
        if (empty($keyword)) {
            return redirect()->to('/courses');
        }

        try {
            $data['courses'] = $this->courseModel->searchCourses($keyword);
            $data['role'] = session()->get('role');
            $data['keyword'] = $keyword;
            
            // Jika mahasiwa, ambil data enrolled mata kuliah
            if (session()->get('role') === 'student') {
                $data['enrolled'] = $this->courseModel->getEnrolledCourses(session()->get('user_id'));
            }
            
            return view('courses', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error searching courses: ' . $e->getMessage());
            return redirect()->to('/courses')->with('error', 'System error occurred');
        }
    }
}