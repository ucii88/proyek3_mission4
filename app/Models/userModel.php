<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $allowedFields = ['username', 'email', 'password', 'role', 'full_name'];
    protected $useTimestamps = true; 
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'username' => 'required|is_unique[users.username]|min_length[3]|max_length[50]',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[6]',
        'role' => 'required|in_list[admin,student]',
        'full_name' => 'required|min_length[2]|max_length[100]'
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Username is required',
            'is_unique' => 'Username already exists',
            'min_length' => 'Username must be at least 3 characters'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'Email already registered'
        ]
    ];

    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function getStudentByUserId($userId)
    {
        return $this->db->table('students')->where('student_id', $userId)->get()->getRowArray();
    }

    public function getStudentById($userId)
    {
        return $this->db->table('users')
            ->select('users.*, students.entry_year')
            ->join('students', 'students.student_id = users.user_id')
            ->where('users.user_id', $userId)
            ->where('users.role', 'student')
            ->get()
            ->getRowArray();
    }

    public function getStudents()
    {
        return $this->db->table('users')
            ->select('users.*, students.entry_year')
            ->join('students', 'students.student_id = users.user_id')
            ->where('users.role', 'student')
            ->orderBy('users.full_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function createStudent($userData, $studentData)
    {
        $this->db->transStart();
        
        try {
 
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            if (!$this->insert($userData)) {
                throw new \Exception('Failed to create user');
            }
            
            $userId = $this->insertID();
            $studentData['student_id'] = $userId;
            
            if (!$this->db->table('students')->insert($studentData)) {
                throw new \Exception('Failed to create student record');
            }
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return $userId;
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creating student: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateStudent($userId, $userData, $studentData = null)
    {
        $this->db->transStart();
        
        try {

            if (empty($userData['password'])) {
                unset($userData['password']);
            } else {
                $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            }
            
            if (!$this->update($userId, $userData)) {
                throw new \Exception('Failed to update user');
            }
            
            if ($studentData && is_array($studentData)) {
                if (!$this->db->table('students')->where('student_id', $userId)->update($studentData)) {
                    throw new \Exception('Failed to update student record');
                }
            }
            
            $this->db->transComplete();
            return $this->db->transStatus();
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error updating student: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteStudent($userId)
    {
        $this->db->transStart();
        
        try {

            $this->db->table('takes')->where('student_id', $userId)->delete();
            
            $this->db->table('students')->where('student_id', $userId)->delete();
            
            $this->delete($userId);
            
            $this->db->transComplete();
            return $this->db->transStatus();
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error deleting student: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAllAdmins()
    {
        return $this->where('role', 'admin')->orderBy('full_name', 'ASC')->findAll();
    }
}