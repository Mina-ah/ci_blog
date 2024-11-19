<?php

namespace App\Controllers\Admins;

use App\Controllers\BaseController;
use App\Models\Admin\Admin;
use App\Models\Category\Category;
use App\Models\Post\Post;
use CodeIgniter\HTTP\ResponseInterface;

class AdminsController extends BaseController
{
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function viewlogin()
    {
        return view('admins/login');
    }
    public function checklogin()
    {
        $session = session();
        $adminModel = new Admin();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $data = $adminModel->where('email', $email)->first();

        if ($data) {
            $pass = $data['password'];
            $authenticatePassword = password_verify($password, $pass);
            if ($authenticatePassword) {
                $ses_data = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'isLoggedIn' => TRUE
                ];
                $session->set($ses_data);
                return redirect()->to(base_url('admins/index'));
            } else {
                $session->setFlashdata('msg', 'Password is incorrect.');
                return redirect()->to(base_url('admins/login'));
            }
        } else {
            $session->setFlashdata('msg', 'Email does not exist.');
            return redirect()->to('admins/login');
        }
    }

    public function adminindex()
    {
        $session = session();

        $numPosts = $this->db->table('posts')->countAllResults();
        $numCategory = $this->db->table('categories')->countAllResults();
        $numAdmin = $this->db->table('admins')->countAllResults();

        return view('admins/index', compact('session', 'numPosts', 'numCategory', 'numAdmin'));
    }
    public function adminlogout()
    {
        $session = session();
        $ses_data = [
            'id' => "",
            'name' => "",
            'email' => "",
            'isLoggedIn' => FALSE
        ];
        $session->set($ses_data);
        return redirect()->to(base_url('admins/login'));
    }

    public function alladmin()
    {
        $session = session();
        $admins = new Admin();
        $alladmins = $admins->select('*')->orderBy('id', 'ASC')->get()->getResult();
        return view('admins/alladmins', compact('session', 'alladmins'));
    }

    public function createadmin()
    {
        $session = session();
        return view('admins/createadmins', compact('session'));
    }

    public function storeadmin()
    {

        $admins = new Admin();

        $data = [
            "email" => $this->request->getPost('email'),
            "name" => $this->request->getPost('name'),
            "password" => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];
        $admins->save($data);

        if ($admins) {
            return redirect()->to(base_url('admins/alladmins'))->with('create', 'Admin Added Sucessfully');
        }
    }

    public function allcategory()
    {
        $session = session();
        $categories = new Category();
        $allcategories = $categories->select('*')->orderBy('id', 'ASC')->get()->getResult();
        return view('category/allcategories', compact('session', 'allcategories'));
    }
    public function createcategory()
    {
        $session = session();
        return view('category/createcategories', compact('session'));
    }
    public function storecategory()
    {

        $categories = new Category();

        $data = [
            "name" => $this->request->getPost('name')
        ];
        $categories->save($data);

        if ($categories) {
            return redirect()->to(base_url('category/allcategories'))->with('create', 'Category Added Sucessfully');
        }
    }

    public function editcategory($id){

        $session = session();
        $categories = new Category();
        $category=$categories->find($id);
        return view('category/editcategories',compact('session','category'));

    }
    public function updatecategory($id){

        $session = session();
        $categories = new Category();

        $data = [
            "name" => $this->request->getPost('name')
        ];
        $categories->update($id,$data);

        if ($categories) {
            return redirect()->to(base_url('category/allcategories'))->with('update', 'Category Updated Sucessfully');
        }
    }

    public function deletecategory($id){

        $categories = new Category();
        $categories->delete($id);
        if($categories){

            return redirect()->to(base_url('category/allcategories'))->with('delete', 'Category Deleted Sucessfully');
        }
        
    }

    public function allposts(){
        $session = session();
        $posts = new Post();
        $allposts = $posts->select('*')->orderBy('id', 'ASC')->get()->getResult();
        return view('category/allposts', compact('session', 'allposts'));
    }

    public function deletepost($id){
        $posts = new Post();

        $post = $posts->find($id);
        unlink('public/assets/images/'.$post['image'].'');

        $posts->delete($id);
        if($posts){

            return redirect()->to(base_url('category/allposts'))->with('delete', 'Post Deleted Sucessfully');
        }

    }
}
