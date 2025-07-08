<?php
class Jwt
{
    protected $key;
    protected $ttl;
    public function __construct(string $key, string $ttl)
    {
        $this->key=$key;
        $this->setTTL($ttl);
    }
    public function setTTL($ttl){
        $this->ttl=$ttl;
    }
    public function encode(array $payload): string
    {

        $header = json_encode([
            "alg" => "HS256",
            "typ" => "JWT",
            "exp" => /* The `->calculateExpiryTime(15)` method call is calculating the expiry time
            for the JWT token. It takes an integer value (in this case, 15) representing
            the number of minutes for which the token will be valid. */
            $this->calculateExpiryTime($this->ttl)
        ]);

        $header = $this->base64URLEncode($header);
        $payload = json_encode($payload);
        $payload = $this->base64URLEncode($payload);

        $signature = hash_hmac("sha256", $header . "." . $payload, $this->key, true);
        $signature = $this->base64URLEncode($signature);
        return $header . "." . $payload . "." . $signature;
    }


   
    public function decode(string $token)
    {
        if (
            preg_match(
                "/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/",
                $token,
                $matches
            ) !== 1
        ) {

            // throw new InvalidArgumentException("invalid token format");
            return false;
        }

        $signature = hash_hmac(
            "sha256",
            $matches["header"] . "." . $matches["payload"],
            $this->key,
            true
        );

        $header = json_decode($this->base64URLDecode($matches["header"]), true);
        
        // var_dump($header);exit;
        $timestamp =$this->calculateExpiryTime(1);
        $left = $timestamp-$header["exp"];
        // echo $left;exit;
        if ($left>0) {
            // 15 mins has passed
            return false;
        }
        $signature_from_token = $this->base64URLDecode($matches["signature"]);

        if (!hash_equals($signature, $signature_from_token)) {

            // throw new Exception("signature doesn't match");
            return false;
        }

        $payload = json_decode($this->base64URLDecode($matches["payload"]), true);
        
        // var_dump($payload);exit;
        return $payload;
    }
    
    public function refresh($token){
        
        $signature=$this->decode($token);
        if(!$signature) return false;
        return $this->encode($signature);
    }
  
    private function base64URLEncode(string $text): string
    {

        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    private function base64URLDecode(string $text): string
    {
        return base64_decode(
            str_replace(
                ["-", "_"],
                ["+", "/"],
                $text
            )
        );
    }

    private function calculateExpiryTime(int $value): int
    {
        $t=time();
        $interval=$value * 60;
        $last = $t - $t % $interval;
        $next = $last + $interval;
        // echo $next;exit;
        return $next;
    }
}