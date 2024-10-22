<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

if (!function_exists('request')) {
    function request($url, $max_attempts = 3, $initial_delay = 2){
		$attempt = 0;
		while ($attempt < $max_attempts) {
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
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($http_code == 200) {
				// Успешный ответ
				curl_close($ch);
				return json_decode($kp_api, true);
			} elseif ($http_code == 429) {
				// Превышен лимит запросов
				$attempt++;
				sleep($initial_delay);
			}
			curl_close ($ch);
		}
  		return json_decode($kp_api, true);
    }
}

if (!function_exists('LoadPage')) {
	function LoadPage($url, $method, $headers) {

		$options = array();
		$options['http'] = array(
			'method' => $method ,
			'header' => $headers   
		);
		$context = stream_context_create($options);
        $page = file_get_contents($url,false,$context);
	
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
	    
	    $_REQUEST['xfname'] = $xfparam[0];
		
	    $t_seite = $m_seite = intval($config['t_seite']);
	    if ( isset($xfparam[13]) && $xfparam[13] ) $t_size = $xfparam[13];
	    else $t_size = 0;
		$m_size = 0;
		if (isset($aaparser_config["images"]["poster_max_up_side"]) && $aaparser_config["images"]["poster_max_up_side"] != 0) $config['max_up_side'] = str_replace("х", "x", $aaparser_config["images"]["poster_max_up_side"]);
		else $config['max_up_side'] = $xfparam[9];
		$config['max_up_size'] = $xfparam[10];
		$config['min_up_side'] = $xfparam[22];
		$make_watermark = $xfparam[11] ? true : false;
		$make_thumb = $xfparam[12] ? true : false;
		$make_medium = false;
		$hidpi = false;

	    $t_size = explode("x", $t_size);
	    if (count($t_size) == 2) {
	    	$t_size = intval($t_size[0]) . "x" . intval($t_size[1]);
	    } else $t_size = intval($t_size[0]);

	    $m_size = explode("x", $m_size);
	    if (count($m_size) == 2) {
	    	$m_size = intval($m_size[0]) . "x" . intval($m_size[1]);
	    } else $m_size = intval($m_size[0]);

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
            
        $image = downloadImage($poster_url, $poster_title);   
        if ( isset($image) && $image ) {
            $exif = exif_read_data($image);

            $_FILES['qqfile'] = [
                'type' => $exif['MimeType'],
                'name' => $exif['FileName'],
                'tmp_name' => $image,
                'error' => 0,
                'size' => $exif['FileSize']
            ];
            
            $uploader = new FileUploader($area, $news_id, $author, $t_size, $t_seite, $make_thumb, $make_watermark, $m_size, $m_seite, $make_medium, $hidpi);
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

if (!function_exists('downloadImage')) {
    function downloadImage($imageUrl, $newFileName) {
        // Устанавливаем директорию для загрузки
        $uploadDir = ROOT_DIR . '/uploads/files/';

        // Создаем директорию, если она не существует
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
            chmod($uploadDir, 0777);
        }

        // Используем cURL для загрузки изображения
        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60 );
        curl_setopt($ch, CURLOPT_TIMEOUT, 60 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $imageData = curl_exec($ch);
        if(curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            return false;
        }
        curl_close($ch);

        // Определяем расширение файла на основе MIME-типа
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);
        $extension = '';

        switch ($mimeType) {
            case 'image/jpeg':
                $extension = 'jpeg';
                break;
            case 'image/jpg':
                $extension = 'jpg';
                break;
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/gif':
                $extension = 'gif';
                break;
            case 'image/webp':
                $extension = 'webp';
                break;
            default:
                return false; // неподдерживаемый тип изображения
        }

        // Генерируем новое имя файла с расширением
        $newFileNameWithExtension = $newFileName . '.' . $extension;

        // Путь к новому файлу
        $newFilePath = $uploadDir . $newFileNameWithExtension;

        // Сохраняем изображение в директорию
        file_put_contents($newFilePath, $imageData);
        chmod($newFilePath, 0777);

        // Возвращаем путь к новому файлу
        return $newFilePath;
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
	
	    if (is_array($langtranslit) AND count($langtranslit) ) $var = strtr($var, $langtranslit);

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
            if ( in_array($genres, $delete) ) {
				unset($oldgenres[$key]);
				continue;
			}
			$genres = trim(mb_strtolower($genres));
			if ($genres == 'военное' || $genres == 'военный') {
				unset($oldgenres[$key]);
				if (!in_array('военное', $oldgenres)) $oldgenres[] = 'военное';
				if (!in_array('военный', $oldgenres)) $oldgenres[] = 'военный';
			}
			if ($genres == 'история' || $genres == 'исторический') {
				unset($oldgenres[$key]);
				if (!in_array('история', $oldgenres)) $oldgenres[] = 'история';
				if (!in_array('исторический', $oldgenres)) $oldgenres[] = 'исторический';
			}
			if ($genres == 'детский' || $genres == 'детское') {
				unset($oldgenres[$key]);
				if (!in_array('детский', $oldgenres)) $oldgenres[] = 'детский';
				if (!in_array('детское', $oldgenres)) $oldgenres[] = 'детское';
			}
			if ($genres == 'игра' || $genres == 'игры') {
				unset($oldgenres[$key]);
				if (!in_array('игра', $oldgenres)) $oldgenres[] = 'игра';
				if (!in_array('игры', $oldgenres)) $oldgenres[] = 'игры';
			}
			if ($genres == 'игра' || $genres == 'игры') {
				unset($oldgenres[$key]);
				if (!in_array('игра', $oldgenres)) $oldgenres[] = 'игра';
				if (!in_array('игры', $oldgenres)) $oldgenres[] = 'игры';
			}
			if ($genres == 'психологическое' || $genres == 'психология') {
				unset($oldgenres[$key]);
				if (!in_array('психологическое', $oldgenres)) $oldgenres[] = 'психологическое';
				if (!in_array('психология', $oldgenres)) $oldgenres[] = 'психология';
			}
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
            'военный' => 'военный',
            'война' => 'война',
            'восточные единоборства' => 'восточные единоборства',
            'гарем' => 'гарем',
            'гурман' => 'гурман',
            'демоны' => 'демоны',
            'детектив' => 'детектив',
            'детский' => 'детский',
            'детское' => 'детское',
            'дзёсей' => 'дзёсей',
            'для взрослых' => 'для взрослых',
            'документальный' => 'документальный', 
            'драма' => 'драма',
            'еда' => 'гурман',
            'закон' => 'закон',
            'игра' => 'игра',
            'игры' => 'игры',
            'исторический' => 'исторический',
            'история' => 'история',
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
            'психология' => 'психология',
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
    global $shikimori_url_domain, $aaparser_config;
    
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