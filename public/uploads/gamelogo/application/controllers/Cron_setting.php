<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron_setting extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('config_model');

        if (!isset($_SESSION['id'])){
            redirect(site_url()."/login");
        }
    }

    public function index() {
        $config = $this->config_model->get_all();
        $data = array('config' => $config);
		$this->load->view('../modules/loggedin_template/header',$data);
        $this->load->view('setting/cron_setting', $data);
		$this->load->view('../modules/loggedin_template/footer');
    }

    function genarateCronKey() {
        header('Access-Control-Allow-Origin: *');
        header("Content-Type:application/json");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method , Authentication");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

		$length = 16;
		$newKey = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);

		$this->db->set('value', $newKey);
        $this->db->where('type', 'cron_key');
        $this->db->update('config');
        if ($this->db->affected_rows() > 0)
        {
            echo json_encode(array('status'=>'success','message' =>"Cron Key Updated successfully",'responsecode'=>'200','data'=>$newKey));
        }
        else
        {
            echo json_encode(array('status'=>'error','message' =>"Cron Key Update error",'responsecode'=>'500','data'=>""));
        }
	}
}