<?php 

use \Firebase\JWT\JWT;

class Token{

    private static $key = 'heavydev';
    private static $algorithm = 'HS256';
    private static $lifespan = 100;
    
    public static function create($claims = []){
        $issuedAt = time();
        $notBefore = $issuedAt + 10;
        $expires = $issuedAt + self::$lifespan;

        $payload = array(
            'jti' => base64_encode(mcrypt_create_iv(32)),
            'iss' => 'http://localhost',
            'aud' => 'http://localhost',
            'iat' => $issuedAt,
            'nbf' => $notBefore,
            'exp' => $expires
        );

        if($claims){
            foreach($claims as $claim => $value){
                $payload[$claim] = $value;
            }
        }

        return JWT::encode($payload, self::$key);
    }

    public static function decode($token){
        JWT::$leeway = 10; // $leeway in seconds
        return JWT::decode($token, self::$key, array(self::$algorithm));
    }

    public static function refresh($token) {
        try{
            JWT::$leeway = 60;
            $decoded = JWT::decode($token, self::$key, [self::$algorithm]);
            return $token;
        }catch ( \Firebase\JWT\ExpiredException $e ) {
            $decoded = (array) JWT::decode($token, self::$key, [self::$algorithm]);
            $decoded['iat'] = time();
            $decoded['exp'] = time() + self::$lifespan;
            return JWT::encode($decoded, self::$key);
        }catch ( \Exception $e ){
            return false;
        }
    }

    public static function verify(){
        // Get token from header
        $token = null;

        $headers = getallheaders();
        if(array_key_exists('Authorization', $headers) == false){
            throw new Exception('No Authorization found in headers');
        }else{        
            list($bearer, $token) = explode(' ', $headers['Authorization']);

            // decode token; validate information
            try{
                JWT::$leeway = 60;
                $decoded = JWT::decode($token, self::$key, [self::$algorithm]);
                return (int) $decoded->exp - time();
            }catch(\Firebase\JWT\ExpiredException $e){
                throw new Exception('Token has expired');
            }catch (\Exception $e){
                throw new Exception($e->getMessage());
            }
        }

        return $token;
    }
}