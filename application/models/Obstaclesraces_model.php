<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Obstaclesraces_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function create_obstaclerace($obstacleid, $raceid) {		
        $data = array(
            'obstacleid' => $obstacleid,
            'raceid' => $raceid
        );

        return $this->db->insert('obstaclesraces', $data);
    }
    
    public function get_obstaclesraces() {
        return $this->db->get('obstaclesraces')->result();      
    }
    
    public function get_obstaclesraces_byobstacleid($obstacleid) {
        return $this->db->get_where('obstaclesraces', array('obstacleid' => $obstacleid));      
    }

    public function delete_obstaclerace($id) {
        $this -> db -> where('id', $id);
        $this -> db -> delete('obstaclesraces');
        
        return true;
    }
}
