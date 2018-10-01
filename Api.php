<?php

require_once 'jwt/BeforeValidException.php';
require_once 'jwt/ExpiredException.php';
require_once 'jwt/SignatureInvalidException.php';
require_once 'jwt/JWT.php';
require 'config.php';
require 'conn.php';

use Firebase\JWT\JWT;

/*
 * Get all headers from the HTTP request
 */
$headers = getallheaders();

    if (array_key_exists('Authorization', $headers)){
        
        $jwt = $headers['Authorization'];
        $secretKey = base64_decode($jwt_key);
        $token = JWT::decode($jwt, $secretKey, [$jwt_algorithm]);
        if ($token->exp >= time()) {

        	$response = array();
        
        	if (isset($_POST['desc']) && strlen($_POST['desc']) > 0 && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        
        		$file = $_FILES['image']['tmp_name'];
        
        		$desc = $_POST['desc'];
        		
        		$extension = getFileExtension($_FILES['image']['name']);
        		
        		$name = round(microtime(true) * 1000) . '.' . $extension;
        		$filedest = "uploads/". $name;
        		move_uploaded_file($file, $filedest);
         
        		$url = "http://handekart.pe.hu/eckovation/uploads/".$name;
        
        		$sql = "INSERT INTO images (description, image) VALUES ('$desc', '$url')";
        		
        		if(mysqli_query($con, $sql)){
        			$response['error'] = false;
        			$response['message'] = 'File Uploaded Successfullly';
        			$response['imageUrl'] = $url;
        		}else{
        		    $response['error'] = true;
        		    $response['message'] = 'Query Failed';
        		}
        	} else {
        		$response['error'] = true;
        		$response['message'] = 'Required parameters are not available';
        	}
         
        	echo json_encode($response);
            
        }else{
            http_response_code(401);
            echo "error";
        }
    }else {
        http_response_code(401);
        echo "error ";
    }
    
    function getFileExtension($file){
        $path_parts = pathinfo($file);
        return $path_parts['extension'];
    }
?>