<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\MessageModel;

class ChatController extends UserController
{
    public function index()
    {
        //
    }

    public function chat_users()
    {
        $session = session();

        $uid = $this->session->get("unique_id");
        if(!isset($uid)){
            $session = session();
            $session->destroy();
            return redirect()->to('login');
            die();
        }

        $fullname = $this->session->get("fullname");
        $img = $this->session->get("image");

        $outgoing_id = $uid;

        $db = \Config\Database::connect();
      
        $output = "";
        $result = "";
           
        $user = $db->table('users');
        $user->select("*");
        $user->where('unique_id !=' , $uid)->orderBy("user_id", "DESC");
        $res = $user->get()->getResult();

        
        $rescounter = count($res);
        if(count($res) == 0){
            $output .= "No users are available to chat";

        }else{
            foreach($res as $row){
                $message = $db->table('messages');
                $message->select("*");
                $message->where("(incoming_msg_id = {$uid} OR outgoing_msg_id = {$uid})");
                $message->where("(outgoing_msg_id = {$row->unique_id} OR incoming_msg_id = {$row->unique_id})");
            

                $message->orderBy("msg_id", "DESC")->limit(1);
                $row2 = $message->get()->getResult();
    
                
                (count($row2) > 0) ? $result = $row2[0]->msg : $result ="No message available";
                (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;
                if(isset($row2[0]->outgoing_msg_id)){
                    ($outgoing_id == $row2[0]->outgoing_msg_id) ? $you = "You: " : $you = "";
                }else{
                    $you = "";
                }
            // $you = "";
            // $msg = "";
                ($row->status == "Offline now") ? $offline = "offline" : $offline = "";
            //     ($outgoing_id == $row['unique_id']) ? $hid_me = "hide" : $hid_me = "";
            
            
                $output .= '<a href="/chat/'. $row->unique_id .'">
                            <div class="content">
                            <img src="/images/'. $row->img .'" alt="">
                            <div class="details">
                                <span>'. $row->fname. " " . $row->lname .'</span>
                                <p>'. $you . $msg .'</p>
                            </div>
                            </div>
                            <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                        </a>';

            }
            
        }

           

        return $output;

    }

    public function get_chat()
    {
        $session = session();

        $uid = $this->session->get("unique_id");
        if(!isset($uid)){
            $session = session();
            $session->destroy();
            return redirect()->to('login');
            die();
        }

        $fullname = $this->session->get("fullname");
        $img = $this->session->get("image");
        $incoming_id = $this->request->getPost('id');

        $outgoing_id = $uid;

        $db = \Config\Database::connect();

        $output = "";
      
       
        $message = $db->table('messages');
        $message->select("*");
        $message->join("users","users.unique_id = messages.outgoing_msg_id","left");
        $message->where("(messages.outgoing_msg_id = {$outgoing_id} AND messages.incoming_msg_id = {$incoming_id}) 
                         OR (messages.outgoing_msg_id = {$incoming_id} AND messages.incoming_msg_id = {$outgoing_id})");
        $message->orderBy("messages.msg_id");
        
        $result = $message->get()->getResult();
           
        if(count($result) > 0){
            for($i = 0; $i < count($result);$i++){
                if($result[$i]->outgoing_msg_id === $outgoing_id){
                    $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>'. $result[$i]->msg .'</p>
                                </div>
                                </div>';
                }else{
                    $output .= '<div class="chat incoming">
                                <img src="/images/'.$result[$i]->img.'" alt="">
                                <div class="details">
                                    <p>'. $result[$i]->msg .'</p>
                                </div>
                                </div>';
                }
            
            }

        }else{
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        }

        return $output;

    }

    public function insert_chat(){

        $session = session();

        $outgoing_msg_id = $this->session->get("unique_id");
        $incoming_msg_id = $this->request->getVar('id');
        $msg = $this->request->getVar('msg');


        $db = \Config\Database::connect();
        $message = $db->table('messages');

			$insert = [
				'incoming_msg_id' => $incoming_msg_id,
				'outgoing_msg_id' => $outgoing_msg_id,
				'msg' => $msg,
			];

			$message->insert($insert);

      echo "chat inserted";
    }

    public function chats($id = null){
        
        $session = session();

        $uid = $this->session->get("unique_id");

        if(!isset($uid)){
            $session = session();
            $session->destroy();
            return redirect()->to('login');
            die();
        }

        $db = \Config\Database::connect();
        $user = $db->table('users');
        $user->select("*");
        $user->where('unique_id' , $id);
        $data['user'] = $user->get()->getResult();
        
        
        return view('chat',$data);
    }

    public function search(){
        $searchTerm = $this->request->getVar('searchBar');
        $outgoing_id = $this->session->get("unique_id");
        $db = \Config\Database::connect();

        $builder = $db->table('users');
        $builder->where('unique_id !=', $outgoing_id);
        $builder->groupStart() // Open a bracket for grouping conditions
                ->like('fname', $searchTerm)
                ->orLike('lname', $searchTerm)
                ->groupEnd(); // Close the bracket

        $query = $builder->get();
        $results = $query->getResult();
        $uid = $outgoing_id;
        $output = "";
        if(count($results)>0){
            
            foreach($results as $row){
                $message = $db->table('messages');
                $message->select("*");
                $message->where("(incoming_msg_id = {$uid} OR outgoing_msg_id = {$uid})");
                $message->where("(outgoing_msg_id = {$row->unique_id} OR incoming_msg_id = {$row->unique_id})");
            
                $message->orderBy("msg_id", "DESC")->limit(1);
                $row2 = $message->get()->getResult();
    
                (count($row2) > 0) ? $result = $row2[0]->msg : $result ="No message available";
                (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;
                if(isset($row2[0]->outgoing_msg_id)){
                    ($outgoing_id == $row2[0]->outgoing_msg_id) ? $you = "You: " : $you = "";
                }else{
                    $you = "";
                }
        
                ($row->status == "Offline now") ? $offline = "offline" : $offline = "";
            
                $output .= '<a href="/chat/'. $row->unique_id .'">
                            <div class="content">
                            <img src="/images/'. $row->img .'" alt="">
                            <div class="details">
                                <span>'. $row->fname. " " . $row->lname .'</span>
                                <p>'. $you . $msg .'</p>
                            </div>
                            </div>
                            <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                        </a>';

            }
                
             
        }else{
            $output .= 'No user found related to your search term';
        }

        return $output;

    }

}
