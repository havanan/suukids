<?php

namespace App\Repositories;


use App\Models\CloudfoneConfig;
use Log;
use Exception;
use DB;

class CloudfoneRepository extends BaseRepository {
    public function __construct() {
        
    }   
    
    function request($path, $method, $param = []) {
        $client = new \GuzzleHttp\Client();
        $url = env('CLOUDFONE_BASE_URL') . $path;
        $config = CloudfoneConfig::query()->where('shop_id', \getCurrentUser()->shop_id)->first();
        if (empty($config)) {
            throw new Exception('Bạn chưa cấu hình Cloudfone. Vui lòng kiểm tra lại.'); 
        }
        $param['ServiceName'] = $config->service_name;
        $param['AuthUser'] = $config->auth_user;
        $param['AuthKey'] = $config->auth_key;
        
        $options = [
            'headers' => [
                'accept' => 'application/json',
            ],
        ];
        
        if ($method == 'GET') {
            $options['query'] = $param;
        } else {
            $options['form_params'] = $param;
        }
        
        
        // Log::info($options);
        // Log::info($url);
        $response = $client->request($method, $url, $options);
        $body = \json_decode((string)$response->getBody());
        if (empty($body->result) || $body->result != "success") {
            $message = empty($body->message) ? $body->message : "Không rõ lỗi";
            throw new \Exception($message);
        }
        return $body;
    }
    
    public function makeACall($cloudfoneCode, $phone, $customer, $order = null) {
        $data = $this->request('/CloudFone/AutoCall', 'POST', [
            "Prefix" => 0,
            "Ext" => $cloudfoneCode,
            "PhoneName" => empty($customer->name) ? $phone : $customer->name,
            "PhoneNumber" => $phone
            // "PhoneName" => "Quan DEV Test",
            // "PhoneNumber" => "0888682696"
        ]);
    }
    
    public function getHistoryList($phone, $dateStart, $dateEnd, $pageIndex) {
        $data = $this->request('/CloudFone/GetCallHistory', 'POST', [
            // "TypeGet" => 2,
            "DateStart" => $dateStart,
            "DateEnd" => $dateEnd,
            "ReceiveNum" => $phone,
            "PageIndex" => $pageIndex,
            "PageSize" => 20,
        ]);
        
        if (empty($data)) {
            throw new \Exception('Không rõ lỗi');
        }
        
        return $data;
    }
}
