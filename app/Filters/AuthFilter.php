<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah user sudah login
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login to access this page');
            return redirect()->to(base_url('/login'));
        }
        

        if ($arguments) {
            $userRole = session()->get('role');
            
        // Cek role user sesuai dengan yang diizinkan
            if (!in_array($userRole, $arguments)) {
                session()->setFlashdata('error', 'Access denied. Insufficient permissions.');
                return redirect()->to(base_url('/dashboard'));
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {

    }
}