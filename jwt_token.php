<?php
    require "conn.php";
    
    require_once 'jwt/BeforeValidException.php';
    require_once 'jwt/ExpiredException.php';
    require_once 'jwt/SignatureInvalidException.php';
    require_once 'jwt/JWT.php';
    require 'config.php';
    
    
    use \Firebase\JWT\JWT;
    
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = md5(mysqli_real_escape_string($con, $_POST['password']));
    
    $sql = "SELECT id FROM users WHERE username like '$username' and password like '$password'";
    $run = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($run) > 0){
    
        $tokenId    = base64_encode(openssl_random_pseudo_bytes(32));
        $issuedAt   = time();
        $notBefore  = $issuedAt + 10;  //Adding 10 seconds
        $expire     = $notBefore + 60; // Adding 60 seconds
        $serverName = $server_name;
        
        /*
         * Create the token as an array
         */
        $data = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss'  => $serverName,       // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expire,           // Expire
            'data' => [                  // Data related to the signer user
                'userId'   => '1', // userid from the users table
                'userName' => 'vaibhav', // User name
            ]
        ];
        
        header('Content-type: application/json');
        
        /*
         * Extract the key, which is coming from the config file. 
         * 
         * Best suggestion is the key to be a binary string and 
         * store it in encoded in a config file. 
         *
         * Can be generated with base64_encode(openssl_random_pseudo_bytes(64));
         *
         * keep it secure! You'll need the exact key to verify the 
         * token later.
         */
        $secretKey = base64_decode($jwt_token);
        
        /*
         * Extract the algorithm from the config file too
         */
        $algorithm = $jwt_algorithm;
        
        /*
         * Encode the array to a JWT string.
         * Second parameter is the key to encode the token.
         * 
         * The output string can be validated at http://jwt.io/
         */
        $jwt = JWT::encode(
            $data,      //Data to be encoded in the JWT
            $secretKey, // The signing key
            $algorithm  // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
            );
            
        $unencodedArray = ['jwt' => $jwt];
        echo json_encode($unencodedArray);
    
    }
?>