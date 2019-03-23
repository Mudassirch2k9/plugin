<?php
use OPA\Inc\Base\BaseController;

$baseController = new BaseController();
?>
<div class="wrap">

    <h1>Filter Questions</h1>

	<?php settings_errors();?>

	<ul class="nav nav-tabs">

		<li class="<?php echo isset($_POST['edit']) ?: 'active'; ?>"><a href="#tab-1">Your Questions</a></li>

        <li class=" <?php echo isset($_POST['edit']) ? 'active' : ''; ?>"><a href="#tab-2"><?php echo isset($_POST['edit']) ? 'Update ' : 'Add '; ?> Question</a></li>

		<li class=""><a href="#tab-3"> Help ?</a></li>

	</ul>

	<div class="tab-content">

		<div id="tab-1" class="tab-pane <?php echo isset($_POST['edit']) ?: 'active'; ?>">

             <table class="cpt-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Product Attribute</th>
                        <th>Question Body</th>
                        <th>Options </th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
<?php
$questions = (get_option($baseController->option_name_questions)) ?: [];

foreach ($questions as $attr) {

    $id = (isset($attr['id'])) ? $attr['id'] : "";
    $pro_attr = (isset($attr['pro_attr'])) ? $attr['pro_attr'] : "";
    $ques_body = (isset($attr['ques_body'])) ? $attr['ques_body'] : "";
    $options = (isset($attr['options'])) ? $attr['options'] : "";

    echo "<tr scope='row'>";

    echo "<td>$id </td>";

    echo "<td>$pro_attr </td>";

    echo "<td>$ques_body </td>";

    echo "<td>";
    if (is_array($options)) {
        foreach ($options as $option) {
            $name = "";

            if (isset($option['name'])) {
                $name = $option['name'];
            } else if (isset($option['value'])) {
                $name = $option['value'];
            }

            echo ($name != "") ? "<span class='button'>" . $name . '</span> ' : "";
        }
    } else {
        echo $options;
    }
    echo "</td>";

    echo '<td>';

    echo '<form method="post" action="" class="inline-block">';

    settings_fields($baseController->option_group_filter_question);

    echo '<input type="hidden" name="edit" value="' . $id . '">';

    submit_button('Edit', 'edit small', 'submit', false);

    echo '</form> ';

    echo '</form> ';

    echo ' <form method="post" action="options.php" class="inline-block">';

    settings_fields($baseController->option_group_filter_question);

    echo '<input type="hidden" name="remove" value="' . $id . '">';

    submit_button('Delete', 'deleet small', 'submit', false, [
        'onclick' => 'return confirm("Are you sure you want to delete this?")',
    ]);

    echo "</form></td>";

    echo "</tr>";
}
?>
                </tbody>
            </table>
        </div>

        <div id="tab-2" class="tab-pane <?php echo isset($_POST['edit']) ? 'active' : ''; ?>">
<?php

// limit to max 3 question
$no_of_ques = count($questions);
if( !isset($_POST['edit']) && $no_of_ques >= 3){
    echo " You have reached the maximum 3 questions limit. 
            </br> Delete a question to add a new one.";
}else{
    

?>

			<form method="post" action="options.php" id="question_form">


            <script type="text/javascript" >

                jQuery(document).ready(function($) {
                    var edit = <?php echo (isset($_POST['edit'])) ? "'" . $_POST['edit'] . "'" : "null" ?>;
                    opaLoadForm(null, edit);
                });

                function addAnotherOption(tableId, name){

                    // TODO: Restrict amount of answers for free users
                    
                    // add option row
                    var row = "<tr>"
                    +"<td><select name='operator[]' >"
                    + "<option value='<='  > Max </option> <option value='>='  > Min </option><option value='='  > Equal </option>"
                    + "</select></td>"
                    + '<td> <input type="number" step="0.01" value="" name="'+ name +'[]" placeholder="e.g. 100" /></td>'
                    + '<td ><input type="text" value="" name="name[]" placeholder="e.g. Max $100 " /> <br/></td>'
                    + "</tr>";

                    // document.getElementById(tableId).appendChild("<tbody ></tbody>");

                    jQuery('#'+tableId).append(row);
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


                        var http_referer = '<?php wp_referer_field()?>';
                        document.getElementById("question_form").innerHTML = response + http_referer;

                        jQuery('.text_options').select2();
                        document.getElementById("pro_attr").onchange = function(){

                            // document.getElementById('submit').style.display === "none";
                            opaLoadForm(this.value);
                    };

                    });
                }
            </script>

            </form>

<?php            
            
}

?>

            </div>

        <div id="tab-3" class="tab-pane">

            <h3>Help</h3>

            <table>
                <tr>
                    <th width="200px">How to use Product Filter</th>
                    <td>
                        <Ul>
                            <li># Use this <code>[product_advisor] </code> shortcode in any page, post or anywhere  </li>
                        </Ul>
                    </td>
                </tr>

            </table>
		</div>
	</div>
</div>