<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notification extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Notification_model');
        $this->load->model('Contest_model');
        $this->load->model('Match_model');  
        $this->load->model('User_model');  
         $this->load->model('config_model');
        $this->load->library('form_validation');

        if (!isset($_SESSION['id'])){
            redirect(site_url()."/login");
        }
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'contest/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'contest/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'contest/index.html';
            $config['first_url'] = base_url() . 'contest/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Notification_model->total_rows($q);
        $notification = $this->Notification_model->get_limit_data($config['per_page'], $start, $q);
        $this->load->library('pagination');
        $this->pagination->initialize($config);       
        $data = array(
            'notification_data' => $notification,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
		$this->load->view('../modules/loggedin_template/header',$data);
        $this->load->view('notification/notification_list');
		$this->load->view('../modules/loggedin_template/footer');
    }

    public function read($id) 
    {
        $row = $this->Notification_model->get_by_id($id);
        if ($row) {
            $data['id'] = $row->id;

           $data['contest'] = $this->Contest_model->get_by_id($row->contest_id);
           $data['match'] = $this->Match_model->get_by_id($row->match_id);
		
	    
			$this->load->view('../modules/loggedin_template/header',$data);
            $this->load->view('notification/notification_read');
				$this->load->view('../modules/loggedin_template/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('contest'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('notification/create_action'),
	    'contest_id' => set_value('contest_id'),	   
	    'match_id' => set_value('match_id'),
        'title' => set_value('title'),
        'msg' => set_value('msg'),
	);

        $data['matchs'] = $this->Notification_model->match_list();
		$this->load->view('../modules/loggedin_template/header',$data);
        $this->load->view('notification/notification_form', $data);
			$this->load->view('../modules/loggedin_template/footer',$data);
    }
    
    public function create_action()  
    {  
        $this->set_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            
             $type =  $this->input->post('user_type',TRUE);

           if($type == "1")
           {
                $resp =   $this->User_model->get_all();
                // echo $this->db->last_query();die();
                foreach ($resp as $key) 
                {
                    $data[] = $key->user_id;
                }

                $diff = $data;
                // echo "<pre>";print_r($diff);die();
                $result = implode(",",$data);
           } 
           elseif($type == "2")
           {
                $resp =   $this->User_model->get_all();
                foreach ($resp as $key) 
                {
                    $data[] = $key->user_id;
                }
                $not_participate =  $this->User_model->not_participate_15_days();
                foreach ($not_participate as  $value) {
                    $not_15_days[] = $value->user_id;
                }

                $diff = array_diff($data,$not_15_days);
                $result = implode(",",$diff);

           } 
           elseif($type == "3")
           {
                $resp =   $this->User_model->get_all();
                foreach ($resp as $key) 
                {
                    $data[] = $key->user_id;
                }
                $not_participate =  $this->User_model->not_participate_30_days();
                foreach ($not_participate as  $value) {
                    $not_30_days[] = $value->user_id;
                }
                $diff = array_diff($data,$not_30_days);
                $result = implode(",",$diff);

           } 
            foreach ($diff as $user) 
            {     

                $device = $this->db->get_where('registration',array('user_id'=>$user))->row();    
                
                $title = $this->input->post('title',TRUE);
                $token = $device->mobiletoken;
               
                $message = $this->input->post('msg',TRUE); 
                if($token !="")
                { 
                    $suc = $this->send_data($message,$title,$token); 
                }  
                
            }
            
            $data = array(
    		'contest_id' => $this->input->post('contest_id',TRUE),
    		'match_id' => $this->input->post('match_id',TRUE),
            'type' => $this->input->post('user_type',TRUE),
            'title' => $this->input->post('title',TRUE),
            'message' => $this->input->post('msg',TRUE),
            'user_ids' => $result,	
            'created_date'=> date("Y-m-d H:i:s"),	
    	    ); 
                      
            $this->Notification_model->insert($data);
            $this->session->set_flashdata('message', 'Notification send Successfully');
            $this->index();
            // redirect(site_url('notification'));
            
        }
    }
    
    public function update($id) 
    {
        $row = $this->Notification_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('notification/update_action'),
		'contest_id' => set_value('contest_id', $row->contest_id),
		'match_id' => set_value('match_id', $row->match_id),
		'id' => set_value('contest_tag', $row->id),
        'title' => set_value('title', $row->title),
        'msg' => set_value('msg', $row->message),
        'type' => set_value('type', $row->type),
	    );

            $data['contests'] = $this->Contest_model->get_contest_by_match($row->match_id);
            $data['matchs'] = $this->Notification_model->match_list();
			$this->load->view('../modules/loggedin_template/header',$data);
            $this->load->view('notification/notification_form', $data);
				$this->load->view('../modules/loggedin_template/footer',$data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('notification'));
        }
    }
    
    public function update_action() 
    {
        $this->set_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id', TRUE));
        } else {
           $type =  $this->input->post('user_type',TRUE);

           if($type == "1")
           {
                $resp =   $this->User_model->get_all();
                foreach ($resp as $key) 
                {
                    $data[] = $key->user_id;
                }
                $result = implode(",",$data);
           } 
           elseif($type == "2")
           {
                $resp =   $this->User_model->get_all();
                foreach ($resp as $key) 
                {
                    $data[] = $key->user_id;
                }
                $not_participate =  $this->User_model->not_participate_15_days();
                foreach ($not_participate as  $value) {
                    $not_15_days[] = $value->user_id;
                }

                $diff = array_diff($data,$not_15_days);
                $result = implode(",",$diff);

           } 
           elseif($type == "3")
           {
                $resp =   $this->User_model->get_all();
                foreach ($resp as $key) 
                {
                    $data[] = $key->user_id;
                }
                $not_participate =  $this->User_model->not_participate_30_days();
                foreach ($not_participate as  $value) {
                    $not_30_days[] = $value->user_id;
                }
                $diff = array_diff($data,$not_30_days);
                $result = implode(",",$diff);

           } 

            $data = array(
            'contest_id' => $this->input->post('contest_id',TRUE),
            'match_id' => $this->input->post('match_id',TRUE),
            'type' => $this->input->post('user_type',TRUE),
            'title' => $this->input->post('title',TRUE),
            'message' => $this->input->post('msg',TRUE),
            'user_ids' => $result,   
            );    

            $this->Notification_model->update($this->input->post('id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('notification'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Notification_model->get_by_id($id);

        if ($row) {
            $this->Notification_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('notification'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('notification'));
        }
    }


    public function  set_rules()
    {
        $this->form_validation->set_rules('user_type', 'User Type', 'trim|required');
        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('msg', 'Message', 'trim|required');
        $this->form_validation->set_rules('contest_id', 'Contest', 'trim|required');
        $this->form_validation->set_rules('match_id', 'Match', 'trim|required');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

    public function get_contest_by_matchid()
    {
        $id = $this->input->post('id');
        $data = $this->Contest_model->get_contest_by_matchid($id);
        echo $data;
        
    }


   

    public function send_data ($message,$title,$token)
   { // print_r($token);die();

            define('server_key', $this->config_model->get_by_type("firebase_server_key")->value);

            $token = [$token];
            //$token = ['cZa87Vam7Eo:APA91bE5lkJh3tHkpi97aXwOYqPilKFI_ZzqXrKFnoDt6NSicRBz2im6b5Ob6ZMy7qbT7b6Nfpr-ObKSUN8ZpiBRb2e41yfcQ1mgiowZfP-Km6q_T5KUYkIRKAS2VrPWNv6mLhVVKqE-'];

            $header = [ 'Authorization: key='.server_key,
                        'Content-Type: application/json'
                      ];


                 $msg = [
                      'title' =>$title,
                      'message' => $message
                 ];    

                 $payload = [
                          'registration_ids' => $token,
                          'data' => $msg
                 ]; 

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
              CURLOPT_RETURNTRANSFER => true,
              
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode($payload),
              CURLOPT_HTTPHEADER => $header
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              echo "cURL Error #:" . $err; //die();
            } else {
             // echo $response; die('success');

              $res =  json_decode($response);// die('success');
                
              $result = $res->success;
              if($result == 1)
              {
                return true;
              }
              else
            {
                    return false;

                }
            }
    }
  

}


