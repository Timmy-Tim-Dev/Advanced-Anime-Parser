<?php

if (!defined("DATALIFEENGINE")) {
    exit("Hacking attempt!");
}

if (!function_exists('limitTextLength')) {
	function limitTextLength($text, $count) {
		$textLength = iconv_strlen($text, "utf-8");
		if ($count < $textLength) $text = iconv_substr($text, 0, $count, "utf-8");
		return $text;
	}
}

if (!function_exists('sanitizeText')) {
	function sanitizeText($text) {
		$text = stripslashes($text);
		$text = str_replace('<br>', '', $text);
		$text = str_replace('<br />', '', $text);
		$text = str_replace('<p>', '', $text);
		$text = str_replace('</p>', '', $text);
		$text = str_replace('<span>', '', $text);
		$text = str_replace('</span>', '', $text);
		$text = html_entity_decode($text, ENT_QUOTES, "UTF-8");
		$text = preg_replace("'\\[attachment=(.*?)\\]'si", "", $text);
		$text = preg_replace("#\\[hide\\](.+?)\\[/hide\\]#ims", "", $text);
		return $text;
	}
}

if (!function_exists('postingCheckXfvalue')) {
	function postingCheckXfvalue($matches = []) {
		global $xfieldsdata;
		global $preg_safe_name;
		global $value;

		if (!empty($matches[1])) {
			$matches[1] = trim($matches[1]);
			if (preg_match("#" . $preg_safe_name . "\\s*\\!\\=\\s*['\"](.+?)['\"]#i",$matches[1],$match)) return $xfieldsdata[$value[0]] != trim($match[1])? $matches[2]: "";
			if (preg_match("#" . $preg_safe_name . "\\s*\\=\\s*['\"](.+?)['\"]#i",$matches[1],$match)) return $xfieldsdata[$value[0]] == trim($match[1])? $matches[2]: "";
		}

		return $matches[0];
	}
}

if (!function_exists('processImages')) {
	function processImages($images, $homeUrl) {
		$processedImages = [];
		$processedImages["external"] = [];

		foreach ($images as $key => $image) {
			if (substr($image, 0, 1) == "/") {
				$processedImages[$key] = ROOT_DIR . str_replace(["thumbs/", "medium/"], "", $image);
			} else {
				if (strpos($image, $homeUrl) === false) {
					$processedImages["external"][] = $key;
					$imageExtension = pathinfo($image, PATHINFO_EXTENSION);
					$file = ENGINE_DIR . "/cache/posting_temp_" . $key . "." . $imageExtension;
					$ch = curl_init($image);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
					curl_setopt($ch, CURLOPT_USERAGENT, "Googlebot-Image/1.0");
					curl_setopt($ch, CURLOPT_REFERER, $homeUrl);
					$imageData = curl_exec($ch);
					$fileHandle = fopen($file, "w");
					fwrite($fileHandle, $imageData);
					fclose($fileHandle);
					$processedImages[$key] = $file;
				} else $processedImages[$key] = ROOT_DIR . str_replace( [$homeUrl, "/thumbs/", "/medium/"], "/", $image );
			}
		}
		return $processedImages;
	}
}

if (!function_exists('makeCurlRequest')) {
	function makeCurlRequest($url, $postParams = [], $proxy = []) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);

		if (!empty($proxy["ip"])) {
			curl_setopt($ch, CURLOPT_PROXY, $proxy["ip"] . ":" . $proxy["port"]);
			if (!empty($proxy["login"])) {
				curl_setopt(
					$ch,
					CURLOPT_PROXYUSERPWD,
					$proxy["login"] . ":" . $proxy["password"]
				);
			}
			curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy["type"]);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		if (!empty($postParams)) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
		}

		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}
}

if (!isset($tlg_news_id)) {
    $tlg = $db->super_query( "SELECT news_id, settings FROM " . PREFIX . "_telegram_sender WHERE error=0 ORDER BY id ASC LIMIT 0,1" );
    if (isset($tlg["news_id"])) {
        $tlg_news_id = $tlg["news_id"];
        $tlg_template = $tlg["settings"];
    }
}

if ( isset($tlg_news_id) && isset($tlg_template) && isset($aaparser_config['push_notifications'][$tlg_template]) && isset($aaparser_config['push_notifications']['tg_bot_token']) && isset($aaparser_config['push_notifications']['tg_chanel']) ) {
    $row = $db->super_query( "SELECT id, date, short_story, full_story, xfields, title, category, alt_name, tags FROM " . PREFIX . "_post WHERE id='" .$tlg_news_id ."' AND approve=1");
    if (isset($row["id"])) {
        if ($config["seo_type"]) {
            if ($config["seo_type"] == 1 || $config["seo_type"] == 2) {
                if ($row["category"] && $config["seo_type"] == 2) {
                    $full_link = $config["http_home_url"] . get_url($row["category"]) . "/" . $row["id"] . "-" . urlencode($row["alt_name"]) . ".html";
                  	$main_category_link = trim($config["http_home_url"] . get_url($row["category"]) . "/");
                } else {
                    $full_link = $config["http_home_url"] . $row["id"] . "-" . urlencode($row["alt_name"]) . ".html";
					$main_category_link = trim($config["http_home_url"] . get_url($row["category"]) . "/");
                }
            } else {
                $row["date"] = strtotime($row["date"]);
                $full_link = $config["http_home_url"] . date("Y/m/d/", $row["date"]) . $row["alt_name"] . ".html";
            }
        } else $full_link = $config["http_home_url"] . "index.php?newsid=" . $row["id"];
		
      	$category_name = $category_name_hashtag = [];
        $category_list = explode(",", $row["category"]);
        foreach ($category_list as $v) {
        	$category_name[] = $cat_info[$v]["name"];
          	$category_name_hashtag[] = "#".str_replace(" ", "_", trim($cat_info[$v]["name"]));
        }
        if ( $category_name ) $category_name = stripslashes(implode(", ", $category_name));
        if ( $category_name_hashtag ) $category_name_hashtag = stripslashes(implode(", ", $category_name_hashtag));

        $row['title'] = sanitizeText(stripslashes($row['title']));
        $row['short_story'] = sanitizeText(stripslashes($row['short_story']));
        $row['full_story'] = sanitizeText(stripslashes($row['full_story']));
        if ( !isset($xfields) ) $xfields = xfieldsload();
        $xfieldsdata = xfieldsdataload($row["xfields"]);

        if ( !empty($aaparser_config['push_notifications']['tg_only_ongoing']) && $aaparser_config['push_notifications']['tg_only_ongoing'] != '-' ) {
            $ongoing_field = $aaparser_config['push_notifications']['tg_only_ongoing'];
            $ongoing_value = isset($xfieldsdata[$ongoing_field]) ? trim($xfieldsdata[$ongoing_field]) : '';
            if ( mb_strtolower($ongoing_value, 'UTF-8') != mb_strtolower('Онгоинг', 'UTF-8') ) {
                return;
            }
        }
        if ($aaparser_config['push_notifications']['tg_enable_poster']) {
            if ($aaparser_config['push_notifications']['tg_source_poster'] == "xfields" && isset($aaparser_config['main_fields']['xf_poster']) && isset($xfieldsdata[$aaparser_config['main_fields']['xf_poster']])) {
                $posters = [];
                if ( strpos( $xfieldsdata[$aaparser_config['main_fields']['xf_poster']], "/uploads/posts/" ) === false ) {
                    $image = $config["http_home_url"] . "uploads/posts/" . $xfieldsdata[$aaparser_config['main_fields']['xf_poster']];
                } else $image = $xfieldsdata[$aaparser_config['main_fields']['xf_poster']];
				if (strpos($image, "https://") !== false) $image = substr($image, strrpos($image, "https://"));
                $temp_image = explode("|", $image);
                $posters[1] = $temp_image[0];
            } elseif ($aaparser_config['push_notifications']['tg_source_poster'] == "short_story") {
                preg_match( "#<img.+?src=['\"](.+?)['\"]#is", stripslashes($row['short_story']), $posters );
            } elseif ($aaparser_config['push_notifications']['tg_source_poster'] == "full_story") {
                preg_match( "#<img.+?src=['\"](.+?)['\"]#is", stripslashes($row['full_story']), $posters );
            }

            $posterImg = [];
            if ($posters[1]) $posterImg["poster"] = trim($posters[1]);
			else $posterImg["poster"] = trim($aaparser_config['main_fields']['poster_empty']);
            $posterImg = processImages($posterImg, $config["http_home_url"]);
        }

        foreach ($xfields as $value) {
            $preg_safe_name = preg_quote($value[0], "'");
            $xfieldsdata[$value[0]] = stripslashes($xfieldsdata[$value[0]]);
            if ($value[3] == "yesorno") {
                if (intval($xfieldsdata[$value[0]])) {
                    $xfgiven = true;
                    $xfieldsdata[$value[0]] = $lang["xfield_xyes"];
                } else {
                    $xfgiven = false;
                    $xfieldsdata[$value[0]] = $lang["xfield_xno"];
                }
            } else {
                if ($xfieldsdata[$value[0]] == "") $xfgiven = false;
				else $xfgiven = true;
            }
            if (!$xfgiven) {
                $aaparser_config['push_notifications'][$tlg_template] = preg_replace( "'\\[xfgiven_" . $preg_safe_name . "\\](.*?)\\[/xfgiven_" . $preg_safe_name . "\\]'is", "", $aaparser_config['push_notifications'][$tlg_template] );
                $aaparser_config['push_notifications'][$tlg_template] = str_ireplace( "[xfnotgiven_" . $value[0] . "]", "", $aaparser_config['push_notifications'][$tlg_template] );
                $aaparser_config['push_notifications'][$tlg_template] = str_ireplace( "[/xfnotgiven_" . $value[0] . "]", "", $aaparser_config['push_notifications'][$tlg_template] );
            } else {
                $aaparser_config['push_notifications'][$tlg_template] = preg_replace( "'\\[xfnotgiven_" . $preg_safe_name . "\\](.*?)\\[/xfnotgiven_" . $preg_safe_name . "\\]'is", "", $aaparser_config['push_notifications'][$tlg_template] );
                $aaparser_config['push_notifications'][$tlg_template] = str_ireplace( "[xfgiven_" . $value[0] . "]", "", $aaparser_config['push_notifications'][$tlg_template] );
                $aaparser_config['push_notifications'][$tlg_template] = str_ireplace( "[/xfgiven_" . $value[0] . "]", "", $aaparser_config['push_notifications'][$tlg_template] );
            }
            if (strpos($aaparser_config['push_notifications'][$tlg_template], "[ifxfvalue") !== false) {
                $aaparser_config['push_notifications'][$tlg_template] = preg_replace_callback( "#\\[ifxfvalue(.+?)\\](.+?)\\[/ifxfvalue\\]#is", "postingCheckXfvalue", $aaparser_config['push_notifications'][$tlg_template] );
            }
            $aaparser_config['push_notifications'][$tlg_template] = str_replace( "[xfvalue_" . $value[0] . "]", $xfieldsdata[$value[0]], $aaparser_config['push_notifications'][$tlg_template] );
            $temporary_xf = explode(",", $xfieldsdata[$value[0]]);
            $temporary_hashtag = [];
            foreach ($temporary_xf as $temporary_value) {
                $temporary_hashtag[] = "#" . str_replace(" ", "_", trim($temporary_value));
            }
            if ($temporary_hashtag) $aaparser_config['push_notifications'][$tlg_template] = str_replace( "[xfvalue_" . $value[0] . "_hashtag]", implode(", ", $temporary_hashtag), $aaparser_config['push_notifications'][$tlg_template] );
            if ( preg_match( "#\\[xfvalue_" . $value[0] . " limit=['\"](.+?)['\"]\\]#i", $aaparser_config['push_notifications'][$tlg_template], $matches ) ) {
                $xfieldsdata[$value[0]] = strip_tags($xfieldsdata[$value[0]]);
                $aaparser_config['push_notifications'][$tlg_template] = str_replace( $matches[0], limitTextLength($xfieldsdata[$value[0]], $matches[1]), $aaparser_config['push_notifications'][$tlg_template] );
            }
        }

        $titleTag = "#" . str_replace(" ", "_", trim($row['title']));
		
        if (stripos($aaparser_config['push_notifications'][$tlg_template], "{title}") !== false) {
			$aaparser_config['push_notifications'][$tlg_template] = str_replace( "{title}", $row['title'], $aaparser_config['push_notifications'][$tlg_template] );
        }
        if (stripos($aaparser_config['push_notifications'][$tlg_template], "{title_tag}") !== false) {
            $aaparser_config['push_notifications'][$tlg_template] = str_replace( "{title_tag}", $titleTag, $aaparser_config['push_notifications'][$tlg_template] );
        }
        if ( preg_match( "#\\{title limit=['\"](.+?)['\"]\\}#i", $aaparser_config['push_notifications'][$tlg_template], $matches ) ) {
            $aaparser_config['push_notifications'][$tlg_template] = str_replace( $matches[0], limitTextLength($row['title'], $matches[1]), $aaparser_config['push_notifications'][$tlg_template] );
        }
        if (stripos($aaparser_config['push_notifications'][$tlg_template], "{short-story}") !== false) {
			$aaparser_config['push_notifications'][$tlg_template] = str_replace( "{short-story}", $row['short_story'], $aaparser_config['push_notifications'][$tlg_template] );
        }
        if ( preg_match( "#\\{short-story limit=['\"](.+?)['\"]\\}#i", $aaparser_config['push_notifications'][$tlg_template], $matches ) ) {
            $aaparser_config['push_notifications'][$tlg_template] = str_replace( $matches[0], limitTextLength($row['short_story'], $matches[1]), $aaparser_config['push_notifications'][$tlg_template] );
        }
        if (stripos($aaparser_config['push_notifications'][$tlg_template], "{full-story}") !== false) {
            $aaparser_config['push_notifications'][$tlg_template] = str_replace( "{full-story}", $row['full_story'], $aaparser_config['push_notifications'][$tlg_template] );
        }
        if ( preg_match( "#\\{full-story limit=['\"](.+?)['\"]\\}#i", $aaparser_config['push_notifications'][$tlg_template], $matches ) ) {
            $aaparser_config['push_notifications'][$tlg_template] = str_replace( $matches[0], limitTextLength($row['full_story'], $matches[1]), $aaparser_config['push_notifications'][$tlg_template] );
        }
        if (stripos($aaparser_config['push_notifications'][$tlg_template], "{full_link}") !== false) {
            $aaparser_config['push_notifications'][$tlg_template] = str_replace( "{full_link}", $full_link, $aaparser_config['push_notifications'][$tlg_template] );
        }
        if (stripos($aaparser_config['push_notifications'][$tlg_template], "{main_category_link}") !== false) {
            if ( isset($main_category_link) ) $aaparser_config['push_notifications'][$tlg_template] = str_replace( "{main_category_link}", $main_category_link, $aaparser_config['push_notifications'][$tlg_template] );
          	else $aaparser_config['push_notifications'][$tlg_template] = str_replace( "{main_category_link}", '', $aaparser_config['push_notifications'][$tlg_template] );
        }
        if (stripos($aaparser_config['push_notifications'][$tlg_template], "{category}") !== false) {
            if ( $category_name ) $aaparser_config['push_notifications'][$tlg_template] = str_replace( "{category}", $category_name, $aaparser_config['push_notifications'][$tlg_template] );
          	else $aaparser_config['push_notifications'][$tlg_template] = str_replace( "{category}", '', $aaparser_config['push_notifications'][$tlg_template] );
        }
        if (stripos($aaparser_config['push_notifications'][$tlg_template], "{category_hashtag}") !== false) {
            if ( $category_name_hashtag ) $aaparser_config['push_notifications'][$tlg_template] = str_replace( "{category_hashtag}", $category_name_hashtag, $aaparser_config['push_notifications'][$tlg_template] );
          	else $aaparser_config['push_notifications'][$tlg_template] = str_replace( "{category_hashtag}", '', $aaparser_config['push_notifications'][$tlg_template] );
        }

        $telegramUrl = "https://api.telegram.org/bot" . $aaparser_config['push_notifications']['tg_bot_token'];
        $telegramCmd = ["chat_id" => $aaparser_config['push_notifications']['tg_chanel']];
		
        if (strpos($aaparser_config['push_notifications'][$tlg_template], "[button") !== false) {
            preg_match_all( "|\[button=(.*)\](.*)\[/button\]|U", $aaparser_config['push_notifications'][$tlg_template], $buttons_arr, PREG_SET_ORDER, 0 );
          	$send_buttons = [];
            foreach ($buttons_arr as $buttons_data) {
              	$send_buttons[] = [ "text" => $buttons_data[2], "url" => $buttons_data[1] ];
              	$aaparser_config['push_notifications'][$tlg_template] = str_replace( "[button=".$buttons_data[1]."]".$buttons_data[2]."[/button]", '', $aaparser_config['push_notifications'][$tlg_template] );
            }
          	$telegramCmd["reply_markup"] = json_encode([
            	"inline_keyboard" => [
                	$send_buttons
                ],
            ]);
        }

        $aaparser_config['push_notifications'][$tlg_template] = str_replace("[|x]", "\\x", $aaparser_config['push_notifications'][$tlg_template]);

        $aaparser_config['push_notifications'][$tlg_template] = sanitizeText($aaparser_config['push_notifications'][$tlg_template]);

        if ( preg_match( "@\\\\x([0-9a-fA-F]{2})@x", $aaparser_config['push_notifications'][$tlg_template], $matches ) ) {
            $aaparser_config['push_notifications'][$tlg_template] = preg_replace_callback( "@\\\\x([0-9a-fA-F]{2})@x", function ($r) { return chr(hexdec($r[1])); }, $aaparser_config['push_notifications'][$tlg_template] );
        }

        $telegramCmd["parse_mode"] = "HTML";
        if ($aaparser_config['push_notifications']['tg_enable_poster'] && 0 < count($posterImg)) {
            if (0 <= version_compare(PHP_VERSION, "5.5")) $telegramCmd["photo"] = new CURLFile($posterImg["poster"]);
            else $telegramCmd["photo"] = "@" . $posterImg["poster"];
            $telegramCmd["caption"] = $aaparser_config['push_notifications'][$tlg_template];
            $response = makeCurlRequest( $telegramUrl . "/sendPhoto", $telegramCmd, $postingProxy );
        } else {
            $telegramCmd["disable_web_page_preview"] = "true";
            $telegramCmd["text"] = $aaparser_config['push_notifications'][$tlg_template];
            $response = makeCurlRequest( $telegramUrl . "/sendMessage", $telegramCmd, $postingProxy );
        }

        $response = json_decode($response, true);
        if ($response["ok"]) {
            $message_id = intval($response["result"]["message_id"]);
            if (0 < $message_id) $db->query( "DELETE FROM " . PREFIX . "_telegram_sender WHERE news_id='".$tlg_news_id."'" );
          	if ( isset($working_mode) && $working_mode == 'cron' ) echo 'News id: '.$tlg_news_id.' - отправили пост в тг';
        } else {
            //$db->query("UPDATE ".PREFIX ."_telegram_sender SET error=1 WHERE news_id='".$tlg_news_id."'");
            $db->query( "DELETE FROM " . PREFIX . "_telegram_sender WHERE news_id='".$tlg_news_id."'" );
          	if ( isset($working_mode) && $working_mode == 'cron' ) echo 'News id: '.$tlg_news_id.' - ошибка отправки';
			echo "<br/>Log:<br/><pre>";
			print_r ($response);
			echo "</pre>";
        }
      	unset($row, $full_link, $main_category_link, $category_name, $category_name_hashtag, $category_list, $xfields, $xfieldsdata, $posters, $posterImg, $telegramCmd, $response);
    } else {
		$db->query( "DELETE FROM " . PREFIX . "_telegram_sender WHERE news_id='".$tlg_news_id."'" );
		echo "В данный момент не смог обработать новость id='" . $tlg_news_id . "'";
	}
} elseif ( !$tlg_news_id && isset($working_mode) && $working_mode == 'cron' ) echo 'В данный момент нет новостей в очереди на отправку в telegram';