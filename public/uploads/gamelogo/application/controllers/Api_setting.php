<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api_setting extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('config_model');
        $this->load->helper('date');

        if (!isset($_SESSION['id'])){
            redirect(site_url()."/login");
        }
    }

    public function index() {

        $data = array('api_url' =>  base_url());
		$this->load->view('../modules/loggedin_template/header',$data);
        $this->load->view('setting/api_setting', $data);
		$this->load->view('../modules/loggedin_template/footer');
    }

    function getAdmin($id) {
        $this->db->where('id', $id);
        return $this->db->get('admin')->row()->EmailAddress;
    }

    public function getApiKeys() {
        $query = $this->db->get('keys');
        $recordsTotal = 0;
        foreach ($query->result() as $row)
        {
            $data[] = array($row->id, $row->key,$this->getAdmin($row->user_id), $row->ip_addresses, date('m-d-Y H:i:s a', $row->date_created), $row->key);
            $recordsTotal++;
        }
        
        $json_data = array(
            "draw"            => intval(1),   
            "recordsTotal"    => intval($recordsTotal),  
            "recordsFiltered" => intval($recordsTotal),
            "data"            => $data
            );
            
            echo json_encode($json_data);
    }

    public function deleteApiKey() {
        $key = $this->input->post('key');

        $this->db->where('key', $key);
        echo $this->db->delete('keys');
    }

    public function genarateApiKey() {
        $length = 16;
		$newKey = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);

		$data = array(
            'user_id' => $this->session->userdata('id'),
            'key' => $newKey,
            'level' => '1',
            'ignore_limits' => '1',
            'is_private_key' => '0',
            'ip_addresses' => $this->input->ip_address(),
            'date_created' => now()
        );
        
        echo $this->db->insert('keys', $data);
    }
}