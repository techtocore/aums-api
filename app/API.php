<?php
namespace Aums;

use Sunra\PhpSimple\HtmlDomParser;

class API {

    private $rollNumber;
    private $password;
    private $client;
    private $baseURI;
    private $loggedIn;

    /**
     * API constructor.
     * @param string $rollNumber
     * @param string $password
     * @param string $baseURI
     */
    public function __construct($rollNumber = null, $password = null, $baseURI = 'https://amritavidya.amrita.edu:8444') {
        $this->rollNumber = strtoupper($rollNumber);
        $this->password = $password;
        $this->baseURI = $baseURI;
        $this->client = new Client($baseURI);
    }

    /**
     * @param null $rollNumber
     */
    public function setRollNumber($rollNumber)
    {
        $this->rollNumber = $rollNumber;
    }

    /**
     * @param null $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }


    /**
     * Start the login flow
     * @throws AumsOfflineException
     * @throws CredentialsInvalidException
     * @throws CredentialsMissingException
     * @return array An array containing basic student info and link to profile pic
     */
    public function login(){

        if($this->rollNumber == null && $this->password == null) {
            throw new CredentialsMissingException("Roll number and password required");
        } else if($this->rollNumber == null) {
            throw new CredentialsMissingException("Roll number required");
        } else if($this->password == null) {
            throw new CredentialsMissingException("Password required");
        }

        $sessionInfo = $this->getSessionID();

        $params = [
            'username' => $this->rollNumber,
            'password' => $this->password,
            '_eventId' => 'submit',
            'lt'       => $sessionInfo['lt'],
            'submit'   => 'LOGIN'
        ];

        $response = $this->client->post($sessionInfo['action'],$params);

        if($response->getCode() == 200){

            if (strpos($response->getBody(),'Log Out') !== false) {
                $studentInfo = $this->getStudentInfo();
                $this->loggedIn = true;
                return $studentInfo;
            } else {
                throw new CredentialsInvalidException("Username or password is incorrect");
            }
        } else {
            throw new AumsOfflineException("Cannot connect to server: Error ".$response->getCode());
        }
    }


    /**
     * Get the Session ID (to bypass CSRF protection)
     * @return array With the form action and hidden lt variable value
     * @throws AumsOfflineException
     */
    private function getSessionID(){

        $params = [
            'service' => $this->baseURI.'/aums/Jsp/Common/index.jsp'
        ];

        $response = $this->client->get('/cas/login',$params);

        if($response->getCode() == 200){
            $dom = HtmlDomParser::str_get_html($response->getBody());

            $form = $dom->find('#fm1')[0];
            $formAction = $form->action;

            $hiddenInput = $dom->find('input[name=lt]')[0];
            $ltValue = $hiddenInput->value;

            return array(
                "action" => $formAction,
                "lt" => $ltValue
            );

        } else {
            throw new AumsOfflineException("Cannot connect to server: Error ".$response->getCode());
        }
    }


    /**
     * Get as much information about the student as possible from the AUMS
     * @return array containing filtered info
     * @throws AumsOfflineException
     */
    private function getStudentInfo(){

        $params = [
            'action' => 'UMS-SRM_INIT_STUDENTPROFILE_SCREEN',
            'isMenu' => 'true'
        ];

        $response = $this->client->get('/aums/Jsp/Student/Student.jsp',$params);

        if($response->getCode() == 200){

            $primaryInputs = $this->getInputsFromDom(HtmlDomParser::str_get_html($response->getBody()));
            $extraInputs = $this->getExtraInfo($primaryInputs);

            $studentInfo = $this->cleanupInputs(array_merge($primaryInputs,$extraInputs));

            $encodedEnrollmentId = $studentInfo['enrollment_id'];
            $firstName = $studentInfo['first_name'];
            $lastName = $studentInfo['last_name'];

            $imageName = $this->storeStudentImage($firstName,$encodedEnrollmentId);

            return array(
                'roll_no'               => $this->rollNumber,
                'first_name'            => $firstName,
                'last_name'             => $lastName,
                'email'                 => $studentInfo['email'],
                'phone'                 => $studentInfo['phone'],
                'degree_program'        => $studentInfo['degree_program'],
                'branch'                => $studentInfo['branch'],
                'semester'              => $studentInfo['semester'],
                'image_filename'        => $imageName
            );

        } else {
            throw new AumsOfflineException("Cannot connect to server: Error ".$response->getCode()." Url => ". $response->getEffectiveUrl());
        }
    }


    /**
     * Download and store the profile image locally
     * @param string $name of the student
     * @param string $encodedEnrollmentId of the student
     * @return string The image's filename for later reference
     * @throws AumsOfflineException
     */
    public function storeStudentImage($name, $encodedEnrollmentId) {

        $params = [
            'action' => 'SHOW_STUDENT_PHOTO',
            'encodedenrollmentId' => $encodedEnrollmentId,
            'flag' => 'photo'
        ];

        $response = $this->client->get('/aums/FileUploadServlet',$params);

        $imageName = Encryption::encode($name . " " .time());

        if($response->getCode() == 200){

            $handle = fopen(__DIR__."/../storage/images/".$imageName, "w");
            fwrite($handle, $response->getBody());
            fclose($handle);
            return $imageName;

        } else {
            throw new AumsOfflineException("Cannot connect to server: Error ".$response->getCode());
        }

    }


    /**
     * Make a new request and get additional info about the student
     * @param array $params all the input feilds and values from the previous request
     * @return array Extra information
     * @throws AumsOfflineException
     */
    public function getExtraInfo($params){

        $params['htmlPageTopContainer_ltabshistag_tabs_clicked_tabpane'] = 'personaldetails';
        $params['htmlPageTopContainer_action'] = 'HIS-TAB_CONTROL_CLICKED';
        $params['Page_refIndex_hidden'] = "1";

        $response = $this->client->post('/aums/Jsp/Student/Student.jsp?action=UMS-SRM_INIT_STUDENTPROFILE_SCREEN&isMenu=true',$params);

        if($response->getCode() == 200){
            return $this->getInputsFromDom(HtmlDomParser::str_get_html($response->getBody()));
        } else {
            throw new AumsOfflineException("Cannot connect to server: Error ".$response->getCode());
        }
    }


    /**
     * Traverses a DOM and gets all the form fields and their values
     * @param mixed $dom The DOM object from the parser
     * @param string $formName (default:mainForm)
     * @return array
     */
    private function getInputsFromDom($dom, $formName = "mainForm"){
        $inputs = array();
        foreach($dom->find("form[name=$formName] input") as $input){
            $inputs[$input->name] = $input->value;
        }

        foreach($dom->find("form[name=$formName] input[checked]") as $input){
            $inputs[$input->name] = $input->value;
        }

        foreach($dom->find("form[name=$formName] select") as $select){
            try {
                $value = $select->find('option[selected]',0)->plaintext;
                $inputs[$select->name] = $value;
            } catch(\Exception $e){
                $inputs[$select->name] = "";
            }

        }
        return $inputs;
    }


    /**
     * Clean up the generated info form by removing unecessary data and using proper names from @see getNameKeyMap()
     * @param $inputs
     * @return array
     */
    private function cleanupInputs($inputs){
        $cleanArr = array();
        foreach($this->getNameKeyMap() as $key => $value){
            $cleanArr[$value] = $inputs[$key];
        }
        $cleanArr['roll_no'] = $this->rollNumber;
        return $cleanArr;
    }


    /**
     * Generates an array name map
     * @return array map of aums standard names to human readable names
     */
    private function getNameKeyMap(){
        return array(
            'htmlPageTopContainer_txtRollNo' => 'roll_no',
            'htmlPageTopContainer_addst_txteditfirstName' => 'first_name',
            'htmlPageTopContainer_addst_txteditlastName' => 'last_name',
            'htmlPageTopContainer_addst_radiogroupSex' => 'gender',
            'htmlPageTopContainer_addst_txteditDob1' => 'date_of_birth',
            'htmlPageTopContainer_adpsndet_texteditEmail' => 'email',
            'htmlPageTopContainer_adpsndet_texteditPnoneNo' => 'phone',
            'htmlPageTopContainer_enrollmentId' => 'enrollment_id',
            'htmlPageTopContainer_personId' => 'person_id',
            'htmlPageTopContainer_addst_txteditapplicationNo' => 'application_number',
            'htmlPageTopContainer_addst_txteditadmissionNo' => 'admission_number',
            'htmlPageTopContainer_addst_radiogroupLateral' => 'lateral_entry_y_n',
            'htmlPageTopContainer_addst_selAdmissionType' => 'admission_type',
            'htmlPageTopContainer_addst_selProgram' => 'degree_program',
            'htmlPageTopContainer_addst_selBranch' => 'branch',
            'htmlPageTopContainer_addst_lstYearMaster' => 'join_year',
            'htmlPageTopContainer_addst_selStep' => 'semester',
            'htmlPageTopContainer_addst_selStatus' => 'enrollment_status',
            'htmlPageTopContainer_adpsndet_radiogrpPlaceType' => 'native_urban_rural',
            'htmlPageTopContainer_adpsndet_txteditNatPlace' => 'native_place',
            'htmlPageTopContainer_adpsndet_txteditNatDistrict' => 'native_district',
            'htmlPageTopContainer_adpsndet_txteditMothertongue' => 'native_mother_tongue',
            'htmlPageTopContainer_adpsndet_radiogrpHostellite' => 'hostellite_y_n',
            'htmlPageTopContainer_adpsndet_txteditHostelName' => 'hostel_name',
            'htmlPageTopContainer_adpsndet_radiogrpBusFacility' => 'bus_y_n',
            'htmlPageTopContainer_adpsndet_txteditRoomNo' => 'hostel_room_no',
            'htmlPageTopContainer_adpsndet_selectNationality' => 'nationality',
            'htmlPageTopContainer_adpsndet_lstNatCountry' => 'country',
            'htmlPageTopContainer_adpsndet_lstAdPDState' => 'state',
            'htmlPageTopContainer_adpsndet_selectReligion' => 'religion',
            'htmlPageTopContainer_adpsndet_selectCaste' => 'caste',
            'htmlPageTopContainer_adpsndet_selectMstatus' => 'marital_status',
            'htmlPageTopContainer_encodedenrollmentId' => 'encoded_enrollment_id'
        );
    }
}