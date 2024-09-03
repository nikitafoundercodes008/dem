<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Match_players_model extends CI_Model
{

    public $table = 'match_players';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // get all
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    // get data by id
    function get_by_id($id)
    {
        $this->db->where('mp.id', $id);
        $this->db->select('mp.*,m.match_status,t.team_name,p.name');
        $this->db->from('match_players mp');
        $this->db->join('match m','m.match_id = mp.matchid');
        $this->db->join('team t','t.team_id = mp.teamid');
        $this->db->join('players p','p.id = mp.playerid');
        return $this->db->get($this->table)->row();
    }
    
    // get total rows
    function total_rows($id,$q = NULL) {
     //    $this->db->like('id', $q);
    	// $this->db->or_like('matchid', $q);
    	// $this->db->or_like('teamid', $q);
    	// $this->db->or_like('playerid', $q);
    	// $this->db->or_like('is_select', $q);
    	// $this->db->or_like('created_date', $q);
    	// $this->db->or_like('modified_date', $q);
    	$this->db->from($this->table);
        $this->db->where('matchid',$id);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($id,$limit, $start = 0, $q = NULL) {
        $this->db->select('mp.*,m.match_status,t.team_name,p.name,p.image');
        $this->db->from('match_players mp');
        $this->db->where('mp.matchid',$id);
        $this->db->order_by('mp.id','DESC');
        $this->db->join('match m','m.match_id = mp.matchid');
        $this->db->join('team t','t.team_id = mp.teamid');
        $this->db->join('players p','p.id = mp.playerid');
        $this->db->join('designation d','d.id = p.designationid');
        //$this->db->limit($limit, $start);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            return $query->result();
        } else {
            return $query->result();
        }
    }

    // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    // update data
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
    }

    // delete data
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }

    function getMatch()
    {
        $query = $this->db->get('match');
        return $query->result();
    }

    function getTeam()
    {
        $query = $this->db->get('team');
        return $query->result();
    }

    function getPlayer()
    {
        $query = $this->db->get('players');
        return $query->result();
    }
    
    function get_players_by_matchid($id)
    {
        $this->db->where('matchid', $id);
      return  $this->db->get($this->table)->result();
    }
    
        function get_match_player_data($id) {
        $this->db->select('mp.*,m.match_status,t.team_name,p.name,p.image');
        $this->db->from('match_players mp');
        $this->db->where('mp.matchid',$id);
        $this->db->order_by('mp.id','DESC');
        $this->db->join('match m','m.match_id = mp.matchid');
        $this->db->join('team t','t.team_id = mp.teamid');
        $this->db->join('players p','p.id = mp.playerid');
        $this->db->join('designation d','d.id = p.designationid');
        //$this->db->limit($limit, $start);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_match_player_details($id)
    {
        $this->db->select('p.name,d.title,t.team_name,mp.points as ppoints,mp.selection_percent,mp.id as m_p_id, mp.matchid as match_id');
        $this->db->where('mp.id', $id);
        $this->db->from('players p');
        $this->db->join('match_players mp','mp.playerid=p.id');
        $this->db->join('team t','t.team_id=p.teamid');
        $this->db->join('designation d','d.id=p.designationid');
        return $this->db->get('')->row();
    }

}
