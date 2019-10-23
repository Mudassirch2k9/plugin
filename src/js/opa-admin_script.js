window.addEventListener("load", function() {

	// store tabs variables
	var tabs = document.querySelectorAll("ul.nav-tabs > li");

	for (var i = 0; i < tabs.length; i++) {
		tabs[i].addEventListener("click", switchTab);
	}

	function switchTab(event) {
		event.preventDefault();

		document.querySelector("ul.nav-tabs li.active").classList.remove("active");
		document.querySelector(".tab-pane.active").classList.remove("active");

		var clickedTab = event.currentTarget;
		var anchor = event.target;
		var activePaneID = anchor.getAttribute("href");

		clickedTab.classList.add("active");
		document.querySelector(activePaneID).classList.add("active");

	}

});

function addAnotherOption(tableId, name) {

    // TODO: Restrict amount of answers for free users   

    // add option row
    var row = "<tr>" +
        "<td><select name='operator[]' >" +
        "<option value='<='  > Max </option> <option value='>='  > Min </option><option value='='  > Equal </option>" +
        "</select></td>" +
        '<td> <input type="number" step="0.01" value="" name="' + name + '[]" placeholder="e.g. 100" /></td>' +
        '<td ><input type="text" value="" name="name[]" placeholder="e.g. Max $100 " /> <br/></td>' +
        "</tr>";

    // document.getElementById(tableId).appendChild("<tbody ></tbody>");

    jQuery('#' + tableId).append(row);
}

function opaLoadForm(pro_attr, edit = null){
    var data = {
        'action': 'load_ques_option_action',
        'pro_attr': pro_attr,
    };

    //pass edit value when try to edit
    if(edit !== null){
        data.edit = edit;
    }

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function(response) {

        document.getElementById("question_form_body").innerHTML = response ;

        jQuery('.text_options').select2();
        document.getElementById("pro_attr").onchange = function(){

            // document.getElementById('submit').style.display === "none";
            opaLoadForm(this.value);
    };

    });
}



//vue script....

