<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Routewaypoints_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function update_routewaypoints($id, $sortorder) {
        $data = array(
            'description' => $sortorder                
        );

        $where = array(
            'id' => $id
        );

        return $this->db->update('routewaypointss', $data, $where);
    }

    public function get_routewaypoints($routeid) {
        $this->db->order_by('sortorder');
        return $this->db->get_where('routewaypoints', array('routeid' => $routeid))->result();      
    }   
    
    public function get_routewaypoint($id) {
        return $this->db->get_where('routewaypoints', array('id' => $id))->result();      
    }

    public function create_routewaypoints($routeid, $route) {
        foreach ($route->waypoints as $waypoint) {
            if ($waypoint->pointid > 0) { $pointid = $waypoint->pointid; } else { $pointid = NULL; }
            
            $data = array(
                'routeid' => $routeid,
                'pointid' => $pointid,
                'latitude' => $waypoint->latitude,
                'longitude' => $waypoint->longitude,
                'sortorder' => $waypoint->sortorder
            );    

            $this->db->insert('routewaypoints', $data);
        }

        return TRUE;
    }
}
