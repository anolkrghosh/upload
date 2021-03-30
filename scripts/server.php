<?php
/**Created by Anol Kr Ghosh */
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require 'Json.php';
$db = new Json();
$message = null;
$requestType = $_SERVER['REQUEST_METHOD'];
if($requestType == 'POST'){  
    if(isset($_FILES)){
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = './files/';
        $dest_path = $uploadFileDir . $newFileName;
        $info = array('name'=>$fileName,'path'=>$newFileName,'file_ext'=>$fileExtension);
        
        if(move_uploaded_file($fileTmpPath, $dest_path)){
            if($db->push($info,'files')){
                $message =array('status'=>200,'msg'=>'File Saved','id'=> $newFileName);
            }else{
                $message =array('status'=>102,'msg'=>'File Info Unsaved','id'=> $newFileName);
            }              
        }
        else
        {
            $message = array('status'=>507,'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.');
        }
        echo json_encode($message);
    }
}else if($requestType == 'GET' && $_GET['data'] == true){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Content-Type: application/json');
    if(isset($_GET['id'])){
        echo $db->get($_GET['id']);
        exit;    
    }
    echo $db->get();
}else{
    echo "404";
}
