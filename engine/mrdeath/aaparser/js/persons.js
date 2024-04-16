$(document).ready(function() {
	function personas()	{
		var sh_id = $("#personas_block").attr("data-sh_id");
		$.ajax({
			url: "/engine/ajax/controller.php?mod=anime_grabber&module=persons",
			type: "POST",
			dataType: "html",
			data: {sh_id:sh_id},
			success: function(data) {
			   $("#personas_block").append(data);
			}
		});
	}

	personas();

	$("#swilly_refresh").click(function(e) {
		personas();
	});
});