<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

	
	public function index()
	{
		$this->load->view('welcome_message');

		if (!isset($_SESSION['id'])){
            redirect(site_url()."/login");
        }
	}
}
