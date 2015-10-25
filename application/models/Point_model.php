<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Point_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
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

    public function get_points() {
        if (isset($_SESSION['user_id'])) {
            return $this->db->query('select p.*, userrating, avgrating, GROUP_CONCAT(k.keyword) as keywords from points as p left join (select pointid, avg(rating) as avgrating from pointsratings group by pointid) as pr on p.id=pr.pointid left join (select pointid, rating as userrating from pointsratings where userid='.$_SESSION['user_id'].') as pru on p.id=pru.pointid  left join (select * from pointskeywords where deleteflag=0) as pk on p.id=pk.pointid left join keywords k on pk.keywordid=k.id group by p.id LIMIT 100')->result_array();
        }
        else {
            return $this->db->query('select p.*, NULL as userrating, avgrating, GROUP_CONCAT(k.keyword) as keywords from points as p left join (select pointid, avg(rating) as avgrating from pointsratings group by pointid) as pr on p.id=pr.pointid left join (select * from pointskeywords where deleteflag=0) as pk on p.id=pk.pointid left join keywords k on pk.keywordid=k.id group by p.id LIMIT 100')->result_array();			
        }
    }
}
