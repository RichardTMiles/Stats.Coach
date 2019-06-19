<?php

namespace Model;


use CarbonPHP\Error\PublicAlert;
use Model\Helpers\GlobalMap;

class Search extends GlobalMap
{
    /**
     * @param $search
     * @throws PublicAlert
     */
    public function all($search)
    {
        global $result, $json;


        // The try catch should be removed eventually, but this feature still acts up without explanation
        try {
            // this is deprecated??
            $json = ['widget' => '#pjax-content'];

            ######################### Team Search
            $sql = "SELECT HEX(team_id) as team_id, team_code, team_school, HEX(team_coach) as team_coach, team_name FROM StatsCoach.carbon_teams WHERE team_name LIKE :search OR team_coach LIKE :search OR team_school LIKE :search OR team_code LIKE :search";
            $stmt = $this->database()->prepare($sql);
            $stmt->bindValue(':search', "%$search%");
            if (!$stmt->execute()) throw new PublicAlert('Search Failed');
            $result = $stmt->fetchAll();

            if (array_key_exists('team_id', $result)) $result = [$result];

            foreach ($result as $key => $array) {

                if (empty($array)) continue;

                $this->team[$array['team_id']] = $array;

                $coach = getUser($array['team_coach'], 'Basic');

                $json['Teams'][] = [
                    'id' => $array['team_id'],
                    'TeamName' => $array['team_name'],
                    'TeamCoach' => $coach['user_first_name'] . ' ' . $coach['user_last_name'],
                    'CoachId' => $coach['user_id'],
                    'TeamSchool' => $array['team_school'],
                    'TeamCode' => $array['team_code'],
                ];
            }

            $sql = "SELECT HEX(user_id) as user_id, user_first_name, user_last_name, user_type, user_sport, user_about_me FROM StatsCoach.carbon_users WHERE user_first_name LIKE :search OR user_last_name LIKE :search";
            $stmt = $this->database()->prepare($sql);
            $stmt->bindValue(':search', "%$search%");
            if (!$stmt->execute())
                throw new PublicAlert('Search Failed');
            $result = $stmt->fetchAll();

            if (array_key_exists('user_id', $result)) $result = [$result];

            ######################## User Search
            foreach ($result as $id => $value) {
                if (empty($value)) continue;
                $json['Users'][] = [
                    'Profile' => $value['user_id'],
                    'FirstName' => $value['user_first_name'],
                    'LastName' => $value['user_last_name'],
                    'Type' => $value['user_type'],
                    'Sport' => $value['user_sport'],
                    'About' => $value['user_about_me']
                ];
            }

            ######################## Course Search
            $sql = "SELECT HEX(course_id) as course_id, course_name, course_par, course_phone, course_holes, course_par, website FROM StatsCoach.carbon_golf_courses WHERE course_name LIKE :search";
            $stmt = $this->database()->prepare($sql);
            $stmt->bindValue(':search', "%$search%");
            if (!$stmt->execute()) throw new PublicAlert('Search Failed');
            $result = $stmt->fetchAll();
            if (array_key_exists('course_id', $result)) $result = [$result];
            foreach ($result as $id => $value) {
                if (empty($value)) continue;
                $this->course[$value['course_id']] = $value;
                $json['Courses'][] = [
                    'id' => $value['course_id'],
                    'Name' => $value['course_name'],
                    'Par' => $value['course_par'],
                    'Phone' => $value['course_phone'],
                    'Holes' => $value['course_holes'],
                    'Web' => $value['website'],
                ];
            }
        } catch (\Throwable $e){
            sortDump($e);
        }
    }
}
