<?php

namespace App\Services\Policeman;

class Token {
    private $token;
    private $randomNumberCount;
    private $rawToken;

    public function __construct(int $userId)
    {
        $this->rawToken = $userId;
    }

    private function setRandomNumberCount(): Token
    {
        $this->randomNumberCount = rand(100, 500);
        return $this;
    }

    private function combileRawTokenWithUniqId(): Token
    {
        $this->rawToken .= uniqid('token_', true);
        return $this;
    }

    private function generateRandomStringByRandomNumberCount(): Token
    {
        $randomString = '_' . rand(999, 99999);
        for ($count=0;$count<=$this->randomNumberCount;$count++) {
            $randomString .= '-' . rand(1, 100);
        }
        $this->rawToken .= $randomString;

        return $this;
    }

    private function convertToHash(): Token
    {
        $this->token = hash('haval256,5', $this->rawToken);
        return $this;
    }

    public function generateToken(): string
    {
        return $this->setRandomNumberCount()
            ->generateRandomStringByRandomNumberCount()
                ->combileRawTokenWithUniqId()
                    ->convertToHash()
                        ->getToken();
    }

    private function getToken(): string
    {
        return $this->token;
    }
}
