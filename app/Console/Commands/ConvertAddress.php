<?php

namespace App\Console\Commands;

use App\Helpers\Common;
use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Log;

class ConvertAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert address text to slug';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $provinces = Province::all();
            foreach ($provinces as $province) {
                $slug = $this->to_slug($province->_name);
                $province->province_slug = $slug;
                $province->update();
            };

            $districts = District::all();
            foreach ($districts as $district) {
                $slug = $this->to_slug($district->_prefix .' '.$district->_name);
                $district->district_slug = $slug;
                $district->update();
            }

            $wards = Ward::all();
            foreach ($wards as $ward) {
                $slug = $this->to_slug($ward->_prefix .' '. $ward->_name);
                $ward->ward_slug = $slug;
                $ward->update();
            }
            DB::commit();
        } catch (\Exception $exception) {
            Log::debug('Console::ConvertAddress:: '. $exception->getMessage());
            echo $exception->getMessage();
        }
    }

    function url_slug($str, $options = array())
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
            '??' => 'a', '??' => 'a', '???' => 'a', '??' => 'a', '???' => 'a', '??' => 'a', '???' => 'a', '???' => 'a', '???' => 'a',
            '???' => 'a', '???' => 'a', '??' => 'a', '???' => 'a', '???' => 'a', '???' => 'a', '???' => 'a', '???' => 'a', '??' => 'd',
            '??' => 'e', '??' => 'e', '???' => 'e', '???' => 'e', '???' => 'e', '??' => 'e', '???' => 'e', '???' => 'e', '???' => 'e',
            '???' => 'e', '???' => 'e', '??' => 'i', '??' => 'i', '???' => 'i', '??' => 'i', '???' => 'i', '??' => 'o', '??' => 'o',
            '???' => 'o', '??' => 'o', '???' => 'o', '??' => 'o', '???' => 'o', '???' => 'o', '???' => 'o', '???' => 'o', '???' => 'o',
            '??' => 'o', '???' => 'o', '???' => 'o', '???' => 'o', '???' => 'o', '???' => 'o', '??' => 'u', '??' => 'u', '???' => 'u',
            '??' => 'u', '???' => 'u', '??' => 'u', '???' => 'u', '???' => 'u', '???' => 'u', '???' => 'u', '???' => 'u', '??' => 'y',
            '???' => 'y', '???' => 'y', '???' => 'y', '???' => 'y'
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

    function to_slug($str) {
        $str = trim(mb_strtolower($str));
        $str = str_replace('-', ' ', $str);
        $str = preg_replace('/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/', 'a', $str);
        $str = preg_replace('/(??|??|???|???|???|??|???|???|???|???|???)/', 'e', $str);
        $str = preg_replace('/(??|??|???|???|??)/', 'i', $str);
        $str = preg_replace('/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/', 'o', $str);
        $str = preg_replace('/(??|??|???|???|??|??|???|???|???|???|???)/', 'u', $str);
        $str = preg_replace('/(???|??|???|???|???)/', 'y', $str);
        $str = preg_replace('/(??)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }
}
