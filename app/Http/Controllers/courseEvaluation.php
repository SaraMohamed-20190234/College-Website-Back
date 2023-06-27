<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class courseEvaluation extends Controller
{
    

//     Public function insertCourseEvaluation(Request $request)
//    {
//         DB::table('evaluation')->insert ([
//             'contentRate' => $request-> contentRate,
//             'isRepeated'=> $request-> isRepeated,
//             'isClear'=> $request-> isClear,
//             'relevantToObjectives'=> $request-> relevantToObjectives,
//             'preparetionForFutureCourses'=> $request-> preparetionForFutureCourses,
//             'courseID'=> $request->courseID,

//             'engagedStudents' => $request-> engagedStudents,
//             'conveiedMaterial'=> $request-> conveiedMaterial,
//             'isClearAgenda'=> $request-> isClearAgenda,
//             'teacherEffectiveness'=> $request-> teacherEffectiveness,
//             'communicationSkills'=> $request-> communicationSkills,
//             'professorId'=> $request-> professorId,
       
//             'TAengagedStudents' => $request-> engagedStudents,
//             'TAconveiedMaterial'=> $request-> conveiedMaterial,
//             'TAisClearAgenda'=> $request-> isClearAgenda,
//             'TAteacherEffectiveness'=> $request-> teacherEffectiveness,
//             'TAcommunicationSkills'=> $request-> communicationSkills,
//             'TAId'=> $request-> TAId,
//            // 'TAId'=>$TAID,
//    ]);
       
//        return response('Data has been inserted successfully');
//    }

// public function insertCourseEvaluation(Request $request)
// {
//     $courseEvaluation = $request->input('course evaluation', []);
//     $professorEvaluation = $request->input('professor evaluation', []);
//     $taEvaluation = $request->input('ta evaluation', []);

//     $courseId = $request->input('course id');
//     // $professorId = $request->input('professor id');
//     // $taId = $request->input('ta id');

//     // Insert evaluation data
//     DB::table('evaluation')->insert([
//         'courseID' => $courseId,
//         // 'professorId1' => $professorId,
//         // 'taId' => $taId,
//         'isClear' => $courseEvaluation[0]['value'],
//         'isRepeated' => $courseEvaluation[1]['value'],
//         'preparetionForFutureCourses' => $courseEvaluation[2]['value'],
//         'relevantToObjectives' => $courseEvaluation[3]['value'],
//         'contentRate' => $courseEvaluation[4]['value'],
//         'engagedStudents' => $professorEvaluation[0]['value'],
//         'teacherEffectiveness' => $professorEvaluation[1]['value'],
//         'communicationSkills' => $professorEvaluation[2]['value'],
//         'isClearAgenda' => $professorEvaluation[3]['value'],
//         'conveiedMaterial' => $professorEvaluation[4]['value'],
//         'TAengagedStudents' => $taEvaluation[0]['value'],
//         'TAteacherEffectiveness' => $taEvaluation[1]['value'],
//         'TAcommunicationSkills' => $taEvaluation[2]['value'],
//         'TAisClearAgenda' => $taEvaluation[3]['value'],
//         'TAconveiedMaterial' => $taEvaluation[4]['value'],
//     ]);

//     return response()->json(['message' => 'Feedback inserted successfully']);
// }

public function insertCourseEvaluation(Request $request)
{
    $requestBody = json_decode($request->getContent(), true);

    $courseEvaluation = $requestBody['course evaluation'] ?? [];
    $professorEvaluation = $requestBody['professor evaluation'] ?? [];
    $taEvaluation = $requestBody['ta evaluation'] ?? [];

    $courseId = $requestBody['course id'];
    $professorId = $requestBody['professor id'];
    $taId = $requestBody['ta id'];

    // Insert evaluation data
    DB::table('evaluation')->insert([
        'courseID' => $courseId,
        'professorId1' => $professorId,
        'taId' => $taId,
        'isClear' => $courseEvaluation[0]['value'] ?? null,
        'isRepeated' => $courseEvaluation[1]['value'] ?? null,
        'preparetionForFutureCourses' => $courseEvaluation[2]['value'] ?? null,
        'relevantToObjectives' => $courseEvaluation[3]['value'] ?? null,
        'contentRate' => $courseEvaluation[4]['value'] ?? null,
        'engagedStudents' => $professorEvaluation[0]['value'] ?? null,
        'teacherEffectiveness' => $professorEvaluation[1]['value'] ?? null,
        'communicationSkills' => $professorEvaluation[2]['value'] ?? null,
        'isClearAgenda' => $professorEvaluation[3]['value'] ?? null,
        'conveiedMaterial' => $professorEvaluation[4]['value'] ?? null,
        'TAengagedStudents' => $taEvaluation[0]['value'] ?? null,
        'TAteacherEffectiveness' => $taEvaluation[1]['value'] ?? null,
        'TAcommunicationSkills' => $taEvaluation[2]['value'] ?? null,
        'TAisClearAgenda' => $taEvaluation[3]['value'] ?? null,
        'TAconveiedMaterial' => $taEvaluation[4]['value'] ?? null,
    ]);

    return response()->json(['message' => 'Feedback inserted successfully']);
}

   public function getCourseID(Request $request)
   {
    $courseID=DB::table('course')->where('courseName', '=', $request->courseName)->get('courseID');
    return $courseID;
   }

//    public function getProfessorID(Request $request)
//    {
//     $professorID=DB::table('professor')->where('courseID', '=', $request->courseID)->get('professorId');
//     return $professorID;
//    }
public function getProfessorDetails( $studID,$courseID){
    $result = DB::table('course_reigesters')
    ->leftJoin('professor AS p1', 'course_reigesters.professorId1', '=', 'p1.professorId')
    ->leftJoin('professor AS p2', 'course_reigesters.professorId2', '=', 'p2.professorId')
    ->select('p1.professorId AS professorID1','p1.professorName AS professorName1', 'p2.professorId AS professorID2','p2.professorName AS professorName2')
    ->where('course_reigesters.studentId', '=', $studID)
    ->where('course_reigesters.courseID', '=', $courseID)
    ->get();
    return $result;

}


public function getTADetails( $studID,$courseID){
    $ta = DB::table('ta')
    ->join('course_reigesters', 'course_reigesters.TAId', '=', 'ta.TAId')
    ->join('student', 'course_reigesters.studentId', '=', 'student.studentId')
    ->join('group', function($join) {
        $join->on('course_reigesters.courseid', '=', 'group.courseId')
             ->on('course_reigesters.groupId', '=', 'group.groupNumber');
    })
    ->select('ta.TAId', 'ta.TAName')
    ->where('student.studentId', '=', $studID)
    ->where('course_reigesters.courseid', '=', $courseID)
    
    ->get();
    return $ta;

}


   Public function getCourseDetails(Request $request){

    $professorName = DB::table('professor')->where('courseID', '=', $request->courseID)->get('professorName');
    $TAName = DB::table('ta')->where('courseID', '=', $request->courseID)->get('TAName');
    $courseName=DB::table('course')->where('courseID', '=', $request->courseID)->get('courseName');

    

    return [
        'professorName' => $professorName,
        'TAName' => $TAName,
        'courseName'=>$courseName
    ];
   }

   public function getFeedbacks($courseName , $teacherId) {
    $courseID = DB::table('course')->where('courseName', '=', $courseName)->pluck('courseID');
    $feedbacks = DB::table('evaluation')
    ->join('ta', 'evaluation.TAId', '=', 'ta.TAId')
    ->select('ta.TAName', 'evaluation.TAId', 'evaluation.contentRate', 
    'evaluation.isRepeated', 'evaluation.isClear',
     'evaluation.relevantToObjectives', 'evaluation.preparetionForFutureCourses',
      'evaluation.engagedStudents', 'evaluation.conveiedMaterial', 'evaluation.isClearAgenda', 
      'evaluation.teacherEffectiveness', 'evaluation.communicationSkills', 'evaluation.TAengagedStudents', 'evaluation.TAconveiedMaterial', 'evaluation.TAisClearAgenda', 'evaluation.TAteacherEffectiveness', 'evaluation.TAcommunicationSkills')

    ->where('professorId1','=',$teacherId)
    ->orWhere('professorId2','=',$teacherId)
    ->orWhere('evaluation.TAId','=',$teacherId)
    ->where('evaluation.courseID', '=',  $courseID)
    ->get();
    return response()->json($feedbacks);
}
public function getTAs_Feedbacks_for_specific_course($courseName , $teacherId)
{
    $courseID = DB::table('course')->where('courseName', '=', $courseName)->pluck('courseID');
    $TAs = DB::table('evaluation')->distinct()->select('TAId')
    ->where('courseID', '=', $courseID)
    ->where('professorId1','=',$teacherId)
    ->orWhere('professorId2','=',$teacherId)
    ->get('TAId');

    foreach ($TAs as $TA) {
        
        $feedbacksData = DB::table('evaluation')
        ->join('ta', 'evaluation.TAId', '=', 'ta.TAId')
        ->select('ta.TAName', 'evaluation.TAId', 'evaluation.contentRate', 
        'evaluation.isRepeated', 'evaluation.isClear',
         'evaluation.relevantToObjectives', 'evaluation.preparetionForFutureCourses',
          'evaluation.engagedStudents', 'evaluation.conveiedMaterial', 'evaluation.isClearAgenda', 
          'evaluation.teacherEffectiveness', 'evaluation.communicationSkills', 'evaluation.TAengagedStudents', 'evaluation.TAconveiedMaterial', 'evaluation.TAisClearAgenda', 'evaluation.TAteacherEffectiveness', 'evaluation.TAcommunicationSkills')
    
    ->Where('evaluation.TAId','=',$TA->TAId)
    ->where('evaluation.courseID', '=',  $courseID)
    ->get();

    $feedbacks[$TA->TAId] = $feedbacksData;
    }

   
    return response()->json($feedbacks);
}

public function getStudentCourses($studID){
    $courses = DB::table('course_reigesters')
    ->select('course.courseName','course.courseID')
   
    ->join('student', 'student.studentId', '=', 'course_reigesters.studentId')
    ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
    ->where('student.studentId', '=', $studID)->get();
        return $courses;

}


}