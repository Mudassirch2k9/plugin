<?php
use OPA\Inc\Base\BaseController;

$baseController = new BaseController();
?>
<div class="wrap">

    <h1><?php _e( 'Filter Questions', 'wp-product-advisor' ); ?></h1>

	<?php settings_errors();?>

	<ul class="nav nav-tabs">

		<li class="<?php echo isset($_POST['edit']) ?: 'active'; ?>"><a href="#tab-1"><?php _e( 'Your Questions', 'wp-product-advisor' ); ?></a></li>

        <li class=" <?php echo isset($_POST['edit']) ? 'active' : ''; ?>">
            <a href="#tab-2">
            <?php 
            if (isset($_POST['edit'])) {
                _e( 'Update Question', 'wp-product-advisor' );
            } else {
                _e( 'Add Question', 'wp-product-advisor' );
            }
            ?>
            </a>
        </li>

		<li class=""><a href="#tab-3"> <?php _e( 'Help', 'wp-product-advisor' ); ?></a></li>

	</ul>

	<div class="tab-content">

		<div id="tab-1" class="tab-pane <?php echo isset($_POST['edit']) ?: 'active'; ?>">

             <table class="cpt-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php _e( 'Product Attribute', 'wp-product-advisor' ); ?></th>
                        <th><?php _e( 'Question Body', 'wp-product-advisor' ); ?></th>
                        <th><?php _e( 'Options', 'wp-product-advisor' ); ?> </th>
                        <th><?php _e( 'Action', 'wp-product-advisor' ); ?></th>

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

if ( wppa()->is__premium_only() ) {
    // This IF will be executed only if the user in a trial mode or have a valid license.
    if ( wppa()->can_use_premium_code() ) {
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
} else {

    // limit to max 3 question
    $no_of_ques = count($questions);
    if( !isset($_POST['edit']) && $no_of_ques >= 3){

        echo "<h2 style='font-size:20px;font-weight:bold;'>";
        _e( 'You\'ve reached the maximum amount!', 'wp-product-advisor' ); 
        echo "</h2>";
        _e( 'You have reached the maximum 3 questions limit. Delete a question to add a new one, or upgrade to our premium version!', 'wp-product-advisor' );
        echo "<br><br>";
        _e( 'With WP Product Advisor Pro you\'ll get unlimited amount of questions & answers as well as premium support and many more soon coming features.', 'wp-product-advisor' );

        ?>
        <div class="premiumDiv" style="padding-top: 50px;">
            <button id="purchase"><?php _e( 'Get WP Product Advisor Pro', 'wp-product-advisor' ); ?></button>
            <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
            <script src="https://checkout.freemius.com/checkout.min.js"></script>
            <script>
                var handler = FS.Checkout.configure({
                    plugin_id:  '3382',
                    plan_id:    '5644',
                    public_key: 'pk_2ae16976d4f7fe64346aa186e57ec',
                    image:      'https://your-plugin-site.com/logo-100x100.png'
                });
                
                $('#purchase').on('click', function (e) {
                    handler.open({
                        name     : 'WP Product Advisor',
                        licenses : 1,
                        // You can consume the response for after purchase logic.
                        purchaseCompleted  : function (response) {
                            // The logic here will be executed immediately after the purchase confirmation.                                // alert(response.user.email);
                        },
                        success  : function (response) {
                            // The logic here will be executed after the customer closes the checkout, after a successful purchase.                                // alert(response.user.email);
                        }
                    });
                    e.preventDefault();
                });
            </script>
        </div>
        <?php 

    } else {
        

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

}

?>

            </div>

        <div id="tab-3" class="tab-pane">

            <h3>Help</h3>

            <table>
                <tr>
                    <th width="200px"><?php _e( 'How to use Product Filter', 'wp-product-advisor' ); ?></th>
                    <td>
                        <Ul>
                            <li>
                                <?php 
                                    _e( '# Use this <code>[product_advisor] </code> shortcode in any page, post or anywhere', 'wp-product-advisor' ); 
                                ?>    
                            </li>
                        </Ul>
                    </td>
                </tr>

            </table>
		</div>
	</div>
</div>