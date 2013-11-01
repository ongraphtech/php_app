<?php
	/**
	* Push Class is class for send notification on IOS 
	* @package  Urban Airship
	* @access   public
	* Required Request Format JSON
	* Return Response Format JSON
	*/

	require_once './vendor/autoload.php';  /// Files Required to load Urban Airship Library
	use UrbanAirship\Airship;
	use UrbanAirship\UALog;
	use UrbanAirship\Push as P;
	use Monolog\Logger;
	use Monolog\Handler\StreamHandler;
	
	class push extends CI_Controller{     ///CI_Controller load the required library to run push class 

    public function __construct()
    {
        parent::__construct();
	    UALog::setLogHandlers(array(new StreamHandler("php://stdout", Logger::INFO)));///Write the log details to UA Package
		$this->load->model('push_model');   ///Load the model for database interaction
    }	
	

	/**
	 * General send function that support the notification functionality
	 * @param key for UrbanAirship API call
	 * @param masterKey for UrbanAirship API call
	 * @param deviceId the device id on which notification to be send
	 * @param message which want to send as notification
	 * @return string The fully formed response object representation of the JSON request
	 */
	
	public function sendNotification()
	{
	    $postData = file_get_contents("php://input");
	    // Get the data from request in JSON Format
		
		$postArray = json_decode($postData,true);
		// Decode the JSON Data into php array
		
		$key = $postArray['pushData']['key'];
		// Set the required Key value into php variable from array 
		
		$masterKey = $postArray['pushData']['masterKey'];
		// Set the required Master Key value into php variable from array
		
		$deviceId = $postArray['pushData']['deviceId'];
	   // Set the required Device Id value into php variable from array
	   
		$message = $postArray['pushData']['message'];
	    // Set the required Message value into php variable from array
		
		/// Validation Process Start
         if($key == '')
		 {
			$errorArray = array("sucess"=>"False","error"=>array("error"=>"Key can not be empty"));
			header('Content-type:application/json');
			$error = json_encode($errorArray);
			echo $error;
			exit();
		 }
		else if($masterKey == '')
		 {
			$errorArray = array("sucess"=>"False","error"=>array("error"=>"Master key can not be empty"));
			header('Content-type:application/json');
			$error = json_encode($errorArray);
			echo $error;
			exit();
		 }
		 else if($deviceId == '')
		 {
			$errorArray = array("sucess"=>"False","error"=>array("error"=>"Device id can not be empty"));
			header('Content-type:application/json');
			$error = json_encode($errorArray);
			echo $error;
			exit();
		 }
		else if($message == '')
		 {
			$errorArray = array("sucess"=>"False","error"=>array("error"=>"Message can not be empty"));
			header('Content-type:application/json');
			$error = json_encode($errorArray);
			echo $error;
			exit();
		 }
 		
		
		$airship = new Airship($key, $masterKey);   
		///Initialize the Urban Airship Object
		
	    $alert= false;	
        /// Set the boolean value false for Displaying Error Message 
		
		$response = $airship->push()->setAudience(P\deviceToken($deviceId))->setNotification(P\notification($message))
		                    ->setDeviceTypes(P\all)->send();
							
		/// Calling the Airship push function for send notification on requested device with requested message
		
		if($response->ok == 1)/// Check for Airship Response Status 
		{
		   $alert = true; // Set the error boolean value to true
		}
		if($alert)   /// Send the corresponding response for success full request
		{
		       $responseArray = array("sucess"=>"True","result"=>"Push Send Succesfully");
				header('Content-type:application/json');
				$response = json_encode($responseArray);
				echo $response;
				exit();
		
		}
		else
		{
			$errorArray = array("sucess"=>"False","error"=>array("error"=>"Invalid Paramester Request"));
			header('Content-type:application/json');
			$error = json_encode($errorArray);
			echo $error;
			exit();
		}
	}
	
	
	/**
	 * Following function is used to get the course data corresponding 
     * to a semester for a perticular student for a  Management  Application
     * Every student is related in a following sequence
       Program->Endorsement->Semester->Course->Schedule  	 
	 * @param Student Id
	 * @return string The fully formed response object representation of the JSON request
	 */
	 
	 public function getStudentCourseDataBySemester()
	 {
	 
	     $postData = file_get_contents("php://input");
	    // Get the data from request in JSON Format
		
		$postArray = json_decode($postData,true);
		// Decode the JSON Data into php array
		
		$studentId = $postArray['courseData']['studentId'];
		// Set the required Student value into php variable from array 
		
		//// Validation for Student id 
		  if($studentId == '')
		 {
			$errorArray = array("sucess"=>"False","error"=>array("error"=>"Student id can not be empty"));
			header('Content-type:application/json');
			$error = json_encode($errorArray);
			echo $error;
			exit();
		 }
		
		
		$semesterDataArray = $this->push_model->getStudentCourseDataBySemester($student_id);
		///calling the model function for database interaction result,Please check corresponding model file for database query
		
		if(!$semesterDataArray)/// Send the corresponding response for success full request
			{
				$errorArray = array("sucess"=>"False","error"=>array("error"=>"No Corse added till now"));
				header('Content-type:application/json');
				$error = json_encode($errorArray);
				echo $error;
				exit();
			}
			else
			{
				$responseArray = array("sucess"=>"True","result"=>$semesterDataArray);
				header('Content-type:application/json');
				$response = json_encode($responseArray);
				echo $response;
				exit();
			}
	 }
	 
		 /**
		 * Following function is used to get the class schedule of a student for a perticular month  
		 * @param Student Id
		 * @param startDate
		 * @param endDate
		 * @return string The fully formed response object representation of the JSON request
		 */
	 
	 
		public function getStudentCalenderDataByDay()
		{  				
			$postData = file_get_contents("php://input");
			// Get the data from request in JSON Format
			   
			$postArray = json_decode($postData,true);
			// Decode the JSON Data into php array
			
			$student_id = $postArray['calenderData']['studentId'];
			// Set the required Student value into php variable from array 
			
			$start_date = $postArray['calenderData']['startDate'];
			// Set the required start date value into php variable from array 
			
			$end_date = $postArray['calenderData']['endDate'];
			// Set the required end date value into php variable from array 
			
			$startDateArray = explode("-",$start_date);
			/// explode the start date to get year,month,day val
			
			$endDateArray = explode("-",$end_date);
			/// explode the end date to get year,month,day val
			
			$calenderDataArray = array();
			$calenderDataArray['course']= $this->push_model->getStudentCalenderDataByDay($student_id,$start_date,$end_date,$startDateArray[1]);
			////calling the databse function for database result array
		
			$month = $startDateArray[1];
			$lastDate = $endDateArray[2];
			$weekNormalArray = array('monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 7);
			///// Initialize the week day array 
			
			$firstDay = date('l', strtotime($start_date));
			//// Get the first day value of requested month
			
			$weekArray = array(1 => '', 2 => '', 3 => '', 4 => '', 5 => '');
			//// Initialize the week array
			
			$counterDay = $weekNormalArray[strtolower($firstDay)];
			
			$counterWeek = 1;/// Setting the week counter 
			for($i = 1; $i <= $lastDate; $i++ )
			{
				if($counterDay == 8)
				{
					$counterDay = 1;
					$counterWeek = $counterWeek +1;
				}
				$weekArray[$counterWeek][$counterDay] = $startDateArray[0].'-'.$startDateArray[1].'-'. $i;
				$counterDay++;
			}
		$cArray = $calenderDataArray['course'];
		
		if($cArray['course'] != 0)
		{
			$dateArray = array();
			foreach($cArray['course'] as $cKey=>$cValue)
			{
				$repeatWeek = $cValue['repeat_week']; /// checking the condition 
				if($repeatWeek == 1)
				{
					foreach($weekArray as $weekKey=>$weekValueArray)
					{
						foreach($weekValueArray as $weekValueKey=>$weekValue)
						{
							$subjectWeekArray = @explode(',', $cValue['repeat_day']);
							if(in_array($weekValueKey, $subjectWeekArray))
							{
								$dataArray[$weekValue][] = $cValue;
							}
						}
						
					}
				}
				else if ($repeatWeek == 2)
				{
					foreach($weekArray as $weekKey=>$weekValueArray)
					{
						if($weekKey == 2 || $weekKey == 4)
						{
							foreach($weekValueArray as $weekValueKey=>$weekValue)
							{
								$subjectWeekArray = @explode(',', $cValue['repeat_day']);
								if(in_array($weekValueKey, $subjectWeekArray))
								{
									$dataArray[$weekValue][] =  $cValue;
								}
							}
						}							
					}
				}
				else
				{
					foreach($weekArray as $weekKey=>$weekValueArray)
					{
						if($weekKey == $cValue['repeat_week'])
						{
							foreach($weekValueArray as $weekValueKey=>$weekValue)
							{
								$subjectWeekArray = @explode(',', $cValue['repeat_day']);
								if(in_array($weekValueKey, $subjectWeekArray))
								{
									$dataArray[$weekValue][] =  $cValue;
								}
							}
						}							
					}
				}
			}
				$calenderDataArray['course'] = $dataArray; 
		}
		
	  if(!$calenderDataArray)/// Send the corresponding response for success full request
		{
			$errorArray = array("sucess"=>"False","error"=>array("error"=>"No class schedule added till now"));
			header('Content-type:application/json');
			$error = json_encode($errorArray);
			echo $error;
			exit();
		}
		else
		{
			$responseArray = array("sucess"=>"True","result"=>$calenderDataArray);
			header('Content-type:application/json');
			$response = json_encode($responseArray);
			echo $response;
			exit();
		}
	  
 }
		
}
?>