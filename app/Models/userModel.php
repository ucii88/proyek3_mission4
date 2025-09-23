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

    
    protected $skipValidation = false; 

   
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
            ->join('students', 'students.student_id = users.user_id', 'inner')
            ->where('users.user_id', $userId)
            ->where('users.role', 'student')
            ->get()
            ->getRowArray();
    }

    public function getStudents()
    {
        return $this->db->table('users')
            ->select('users.*, students.entry_year')
            ->join('students', 'students.student_id = users.user_id', 'inner')
            ->where('users.role', 'student')
            ->orderBy('users.full_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function createStudent($userData, $studentData)
    {
        $this->db->transStart();
        try {
            log_message('info', 'Data diterima di createStudent: ' . json_encode($userData));
            
            
            if (!isset($userData['password']) || empty($userData['password'])) {
                log_message('error', 'Password kosong atau tidak ada');
                throw new \Exception('Password is required');
            }
            
           
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            log_message('info', 'Password berhasil di-hash');
            
            
            $this->skipValidation(true);
            if (!$this->insert($userData, false)) { 
                $errors = $this->errors();
                log_message('error', 'Gagal insert user: ' . json_encode($errors));
                throw new \Exception('Failed to create user: ' . implode(', ', $errors));
            }
            
            $userId = $this->insertID();
            log_message('info', 'User berhasil dibuat dengan ID: ' . $userId);
            
        
            $studentData['student_id'] = $userId;
            if (!$this->db->table('students')->insert($studentData)) {
                $dbError = $this->db->error();
                log_message('error', 'Gagal insert student record: ' . json_encode($dbError));
                throw new \Exception('Failed to create student record');
            }
            
            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                log_message('error', 'Transaksi gagal');
                throw new \Exception('Transaction failed');
            }
            
            log_message('info', 'Mahasiswa berhasil dibuat. ID: ' . $userId);
            return $userId;
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error di createStudent: ' . $e->getMessage());
            throw $e;
        } finally {
     
            $this->skipValidation(false);
        }
    }

    public function updateStudent($userId, $userData, $studentData = null)
    {
        $this->db->transStart();
        
        try {
            
            if (isset($userData['password']) && !empty($userData['password'])) {
                $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            } else {
                
                unset($userData['password']);
            }
            
            
            $this->skipValidation(true);
            
            // Update tabel users
            if (!$this->update($userId, $userData, false)) { 
                throw new \Exception('Failed to update user: ' . implode(', ', $this->errors()));
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
        } finally {
           
            $this->skipValidation(false);
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

    // Method untuk validasi update dengan pengecualian user_id
    public function validateUpdate($data, $userId)
    {
        $rules = [
            'username' => "required|is_unique[users.username,user_id,{$userId}]|min_length[3]|max_length[50]",
            'email' => "required|valid_email|is_unique[users.email,user_id,{$userId}]",
            'full_name' => 'required|min_length[2]|max_length[100]'
        ];
        
        if (!empty($data['password'])) {
            $rules['password'] = 'min_length[6]';
        }
        
        return $this->validate($data, $rules);
    }

    // Method untuk cek apakah user adalah student
    public function isStudent($userId)
    {
        $user = $this->find($userId);
        return $user && $user['role'] === 'student';
    }

    // Method untuk cek apakah user adalah admin
    public function isAdmin($userId)
    {
        $user = $this->find($userId);
        return $user && $user['role'] === 'admin';
    }

    // Method untuk mendapatkan total students
    public function getTotalStudents()
    {
        return $this->where('role', 'student')->countAllResults();
    }
}