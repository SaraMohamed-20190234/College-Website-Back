<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\student;

use Illuminate\Http\Request;

class professorAndTa extends Controller
{
    public function getMyCourses($teacherId)
    {
        $courses = DB::table('course')
            ->select(
                'course.Course_Code',
                'course.courseName',
                'course.Level',
                'course.Semester',
                'course.courseID',
                'course.departmentCode'
            )
            ->where('course.professor1', '=', $teacherId)
            ->orWhere('course.professor2', '=', $teacherId)
            ->orWhere(function ($query) use ($teacherId) {
                $query->whereExists(function ($subquery) use ($teacherId) {
                    $subquery->select('courseId')
                        ->from('group')
                        ->whereRaw('group.courseId = course.courseID')
                        ->where('group.TAId', '=', $teacherId);
                });
            })
            ->distinct()
            ->get();
    
        return $courses;
    }


public function getTACourses($TAId)
{
    $courses = DB::table('course_reigesters')
    ->select('course.Course_Code',
    'course.courseName','course.Level'
    ,'course.Semester','course.courseID','course.departmentCode')
    ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
    ->join('ta', 'course_reigesters.TAId', '=', 'ta.TAId')
    ->where('ta.TAId', '=', $TAId)->distinct()
    ->get();

    return $courses;
}
public function getCourseProfYears($professorId,$CourseId)
{
    $years = DB::table('course_reigesters')
    ->select('course_reigesters.Year')
    // ->join('professor', 'professor.professorId', '=', 'course_reigesters.professorId1')
    ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
    ->where('course.professor2', '=', $professorId)->orwhere('course.professor1', '=', $professorId) 
    ->where('course.courseID', '=', $CourseId)->distinct()
    ->get();
    return $years;
}
public function getCourseProfYears2($professorId,$courseName)
{
    $years = DB::table('course_reigesters')
    ->select('course_reigesters.Year')
    // ->join('professor', 'professor.professorId', '=', 'course_reigesters.professorId1')
    ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
    ->where('course.professor2', '=', $professorId)->orwhere('course.professor1', '=', $professorId) 
    ->where('course.courseName', '=', $courseName)->distinct()
    ->get();
    return $years;
}
public function getCourseTAYears($TAID,$courseName)
{
    $years = DB::table('course_reigesters')
    ->select('course_reigesters.Year')
    ->join('ta', 'ta.TAId', '=', 'course_reigesters.TAId')
    ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
    ->where('ta.TAId', '=', $TAID) 
    ->where('course.courseName', '=', $courseName)->distinct()
    ->get();
    return $years;
}
public function getCourseYears()
{
    $years = DB::table('course_reigesters')
    ->select('course_reigesters.Year')
    ->join('professor', 'professor.professorId', '=', 'course_reigesters.professorId1')
    ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
     
   ->distinct()
    ->get();
    return $years;
}

public function getMyStudents($teacherId)
{
    $students = DB::table('course_reigesters')->select('course.courseName','student.Level'
    ,'course.Semester',
    'student.studentName','student.GPA','course_reigesters.grade')
    ->join('student', 'student.studentId', '=', 'course_reigesters.studentId')
    ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
    ->where('course_reigesters.professorId1', '=', $teacherId)
    ->orWhere('course_reigesters.professorId2', '=', $teacherId)
    ->orWhere('course_reigesters.TAId', '=', $teacherId)
    ->get();
    
    return $students;
}
public function getGrades($teacherId)
{
    $courses = DB::table('course_reigesters')->select('course_reigesters.grade')
    ->join('professor', 'professor.professorId', '=', 'course_reigesters.professorId1')
    // ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
    ->where('course_reigesters.professorId1', '=', $teacherId)
    ->orWhere('course_reigesters.professorId2', '=', $teacherId)
    ->orWhere('course_reigesters.TAId', '=', $teacherId)
    ->get();

    return $courses;
}

public function selectCourse($courseName,$teacherId){ 
    // $students = DB::table('student')->where('departmentCode', '=', $dept)->orderBy("GPA","DESC")->get()->take(3);
    $students = DB::table('course_reigesters')
    ->select('course.courseName','student.Level'
    ,'course.Semester',
    'student.studentName','student.GPA','course_reigesters.grade')
    ->join('student', 'student.studentId', '=', 'course_reigesters.studentId')
    ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
    ->where('course.courseName', '=', $courseName)
    ->where('course_reigesters.professorId1', '=', $teacherId)
    ->orWhere('course_reigesters.professorId2', '=', $teacherId)
    ->orWhere('course_reigesters.TAId', '=', $teacherId)
    ->get();
    return $students;
}
public function selectGrade_level_course($grade,$level,$course){ 
    // $students = DB::table('student')->where('departmentCode', '=', $dept)->orderBy("GPA","DESC")->get()->take(3);
    $students = DB::table('course_reigesters')
    ->select('course.courseName','student.Level'
    ,'course.Semester',
    'student.studentName','student.GPA','course_reigesters.grade')
    ->join('student', 'student.studentId', '=', 'course_reigesters.studentId')
    ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
    ->where('course_reigesters.grade', '=', $grade)
    ->where('student.Level', '=', $level)
    ->where('course.courseName', '=', $course)
    ->get();
    return $students;
}
public function selectLevel_Course($level,$course){ 
    // $students = DB::table('student')->where('departmentCode', '=', $dept)->orderBy("GPA","DESC")->get()->take(3);
    $students = DB::table('course_reigesters')
    ->select('course.courseName','student.Level'
    ,'course.Semester',
    'student.studentName','student.GPA','course_reigesters.grade')
    ->join('student', 'student.studentId', '=', 'course_reigesters.studentId')
    ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
    ->where('student.Level', '=', $level)
    ->where('course.courseName', '=', $course)
    ->get();
    return $students;
}
public function selectLevel($level){ 
    // $students = DB::table('student')->where('departmentCode', '=', $dept)->orderBy("GPA","DESC")->get()->take(3);
    $students = DB::table('course_reigesters')
    ->select('course.courseName','student.Level'
    ,'course.Semester',
    'student.studentName','student.GPA','course_reigesters.grade')
    ->join('student', 'student.studentId', '=', 'course_reigesters.studentId')
    ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
   
    ->where('student.Level', '=', $level)
    ->get();
    return $students;
}
public function selectGrade($grade){ 
    // $students = DB::table('student')->where('departmentCode', '=', $dept)->orderBy("GPA","DESC")->get()->take(3);
    $students = DB::table('course_reigesters')
    ->select('course.courseName','student.Level'
    ,'course.Semester',
    'student.studentName','student.GPA','course_reigesters.grade')
    ->join('student', 'student.studentId', '=', 'course_reigesters.studentId')
    ->join('course', 'course.courseID', '=', 'course_reigesters.courseid')
    ->where('course_reigesters.grade', '=', $grade)
    ->get();
    return $students;
}

public function returnTeacherGPs($teacherId)
    {           
                    $result = DB::table('gp')    
                    ->join('ta', 'ta.TAId', '=','gp.TA' ) 
                    ->join('professor', 'professor.professorId', '=','gp.professor' ) 
                    ->select('gp.idea','gp.requirements','gp.member1','gp.member2','gp.member3','gp.member4','gp.member5','gp.professor','gp.TA','ta.TAName','professor.professorName')
                    ->where('Ta_status','Accepted')             
                    ->where('Prof_status','Accepted')   
                    ->where('gp.professor',$teacherId)  
                    ->orWhere('gp.TA',$teacherId)            
                    ->get();                
                    return $result;           

}




}
