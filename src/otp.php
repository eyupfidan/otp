<?php

namespace Netgsm\Otp;

use Exception;
use Ramsey\Uuid\Type\Integer;
use SimpleXMLElement;

class OTPService
{
    private string $username;
    private string $password;
    private string $header;

    const API_URL = 'https://api.netgsm.com.tr/sms/send/otp';
    const TIMEOUT = 30;

    public function __construct(array $config)
    {
        $this->username = $config['username'] ?? 'x';
        $this->password = $config['password'] ?? 'x';
        $this->header = $config['header'] ?? 'x';
    }

    public function sendOTP(array $data): array
    {
        $this->validateData($data);

        $header = $data['header'] ?? $this->header;
        if (empty($header)) {
            throw new Exception('Header information is missing or invalid.');
        }

        $xmlData = $this->buildXMLData($data['message'], $data['no'], $header);
        $response = $this->executeCurl($xmlData);
        return $this->parseResponse($response);
    }

    private function validateData(array $data): void
    {
        if (empty($data['message'])) {
            throw new Exception('Please provide a message.');
        }

        if (empty($data['no'])) {
            throw new Exception('Please provide a number.');
        }
    }

    private function buildXMLData(string $message, string $number, string $header): string
    {
        return "<?xml version='1.0'?>
        <mainbody>
            <header>
                <usercode>{$this->username}</usercode>
                <password>{$this->password}</password>
                <msgheader>{$header}</msgheader>
            </header>
            <body>
                <msg>{$message}</msg>
                <no>{$number}</no>
            </body>
        </mainbody>";
    }

    private function executeCurl(string $xmlData): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: text/xml"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('CURL error: ' . curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }

    private function parseResponse(string $result): array
    {
        $parsedResult = new SimpleXMLElement($result);
        $code = strval($parsedResult->main->code);
        $errorCodes = [
            20 => 'Check your message text or length.',
            30 => 'Invalid username, password, or no API access.',
            40 => 'Check your sender ID.',
            50 => 'Check the recipient number.',
            60 => 'No OTP SMS Package assigned to your account.',
            70 => 'Check your input parameters.',
            80 => 'Query limit exceeded.',
            100 => 'System error.',
        ];

        if (array_key_exists($code, $errorCodes)) {
            return ['status' => $errorCodes[$code], 'code' => $code];
        }

        return [
            'status' => 'Sending successful.',
            'jobId' => strval($parsedResult->main->jobID[0])
        ];
    }
}
