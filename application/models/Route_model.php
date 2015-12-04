<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Route_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function update_route($id, $routename) {
        $data = array(
            'routename' => $routename                
        );

        $where = array(
            'id' => $id
        );

        return $this->db->update('routes', $data, $where);
    }

    public function get_routes($latitude = NULL, $longitude = NULL) {
        if ($latitude !== NULL && $longitude !== NULL) {
            $this->db->query('SET SQL_BIG_SELECTS=1');
            return $this->db->query('select r.* from (select r.*, '.$this->utilities->calculate_distance($latitude, $longitude).' as distance from routes r left join (select routeid, latitude, longitude from routewaypoints where sortorder=1) rw on r.id = rw.routeid) r where r.distance < 10');
        }
    }
    
    public function get_route($id) {
        return $this->db->get_where('routes', array('id' => $id))->result();      
    }

    public function create_route($routename) {		
        $data = array(
            'routename' => $routename,
            'userid' => $_SESSION['user_id'],
            'createdate' => date('Y-m-j H:i:s'),
            'lastmodifieddate' => date('Y-m-j H:i:s')
        );

        $this->db->insert('routes', $data);
        return $this->db->insert_id();
    }
}
