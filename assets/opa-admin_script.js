!function o(a,c,u){function l(t,e){if(!c[t]){if(!a[t]){var r="function"==typeof require&&require;if(!e&&r)return r(t,!0);if(s)return s(t,!0);var n=new Error("Cannot find module '"+t+"'");throw n.code="MODULE_NOT_FOUND",n}var i=c[t]={exports:{}};a[t][0].call(i.exports,function(e){return l(a[t][1][e]||e)},i,i.exports,o,a,c,u)}return c[t].exports}for(var s="function"==typeof require&&require,e=0;e<u.length;e++)l(u[e]);return l}({1:[function(e,t,r){"use strict";window.addEventListener("load",function(){for(var e=document.querySelectorAll("ul.nav-tabs > li"),t=0;t<e.length;t++)e[t].addEventListener("click",r);function r(e){e.preventDefault(),document.querySelector("ul.nav-tabs li.active").classList.remove("active"),document.querySelector(".tab-pane.active").classList.remove("active");var t=e.currentTarget,r=e.target.getAttribute("href");t.classList.add("active"),document.querySelector(r).classList.add("active")}})},{}]},{},[1]);
//# sourceMappingURL=opa-admin_script.js.map

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


