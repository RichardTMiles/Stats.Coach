<?php

namespace Model;


use Carbon\Error\PublicAlert;
use Model\Helpers\GlobalMap;

class Search extends GlobalMap
{
    public function __construct($search)
    {
        global $result, $json;
        parent::__construct();

        ######################### Team Search
        $sql = "SELECT team_id, team_code, team_school, team_coach, team_name FROM StatsCoach.teams WHERE team_name LIKE :search OR team_coach LIKE :search OR team_school LIKE :search OR team_code LIKE :search";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search', "%$search%");
        if (!$stmt->execute()) throw new PublicAlert('Search Failed');
        $result = $stmt->fetchAll();

        if (array_key_exists('team_id', $result)) $result = [$result];

        foreach ($result as $key => $array) {
            $this->team[$array['team_id']] = $array;
            $json['Teams'][] = [
                'id' => $array['team_id'],
                'TeamName' => $array['team_name'],
                'TeamCoach' => $array['team_coach'],
                'TeamSchool' => $array['team_school'],
                'TeamCode' => $array['team_code'],
            ];
        }

        $sql = "SELECT * FROM StatsCoach.user WHERE user_first_name LIKE :search OR user_last_name LIKE :search";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search', $search);
        if (!$stmt->execute())
            throw new PublicAlert('Search Failed');
        $result = $stmt->fetchAll();

        if (array_key_exists('user_id', $result)) $result = [$result];

        ######################## User Search
        foreach ($result as $id => $value) {
            $this->user[$result['user_id']] = $value;
            $json['Users'][] = [
                'Profile' => $value['user_id'],
                'FirstName' => $value['first_name'],
                'LastName' => $value['last_name'],
                'Type' => $value['type'],
                'Sport' => $value['sport'],
                'About' => $value['about_me']
            ];
        }

        ######################## Course Search
        $sql = "SELECT * FROM StatsCoach.golf_course WHERE course_name LIKE :search";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search', $search);
        if (!$stmt->execute()) throw new PublicAlert('Search Failed');
        $result = $stmt->fetchAll();
        if (array_key_exists('course_id', $result)) $result = [$result];
        foreach ($result as $id => $value) {
            $json['Courses'][] = [
                'id' => $value['course_id'],
                'Name' => $value['course_name'],
                'Par' => $value['course_par'],
                'Holes' => $value['course_holes'],
                'Web' => $value['course_par'],
            ];
        }
    }
}
