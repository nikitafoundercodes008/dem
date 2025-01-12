<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_team_model extends CI_Model
{

    public $table = 'my_team_player';
    public $id = 'my_team_id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // get all
    function get_all($id)
    {   $this->db->where($this->id, $id);
        $this->db->order_by('id', $this->order);
        return $this->db->get($this->table)->result();
    }

     function getmatch($id)
    {
        $this->db->select('match_id');
        $this->db->where('id',$id);
      return $this->db->get('my_team')->row_array();
    }

    function get_match_players($id)
    {
        $this->db->select('*');
        $this->db->where('match_players.matchid',$id);
        $this->db->from('match_players');
        $this->db->join('players','players.id = match_players.playerid');
       return  $this->db->get()->result_array();
    }

    function get_match_players_status($id)
    {
        $this->db->select('id,designationid');
        $this->db->where('id',$id);
        return $this->db->get('players')->row_array();
    }
    

}

/* End of file Team_model.php */
/* Location: ./application/models/Team_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2018-10-29 11:28:00 */
/* http://harviacode.com */