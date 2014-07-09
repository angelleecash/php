<?php
/**
 * Created by PhpStorm.
 * User: chenliang
 * Date: 14-7-8
 * Time: 下午5:57
 */
namespace Home\Controller;
use Think\Controller;
class MissionController extends Controller {
    private function MC($table){
        return M('',$table,'DB_CONFIG1');
    }

    private function MP($table){
        return M('',$table,'DB_CONFIG2');
    }

    public function all() {
        $missions = $this->MP('mission')->field("id,startTime")->select();
        foreach($missions as &$mission) {
            $timestamp = $mission['startTime']/1000;
            $translatedDate = gmdate("m月d日", $timestamp);

            $mission['startTime'] = $translatedDate;
        }

        echo json_encode($missions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function detail() {
        $missionId = $_GET['missionId'];

        //from common database
        $knowledgeNames = $this->MC('knowledge')->field('id,title')->select();
        foreach($knowledgeNames as $knowledge) {
            $knowledgeIdToName[$knowledge['id']] = $knowledge['title'];
        }

        $students = $this->MP('student')->field('id,studentName')->where('status=1')->select();
        foreach($students as $student) {
            $studentIdToName[$student['id']] = $student['studentName'];
            $studentNames[] = $student['studentName'];
            $studentIds[] = $student['id'];
        }

        $studyTasks = $this->MP('study_task')->field('id,assignmentId')->select();
        foreach($studyTasks as $studyTask) {
            $studyTaskIdToAssignmentId[$studyTask['id']] = $studyTask['assignmentId'];
        }

        //from local database
        $missionTable = $this->MP('mission');
        $missionSubTasks = $missionTable->field('subTasks')->where('id='.$missionId)->select();

        $sequences = json_decode($missionSubTasks[0]['subTasks'], true);

        foreach($sequences as $sequence) {
            $knowledgeId = $sequence['knowledgeId'];
            $studyTaskId = $sequence['taskIds'][0];
            $missionTaskIdToKnowledgeId[$studyTaskId] = $knowledgeId;
            $missionAssignmentIds[] = $studyTaskIdToAssignmentId[$studyTaskId];
            $missionKnowledgeNames[] = $knowledgeIdToName[$knowledgeId];
            //need another database
            $questionIds = $this->MP('assignment')->field("id,questionIds")->where("id=".$studyTaskId)->select();
            if(count($questionIds) == 1) {
                $jsonToDecode = $questionIds[0]["questionIds"];
                $decodedIds = json_decode($jsonToDecode);

                $knowledgeQuestionsCount[] = count($decodedIds);
//                echo "knowledge".$knowledgeId."-------->".count($decodedIds);
            } else {
                $knowledgeQuestionsCount[] = 0;
            }

        }

        $condition['taskId'] = array('IN', $missionAssignmentIds);
        $submissions = $this->MP('submission')->field('taskId, studentId, expense, correctCount, totalCount')->where($condition)->select();

        $correctRate = array();
        $timeExpense = array();
        foreach($submissions as $submission) {
            $studentId = $submission['studentId'];
            $studentRecord = &$correctRate[$studentId];
            $studentTimeRecord = &$timeExpense[$studentId];

            if(!array_key_exists($studentId, $correctRate)){
                $studentRecord = array();
            }

            if(!array_key_exists($studentId, $timeExpense)){
                $studentTimeRecord = array();
            }

            $cr = $submission['correctCount'] / $submission['totalCount'];

            $correctRate[$studentId][$submission['taskId']] = $cr;
            $timeExpense[$studentId][$submission['taskId']] = $submission['expense'];
        }

        foreach($missionAssignmentIds as $assignmentId) {
            $studentData = [];
            $studentTimeData = [];

            foreach($studentIds as $studentId) {
                if(is_numeric($correctRate[$studentId][$assignmentId])) {
                    $studentData[] = $correctRate[$studentId][$assignmentId];
                } else {
                    $studentData[] = 0;
                }

                if(is_numeric($timeExpense[$studentId][$assignmentId])) {
                    $studentTimeData[] = $timeExpense[$studentId][$assignmentId];
                } else {
                    $studentTimeData[] = 0;
                }

            }

            $knowledgeAccurateRate[] = $studentData;
            $knowledgeTimeData[] = $studentTimeData;

//            echo $assignmentId."---------->".count($studentData)."-->".implode("+",$studentData)."<br/>";
        }

        $data = array('studentNames'=> $studentNames,
                      'knowledges'  => $missionKnowledgeNames,
                      'knowledgeCount' => count($missionKnowledgeNames),
                      'knowledgeQuestionCount' => $knowledgeQuestionsCount,
                      'accuracy'    => $knowledgeAccurateRate,
                      'knowledgeTimeData' => $knowledgeTimeData);

//        var_dump($studentNames);
//        echo "<br/>";

        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function index(){
        $this->display();
    }
}