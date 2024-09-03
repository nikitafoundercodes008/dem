<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Setting extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Website_model');
        $this->load->model('config_model');

        if (!isset($_SESSION['id'])){
            redirect(site_url()."/login");
        }
    }

    public function index()
    {
        $version = $this->Website_model->version_data();
        $config = $this->config_model->get_all();

        $data = array('version' => $version, 'config' => $config);
		$this->load->view('../modules/loggedin_template/header',$data);
        $this->load->view('setting/list');
		$this->load->view('../modules/loggedin_template/footer');
    }

    public function on_off()
    {
        $id = $this->input->post('id');
        $this->db->select('maintenance_status');
        $this->db->where('id','1');
        $record = $this->db->get('version')->row_array();

        if($record['maintenance_status'] == "1")
        {
            $status = "0";
        }   
        if($record['maintenance_status'] == "0")
        {
            $status = "1";
        }   
        $data = array('maintenance_status' =>$status);
        
        $this->db->where('id','1');
        $this->db->update('version',$data);
        
        if($status ==1)
        {
            echo json_encode('<button style="border-radius: 6px;" type="button" class="btn btn-danger">Under Maintenance</button>');
        }    
        else if($status ==0){
            echo json_encode('<button style="border-radius: 6px;" type="button" class="btn btn-success">Active</button>');
        } 
            
        
    }

    public function updateConfig()
    {
        $type = $this->input->post('type');
        $value = $this->input->post('value');
        $table = $this->input->post('table');

        if($table == "config") {
            $this->db->set('value', $value);
            $this->db->where('type',$type);
            echo $this->db->update('config');
        } else if($table == "version") {
            $this->db->set($type, $value);
            $this->db->where('id',1);
            echo $this->db->update('version');
        }
    }
}
