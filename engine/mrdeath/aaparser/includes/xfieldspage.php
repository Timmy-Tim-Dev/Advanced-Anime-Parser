<?php
echo <<<HTML
	<div id="xfields" class="panel panel-flat" style='display:none'>
	    <div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Дополнительное поле с ID Shikimori', 'Выберите дополнительное поле, в котором содержится id аниме с Shikimori. Выберите если добавляете на сайт аниме', makeDropDown( $xfields_list, "fields[xf_shikimori_id]", $aaparser_config['fields']['xf_shikimori_id']));
showRow('Дополнительное поле с ID MyDramaList', 'Выберите дополнительное поле, в котором содержится id дорам с MyDramaList. Выберите если добавляете на сайт дорамы', makeDropDown( $xfields_list, "fields[xf_mdl_id]", $aaparser_config['fields']['xf_mdl_id']));
showRow('Дополнительное поле "переключатель да/нет" с указанием релиз camrip или нет', 'Выберите дополнительное поле "переключатель да/нет" с указанием релиз camrip или нет, если у вас такое поле есть. Заполняется в том случае, если данное аниме/дорама есть в базе Kodik', makeDropDown( $xfield_yesorno, "fields[xf_camrip]", $aaparser_config['fields']['xf_camrip']));
showRow('Дополнительное поле "переключатель да/нет" с указанием содержания LGBT сцен', 'Выберите дополнительное поле "переключатель да/нет" с указанием содержания LGBT сцен, если у вас такое поле есть. Заполняется в том случае, если данное аниме/дорама есть в базе Kodik', makeDropDown( $xfield_yesorno, "fields[xf_lgbt]", $aaparser_config['fields']['xf_lgbt']));
echo <<<HTML
            </table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка шаблона заполнения данных</div>
			<br><br>
			<div class="rcol-2col">
			<div class="rcol-2col-header">
			    <span>Доступные теги</span>
			    <div class="show-hide">Show</div>
			</div>
			    <div class="rcol-2col-body" style="display: none;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					    <tr class="rcol-2col-body-tr-even">
                            <td width="100%"><b>Для каждого тега доступны конструкции [if_x]...[/if_x], а так же [ifnot_x]...[/ifnot_x], где x - тег. Например:</b><br>[if_shikimori_russian]{shikimori_russian}[/if_shikimori_russian][ifnot_shikimori_russian]{shikimori_name}[/ifnot_shikimori_russian]</td>
                        </tr>
					</table>
					<table class="anime-settings" width="100%" border="0" cellspacing="0" cellpadding="0">
					    <tr class="rcol-2col-body-tr-even">
                            <td width="100%"><b>Теги Shikimori и World-Art (данные доступны только в аниме):</b></td>
                        </tr>
					</table>
					<table class="anime-settings" width="100%" border="0" cellspacing="0" cellpadding="0">
					    <tr class="rcol-2col-body-tr-even">
                            <td width="50%">Теги для поля</td>
                            <td width="50%">Описание тега для поля</td>
                        </tr>
HTML;
			$field_list = "";
			foreach ( $data_list as $field_num => $field ) {
				$field_list .= "<tr class=\"rcol-2col-body-tr-even\">
									<td width=\"50%\"><b>{".$field."}</b></td>
									<td width=\"50%\">".$fields_description[$field_num]."</td>
								</tr>";
			}
echo <<<HTML
						{$field_list}
					</table>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					    <tr class="rcol-2col-body-tr-even">
                            <td width="100%"><b>Теги Kodik (данные доступны и в дорамах и в аниме):</b></td>
                        </tr>
					</table>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					    <tr class="rcol-2col-body-tr-even">
                            <td width="50%">Теги для поля</td>
                            <td width="50%">Описание тега для поля</td>
                        </tr>
HTML;
			$field_list = "";
			foreach ( $data_list_kodik as $field_num => $field ) {
				$field_list .= "<tr class=\"rcol-2col-body-tr-even\">
									<td width=\"50%\"><b>{".$field."}</b></td>
									<td width=\"50%\">".$fields_description_kodik[$field_num]."</td>
								</tr>";
			}
echo <<<HTML
						{$field_list}
					</table>
				</div>
			</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
foreach ($main_fields as $key => $value) {
	if ($key == 'title') {
		showTrInline('Заголовок', '', 'input', ['xfields['.$key.']', 'text', $aaparser_config['xfields'][$key]]);
	}
	elseif ($key == 'short_story') {
		showTrInline('Краткое описание', '', 'textarea', ['xfields['.$key.']', $aaparser_config['xfields'][$key]]);
	}
	elseif ($key == 'full_story') {
		showTrInline('Полное описание', '', 'textarea', ['xfields['.$key.']', $aaparser_config['xfields'][$key]]);
	}
	elseif ($key == 'alt_name') {
		showTrInline('ЧПУ URL статьи', '', 'input', ['xfields['.$key.']', 'text', $aaparser_config['xfields'][$key]]);
	}
	elseif ($key == 'tags') {
		showTrInline('Ключевые слова для облака тегов', '', 'input', ['xfields['.$key.']', 'text', $aaparser_config['xfields'][$key]]);
	}
	elseif ($key == 'meta_title') {
		showTrInline('Метатег Title', '', 'input', ['xfields['.$key.']', 'text', $aaparser_config['xfields'][$key]]);
	}
	elseif ($key == 'meta_description') {
		showTrInline('Метатег Description', '', 'input', ['xfields['.$key.']', 'text', $aaparser_config['xfields'][$key]]);
	}
	elseif ($key == 'meta_keywords') {
		showTrInline('Метатег Keywords', '', 'input', ['xfields['.$key.']', 'text', $aaparser_config['xfields'][$key]]);
	}
	elseif ($key == 'catalog') {
		showTrInline('Буквенный каталог', '', 'input', ['xfields['.$key.']', 'text', $aaparser_config['xfields'][$key]]);
	}
	else {
		showTrInline('Доп поле '.$value, '', 'input', ['xfields['.$key.']', 'text', $aaparser_config['xfields'][$key]]);
	}
}
echo <<<HTML
			</table>
		</div>
	</div>
HTML;
?>