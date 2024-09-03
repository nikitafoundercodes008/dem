<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Website extends MY_Controller
{
    function __construct()
    { 
        parent::__construct();
        $this->load->model('Website_model');
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
            $config['base_url'] = base_url() . 'website/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'website/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'website/index.html';
            $config['first_url'] = base_url() . 'website/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Website_model->total_rows($q);
        $content = $this->Website_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'content_data' => $content,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
		$this->load->view('../modules/loggedin_template/header',$data);
        $this->load->view('website/list', $data);
		$this->load->view('../modules/loggedin_template/footer',$data);
    }

    
    public function update($id) 
    {
        $row = $this->Website_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('Website/update_action'),
		'id' => set_value('id', $row->id),
		'name' => set_value('name', $row->name),
		'content' => set_value('content', $row->content)
	    );
		$this->load->view('../modules/loggedin_template/header',$data);
            $this->load->view('website/form', $data);
			$this->load->view('../modules/loggedin_template/footer',$data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('Website'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id', TRUE));
        } else {
            $data = array(
		'content' => $this->input->post('editor1',TRUE)
		
	    );

            $this->Website_model->update($this->input->post('id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('Website'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Website_model->get_by_id($id);

        if ($row) {
            $this->Website_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('Website'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('Website'));
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('editor1', 'Content', 'trim|required');


	$this->form_validation->set_rules('id', 'id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}