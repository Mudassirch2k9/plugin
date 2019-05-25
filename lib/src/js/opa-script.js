function updateProgress() {
    var p = document.getElementById("question_area").dataset.progress;
    p = Math.round(p * 100) / 100;
    document.getElementById("progressBar").style.width = p + "%";
    document.getElementById('progressBar').getElementsByTagName('span')[0].innerHTML = p;
}

function callAjaxUnset(e) {
    var arrayData = JSON.stringify({ unset: e.dataset.unset });

    opaGetProduct(arrayData);

}

function callAjax(e) {
    var arrayData = JSON.stringify({ question_id: e.dataset.question_id, selected_option: e.dataset.value });

    opaGetProduct(arrayData);
}

function opaGetProduct(values = null, myAjaxUrl) {

    var data = {
        'action': 'front_filter_action',
        'data': values,
    };
    var myAjaxUrl = '<?php echo "admin_url(\'admin-ajax.php\')" ?>';

    jQuery.post(myAjaxUrl, data, function (response) {
        document.getElementById("opa_container").innerHTML = response;
        updateProgress();

    });
}