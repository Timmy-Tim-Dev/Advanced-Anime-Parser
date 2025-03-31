<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

if( !defined('DATALIFEENGINE' ) ) {
	die('Hacking attempt!');
}

if ($aaparser_config['persons']['personas_on'] == 1 && isset($_POST['sh_id']) && $_POST['sh_id']) {
    
	require_once (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/functions/module.php'));
	require_once (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php'));
	
	if ( isset($aaparser_config['settings']['shikimori_api_domain']) ) $shikimori_api_domain = $aaparser_config['settings']['shikimori_api_domain'];
	else $shikimori_api_domain = 'https://shikimori.one/';
	
	$shikimori_url_domain = clean_url($shikimori_api_domain);
	$site_url_domain = clean_url($config['http_home_url']);
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
	
	$shiki_id = isset($_POST['sh_id']) ? $_POST['sh_id'] : '';
	
	if ($shiki_id == '') die('Не был передан Shikimori id');
	
	//Проверяем наличие кеша
	
	if ( isset($aaparser_config['persons']['personas_cache']) && $aaparser_config['persons']['personas_cache'] == 1 ) {
		$shiki_cache = kodik_cache('personas_'.$shiki_id, false, 'personas_characters');
		if ($shiki_cache == '{"main_characters":"","sub_characters":"","persons":""}') $shiki_cache = false;
	} else $shiki_cache = false;
	
	//Если кеша нет или он отключён
	
	if ( $shiki_cache === false ) {
	    $shiki_cache = [];
		
	    //Парсим данные
		$postfields = [
			'query' => '{
				animes(ids: "'.$shiki_id.'", limit: 1) {
					id
					characterRoles {
					  id
					  rolesRu
					  rolesEn
					  character { id name russian url poster { id originalUrl previewUrl miniUrl mainUrl } }
					  
					}
					personRoles {
					  id
					  rolesRu
					  rolesEn
					  person { id name russian url poster { id originalUrl previewUrl miniUrl mainUrl } }
					}
				}
			}'
		];
		$shikimori = request('https://shikimori.one/api/graphql', 1, $postfields);
		$shiki_request = $shikimori['data']['animes']['0'];
			
		if ( !$shikimori['message'] || !$shikimori['code'] || !$shikimori['error']) {
		    $main_characters = $sub_characters = $persons = [];
		    $mc = $sc = $pr = 0;
			foreach ( $shiki_request['characterRoles'] as $data ) {
			    if (isset($data['character']['url'])) $data['character']['url'] = str_replace(['https://shikimori.one', 'https://shikimori.me'], '', $data['character']['url']);
			    if (isset($data['character']['poster']['originalUrl'])) $data['character']['poster']['originalUrl'] = str_replace(['https://shikimori.one', 'https://shikimori.me'], '', $data['character']['poster']['originalUrl']);
			    if (isset($data['character']['poster']['previewUrl'])) $data['character']['poster']['previewUrl'] = str_replace(['https://shikimori.one', 'https://shikimori.me'], '', $data['character']['poster']['previewUrl']);
			    if (isset($data['character']['poster']['miniUrl'])) $data['character']['poster']['miniUrl'] = str_replace(['https://shikimori.one', 'https://shikimori.me'], '', $data['character']['poster']['miniUrl']);
			    if (isset($data['character']['poster']['mainUrl'])) $data['character']['poster']['mainUrl'] = str_replace(['https://shikimori.one', 'https://shikimori.me'], '', $data['character']['poster']['mainUrl']);
			    if ( in_array("Main", $data['rolesEn']) && $data['character'] ) {
			        $main_characters[$mc]['role'] = implode(', ', $data['rolesEn']);
			        $main_characters[$mc]['role_rus'] = implode(', ', $data['rolesRu']);
			        $main_characters[$mc]['data'] = $data['character'];
			        $mc++;
			    }
			    elseif ( in_array("Supporting", $data['rolesEn']) && $data['character'] ) {
			        $sub_characters[$sc]['role'] = implode(', ', $data['rolesEn']);
			        $sub_characters[$sc]['role_rus'] = implode(', ', $data['rolesRu']);
			        $sub_characters[$sc]['data'] = $data['character'];
			        $sc++;
			    }
			    
			}
			
			foreach ( $shiki_request['personRoles'] as $data ) {
			    if (isset($data['person']['url'])) $data['person']['url'] = str_replace(['https://shikimori.one', 'https://shikimori.me'], '', $data['person']['url']);
			    if (isset($data['person']['poster']['originalUrl'])) $data['person']['poster']['originalUrl'] = str_replace(['https://shikimori.one', 'https://shikimori.me'], '', $data['person']['poster']['originalUrl']);
			    if (isset($data['person']['poster']['previewUrl'])) $data['person']['poster']['previewUrl'] = str_replace(['https://shikimori.one', 'https://shikimori.me'], '', $data['person']['poster']['previewUrl']);
			    if (isset($data['person']['poster']['miniUrl'])) $data['person']['poster']['miniUrl'] = str_replace(['https://shikimori.one', 'https://shikimori.me'], '', $data['person']['poster']['miniUrl']);
			    if (isset($data['person']['poster']['mainUrl'])) $data['person']['poster']['mainUrl'] = str_replace(['https://shikimori.one', 'https://shikimori.me'], '', $data['person']['poster']['mainUrl']);
			    
			    if ( $data['person'] ) {
			        $persons[$pr]['role'] = implode(', ', $data['rolesEn']);
			        $persons[$pr]['role_rus'] = implode(', ', $data['rolesRu']);
			        $persons[$pr]['data'] = $data['person'];
			        $pr++;
			    }
			    
			}
			if ( !defined('TEMPLATE_DIR') ) define ( 'TEMPLATE_DIR', ROOT_DIR . '/templates/' . totranslit($config['skin'], false, false) );
			
			//Если включёны главные персонажи
			if ( $aaparser_config['persons']['main_characters'] == 1 ) {
			    
			    //Об'являем шаблон c блоком всех главных персонажей и подключаем шаблонизатор
	
	            $tplmcharacters = new dle_template();
	            $tplmcharacters->dir = TEMPLATE_DIR;
	            $tplmcharacters->load_template( 'main_characters_block.tpl' );
	            
	            if ( $main_characters ) {
			    
			        if ( $aaparser_config['persons']['main_characters_limit'] && count($main_characters) > $aaparser_config['persons']['main_characters_limit'] ) $main_characters = array_slice($main_characters, 0, $aaparser_config['persons']['main_characters_limit']);
	            
	                $tplmcharacters->set( '[main-characters-list]', "" );
		            $tplmcharacters->set( '[/main-characters-list]', "" );
		            
		            //Об'являем шаблон c блоком одного персонажа и подключаем шаблонизатор
		
		            $tplmcharactersone = new dle_template();
	                $tplmcharactersone->dir = TEMPLATE_DIR;
	                $tplmcharactersone->load_template( 'main_characters_info.tpl' );
	                
	                foreach ( $main_characters as $main_character ) {
	                    change_tags($tplmcharactersone, $main_character['data']['id'], 'characters_id');
						change_tags($tplmcharactersone, $main_character['data']['name'], 'characters_name_eng');
						change_tags($tplmcharactersone, $main_character['data']['russian'], 'characters_name_rus');
						change_tags($tplmcharactersone, $main_character['data']['url'], 'characters_url', $protocol. '://' . $shikimori_url_domain);
						change_tags($tplmcharactersone, $main_character['data']['url'], 'site_characters_url', $protocol. '://' . $site_url_domain);
						
						change_tags_img($tplmcharactersone, $main_character['data']['poster']['originalUrl'], 'characters_image_orig', $aaparser_config['persons']['default_image']);
						change_tags_img($tplmcharactersone, $main_character['data']['poster']['previewUrl'], 'characters_image_prev', $aaparser_config['persons']['default_image']);
						change_tags_img($tplmcharactersone, $main_character['data']['poster']['mainUrl'], 'characters_image_x96', $aaparser_config['persons']['default_image']);
						change_tags_img($tplmcharactersone, $main_character['data']['poster']['miniUrl'], 'characters_image_x48', $aaparser_config['persons']['default_image']);
						
	                    $tplmcharactersone->set( '[characters_role_eng]', "" );
				        $tplmcharactersone->set( '[/characters_role_eng]', "" );
				        $tplmcharactersone->set( '{characters_role_eng}', 'Main character' );
				        $tplmcharactersone->set_block( "'\\[not_characters_role_eng\\](.*?)\\[/not_characters_role_eng\\]'si", "" );
				        
				        $tplmcharactersone->set( '[characters_role_rus]', "" );
				        $tplmcharactersone->set( '[/characters_role_rus]', "" );
				        $tplmcharactersone->set( '{characters_role_rus}', 'Главный персонаж' );
				        $tplmcharactersone->set_block( "'\\[not_characters_role_rus\\](.*?)\\[/not_characters_role_rus\\]'si", "" );
				        
				        $tplmcharactersone->compile( 'main_characters_info' );
	                    
	                }
	                
	                $tplmcharacters->set( '{main-characters-list}', $tplmcharactersone->result['main_characters_info'] );
	                unset($tplmcharactersone);
		            
	            } else {
	                $tplmcharacters->set_block( "'\\[main-characters-list\\](.*?)\\[/main-characters-list\\]'si", "" );
	                $tplmcharacters->set( '{main-characters-list}', '' );
	            }
		        
	            $tplmcharacters->compile( 'main_characters' );
	            $shiki_cache['main_characters'] = $tplmcharacters->result['main_characters'];
	            unset($tplmcharacters);
		        
			    
			} else $shiki_cache['main_characters'] = '';
			
			//Если включёны второстепенные персонажи
			if ( $aaparser_config['persons']['characters'] == 1 ) {
			    
			    //Об'являем шаблон c блоком всех второстепенных персонажей и подключаем шаблонизатор
	
	            $tplscharacters = new dle_template();
	            $tplscharacters->dir = TEMPLATE_DIR;
	            $tplscharacters->load_template( 'sub_characters_block.tpl' );
	            
	            if ( $sub_characters ) {
			    
			        if ( $aaparser_config['persons']['characters_limit'] && count($sub_characters) > $aaparser_config['persons']['characters_limit'] ) $sub_characters = array_slice($sub_characters, 0, $aaparser_config['persons']['characters_limit']);
	            
	                $tplscharacters->set( '[sub-characters-list]', "" );
		            $tplscharacters->set( '[/sub-characters-list]', "" );
		            
		            //Об'являем шаблон c блоком одного персонажа и подключаем шаблонизатор
		
		            $tplscharactersone = new dle_template();
	                $tplscharactersone->dir = TEMPLATE_DIR;
	                $tplscharactersone->load_template( 'sub_characters_info.tpl' );
	                
	                foreach ( $sub_characters as $sub_character ) {
						
						change_tags($tplscharactersone, $sub_character['data']['id'], 'characters_id');
						change_tags($tplscharactersone, $sub_character['data']['name'], 'characters_name_eng');
						change_tags($tplscharactersone, $sub_character['data']['russian'], 'characters_name_rus');
						change_tags($tplscharactersone, $sub_character['data']['url'], 'characters_url', $protocol. '://' . $shikimori_url_domain);
						change_tags($tplscharactersone, $sub_character['data']['url'], 'site_characters_url', $protocol. '://' . $site_url_domain);
						
						change_tags_img($tplscharactersone, $sub_character['data']['poster']['originalUrl'], 'characters_image_orig', $aaparser_config['persons']['default_image']);
						change_tags_img($tplscharactersone, $sub_character['data']['poster']['previewUrl'], 'characters_image_prev', $aaparser_config['persons']['default_image']);
						change_tags_img($tplscharactersone, $sub_character['data']['poster']['mainUrl'], 'characters_image_x96', $aaparser_config['persons']['default_image']);
						change_tags_img($tplscharactersone, $sub_character['data']['poster']['miniUrl'], 'characters_image_x48', $aaparser_config['persons']['default_image']);
							
	                    $tplscharactersone->set( '[characters_role_eng]', "" );
				        $tplscharactersone->set( '[/characters_role_eng]', "" );
				        $tplscharactersone->set( '{characters_role_eng}', 'Supporting character' );
				        $tplscharactersone->set_block( "'\\[not_characters_role_eng\\](.*?)\\[/not_characters_role_eng\\]'si", "" );
				        
				        $tplscharactersone->set( '[characters_role_rus]', "" );
				        $tplscharactersone->set( '[/characters_role_rus]', "" );
				        $tplscharactersone->set( '{characters_role_rus}', 'Второстепенный персонаж' );
				        $tplscharactersone->set_block( "'\\[not_characters_role_rus\\](.*?)\\[/not_characters_role_rus\\]'si", "" );
				        
				        $tplscharactersone->compile( 'sub_characters_info' );
	                    
	                }
	                
	                $tplscharacters->set( '{sub-characters-list}', $tplscharactersone->result['sub_characters_info'] );
	                unset($tplscharactersone);
		            
	            } else {
	                $tplscharacters->set_block( "'\\[sub-characters-list\\](.*?)\\[/sub-characters-list\\]'si", "" );
	                $tplscharacters->set( '{sub-characters-list}', '' );
	            }
		        
	            $tplscharacters->compile( 'sub_characters' );
	            $shiki_cache['sub_characters'] = $tplscharacters->result['sub_characters'];
	            unset($tplscharacters);
		        
			    
			} else $shiki_cache['sub_characters'] = '';
			
			//Если включёны персоны
			if ( $aaparser_config['persons']['persons'] == 1 ) {
			    
			    //Об'являем шаблон c блоком всех персон и подключаем шаблонизатор
	
	            $tplpersons = new dle_template();
	            $tplpersons->dir = TEMPLATE_DIR;
	            $tplpersons->load_template( 'persons_block.tpl' );
	            
	            if ( $persons ) {
			    
			        if ( $aaparser_config['persons']['persons_limit'] && count($persons) > $aaparser_config['persons']['persons_limit'] ) $persons = array_slice($persons, 0, $aaparser_config['persons']['persons_limit']);
	            
	                $tplpersons->set( '[persons-list]', "" );
		            $tplpersons->set( '[/persons-list]', "" );
		            
		            //Об'являем шаблон c блоком одной персоны и подключаем шаблонизатор
		
		            $tplpersonsone = new dle_template();
	                $tplpersonsone->dir = TEMPLATE_DIR;
	                $tplpersonsone->load_template( 'persons_info.tpl' );
	                
	                foreach ( $persons as $person ) {
						change_tags($tplpersonsone, $person['data']['id'], 'persons_id');
						change_tags($tplpersonsone, $person['data']['name'], 'persons_name_eng');
						change_tags($tplpersonsone, $person['data']['russian'], 'persons_name_rus');
						change_tags($tplpersonsone, $person['data']['url'], 'persons_url', $protocol. '://' . $shikimori_url_domain);
						change_tags($tplpersonsone, $person['data']['url'], 'site_persons_url', $protocol. '://' . $site_url_domain);
						
						change_tags_img($tplpersonsone, $person['data']['poster']['originalUrl'], 'persons_image_orig', $aaparser_config['persons']['default_image']);
						change_tags_img($tplpersonsone, $person['data']['poster']['previewUrl'], 'persons_image_prev', $aaparser_config['persons']['default_image']);
						change_tags_img($tplpersonsone, $person['data']['poster']['mainUrl'], 'persons_image_x96', $aaparser_config['persons']['default_image']);
						change_tags_img($tplpersonsone, $person['data']['poster']['miniUrl'], 'persons_image_x48', $aaparser_config['persons']['default_image']);
							
	                    change_tags($tplpersonsone, $person['role'], 'persons_role_eng');
	                    change_tags($tplpersonsone, $person['role_rus'], 'persons_role_rus');
				        
				        $tplpersonsone->compile( 'persons_info' );
	                }
	                
	                $tplpersons->set( '{persons-list}', $tplpersonsone->result['persons_info'] );
	                unset($tplpersonsone);
		            
	            } else {
	                $tplpersons->set_block( "'\\[persons-list\\](.*?)\\[/persons-list\\]'si", "" );
	                $tplpersons->set( '{persons-list}', '' );
	            }
		        
	            $tplpersons->compile( 'persons' );
	            $shiki_cache['persons'] = $tplpersons->result['persons'];
	            unset($tplpersons);
		        
			    
			} else $shiki_cache['persons'] = '';
			
		}
		
		//Кешируем результат
		if ( isset($shiki_cache) && $shiki_cache ) {
		    $shiki_cache = json_encode($shiki_cache, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
			if ($shiki_cache != '{"main_characters":"","sub_characters":"","persons":""}') {
				if ( isset($aaparser_config['persons']['personas_cache']) && $aaparser_config['persons']['personas_cache'] == 1 ) kodik_create_cache('personas_'.$shiki_id, $shiki_cache, false, 'personas_characters');
			}
			echo $shiki_cache;
		}
		else echo "Shikimori/Mydramalist не вернул ничего";
		
	}
	else {
	    echo $shiki_cache;
	}
			    
				
}
elseif ($aaparser_config['persons']['personas_on_dorama'] == 1 && isset($_POST['mdl_id']) && $_POST['mdl_id']) {
    $site_url_domain = clean_url($config['http_home_url']);
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
	
    if (!function_exists('mdl_request')) {
        function mdl_request($url) {
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60 );
            curl_setopt($ch, CURLOPT_TIMEOUT, 60 );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
            $headers = [
    		    'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.2924.87 Safari/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
                'Connection: keep-alive',
                'Cache-Control: max-age=0',
                'Upgrade-Insecure-Requests: 1'
		    ];

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		    $mdl_page = curl_exec ($ch);
		    curl_close ($ch);
  
  		    return $mdl_page;
        }
    }
    
	require_once (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php'));
	
	$mdl_id = isset($_POST['mdl_id']) ? $_POST['mdl_id'] : '';
	
	if ($mdl_id == '') die('Не был передан Mydramalist id');
	
	//Проверяем наличие кеша
	
	if ( isset($aaparser_config['persons']['personas_cache_dorama']) && $aaparser_config['persons']['personas_cache_dorama'] == 1 ) $mdl_request = kodik_cache('personas_'.$mdl_id, false, 'personas_characters');
	else $mdl_request = false;
	
	//Если кеша нет или он отключён
	
	if ( $mdl_request === false ) {
	    
	    //Парсим актёров
	    if (isset($aaparser_config['persons']['personas_other_dorama_api']) && $aaparser_config['persons']['personas_other_dorama_api'] == 1) {		
			$mdl_url = "https://api.allorigins.win/get?url=https://mydramalist.com/".$mdl_id;
			$mdl_request = mdl_request($mdl_url);
			$mdl_request = json_decode($mdl_request, true);
			$mdl_request = $mdl_request['contents'];
		} else {
			$mdl_url = "https://mydramalist.com/".$mdl_id;
			$mdl_request = mdl_request($mdl_url);
		}
		$actors = [];

        $arr1 = explode('<ul class="list no-border p-b credits">', $mdl_request);
        $arr2 = explode('</ul>', $arr1[1]);

        $arr3 = explode('<li class="list-item col-sm-4">', $arr2[0]);
        $delete = array_shift($arr3);
        foreach ( $arr3 as $num3 => $tmp3 ) {
            $tmp4 = explode('</li>', $tmp3);
            $tmp5 = explode('data-src="', $tmp4[0]);
            $tmp6 = explode('"', $tmp5[1]);
            $photo = $tmp6[0];
            $tmp7 = explode('<b itempropx="name">', $tmp4[0]);
            $tmp8 = explode('</b>', $tmp7[1]);
            $name = $tmp8[0];
			if (preg_match('/\/people\/([\d\w-]+)/', $tmp4[0], $matches)) $urlik = $matches[1];
            $actors_list[] = [
                'name_eng' => $name,
                'image_orig' => $photo,
				'url' => $urlik
            ];
        }
        
        //Если спарсили актёров
        
        if ( $actors_list ) {
            
            //Об'являем шаблон c блоком всех персон и подключаем шаблонизатор
            
            if ( !defined('TEMPLATE_DIR') ) define ( 'TEMPLATE_DIR', ROOT_DIR . '/templates/' . totranslit($config['skin'], false, false) );
	
	        $tplpersons = new dle_template();
	        $tplpersons->dir = TEMPLATE_DIR;
	        $tplpersons->load_template( 'persons_block.tpl' );
            
            $tplpersons->set( '[persons-list]', "" );
		    $tplpersons->set( '[/persons-list]', "" );
		    
		    //Об'являем шаблон c блоком одной персоны и подключаем шаблонизатор
		
		    $tplactors = new dle_template();
	        $tplactors->dir = TEMPLATE_DIR;
	        $tplactors->load_template( 'persons_info.tpl' );
	        
		    foreach ( $actors_list as $key => $data ) {
		        if ( $data['name_eng'] ) {
		            $tplactors->set( '[personas_name_eng]', "" );
				    $tplactors->set( '[/personas_name_eng]', "" );
				    $tplactors->set( '{personas_name_eng}', $data['name_eng'] );
				    $tplactors->set_block( "'\\[not_personas_name_eng\\](.*?)\\[/not_personas_name_eng\\]'si", "" );
		        } else {
		            $tplactors->set( '[not_personas_name_eng]', "" );
				    $tplactors->set( '[/not_personas_name_eng]', "" );
		            $tplactors->set_block( "'\\[personas_name_eng\\](.*?)\\[/personas_name_eng\\]'si", "" );
		            $tplactors->set( '{personas_name_eng}', '' );
		        } 
				if ( $data['image_orig'] ) {
		            $tplactors->set( '[personas_image_orig]', "" );
				    $tplactors->set( '[/personas_image_orig]', "" );
				    $tplactors->set( '{personas_image_orig}', $data['image_orig'] );
				    $tplactors->set_block( "'\\[not_personas_image_orig\\](.*?)\\[/not_personas_image_orig\\]'si", "" );
		        } elseif ( $aaparser_config['persons']['default_image_dorama'] ) {
		            $tplactors->set( '[personas_image_orig]', "" );
				    $tplactors->set( '[/personas_image_orig]', "" );
				    $tplactors->set( '{personas_image_orig}', $aaparser_config['persons']['default_image_dorama'] );
				    $tplactors->set_block( "'\\[not_personas_image_orig\\](.*?)\\[/not_personas_image_orig\\]'si", "" );
		        } else {
		            $tplactors->set( '[not_personas_image_orig]', "" );
				    $tplactors->set( '[/not_personas_image_orig]', "" );
		            $tplactors->set_block( "'\\[personas_image_orig\\](.*?)\\[/personas_image_orig\\]'si", "" );
		            $tplactors->set( '{personas_image_orig}', '' );
		        }
				$tplactors->set( '{site_persons_url}', $protocol . "://" . $site_url_domain . "/persons/" . $data['url'] );
		        $tplactors->compile( 'actors_info' );
		    }
			
		    $tplpersons->set( '{persons-list}', $tplactors->result['actors_info'] );
		    unset($tplactors);
		    $tplpersons->compile( 'persons_info' );
		    
		    //Кешируем результат
		    if ( isset($aaparser_config['persons']['personas_cache_dorama']) && $aaparser_config['persons']['personas_cache_dorama'] == 1 ) kodik_create_cache('personas_'.$mdl_id, $tplpersons->result['persons_info'], false, 'personas_characters');
		    
		    echo $tplpersons->result['persons_info'];
		    unset($tplpersons);
		    
		} else echo "Shikimori/Mydramalist не вернул ничего";
	} else echo $mdl_request;
}
elseif ( isset($kodik_persons_dorama) && $kodik_persons_dorama == 'yes' ) {
    if (!function_exists('kodik_cache')) require_once (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php'));
    
    $mdl_id = $xfieldsdata[$aaparser_config['main_fields']['xf_mdl_id']];
    
    //Проверяем наличие кеша
	if ( isset($aaparser_config['persons']['personas_cache_dorama']) && $aaparser_config['persons']['personas_cache_dorama'] == 1 ) $mdl_request = kodik_cache('personas_'.$mdl_id, false, 'personas_characters');
	else $mdl_request = false;
	
	if ( $mdl_request === false ) $tpl->set( '{kodik_persons_dorama}', '<div id="personas_block" data-mdl_id="'.$mdl_id.'" data-has_cache="no"></div>' );
	else $tpl->set( '{kodik_persons_dorama}', '<div id="personas_block" data-mdl_id="'.$mdl_id.'" data-has_cache="yes">'.$mdl_request.'</div>' );
}

?>