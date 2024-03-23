<?php
echo <<<HTML
	<div id="categories" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="font-size:20px; font-weight:bold;">Настройка проставления ваших категорий</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;

$cats_json = array();

		foreach ($category_values as $key => $value) {
			$item = array();
			$item['name'] = $value;
			$item['cat_id'] = $value;
			$cats_json[] = $item;	
		}	
	
		
foreach ($cat_info as $cat) {
    $cat_id = $cat["parentid"];
    $name = $cat["name"];
    while ($cat_id) {
        $name = $cat_info[$cat_id]["name"] . " / " . $name;
        $cat_id = $cat_info[$cat_id]["parentid"];
    }

	
	$options = [];
	$mode = 1;
	if ($aaparser_config['categories'][$cat["id"]] != "")
	{
		$ar_ray = explode(',', $aaparser_config['categories'][$cat['id']]);

		foreach ($category_values as $key => $value) {
			if ( $mode == 1 ) $key = $value;
			if (in_array($key, $ar_ray)) {

				$options[] = '<option value="'.$key.'" selected>'.$value.'</option>';
			}
		}		
	}

	if (count($options) == 0)
	{
		foreach ($category_values as $key => $value) {
			if ( $mode == 1 ) $key = $value;
				$options[] = '<option value="'.$key.'">'.$value.'</option>';
				break;
		}		
	}

	$options = implode('', $options);
	$category_html = <<<HTML
		<select onclick="alert();" name="categories[{$cat["id"]}][]" id="categories[{$cat["id"]}]" data-placeholder=" " placeholder=" " class="valuesselect" multiple style="width:100%;max-width:350px;">
			$options
		</select>
		<script>
		$("select[id='categories[{$cat["id"]}]'").chosen().on('chosen:showing_dropdown', function () {
				doLoadCats(this);
			});
		</script>
HTML;
    //showRow($name.' (id '.$cat["id"].')', '', makeSelect( $category_values, "categories[{$cat["id"]}]", $aaparser_config['categories'][$cat["id"]], 'Выберите тег - тип, жанр, страну, озвучку', 1));
	showRow($name.' (id '.$cat["id"].')', '', $category_html);
}

$cats_json = json_encode($cats_json);
$cats_json = str_replace('`', '\`', $cats_json);
echo <<<HTML
			</table>
			<script>
				function doLoadCats(obj)
				{
					var cat_json = `$cats_json`;
					
					var a = [];
					var b = [];
					
					$(obj.options).each(function() {
						a.push(this.value);
					});
					
					var jsonData = JSON.parse(cat_json);
					jsonData.forEach(function(element){ 
					  	if (a.includes(element.name) == false)
						{
							$(obj).append($('<option>', {
								value: element.name,
								text: element.name
							}));							
						}	
					});
					$('.valuesselect').trigger("chosen:updated");
				}
			</script>			
		</div>
	</div>
HTML;
?>