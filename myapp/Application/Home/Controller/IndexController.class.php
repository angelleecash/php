<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function wrap(&$array) {
        array_walk($array, function (&$item, $key, $prefix)
        {
            $item = '\''.$item.'\'';
        });
    }

    public function index(){
        //from common database
        $knowledgeNames = M('knowledge')->field('id,title')->select();
        foreach($knowledgeNames as $knowledge) {
            $knowledgeIdToName[$knowledge['id']] = $knowledge['title'];
        }

        $students = M('student')->field('id,studentName')->where('status=1')->select();
        foreach($students as $student) {
            $studentIdToName[$student['id']] = $student['studentName'];
            $studentNames[] = $student['studentName'];
            $studentIds[] = $student['id'];
        }

        $studyTasks = M('study_task')->field('id,assignmentId')->select();
        foreach($studyTasks as $studyTask) {
            $studyTaskIdToAssignmentId[$studyTask['id']] = $studyTask['assignmentId'];
        }

        //from local database
        $missionTable = M('mission');
        $missionSubTasks = $missionTable->field('subTasks')->where('id=23')->select();

        $sequences = json_decode($missionSubTasks[0]['subTasks'], true);

        foreach($sequences as $sequence) {
            $knowledgeId = $sequence['knowledgeId'];
            $studyTaskId = $sequence['taskIds'][0];
            $missionTaskIdToKnowledgeId[$studyTaskId] = $knowledgeId;
            $missionAssignmentIds[] = $studyTaskIdToAssignmentId[$studyTaskId];
            $missionKnowledgeNames[] = $knowledgeIdToName[$knowledgeId];
            //need another database
            $questionIds = M('assignment')->field("id,questionIds")->where("id=".$studyTaskId)->select();
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
        $submissions = M('submission')->field('taskId, studentId, correctCount, totalCount')->where($condition)->select();

        $correctRate = array();
        foreach($submissions as $submission) {
            $studentId = $submission['studentId'];
            $studentRecord = &$correctRate[$studentId];

            if(!array_key_exists($studentId, $correctRate)){
                $studentRecord = array();
            }

            $cr = $submission['correctCount'] / $submission['totalCount'];

//            echo $studentId.'--->'.$submission['taskId'].'----->'.$cr."<br/>";

            $correctRate[$studentId][$submission['taskId']] = $cr;
        }

        foreach($missionAssignmentIds as $assignmentId) {
            $studentData = [];

            foreach($studentIds as $studentId) {
                if(is_numeric($correctRate[$studentId][$assignmentId])) {
                    $studentData[] = $correctRate[$studentId][$assignmentId];
                } else {
                    $studentData[] = 0;
                }

            }

            $knowledgeAccurateRate[] = $studentData;

//            echo $assignmentId."---------->".count($studentData)."-->".implode("+",$studentData)."<br/>";
        }

        $this->wrap($studentNames);
        $this->wrap($missionKnowledgeNames);


        //[[0.1,0.2,0.3],[]]
        $this->assign('knowledgeCount', count($missionAssignmentIds));
        $this->assign('studentNames', implode(',', $studentNames));
        $this->assign('knowledgeNames', implode(',', $missionKnowledgeNames));
        $this->assign('knowledgeQuestionsCount', implode(",", $knowledgeQuestionsCount));

        $accurateContent = "[";
        foreach($knowledgeAccurateRate as $eachArray) {
            $eachContent = implode(",", $eachArray);
            $accurateContent = $accurateContent ."[". $eachContent ."],";
        }

        if($accurateContent[strlen($accurateContent)-1] == ",") {
            $accurateContent = substr($accurateContent, 0, strlen($accurateContent)-1);
        }
        $accurateContent = $accurateContent . "]";

//        echo $accurateContent."<br/>";

        $this->assign('knowledgeAccurateRate', $accurateContent);

        $this->display();
    }
}