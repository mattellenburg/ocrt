<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Point_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
        $this->load->library('utilities');
    }

    public function create_point($title, $description, $latitude, $longitude, $icon) {		
        $data = array(
            'title' => $title,
            'description' => $description,
            'icon' => $icon,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'createdbyid' => $_SESSION['user_id'],
            'createdate' => date('Y-m-j H:i:s'),
            'lastmodifiedbyid' => $_SESSION['user_id'],
            'lastmodifieddate' => date('Y-m-j H:i:s')
        );

        return $this->db->insert('points', $data);
    }
    
    public function update_point($id, $title, $description, $latitude, $longitude, $icon) {		
        $data = array(
            'title' => $title,
            'description' => $description,
            'icon' => $icon,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'createdbyid' => $_SESSION['user_id'],
            'createdate' => date('Y-m-j H:i:s'),
            'lastmodifiedbyid' => $_SESSION['user_id'],
            'lastmodifieddate' => date('Y-m-j H:i:s')
        );
        
        $where = array(
            'id' => $id
        );

        return $this->db->update('points', $data, $where);
    }

    public function delete_point($id) {
        $this->db->where('id', $id);
        $this->db->delete('points');
        
        return TRUE;
    }
    
    public function get_point($id) {
        return $this->db->get_where('points', array('id' => $id))->result();
    }

    public function get_points($latitude = NULL, $longitude = NULL, $filters = NULL) {
        if ($latitude !== NULL && $longitude !== NULL) {
            $where = $this->utilities->build_where($filters, $this->input->post('mysubmissions'));

            $userid=0;
            if (isset($_SESSION['user_id'])) {
                $userid=$_SESSION['user_id'];
                if ($_SESSION['is_admin'] && $this->input->get('debug', TRUE) == 'y') {
                    echo 'SET SQL_BIG_SELECTS=1; select * from (select p.*, userrating, avgrating, GROUP_CONCAT(k.keyword) as keywords, '.$this->utilities->calculate_distance($latitude, $longitude).' as distance from points as p left join (select pointid, avg(rating) as avgrating from pointsratings group by pointid) as pr on p.id=pr.pointid left join (select pointid, rating as userrating from pointsratings where userid='.$userid.') as pru on p.id=pru.pointid  left join (select * from pointskeywords where deleteflag=0) as pk on p.id=pk.pointid left join keywords k on pk.keywordid=k.id '.$where->where1.' group by p.id) as p '.$where->where2;
                }
            }

            $this->db->query('SET SQL_BIG_SELECTS=1');
            return $this->db->query('select * from (select p.*, userrating, avgrating, GROUP_CONCAT(k.keyword) as keywords, '.$this->utilities->calculate_distance($latitude, $longitude).' as distance from points as p left join (select pointid, avg(rating) as avgrating from pointsratings group by pointid) as pr on p.id=pr.pointid left join (select pointid, rating as userrating from pointsratings where userid='.$userid.') as pru on p.id=pru.pointid  left join (select * from pointskeywords where deleteflag=0) as pk on p.id=pk.pointid left join keywords k on pk.keywordid=k.id '.$where->where1.' group by p.id) as p '.$where->where2)->result_array();       
        }
    }
}
