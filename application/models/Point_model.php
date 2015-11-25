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

    private function build_where ($filters) {
        $where = new stdClass();

        $where->where1 = ' where (p.title like '."'%".$filters->search."%'".' or p.description like '."'%".$filters->search."%'".')';
        $where->where2 = ' where p.distance <= 3';

        if ($filters->rating > 0) {
            $where->where1 .= ' and pr.avgrating >= '.$filters->rating;
        }
        
        if ($this->input->post('mysubmissions') == 'on') {

            $where->where1 .= ' and p.createdbyid = '.(string)$filters->userid;
        }

        if (sizeof($filters->keywords) > 0) {

            $where->where2 .= ' and (';
            foreach ($filters->keywords as $keywordid) {
                $where->where2 .= 'p.keywords like \'%'.$this->keyword_model->get_keyword($keywordid)[0]->keyword.'%\' OR';
            }
            $where->where2 = substr($where->where2, 0, strlen($where->where2)-2).')';
        }
        
        return $where;
    }
    
    private function calculate_distance ($latitude, $longitude) {
        return '3959*acos(cos(radians('.$latitude.'))*cos(radians(p.latitude))*cos(radians(p.longitude)-radians('.$longitude.'))+sin(radians('.$latitude.'))*sin(radians(p.latitude)))';
    }
    
    public function get_points($latitude = NULL, $longitude = NULL, $filters = NULL) {
        if ($latitude !== NULL && $longitude !== NULL) {
            $where = $this->build_where($filters);

            $userid=0;
            if (isset($_SESSION['user_id'])) {
                $userid=$_SESSION['user_id'];
                if ($_SESSION['is_admin'] && $this->input->get('debug', TRUE) == 'y') {
                    echo 'SET SQL_BIG_SELECTS=1; select * from (select p.*, userrating, avgrating, GROUP_CONCAT(k.keyword) as keywords, '.$this->calculate_distance($latitude, $longitude).' as distance from points as p left join (select pointid, avg(rating) as avgrating from pointsratings group by pointid) as pr on p.id=pr.pointid left join (select pointid, rating as userrating from pointsratings where userid='.$userid.') as pru on p.id=pru.pointid  left join (select * from pointskeywords where deleteflag=0) as pk on p.id=pk.pointid left join keywords k on pk.keywordid=k.id '.$where->where1.' group by p.id) as p '.$where->where2;
                }
            }

            $this->db->query('SET SQL_BIG_SELECTS=1');
            return $this->db->query('select * from (select p.*, userrating, avgrating, GROUP_CONCAT(k.keyword) as keywords, '.$this->calculate_distance($latitude, $longitude).' as distance from points as p left join (select pointid, avg(rating) as avgrating from pointsratings group by pointid) as pr on p.id=pr.pointid left join (select pointid, rating as userrating from pointsratings where userid='.$userid.') as pru on p.id=pru.pointid  left join (select * from pointskeywords where deleteflag=0) as pk on p.id=pk.pointid left join keywords k on pk.keywordid=k.id '.$where->where1.' group by p.id) as p '.$where->where2)->result_array();       
        }
    }
}
