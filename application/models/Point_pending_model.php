<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Point_pending_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
	
    public function create_point($title, $description, $latitude, $longitude, $icon, $pointid, $delete) {	
        $data = array(
            'title' => $title,
            'description' => $description,
            'icon' => $icon,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'createdbyid' => $_SESSION['user_id'],
            'createdate' => date('Y-m-j H:i:s'),
            'pointid' => $pointid,
            'delete' => $delete
        );

        return $this->db->insert('points_pending', $data);	
    }

    public function update_point($id, $title, $description, $latitude, $longitude, $icon, $pointid) {	
        $data = array(
            'title' => $title,
            'description' => $description,
            'icon' => $icon,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'createdbyid' => $_SESSION['user_id'],
            'createdate' => date('Y-m-j H:i:s'),
            'pointid' => $pointid
        );
        
        $where = array(
            'id' => $id
        );

        return $this->db->update('points_pending', $data, $where);
    }

    public function delete_point($id) {
        $this->db->where('id', $id);
        $this->db->delete('points_pending');
        
        return TRUE;
    }

    public function get_point($id) {
        $query = $this->db->get_where('points_pending', array('id' => $id));      

        return $query->row();
    }

    public function get_points() {
        if (isset($_SESSION['user_id'])) {
            if ($_SESSION['is_admin']) {
                $this->db->order_by('pointid', 'createdate');
                return $this->db->get('points_pending', 0, 100)->result();						
            }
            else {
                return $this->db->get_where('points_pending', array('createdbyid' => $_SESSION['user_id']))->result();		
            }
        }
        else {
            return $this->db->get_where('points_pending', array('createdbyid' => 0))->result();		
        }
    }
}
