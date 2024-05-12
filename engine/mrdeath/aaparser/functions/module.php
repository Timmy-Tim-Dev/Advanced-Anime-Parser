<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

if (!function_exists('request')) {
    function request($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60 );
        curl_setopt($ch, CURLOPT_TIMEOUT, 60 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        $headers = [
    		'Content-Type: application/json'
		];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$kp_api = curl_exec ($ch);
		curl_close ($ch);
  
  		return json_decode($kp_api, true);
    }
}

if (!function_exists('LoadPage')) {
	function LoadPage($url, $method, $headers) {

		$options = array();
		$options['http'] = array('method' => $method ,
                             'header' => $headers   );
		$context = stream_context_create($options);
        $page    = file_get_contents($url,false,$context);
		

		return $page;
	}
}

if (!function_exists('check_if')) {
    function check_if($check_value, $dataArray) {
        $tags_array = array();
        foreach($dataArray as $named => $zna4enie) {
			
			$check_value = str_replace("{".$named."}", $zna4enie, $check_value);

            if (strpos(strval($check_value), '[if_'.$named.']') !== false) {
                if ($zna4enie) $check_value = preg_replace(';\[if_'.$named.'\](.*?)\[\/if_'.$named.'\];ius', '$1', $check_value);
                else $check_value = preg_replace(';\[if_'.$named.'\](.*?)\[\/if_'.$named.'\];ius', '', $check_value);
            }
            if (strpos(strval($check_value), '[ifnot_'.$named.']') !== false) {
                if ($zna4enie) $check_value = preg_replace(';\[ifnot_'.$named.'\](.*?)\[\/ifnot_'.$named.'\];ius', '', $check_value);
                else $check_value = preg_replace(';\[ifnot_'.$named.'\](.*?)\[\/ifnot_'.$named.'\];ius', '$1', $check_value);
            }
            $tags_array[] = '{'.$named.'}';
        }
        $check_value = str_ireplace( $tags_array, $dataArray, $check_value);
    	return $check_value;
    }
}

if (!function_exists('generate_numbers')) {
    function generate_numbers($number, $type) {
		$number = intval($number);
		$type = intval($type);
        if ( $type == 1 ) {
			if ( $number == 1 ) $generate_numbers = '1';
			elseif( $number == 2 ) $generate_numbers = '1,2';
			elseif( $number > 2 ) $generate_numbers = '1-'.$number;
		}
		elseif ( $type == 2 ) {
			if ( $number == 1 ) $generate_numbers = '1';
			elseif( $number == 2 ) $generate_numbers = '1,2';
			elseif( $number == 3 ) $generate_numbers = '1,2,3';
			elseif( $number > 3 ) $generate_numbers = '1-'.($number-1).','.$number;
		}
		elseif ( $type == 3 ) {
			if ( $number == 1 ) $generate_numbers = '1';
			elseif( $number == 2 ) $generate_numbers = '1,2';
			elseif( $number == 3 ) $generate_numbers = '1,2,3';
			elseif( $number == 4 ) $generate_numbers = '1,2,3,4';
			elseif( $number > 4 ) $generate_numbers = '1-'.($number-2).','.($number-1).','.$number;
		}
		elseif ( $type == 4 ) {
			if ( $number == 1 ) $generate_numbers = '1';
			elseif( $number > 1 ) {
				$number_mas = array();
				for ($i = 1; $i <= $number; $i++) {
					$number_mas[] = $i;
				}
				$generate_numbers = implode(",", $number_mas);
			}
		}
		elseif ( $type == 5 ) {
			if ( $number == 1 ) $generate_numbers = '1,2';
			elseif( $number == 2 ) $generate_numbers = '1-3';
			elseif( $number > 2 ) $generate_numbers = '1-'.($number+1);
		}
		elseif ( $type == 6 ) {
			if ( $number == 1 ) $generate_numbers = '1,2';
			elseif( $number == 2 ) $generate_numbers = '1,2,3';
			elseif( $number == 3 ) $generate_numbers = '1-3,4';
			elseif( $number > 3 ) $generate_numbers = '1-'.$number.','.($number+1);
		}
		elseif ( $type == 7 ) {
			if ( $number == 1 ) $generate_numbers = '1,2';
			elseif( $number == 2 ) $generate_numbers = '1,2,3';
			elseif( $number == 3 ) $generate_numbers = '1,2,3,4';
			elseif( $number == 4 ) $generate_numbers = '1-3,4,5';
			elseif( $number > 4 ) $generate_numbers = '1-'.($number-1).','.$number.','.($number+1);
		}
		elseif ( $type == 8 ) {
			$number_mas = array();
			for ($i = 1; $i <= ($number+1); $i++) {
				$number_mas[] = $i;
			}
			$generate_numbers = implode(",", $number_mas);
		}
    	return $generate_numbers;
    }
}

if (!function_exists('xfieldsdatasaved')) {
    function xfieldsdatasaved($xfields) {
        $filecontents = [];
        foreach ($xfields as $xfielddataname => $xfielddatavalue) {
            if ($xfielddatavalue === '') continue;
            $xfielddataname = str_replace( "|", "&#124;", $xfielddataname);
            $xfielddataname = str_replace( "\r\n", "__NEWL__", $xfielddataname);
            $xfielddatavalue = str_replace( "|", "&#124;", $xfielddatavalue);
            $xfielddatavalue = str_replace( "\r\n", "__NEWL__", $xfielddatavalue);
            $filecontents[] = $xfielddataname."|".$xfielddatavalue;
        }
        $filecontents = join('||', $filecontents );
        return $filecontents;
    }
}

if (!function_exists('xfparamload')) {
    function xfparamload( $xfname ) {
        $path = ENGINE_DIR . '/data/xfields.txt';
        $filecontents = file( $path );
        
        foreach ( $filecontents as $name => $value ) {
            $filecontents[$name] = explode( "|", trim( $value ) );
            if($filecontents[$name][0] == $xfname ) return $filecontents[$name];
        }
        return false;
    }    
}


if (!function_exists('setPoster')) {
    function setPoster($poster_url, $poster_title, $image_kind, $poster_name = false, $news_id = 0) {
	
	    global $config, $aaparser_config, $kp_config, $db, $member_id, $user_group;
	
	    $area = 'xfieldsimage';
	
	    if ( $poster_name ) {
	    	$xfparam = xfparamload($poster_name);
	    }
	    else $xfparam = [];
		
	    $xfname = $xfparam[0];
	    $t_seite = (int)$config['t_seite'];
	    $m_seite = $t_seite;
	    $t_size = $xfparam[13];
	    $m_size = 0;
	    if (isset($xfparam[9])) $config['max_up_side'] = $xfparam[9];
	    elseif ( $image_kind == 'poster' ) $config['max_up_side'] = $aaparser_config['images']['poster_max_up_side'];
	    elseif ( $image_kind == 'kadr' ) $config['max_up_side'] = $aaparser_config['images']['screens_max_up_side'];
	    elseif ( $image_kind == 'logo' ) $config['max_up_side'] = $aaparser_config['images']['logo_max_up_side'];
	    elseif ( $image_kind == 'cover' ) $config['max_up_side'] = $aaparser_config['images']['cover_max_up_side'];
	    $config['max_up_size'] = 2048;
	    $config['min_up_side'] = 0;
	    $make_watermark = (bool)$xfparam[11];
	    $make_thumb = (bool)$xfparam[12];
	    $make_medium = false;

	    $t_size = explode("x", $t_size);
	    if (count($t_size) == 2) {
	    	$t_size = (int)$t_size[0] . "x" . (int)$t_size[1];
	    } else $t_size = (int)$t_size[0];

	    $m_size = explode("x", $m_size);
	    if (count($m_size) == 2) {
	    	$m_size = (int)$m_size[0] . "x" . (int)$m_size[1];
	    } else $m_size = (int)$m_size[0];

        $author = $db->safesql($member_id['name']);
        
        $temp_dir = ROOT_DIR . "/uploads/posts/" . date( "Y-m" ) .'/';
        
        if( !is_dir( $temp_dir ) ) {
            @mkdir( $temp_dir, 0777 );
            @chmod( $temp_dir, 0777 );
        }
        else @chmod( $temp_dir, 0777 );
        
        if( !is_dir( $temp_dir.'thumbs/' ) ) {
            @mkdir( $temp_dir.'thumbs/', 0777 );
            @chmod( $temp_dir.'thumbs/', 0777 );
        }
        else @chmod( $temp_dir.'thumbs/', 0777 );
        
        if( !is_dir( $temp_dir.'medium/' ) ) {
            @mkdir( $temp_dir.'medium/', 0777 );
            @chmod( $temp_dir.'medium/', 0777 );
        }
        else @chmod( $temp_dir.'medium/', 0777 );
            
        $poster_title = totranslit(stripslashes( $poster_title ), true, false);
            
        $new_poster = ROOT_DIR . '/uploads/files/' . $poster_title;
            
        $image = request_file($poster_url, $new_poster);
            
        if ( isset($image) && $image ) {
            $exif = exif_read_data($image);

            $_FILES['qqfile'] = [
                'type' => $exif['MimeType'],
                'name' => $exif['FileName'],
                'tmp_name' => $image,
                'error' => 0,
                'size' => $exif['FileSize']
            ];
            
            $uploader = new FileUploader($area, $news_id, $author, $t_size, $t_seite, $make_thumb, $make_watermark, $m_size, $m_seite, $make_medium);
            $result = json_decode($uploader->FileUpload(), true);

            @unlink($image);
            return $result;
        }
        else {
            @unlink($image);
            return '';
        }
    }
}

if (!function_exists('request_file')) {
    function request_file($url, $file = false){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60 );
        curl_setopt($ch, CURLOPT_TIMEOUT, 60 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $headers = array(
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.2924.87 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'Connection: keep-alive',
            'Cache-Control: max-age=0',
            'Upgrade-Insecure-Requests: 1'
        );
        if($file){
			@chmod( ROOT_DIR . "/uploads/files/", 0777 );
            $fp = fopen($file, "wb");
            curl_setopt($ch, CURLOPT_FILE, $fp);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $res = curl_exec($ch);
        curl_close($ch);
        if($file) {
            fclose($fp);
            @chmod($file, 0777);
            $info = @getimagesize($file);
            if(is_array($info)){
                if( $info[2] == 2 ) {
                    $ext = 'jpg';
                } elseif( $info[2] == 3 ) {
                    $ext =  'png';
                } elseif( $info[2] == 1 ) {
                    $ext = 'gif';
                } elseif($info['mime'] == 'image/webp' or $info['mime'] == 'image/x-webp') {
                    $ext = 'webp';    
                } else $ext = 'jpg';
                $GLOBALS['EXT'] = $ext;
                rename($file, $file.'.'.$ext);
                return $file.'.'.$ext;
            } else {
                @unlink($file);
                return false;
            }
        }
        return $res;
    }
}

if (!function_exists('totranslit_it')) {
    function totranslit_it($var, $lower = true, $punkt = true) {
	
$langtranslit = array(
	'а' => 'a', 'б' => 'b', 'в' => 'v',
	'г' => 'g', 'д' => 'd', 'е' => 'e',
	'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
	'и' => 'i', 'й' => 'j', 'к' => 'k',
	'л' => 'l', 'м' => 'm', 'н' => 'n',
	'о' => 'o', 'п' => 'p', 'р' => 'r',
	'с' => 's', 'т' => 't', 'у' => 'u',
	'ф' => 'f', 'х' => 'h', 'ц' => 'c',
	'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
	'ь' => '', 'ы' => 'y', 'ъ' => '',
	'э' => 'je', 'ю' => 'ju', 'я' => 'ja',
	"ї" => "yi", "є" => "ye",
	
	'А' => 'A', 'Б' => 'B', 'В' => 'V',
	'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
	'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
	'И' => 'I', 'Й' => 'J', 'К' => 'K',
	'Л' => 'L', 'М' => 'M', 'Н' => 'N',
	'О' => 'O', 'П' => 'P', 'Р' => 'R',
	'С' => 'S', 'Т' => 'T', 'У' => 'U',
	'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
	'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
	'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
	'Э' => 'Je', 'Ю' => 'Ju', 'Я' => 'Ja',
	"Ї" => "yi", "Є" => "ye", 
	"À"=>"A", "à"=>"a", "Á"=>"A", "á"=>"a", 
	"Â"=>"A", "â"=>"a", "Ä"=>"A", "ä"=>"a", 
	"Ã"=>"A", "ã"=>"a", "Å"=>"A", "å"=>"a", 
	"Æ"=>"AE", "æ"=>"ae", "Ç"=>"C", "ç"=>"c", 
	"Ð"=>"D", "È"=>"E", "è"=>"e", "É"=>"E", 
	"é"=>"e", "Ê"=>"E", "ê"=>"e", "Ì"=>"I", 
	"ì"=>"i", "Í"=>"I", "í"=>"i", "Î"=>"I", 
	"î"=>"i", "Ï"=>"I", "ï"=>"i", "Ñ"=>"N", 
	"ñ"=>"n", "Ò"=>"O", "ò"=>"o", "Ó"=>"O", 
	"ó"=>"o", "Ô"=>"O", "ô"=>"o", "Ö"=>"O", 
	"ö"=>"o", "Õ"=>"O", "õ"=>"o", "Ø"=>"O", 
	"ø"=>"o", "Œ"=>"OE", "œ"=>"oe", "Š"=>"S", 
	"š"=>"s", "Ù"=>"U", "ù"=>"u", "Û"=>"U", 
	"û"=>"u", "Ú"=>"U", "ú"=>"u", "Ü"=>"U", 
	"ü"=>"u", "Ý"=>"Y", "ý"=>"y", "Ÿ"=>"Y", 
	"ÿ"=>"y", "Ž"=>"Z", "ž"=>"z", "Þ"=>"B", 
	"þ"=>"b", "ß"=>"ss", "£"=>"pf", "¥"=>"ien", 
	"ð"=>"eth", "ѓ"=>"r"
);
	
	    if ( is_array($var) ) return "";

	    $var = str_replace(chr(0), '', $var);
	
	    $var = trim( strip_tags( $var ) );
	    $var = preg_replace( "/\s+/u", "-", $var );
	    $var = str_replace( "/", "-", $var );
	
	    if (is_array($langtranslit) AND count($langtranslit) ) {
	    	$var = strtr($var, $langtranslit);
	    }

	    if ( $punkt ) $var = preg_replace( "/[^a-z0-9\_\-.]+/mi", "", $var );
	    else $var = preg_replace( "/[^a-z0-9\_\-]+/mi", "", $var );

	    $var = preg_replace( '#[\-]+#i', '-', $var );
	    $var = preg_replace( '#[.]+#i', '.', $var );

	    if ( $lower ) $var = strtolower( $var );

	    $var = str_ireplace( ".php", "", $var );
	    $var = str_ireplace( ".php", ".ppp", $var );

	    if( strlen( $var ) > 200 ) {
		
	    	$var = substr( $var, 0, 200 );
		
	    	if( ($temp_max = strrpos( $var, '-' )) ) $var = substr( $var, 0, $temp_max );
	
	    }
	
	    return $var;
    }
}

if (!function_exists('RenameGenres')) {
    function RenameGenres($oldgenres) {
        
        $new_genres = [];
        
        $delete = ['аниме', 'мультфильм', 'хентай', 'юри', 'яой'];
        
        foreach ( $oldgenres as $key => $genres ) {
            if ( in_array($genres, $delete) ) unset($oldgenres[$key]);
        }

        $fromto = [
            'sci-fi' => 'sci-fi',
            'безумие' => 'безумие',
            'бизнес' => 'бизнес',
            'биография' => 'биография',
            'боевик' => 'боевик',
            'боевые искусства' => 'боевые искусства',
            'вампиры' => 'вампиры',
            'вестерн' => 'вестерн',
            'взрослая жизнь' => 'взрослая жизнь',
            'военное' => 'военное',
            'военный' => 'военное',
            'война' => 'война',
            'восточные единоборства' => 'восточные единоборства',
            'гарем' => 'гарем',
            'гурман' => 'гурман',
            'демоны' => 'демоны',
            'детектив' => 'детектив',
            'детский' => 'детское',
            'детское' => 'детское',
            'дзёсей' => 'дзёсей',
            'для взрослых' => 'для взрослых',
            'документальный' => 'документальный', 
            'драма' => 'драма',
            'еда' => 'гурман',
            'закон' => 'закон',
            'игра' => 'игры',
            'игры' => 'игры',
            'исторический' => 'исторический',
            'история' => 'исторический',
            'комедия' => 'комедия',
            'концерт' => 'концерт',
            'короткометражка' => 'короткометражка',
            'космос' => 'космос',
            'криминал' => 'криминал',
            'магия' => 'магия',
            'машины' => 'машины',
            'медицина' => 'медицина',
            'мелодрама' => 'мелодрама',
            'меха' => 'меха',
            'мистика' => 'мистика',
            'молодость' => 'молодость',
            'музыка' => 'музыка',
            'мюзикл' => 'мюзикл',
            'пародия' => 'пародия',
            'повседневность' => 'повседневность',
            'политика' => 'политика',
            'полиция' => 'полиция',
            'приключения' => 'приключения',
            'психологическое' => 'психологическое',
            'психология' => 'психологическое',
            'работа' => 'работа',
            'реальное тв' => 'реальное тв',
            'романтика' => 'романтика',
            'самураи' => 'самураи',
            'сверхъестественное' => 'сверхъестественное',
            'семейный' => 'семейный',
            'ситком' => 'ситком',
            'спорт' => 'спорт',
            'супер сила' => 'супер сила',
            'сэйнэн' => 'сэйнэн',
            'сёдзё' => 'сёдзё',
            'сёдзё-ай' => 'сёдзё-ай',
            'сёнен' => 'сёнен',
            'сёнен-ай' => 'сёнен-ай',
            'ток-шоу' => 'ток-шоу',
            'триллер' => 'триллер',
            'ужасы' => 'ужасы',
            'фантастика' => 'фантастика',
            'фильм-нуар' => 'фильм-нуар',
            'фэнтези' => 'фэнтези',
            'школа' => 'школа',
            'экшен' => 'экшен',
            'эротика' => 'эротика',
            'этти' => 'этти'
        ];

        foreach ($oldgenres as $key => $genre) {
            $genre = mb_strtolower($genre, 'UTF-8');
            if ( $fromto[$genre] ) $new_genres[$key] = $fromto[$genre];
            else $new_genres[$key] = $genre;
        }

        return array_unique($new_genres);
    }
}

function change_tags ($type, $needVal, $nameTag, $urlPrefix = '') {
    global $shikimori_url_domain;
    
    if ($needVal) {
        $type->set("[" . $nameTag . "]", "");
        $type->set("[/" . $nameTag . "]", "");
        $fullUrl = ($urlPrefix != '') ? $urlPrefix . $needVal : $needVal;
        $type->set("{" . $nameTag . "}", $fullUrl);
        $type->set_block("'\\[not_" . $nameTag . "\\](.*?)\\[/not_" . $nameTag . "\\]'si", "");
    } else {
        $type->set("[not_" . $nameTag . "]", "");
        $type->set("[/not_" . $nameTag . "]", "");
        $type->set_block("'\\[" . $nameTag . "\\](.*?)\\[/" . $nameTag . "\\]'si", "");
        $type->set("{" . $nameTag . "}", '');
    }
}

function change_tags_img($type, $needVal, $nameTag, $defaultImage = '') {
    global $shikimori_url_domain, $aaparser_config_push;
    
    if ($needVal) {
        $type->set("[" . $nameTag . "]", "");
        $type->set("[/$" . $nameTag . "]", "");
        $fullUrl = 'https://' . $shikimori_url_domain . $needVal;
        $type->set("{" . $nameTag . "}", $fullUrl);
        $type->set_block("'\\[not_" . $nameTag . "\\](.*?)\\[/not_" . $nameTag . "\\]'si", "");
    } elseif ($defaultImage) {
        $type->set("[" . $nameTag . "]", "");
        $type->set("[/" . $nameTag . "]", "");
        $type->set("{" . $nameTag . "}", $defaultImage);
        $type->set_block("'\\[not_" . $nameTag . "\\](.*?)\\[/not_" . $nameTag . "\\]'si", "");
    } else {
        $type->set("[not_" . $nameTag . "]", "");
        $type->set("[/not_" . $nameTag . "]", "");
        $type->set_block("'\\[" . $nameTag . "\\](.*?)\\[/" . $nameTag . "\\]'si", "");
        $type->set("{" . $nameTag . "}", "");
    }
}