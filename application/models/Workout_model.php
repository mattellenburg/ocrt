<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Workout_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function update_workout($id, $workout) {
        $data = array(
            'workout' => $workout               
        );

        $where = array(
            'id' => $id
        );

        return $this->db->update('workouts', $data, $where);
    }

    public function get_workouts($pointid) {
        return $this->db->get_where('workouts', array('pointid' => $pointid))->result();      
    }

    public function create_workout($pointid, $workout) {		
        $data = array(
            'pointid' => $pointid,
            'workout' => preg_replace("/[\n\r]/", "<br/>", $workout),
            'userid' => $_SESSION['user_id'],
            'createdate' => date('Y-m-j H:i:s'),
            'lastmodifieddate' => date('Y-m-j H:i:s')
        );

        return $this->db->insert('workouts', $data);
    }
    
    public function delete_workout($id) {
        $this->db->where('id', $id);
        $this->db->delete('workouts');
        
        return TRUE;
    }
}
