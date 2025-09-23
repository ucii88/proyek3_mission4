<?php
namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'course_id';
    protected $allowedFields = ['course_name', 'credits'];
    protected $returnType = 'array'; 
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

 
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
            'less_than_equal_to' => 'Credits cannot exceed 6'
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
                'enroll_date' => date('Y-m-d H:i:s') 
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
            ->select('courses.course_id, courses.course_name, courses.credits, takes.enroll_date')
            ->join('courses', 'courses.course_id = takes.course_id')
            ->where('takes.student_id', $studentId)
            ->orderBy('takes.enroll_date', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getEnrolledStudents($courseId)
    {
        return $this->db->table('takes')
            ->select('users.user_id, users.full_name, users.email, users.entry_year, takes.enroll_date')
            ->join('users', 'users.user_id = takes.student_id')
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
                        'enroll_date' => date('Y-m-d H:i:s')
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