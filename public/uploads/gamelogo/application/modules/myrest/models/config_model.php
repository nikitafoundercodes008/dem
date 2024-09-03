<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class config_model extends CI_Model
{

    public $table = 'config';

    function __construct()
    {
        parent::__construct();
    }

    // get all
    function get_all()
    {
        return $this->db->get($this->table)->result();
    }

    // get data by type
    function get_by_type($type)
    {
        $this->db->where('type', $type);
        return $this->db->get($this->table)->row();
    }
}