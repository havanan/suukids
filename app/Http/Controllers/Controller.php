<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function statusOK()
    {
        return response(json_encode(['status' => "OK"]), HTTP_STATUS_SUCCESS);
    }

    public function statusNG()
    {
        return response(json_encode(['status' => "Bad Request"]), HTTP_STATUS_BAD_REQUEST);
    }

    public function responseWithErrorMessage($message = null)
    {
        return response(json_encode(['message' => $message]), HTTP_STATUS_BAD_REQUEST);
    }

    public function responseWithSuccessMessage($message = null)
    {
        return response(json_encode(['message' => $message]), HTTP_STATUS_SUCCESS);
    }

    /**
     * Generate a "random" alpha-numeric string.
     *
     * Should not be considered sufficient for cryptography, etc.
     *
     * @param  int  $length
     * @return string
     */
    public function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    public function hashRandom()
    {
        return md5(Hash::make(time() . Auth::id() . rand(0, 10000)));
    }

    public function redirectWithError($request, $url, $message = null, $withInput = false)
    {
        Common::setMessage($request, 'danger', !empty($message) ? $message : "Some error have been detected on the server!");
        if ($withInput) {
            return redirect($url)->withInput();
        }
        return redirect($url);
    }

    public function redirectWithSuccess($request, $url, $message = null)
    {
        Common::setMessage($request, 'success', !empty($message) ? $message : "Task was successful!");
        return redirect($url);
    }

    public function escapeSpace($str)
    {
        return preg_replace("@[ 　]@u", ' ', urldecode($str));
    }

    /**
     * @param $file
     * @param $options
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveImageCDN($file, $options)
    {

        $date = new DateTime();

        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, ACCESS_IMAGE_EXTENSION)) {
            return redirect()->back(403, 'Extension not except');
        }

        $filename = $date->getTimestamp() . "_" . substr(md5($file->getClientOriginalName()), 0, 8) . "." . $extension;

        $file_default = "cdn/files/" . $filename;
        $file_small = "cdn/thumbs/" . $filename;
        $file_original = "upload/files/";
        if (!empty($options['folder'])) {
            $file_default = "cdn/" . $options['folder'] . "/default/" . $filename;
            $file_small = "cdn/" . $options['folder'] . "/small/" . $filename;
        }
        $image = Image::make($file);
        $width = !empty($options['width']) ? $options['width'] : $image->width();
        $height = !empty($options['height']) ? $options['height'] : $image->height();
        switch ($options['type']) {
            case 'teacher':
                $file_original = "upload/teachers/";
                break;
            case 'blog':
                $file_original = "upload/blogs/";
                break;
            case 'advertise':
                $file_original = "upload/advertise/";
                break;
            case 'payment-gates':
                $file_original = "upload/payment-gates/";
                break;
            case 'course':
                $file_original = "upload/courses/";
                break;
            case 'lesson':
                $file_original = "upload/lessons/";
                break;
            case 'popup':
                $file_original = "upload/popups/";
                break;
            case 'slider':
                $file_original = "upload/sliders/";
                break;
            case 'book':
                $file_original = "upload/books/";
                break;
            case 'course_banners':
                $file_original = "upload/course_banners/";
                break;
            case 'item':
                $file_original = "upload/items/";
                break;
        }
        $image->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        })->save($file_default);
        $smallWidth = !empty($options['small_width']) ? $options['small_width'] : 50;
        $smallHeight = !empty($options['small_height']) ? $options['small_height'] : 50;
        $image->resize($smallWidth, $smallHeight)->save($file_small);
        $file->move($file_original, $filename);
        return $filename;
    }
    public function deleteImage($imageName, array $folders)
    {
        if (empty($imageName) || empty($folders)) {
            return false;
        }
        if (!empty($folders['cdn'])) {
            $file = public_path('cdn/' . $folders['cdn'] . '/default/' . $imageName);
            $fileSmall = public_path('cdn/' . $folders['cdn'] . '/small/' . $imageName);

            if (File::exists($file, $fileSmall)) {
                File::delete($file, $fileSmall);
            }
        }
        if (!empty($folders['original'])) {
            $file = public_path('upload/' . $folders['original'] . '/' . $imageName);
            if (File::exists($file)) {
                File::delete($file);
            }
        }
    }

    public function saveNoReizeImageCDN($file, $options)
    {

        $date = new DateTime();

        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, ACCESS_IMAGE_EXTENSION)) {
            return redirect()->back(403, 'Extension not except');
        }

        $filename = $date->getTimestamp() . "_" . substr(md5($file->getClientOriginalName()), 0, 8) . "." . $extension;

        // di chuyển file đến thư mục theo size ảnh
        $file->move("cdn/" . $options['folder'] . "/" . $options['image_type'], $filename);

        //di chuyển file đến thư mục upload
        //        $file->move($file_original, $filename);
        return $filename;
    }

    public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    public  function uploadImage($image,$path){
        $date = new \DateTime();
        $checkSize = filesize($image) / 1000000;

        // Không cho phép ảnh có dung lượng quá 8MB
        if ($checkSize >= 8) {
            return response()->json(['error' => "image size too large"], HTTP_STATUS_BAD_REQUEST);
        }
        $extension = strtolower($image->extension());

        // Tạo tên ảnh
        $imageName = $date->getTimestamp() . "_" .
            substr(md5(Auth::guard('users')->id()), 0, 3) .
            substr(md5('avatar'), 0, 3) . '_' .
            rand(10000, 99999) .'.'.
            $extension;

        // Copy dữ liệu vào thư mục Storage
        $target = '../storage/app/public/'. $imageName;
        if (!file_exists($target)) {
            copy($image, $target);
        }
        // Copy dữ liệu vào CDN
        $options = [
            'path' => $path,
            'imageName' => $imageName
        ];
        $target = $this->uploadImageCDN($image,$options);
        return $target;
    }
    public function uploadImageCDN($file, $options){
        $path = $options['path'];
        $imageName = $options['imageName'];
        //file path
        $file_path_default = 'cdn/'.$path;
        $target = 'cdn/'.$path.'/' . $imageName;
        // Tạo folder neu chua ton tai
        if (!file_exists($file_path_default)) mkdir($file_path_default, 0755, true);
        // Copy
        if (!file_exists($target)) {
            copy($file, $target);
        }
        return $target;
    }
    public function deleteFile($imageName,$path){
        if ($imageName == null){
            return false;
        }
        $target = 'cdn/'.$path.'/' . $imageName;
        // Xóa file trong cdn
        if (file_exists($target)) {
            unlink($target);
            return true;
        }
        return false;
    }
    public function deleteImageWithPath($path){

        // Xóa file trong cdn
        if (file_exists($path)) {
            unlink($path);
            return true;
        }
        return false;
    }
}
