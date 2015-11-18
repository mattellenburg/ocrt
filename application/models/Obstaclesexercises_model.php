<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Obstaclesexercises_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function create_obstacleexercise($obstacleid, $exerciseid) {		
        $data = array(
            'obstacleid' => $obstacleid,
            'exerciseid' => $exerciseid
        );

        return $this->db->insert('obstaclesexercises', $data);
    }
    
    public function get_obstaclesexercises() {
        return $this->db->get('obstaclesexercises')->result();      
    }
    
    public function get_obstaclesexercises_byobstacleid($obstacleid) {
        return $this->db->get_where('obstaclesexercises', array('obstacleid' => $obstacleid))->result();      
    }
    
    public function delete_obstacleexercise($id) {
        $this -> db -> where('id', $id);
        $this -> db -> delete('obstaclesexercises');
        
        return true;
    }
}
