<?php
namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'course_id';
    protected $allowedFields = ['course_name', 'credits'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'course_name' => 'required|min_length[3]|max_length[100]',
        'credits' => 'required|integer|greater_than[0]|less_than_equal_to[6]' 
    ];

    protected $validationMessages = [
        'course_name' => [
            'required' => 'Course name is required',
            'min_length' => 'Course name must be at least 3 characters',
            'max_length' => 'Course name cannot exceed 100 characters'
        ],
        'credits' => [
            'required' => 'Credits is required',
            'integer' => 'Credits must be a number',
            'greater_than' => 'Credits must be greater than 0',
            'less_than_equal' => 'Credits cannot exceed 6'
        ]
    ];

    public function getAllCourses()
    {
        return $this->orderBy('course_name', 'ASC')->findAll();
    }

    public function getCourseById($courseId)
    {
        return $this->find($courseId);
    }

    public function enroll($studentId, $courseId)
    {
        try {
            $data = [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'enroll_date' => date('Y-m-d')
            ];
            return $this->db->table('takes')->insert($data);
        } catch (\Exception $e) {
            log_message('error', 'Error enrolling student: ' . $e->getMessage());
            return false;
        }
    }

    public function unenroll($studentId, $courseId)
    {
        try {
            return $this->db->table('takes')
                ->where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->delete();
        } catch (\Exception $e) {
            log_message('error', 'Error unenrolling student: ' . $e->getMessage());
            return false;
        }
    }

    public function isStudentEnrolled($studentId, $courseId)
    {
        $result = $this->db->table('takes')
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->countAllResults();
        
        return $result > 0;
    }

    public function getEnrolledCourses($studentId)
    {
        return $this->db->table('takes')
            ->select('courses.*, takes.enroll_date')
            ->join('courses', 'courses.course_id = takes.course_id')
            ->where('takes.student_id', $studentId)
            ->orderBy('takes.enroll_date', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getEnrolledStudents($courseId)
    {
        return $this->db->table('takes')
            ->select('users.user_id, users.full_name, users.email, students.entry_year, takes.enroll_date')
            ->join('users', 'users.user_id = takes.student_id')
            ->join('students', 'students.student_id = takes.student_id')
            ->where('takes.course_id', $courseId)
            ->orderBy('takes.enroll_date', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getEnrolledStudentsCount($courseId)
    {
        return $this->db->table('takes')
            ->where('course_id', $courseId)
            ->countAllResults();
    }

    public function getStudentTotalCredits($studentId)
    {
        $result = $this->db->table('takes')
            ->select('SUM(courses.credits) as total_credits')
            ->join('courses', 'courses.course_id = takes.course_id')
            ->where('takes.student_id', $studentId)
            ->get()
            ->getRowArray();
        
        return $result['total_credits'] ? (int)$result['total_credits'] : 0;
    }

    public function getEnrolledStudentsByCourse($courseId)
    {
        return $this->db->table('takes')
            ->select('users.user_id, users.full_name, users.email, students.entry_year, takes.enroll_date')
            ->join('users', 'users.user_id = takes.student_id')
            ->join('students', 'students.student_id = takes.student_id')
            ->where('takes.course_id', $courseId)
            ->orderBy('users.full_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getTotalCoursesCount()
    {
        return $this->countAll();
    }

    public function getCourseWithMostStudents()
    {
        return $this->db->table('courses')
            ->select('courses.*, COUNT(takes.student_id) as student_count')
            ->join('takes', 'takes.course_id = courses.course_id', 'left')
            ->groupBy('courses.course_id')
            ->orderBy('student_count', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
    }

    public function getCoursesWithEnrollmentCount()
    {
        return $this->db->table('courses')
            ->select('courses.*, COUNT(takes.student_id) as student_count')
            ->join('takes', 'takes.course_id = courses.course_id', 'left')
            ->groupBy('courses.course_id')
            ->orderBy('courses.course_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function searchCourses($keyword)
    {
        return $this->like('course_name', $keyword)
            ->orderBy('course_name', 'ASC')
            ->findAll();
    }

    public function getCoursesByCredits($credits)
    {
        return $this->where('credits', $credits)
            ->orderBy('course_name', 'ASC')
            ->findAll();
    }

    public function courseExists($courseId)
    {
        return $this->find($courseId) !== null;
    }

    public function courseNameExists($courseName, $excludeId = null)
    {
        $query = $this->where('course_name', $courseName);
        
        if ($excludeId) {
            $query->where('course_id !=', $excludeId);
        }
        
        return $query->first() !== null;
    }

    public function bulkDelete($courseIds)
    {
        if (!is_array($courseIds) || empty($courseIds)) {
            return false;
        }

        try {
            $this->db->transStart();
            
            // Hapus data takes terlebih dahulu
            $this->db->table('takes')->whereIn('course_id', $courseIds)->delete();
            
            // Hapus courses
            $this->whereIn('course_id', $courseIds)->delete();
            
            $this->db->transComplete();
            return $this->db->transStatus();
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error bulk deleting courses: ' . $e->getMessage());
            return false;
        }
    }

    public function getEnrollmentReport()
    {
        return $this->db->query("
            SELECT 
                c.course_name,
                c.credits,
                COUNT(t.student_id) as total_students,
                MIN(t.enroll_date) as first_enrollment,
                MAX(t.enroll_date) as latest_enrollment
            FROM courses c
            LEFT JOIN takes t ON c.course_id = t.course_id
            GROUP BY c.course_id, c.course_name, c.credits
            ORDER BY total_students DESC, c.course_name ASC
        ")->getResultArray();
    }

    public function getMonthlyEnrollmentStats($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        return $this->db->query("
            SELECT 
                MONTH(t.enroll_date) as month,
                MONTHNAME(t.enroll_date) as month_name,
                COUNT(*) as enrollment_count
            FROM takes t
            WHERE YEAR(t.enroll_date) = ?
            GROUP BY MONTH(t.enroll_date), MONTHNAME(t.enroll_date)
            ORDER BY MONTH(t.enroll_date)
        ", [$year])->getResultArray();
    }

    public function deleteWithCheck($courseId)
    {
        try {
            $enrolledCount = $this->getEnrolledStudentsCount($courseId);
            
            if ($enrolledCount > 0) {
                throw new \Exception("Cannot delete course with enrolled students");
            }

            return $this->delete($courseId);
        } catch (\Exception $e) {
            log_message('error', 'Error deleting course with check: ' . $e->getMessage());
            throw $e;
        }
    }

    public function enrollMultiple($studentId, $courseIds)
    {
        if (!is_array($courseIds) || empty($courseIds)) {
            return false;
        }

        try {
            $this->db->transStart();
            $successCount = 0;

            foreach ($courseIds as $courseId) {
                if (!$this->isStudentEnrolled($studentId, $courseId)) {
                    $data = [
                        'student_id' => $studentId,
                        'course_id' => $courseId,
                        'enroll_date' => date('Y-m-d')
                    ];
                    if ($this->db->table('takes')->insert($data)) {
                        $successCount++;
                    }
                }
            }

            $this->db->transComplete();
            return $successCount > 0 ? $successCount : false;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error enrolling multiple courses: ' . $e->getMessage());
            return false;
        }
    }

    public function unenrollMultiple($studentId, $courseIds)
    {
        if (!is_array($courseIds) || empty($courseIds)) {
            return false;
        }

        try {
            $this->db->transStart();
            $successCount = 0;

            foreach ($courseIds as $courseId) {
                if ($this->isStudentEnrolled($studentId, $courseId)) {
                    if ($this->db->table('takes')
                        ->where('student_id', $studentId)
                        ->where('course_id', $courseId)
                        ->delete()) {
                        $successCount++;
                    }
                }
            }

            $this->db->transComplete();
            return $successCount > 0 ? $successCount : false;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error unenrolling multiple courses: ' . $e->getMessage());
            return false;
        }
    }
}