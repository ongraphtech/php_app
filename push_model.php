<?php
/**
	* Push_model class handles the database interaction query of push Controller 
	*/
	
class Push_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->load->database();//// Load the database class
    }
	
	/**
	 * General function that fetch the student course data corresponding to a semester
	 * @param Student id
	 * Following table will take place wp_course,wp_endorsement,wp_endorsement_semester,wp_endorsement_fixed
     *  wp_semester_schedule,wp_course_semester_schedule,wp_student_semester_details,wp_student_course_details,
     * 	wp_students,wp_grade
	 */
	
	public function getStudentCourseDataBySemester($student_id)
	{
		$sql = "SELECT DISTINCT wc.name, wc.id,wc.code,wssd.semester,wg.grade,wscd.course_type FROM wp_courses wc
						JOIN wp_course_semester_schedule wcs ON wc.id = wcs.course_id
						JOIN wp_semester_schedule wss ON wcs.semester_schedule_id = wss.id 
						JOIN wp_endorsement_fixed wdf ON wcs.course_id = wdf.course_id 
						JOIN wp_endorsement_semester wes ON wdf.endorsement_semester_id = wes.id
                        JOIN wp_student_semester_details wssd ON wes.endorsement_id=wssd.endorsement_id
						AND wss.semester=wssd.semester AND wes.semester=wssd.semester_id
                        JOIN wp_student_course_details wscd ON wssd.student_id = wscd.student_id AND wscd.course_id = wc.id
						JOIN wp_students ws ON wscd.student_id = ws.id LEFT JOIN wp_grade wg ON wg.course_id = wc.id
                        AND wg.student_id = ws.student_id
						WHERE wssd.student_id='$student_id' AND wss.is_active = '1' AND wc.is_active = '1'";	 
						
				$res = mysql_query($sql);
				$rowCount = mysql_num_rows($res);
				if($rowCount>0)
				{
				   $resDataArray = array();
				   while($rows = mysql_fetch_assoc($res))
					{
					   
					    $resDataArray[$rows['semester']][] = array(
						                 'id'=>$rows['id'],
										 'name'=>$rows['name'],
										 'code'=>$rows['code'],
										  'semester'=>$rows['semester'],
										  'grade'=>$rows['grade'],
										  'course_type'=>$rows['course_type']
										
						                 );	
					}
					return $resDataArray;
				}
				else
				{
				   return false;
				}
	}
	
	
	
	/**
	 * General function that fetch the student class course schedule corresponding for a month
	 * @param student_id,start_date,end_date
	 * Following table will take place wp_class_schedule,wp_classifications,wp_student_course_details,wp_courses
	 */
	
	public function getStudentCalenderDataByDay($student_id,$start_date,$end_date)
	{
		$sql = "SELECT DISTINCT wcs.is_type_lecture, wcs.start_date, wcs.end_date, wcs.start_time, wcs.end_time, wcs.is_repeated, wcs.repeat_week, wcs.repeat_day, wcs.room_number,cs.id,cs.name
		FROM wp_class_schedule wcs INNER JOIN wp_classifications wc ON wcs.classification_id = wc.id INNER JOIN wp_student_course_details wscd ON wc.course_id = wscd.course_id INNER JOIN wp_courses cs ON wscd.course_id = cs.id WHERE (('$start_date' between wcs.start_date AND wcs.end_date) OR ('$end_date' between wcs.start_date AND wcs.end_date)) AND wscd.student_id = '$student_id'";
		$res = mysql_query($sql);
		$rowCount = mysql_num_rows($res);	
		$resDataArray = array(); 
		if($rowCount > 0)
		{
				while($rows = mysql_fetch_assoc($res))
				{	
					$resDataArray['course'][] = array(
						'is_type_lecture'  => $rows['is_type_lecture'],
						'start_date'  => $rows['start_date'],
						'end_date'  => $rows['end_date'],
						'start_time'  => $rows['start_time'],
						'end_time'  => $rows['end_time'],
						'is_repeated'  => $rows['is_repeated'],
						'repeat_week'  => $rows['repeat_week'],
						'repeat_day'  => $rows['repeat_day'],
						'course_id'  => $rows['id'],
						'name'  => $rows['name'],
						'room_number'  => $rows['room_number']
					);
				}
			return $resDataArray;
		}
	  else
		{
			return false;
		}
	}
	
	
}
?>