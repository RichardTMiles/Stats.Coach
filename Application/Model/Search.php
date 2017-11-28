<?php
namespace Model;


use Carbon\Error\PublicAlert;
use Model\Helpers\GlobalMap;

class Search extends GlobalMap
{
    public function __construct($search)
    {
        global $result;

        parent::__construct();
        $sql = "SELECT * FROM StatsCoach.user WHERE user_first_name LIKE :search OR user_last_name LIKE :search";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search', $search);
        if (!$stmt->execute())
            throw new PublicAlert('Search Failed');

        $result = $stmt->fetch();

        print '';
        // $sql = "SELECT * FROM StatsCoach.golf_course WHERE course_name LIKE :search";
        // $sql = "SELECT * FROM StatsCoach.teams WHERE team_name LIKE :search";


    }
}
