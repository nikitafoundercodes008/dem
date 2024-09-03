<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cancel extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Match_model');

        if (!isset($_SESSION['id'])){
            redirect(site_url()."/login");
        }
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'match/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'match/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'match/index.html';
            $config['first_url'] = base_url() . 'match/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Match_model->total_rows($q);
        $match = $this->Match_model->get_all_cancell_match();

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'match_data' => $match,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
		$this->load->view('../modules/loggedin_template/header',$data);
        $this->load->view('cancel/match_list', $data);
		$this->load->view('../modules/loggedin_template/footer',$data);
    }

   

}
?>    