<?php
use OPA\Inc\Base\BaseController;

$baseController = new BaseController();


			
function templateOPAScript() {
	wp_enqueue_script('inline_script', 'assets/opa-inline_script.js', ['jquery']);
	wp_add_inline_script( 'inline_script', "
	jQuery(document).ready(function($) {
		var option = '';
		opaGetProduct(option);
	});

	function updateProgress(){
		var p = document.getElementById(\"question_area\").dataset.progress;
		p = Math.round(p * 100 / 100);
		document.getElementById(\"progressBar\").style.width = p+\"%\";
		document.getElementById('progressBar').getElementsByTagName('span')[0].innerHTML = p;
	}

	function callAjaxUnset(e){
		var arrayData = JSON.stringify( { unset: e.dataset.unset } );

		opaGetProduct(arrayData);

	}

	function callAjax(e)
	{
		var arrayData = JSON.stringify( { question_id: e.dataset.question_id, selected_option: e.dataset.value } );

		opaGetProduct(arrayData);
	}

	function opaGetProduct( values  = null ){

		var data = {
			'action': 'front_filter_action',
			'data': values,
		};
		var myAjaxUrl  = '". admin_url('admin-ajax.php') ."';

		jQuery.post( myAjaxUrl, data, function(response) {
			document.getElementById(\"opa_container\").innerHTML = response ;
			updateProgress();

		});
	}
	" );
 }
 add_action('wp_footer', 'templateOPAScript');

?>

<div id="filter_questions"  >
	<div class="row">
		<div class="col-md-12 text-center">
			<div class="progress">
				<div id="progressBar" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
					<span>0</span>% completed
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div id="opa_container">
			</div>

		</div>
	</div>
	<br>
	<br>
</div>

