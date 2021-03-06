<?php
use App\Controller\Volunteer\VolunteerController;
use App\Controller\Auth\Register;
use App\Controller\Utils\Utils;

class Volunteer extends Controller{
  public function __construct(){
  }

  function index(){
    header("HTTP/1.0 404 Not Found");
    exit();
  }

  function login(){
    $email = $_POST['email'];
    $pass = $_POST['password'];
    VolunteerController::login($email, $pass);
  }

  function register(){
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    VolunteerController::register($email, $pass, $fname, $lname);
  }

  function duty($status){
    if(isset($status)){
      if(isset($_POST['id'])){
        $id = $_POST['id'];
        if($status == "online"){
          $online = 1;
          $category = $_POST['category'];
          VolunteerController::setOnline($id, $online, $category);
        }else if($status == "offline"){
          $online = 0;
          VolunteerController::setOnline($id, $online);
        }
      }
    }
  }

  function setToken(){
    if(isset($_POST['id'])){
      $id = $_POST['id'];
      $token = $_POST['token'];
      VolunteerController::setToken($id, $token);
    }
  }

  function setLocation(){
    if(isset($_POST['id'])){
      $id = $_POST['id'];
      $lat = $_POST['lat'];
      $lng = $_POST['lng'];
      VolunteerController::setLocation($id, $lat, $lng);
    }
  }

  function getAllCat(){
    $category = VolunteerController::getCategory();
    $jsonArray = array();
    while($row = mysqli_fetch_assoc($category)){
      $arr = array(
        'id' => $row['id'],
        'name' => $row['name']
      );
      array_push($jsonArray, $arr);
    }
    echo json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
  }

  function getUserDetail($id){
    $volunteer = VolunteerController::getVolunteer($id);
    $jsonArray = array();
    $arr = array('id' => $volunteer['id'],
      'email' => $volunteer['email'],
      'first_name' => $volunteer['first_name'],
      'last_name' => $volunteer['last_name'],
      'profile_picture' => $volunteer['profile_picture'],
      'telephone' => $volunteer['telephone'],
      'on_order' => $volunteer['on_order'],
      'category' => $volunteer['category'],
      'status' => $volunteer['status'],
      'validate' => $volunteer['validate'],
      'online' => $volunteer['online']
    );
    array_push($jsonArray, $arr);
    echo str_replace(array('[', ']'), '', json_encode($arr, JSON_UNESCAPED_UNICODE));
  }

  function getVolunteerLocation(){
    $result = VolunteerController::getLocation();
    $jsonArray = array();
    while($row = mysqli_fetch_assoc($result)){
      $volunteer = VolunteerController::getVolunteer($row['volunteer_id']);
      $arr = array('id' => $volunteer['id'],
        'email' => $volunteer['email'],
        'first_name' => $volunteer['first_name'],
        'last_name' => $volunteer['last_name'],
        'profile_picture' => $volunteer['profile_picture'],
        'telephone' => $volunteer['telephone'],
        'on_order' => $volunteer['on_order'],
        'category' => $volunteer['category'],
        'status' => $volunteer['status'],
        'validate' => $volunteer['validate'],
        'online' => $volunteer['online'],
        'lat' => $row['lat'],
        'lng' => $row['lng'],
      );
      array_push($jsonArray, $arr);
    }
    echo json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
  }

  function requestVolunteer(){
    $uid = $_POST['id'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $vcat = $_POST['volcat'];
    VolunteerController::requestVolunteer($uid, $vcat, $lat, $lng);
  }

  function checkOrderStatus($id = NULL){
    if($id != NULL){
      $result = VolunteerController::checkOrderStatus($id);
      if($result['status'] == 1){
        $arr = array('status' => true, 'volunteer_id' => $result['volunteer_id']);
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
      }else{
        $arr = array('status' => false, 'now' => $result['status']);
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
      }
    }else{
      header("Location: /");
      exit();
    }
  }

  function setOrderStatus($id = NULL){
    if($id != NULL){
      if(isset($_POST['volunteer_id'])){
        VolunteerController::setOrderStatus($id, $_POST['status'], $_POST['volunteer_id']);
      }else{
        VolunteerController::setOrderStatus($id, $_POST['status']);
      }
    }else{
      header("Location: /");
      exit();
    }
  }

  function getAllOrder($category = NULL){
    $order = VolunteerController::getAllOrder();
    $jsonArray = array();
    if($category != NULL){
      while($row = mysqli_fetch_assoc($order)){
        if($row['category'] == $category){
          array_push($jsonArray, $row);
        }
      }
    }else{
      while($row = mysqli_fetch_assoc($order)){
        array_push($jsonArray, $row);
      }
    }
    echo json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
  }

  function editProfilePic($id, $filename){
      $file = $_FILES['file'];
      echo Utils::uploadPic($file, $id, false, $filename);
  }

  function editProfile($id){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    VolunteerController::editProfile($firstname, $lastname, $id);
  }
}
