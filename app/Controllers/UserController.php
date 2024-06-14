<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use CodeIgniter\Files\File;

class UserController extends BaseController
{

    protected $session;


    function __construct()
    {
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    public function index()
    {
        //
        return view('index');
    }
    public function signup()
    {
        //
        return view('signup');
    }

    public function register(){
        helper(['form']);
        $session = session();

        $fname = $this->request->getVar('fname');
        $lname = $this->request->getVar('lname');
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $db = \Config\Database::connect();

        if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)){
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){

                $userEmail = $db->table('users');
                $userEmail->select("*");
                $userEmail->where('email' , $email);
                $result = $userEmail->get()->getResult();

                if(count($result) > 0){
                    echo "$email - This email already exist!";
                }else{
                    if(isset($_FILES['image'])){
                        $img_name = $_FILES['image']['name'];
                        $img_type = $_FILES['image']['type'];
                        $tmp_name = $_FILES['image']['tmp_name'];
                        
                        $img_explode = explode('.',$img_name);
                        $img_ext = end($img_explode);
        
                        $extensions = ["jpeg", "png", "jpg"];
                        if(in_array($img_ext, $extensions) === true){
                            $types = ["image/jpeg", "image/jpg", "image/png"];
                            if(in_array($img_type, $types) === true){
                                $time = time();
                                $new_img_name = $time.$img_name;
                                if(move_uploaded_file($tmp_name,"images/".$new_img_name)){
                                    $ran_id = rand(time(), 100000000);
                                    $status = "Active now";
                                    $encrypt_pass = md5($password);
                                  

                                    $userData = $db->table('users');

                                    $insert = [
                                        'unique_id' => $ran_id,
                                        'fname' => $fname,
                                        'lname' => $lname,
                                        'email' => $email,
                                        'password'=> $encrypt_pass,
                                        'img' => $new_img_name,
                                        'status' => $status
                                    ];

                                    // $userData->insert($insert);

                                    if($userData->insert($insert)){

                                        $model = new UserModel();
                                        $data = $model->where('email', $email)->first();

                                        if(count($data) > 0){
                                            $ses_data = [
                                                'id' => $data['user_id'],
                                                'unique_id' => $data['unique_id'],
                                                'fullname' => $data['fname']." ".$data['lname'],
                                                'img' => $data['img'],
                                                'status' => $status,
                                            ];
                                            $session->set($ses_data);
                            
                                            return redirect()->to('/users');
                                            // echo "success";
                                        }else{
                                            echo "This email address not Exist!";
                                        }
                                    }else{
                                        echo "Something went wrong. Please try again!";
                                    }
                                }
                            }else{
                                echo "Please upload an image file - jpeg, png, jpg";
                            }
                        }else{
                            echo "Please upload an image file - jpeg, png, jpg";
                        }
                    }
                }
            }else{
                echo "$email is not a valid email!";
            }
        }else{
            echo "All input fields are required!";
        }
    }

    public function users()
    {
        $session = session();
        $db = \Config\Database::connect();
       
        //session data from auth
        $data['username'] = $this->session->get("fullname");
        $data['status'] = $this->session->get("status");
        $data['image'] = $this->session->get("img");
      
        if(!$data['username']){
            return redirect()->to('/login');
        }

        return view('/users', $data);
       
    }

    public function login()
    {
        //
        return view('login');
    }

    public function auth()
    {
        helper(['form']);
        $session = session();
        $model = new UserModel();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        $data = $model->where('email', $email)->first();
        if($data){
            $pass = $data['password'];
            $user_pass = md5($password);
            // $verify_pass = password_verify($password, $pass);
            if($user_pass===$pass){
                $db = \Config\Database::connect();
              
                $status = "Active now";
                $user = $db->table('users');

                $dataStatus = [
                    'status' => $status,
                ];
                $user->where('unique_id',$data['unique_id']);
                $user->update($dataStatus);

                $ses_data = [
                    'id' => $data['user_id'],
                    'unique_id' => $data['unique_id'],
                    'fullname' => $data['fname']." ".$data['lname'],
                    'img' => $data['img'],
                    'status' => $status,
                ];
                $session->set($ses_data);

                return redirect()->to('/users');
               
            }else{
                $session->setFlashdata('msg', 'Email or Password is incorrect');
              
                return view('login');
            }
        }else{
            $session->setFlashdata('msg', 'Something went wrong');
        
            return view('login');
        }
    }

    public function logout()
    {
        
        $session = session(); 

        $uid = $this->session->get("unique_id");
        $db = \Config\Database::connect();
        $user = $db->table('users');

        $data = [
            'status' => "Offline now",
        ];
        $user->where('unique_id',$uid);
        $user->update($data);

        $session->destroy();
        return redirect()->to('login');
        die();
        // return view('login');
    }
}
