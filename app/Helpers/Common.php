<?php

namespace App\Helpers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use stdClass;

class Common
{
    protected static $messages;

    /**
     * @param $request
     * @param $type
     * @param $dataMessage
     * @throws \Throwable
     */
    public static function setMessage($request, $type, $dataMessage) {
        if(is_array($dataMessage)){
            static::$messages = static::array_values_recursive($dataMessage);
        }
        else{
            static::$messages = $dataMessage;
        }
        $request->session()->flash('alert-message', view('backend.elements.alert_message')->with([
            'type' => $type,
            'messages' => static::$messages,
        ])->render());
    }

    /**
     * @param $request
     * @return null
     */
    public static function getMessage($request) {
        $message = null;
        if($request->session()->has('alert-message')) {
            $message = $request->session()->get('alert-message');
        }
        return $message;
    }

    /**
     * @param $arr
     * @return array
     */
    public static function array_values_recursive($arr)
    {
        $lst = array();
        foreach( array_keys($arr) as $k ){
            $v = $arr[$k];
            if (is_scalar($v)) {
                $lst[] = $v;
            } elseif (is_array($v)) {
                $lst = array_merge( $lst,
                    static::array_values_recursive($v)
                );
            }
        }
        return $lst;
    }

    /**
     * Json Data for bootstrap table
     * @param $collection
     * @return false|string
     */
    public static function toJson($collection){
        $data = [];
        $data['total'] = $collection->total();
        $data['lastPage'] = $collection->lastPage();
        $data['perPage'] = $collection->perPage();
        $data['currentPage'] = $collection->currentPage();
        $data['rows'] = $collection->items();
        return json_encode($data);
    }

    /**
     * @param $params
     * @param null $table
     * Generate parameter for pagination
     * @return array
     */
    public static function toPagination($params, $table = null){
        $limit = 10;
        $sort = "id";
        $order = "desc";
        if (!empty($params['limit'])) {
            $limit = in_array($params['limit'], LIMIT_PAGINATE) ? intval($params['limit']) : 10;
        }
        if (!empty($params['sort'])) {
            $sort = $params['sort'];
        }
        if(!empty($table)){
            $sort = $table.".".$sort;
        }
        if (!empty($params['order'])) {
            $order = $params['order'];
        }
        return ['limit' => $limit,'sort' => $sort, 'order' => $order];
    }

    /**
     * Function: Get file url
     * @param $filename
     * @param $folder
     * @return string
     */
    public static function getFileUrl($filename, $folder, $type){
        $dir = !empty($folder) ? $folder : "";
        $imageType = "default";
        if(in_array($type,IMAGE_TYPE_FOLDER)){
            $imageType = IMAGE_TYPE_FOLDER[$type];
        }
        $url = "cdn/".$dir."/".$imageType. "/". $filename;
        if(!File::exists(public_path(). "/". $url)){
            return false;
        }
        return url($url);
    }

    /**
     * Function: Get file url
     * @param $filename
     * @param $folder
     * @return string
     */
    public static function getImageWithDefaultUrl($filename, $folder, $type){

        $dir = !empty($folder) ? $folder : "";
        if(empty($filename)){
            return url('assets/img/no_image.png');
        }
        $imageType = "default";
        if(in_array($type,array_flip(IMAGE_TYPE_FOLDER))){
            $imageType = IMAGE_TYPE_FOLDER[$type];
        }
        $url = "cdn/".$dir."/".$imageType. "/". $filename;
        $urlSmall = "cdn/".$dir."/small/". $filename;

        if(File::exists(public_path(). "/". $url)){
            return url($url);
        }elseif (File::exists(public_path(). "/". $urlSmall)){
            return url($urlSmall);
        }
        else{
            return url('assets/img/no_image.png');
        }

    }
    public static function checkValidPhoneNumber($str){
        return preg_match('/(0)[0-9]{9,10}$/', $str);
    }

    /**
     * Function: slugify
     * @param $str
     * @param array $options
     * @return string
     */
    public function slugify($str, $options = array())
    {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

        $defaults = array(
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => array(),
            'transliterate' => true,
        );

        // Merge options
        $options = array_merge($defaults, $options);

        // Lowercase
        if ($options['lowercase']) {
            $str = mb_strtolower($str, 'UTF-8');
        }

        $char_map = array(
            // Latin
            'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a',
            'ẵ' => 'a', 'ặ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a', 'đ' => 'd',
            'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e',
            'ễ' => 'e', 'ệ' => 'e', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i', 'ó' => 'o', 'ò' => 'o',
            'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
            'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o', 'ú' => 'u', 'ù' => 'u', 'ủ' => 'u',
            'ũ' => 'u', 'ụ' => 'u', 'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'ý' => 'y',
            'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y'
        );

        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }

        // Replace non-alphanumeric characters with our delimiter
//    $str = preg_replace('/[^p{L}p{Nd}]+/u', $options['delimiter'], $str);
        $str = preg_replace('/[[:space:]]+/', '-', $str);
        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $str;
    }

    public function arrayToString($arr = [])
    {
        $str = '';
        foreach ($arr as $item) {
            $str = $str .''. ucwords(strtolower($item)) .' ';
        }

        return $str;
    }
}
