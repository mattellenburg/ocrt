<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Race_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function update_race($id, $race, $date, $location, $description) {
        $data = array(
            'race' => $race,
            'date' => $date,
            'location' => $location,
            'description' => $description                
        );

        $where = array(
            'id' => $id
        );

        return $this->db->update('races', $data, $where);
    }

    public function get_races() {
        $this->db->order_by('date');
        return $this->db->get('races')->result();      
    }   
    
    public function get_race($id) {
        return $this->db->get_where('races', array('id' => $id))->result();      
    }

    public function create_race($race, $date, $location, $description) {		
        $data = array(
            'race' => $race,
            'date' => $date,
            'location' => $location,
            'description' => $description
        );

        return $this->db->insert('races', $data);
    }
}
