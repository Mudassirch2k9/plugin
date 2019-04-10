<?php

/**
 * @package  OPA Plugin
 */

namespace OPA\Inc\Api\Callbacks;

use OPA\Inc\Base\BaseController;

class FqCallbacks extends BaseController
{

    
    //...section,  sanitizer 
    public function fqSectionOther()
    {
        _e( 'Other Settings Options', 'wp-product-advisor' );
    }

    public function fqOtherSanitizer($input)
    {

        $output = [];

        $name = 'filter_page_title';

        if( isset( $input[ $name ] ) ){

            $output[ $name ]   =  sanitize_text_field($input[ $name ]);
        }

        return $output;
    }

    //...section,  sanitizer , inputboxes functions,
    public function fqSection()
    {
        _e( 'Add and remove filter questions', 'wp-product-advisor' );
    }

    public function fqSanitizer($input)
    {

        $option_name = $this->option_name_questions;

        $output = get_option($option_name, []);

        if (!$output || !is_array($output)) {

            $output = [];
        }

        if (isset($_POST['remove'])) {

            unset($output[$_POST['remove']]);

            return $output;
        }
        // limit to max 3 question
        if( !isset( $output[ $input['pro_attr'] ] ) && count($output) >= 3){  //its not an update request and has 3 question
            
            $counter = 1;
            $new_output = [];
            
            foreach ($output as $key => $value) {  
                $counter++;              
                
                if($counter <= 3 ){
                    $new_output[$key] = $value;
                    
                }else{
                    return $new_output;
                }
            }
            return $output;
        }

        $type = $this->getAttributeType($input['pro_attr']);

        if ($type && $type == 'number') {

            if (isset($input['options']) && isset($_POST['options']) && isset($_POST['operator']) && isset($_POST['name'])) { // number type option has operator

                $option_array = [];
                $c = 0;
                foreach ($_POST['options'] as $opt) {

                    if (isset($_POST['options'][$c]) && $_POST['options'][$c] != "") {

                        $opt_value = $_POST['options'][$c];
                        $opt_operator = $_POST['operator'][$c];
                        $opt_name = (isset($_POST['name'][$c]) && $_POST['name'][$c] != "")
                        ? $_POST['name'][$c] : $opt_operator . "" . $opt_value;

                        $option_array[] = ['value' => $opt_value, 'operator' => $opt_operator, 'name' => $opt_name];

                    }

                    $c++;
                }

                $input['options'] = $option_array;

            } else {
                // triger error : Wrong submission;
            }
        } else if ($type && $type == 'checkbox') {

            $option_array = [
                ['value' => '1', 'name' => "Yes"],
                ['value' => '0', 'name' => "No"],
            ];

            $input['options'] = $option_array;

        } else if ($type && $type == 'text') {

            $option_array = [];

            foreach ($_POST['options'] as $opt) {
                $option_array[] = [
                    'value' => strtolower($opt),
                    'name' => $opt,
                ];
            }

            $input['options'] = $option_array;
            //load inserted text

        } else {
            // triger error : Wrong product type;
        }

        $id = $input['pro_attr'];

        $input['id'] = $id;

        $output[$id] = $input;

        // must initialize an empty array
        return $output;
    }

    //...form elements
    
    public function settingsOthertextboxField($args)
    {
        $name = $args['label_for'];
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        $option_name = $args['option_name'];
        $value = '';

        $value = ( isset( get_option( $option_name )[$name] ) ) ? get_option($option_name)[$name] : '';
        
        echo '<input type="text" value="' . $value . '" name="' . $option_name . '[' . $name . ']" placeholder="' . $placeholder . '" />';

    }


    public function settingsQuestionOptionsField($args)
    {

        $name = $args['label_for'];
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        $option_name = $args['option_name'];
        $value = '';

        $type = (isset($_POST['type'])) ? $_POST['type'] : '';

        $pro_attr = (isset($_POST['pro_attr'])) ? $_POST['pro_attr'] : false;

        if (isset($_POST['edit'])) {

            $value = (isset(get_option($option_name)[$_POST['edit']][$name])) ? get_option($option_name)[$_POST['edit']][$name] : false;

            $type = $this->getAttributeType($_POST['edit']);

            $pro_attr = $_POST['edit'];
        }

        switch ($type) {
            case 'checkbox':
                echo "<span class='button'>";
                    _e( 'Yes', 'wp-product-advisor' );
                echo "</span> <span class='button'>"; 
                    _e( 'No', 'wp-product-advisor' );
                echo "</span>";
                break;

            case 'number':

                echo '<input type="hidden" name="' . $option_name . '[' . $name . ']" />';

                echo "<table class='border-less' > ";
                
                echo"<tbody id='answer_options'>
                <tr>
                    <th>"; 
                        _e( 'Operator', 'wp-product-advisor' );
                    echo "</th>
                    <th>";
                        _e( 'Value', 'wp-product-advisor' ); 
                    echo "</th>
                    <th>";
                        _e( 'Label', 'wp-product-advisor' ); 
                    echo "</th>
                </tr>
                ";

                //number of options to display
                $option_count = 4;

                //on edit > set number of previous value saved
                if (is_array($value)) {
                    $option_count = count($value);
                }

                for ($i = 0; $i < $option_count; $i++) {

                    $pre_value = isset($value[$i]['value']) ? $value[$i]['value'] : '';

                    $value_operator = isset($value[$i]['operator']) ? $value[$i]['operator'] : '';

                    $pre_name = isset($value[$i]['name']) ? $value[$i]['name'] : '';

                    $selected_max = ($value_operator == '<=') ? ' Selected' : '';
                    $selected_min = ($value_operator == '>=') ? ' Selected' : '';
                    $selected_equal = ($value_operator == '=') ? ' Selected' : '';

                    echo "<tr>";
                    echo "<td>
                    <select name='operator[]' >
                        <option value='<=' " . $selected_max . " > "; 
                            _e( 'Max', 'wp-product-advisor' );
                        echo " </option>
                        <option value='>=' " . $selected_min . " > "; 
                            _e( 'Min', 'wp-product-advisor' );
                        echo " </option>
                        <option value='=' " . $selected_equal . " > "; 
                            _e( 'Equal', 'wp-product-advisor' );
                        echo " </option>
                    </select>
                    </td>";
                    echo '<td>
                    <input type="number" step="0.01" value="' . $pre_value . '" name="' . $name . '[]" placeholder="e.g.: \'100\'" />
                    </td>';

                    echo '<td ><input type="text" value="' . $pre_name . '" name="name[]" placeholder="e.g.: \'Max $100\' " /> <br/>
                    </td>';

                    echo '</tr>';
                }
                echo "</tbody> </table>";
                echo " <span class='button ' onclick=\"addAnotherOption('answer_options', '$name')\" > + "; 
                    _e( 'Add', 'wp-product-advisor' );
                echo " </span>";

                break;

            case 'text':

                if ($pro_attr) {
                    $inserted_data = $this->get_meta_unique_values($pro_attr, 'product');

                    $inserted_options = "";

                    // on edit make simple previous data list from nested $value array
                    if (isset($_POST['edit'])) {

                        $seleccted_data_list = [];

                        foreach ($value as $v) {
                            $seleccted_data_list[] = strtolower($v['value']);
                        }

                    }

                    foreach ($inserted_data as $text) {

                        $lower_text = strtolower($text);
                        //on edit check if already selected
                        $selected = '';
                        if (isset($_POST['edit'])) {
                            $selected = (in_array($lower_text, $seleccted_data_list)) ? " selected " : " not found";
                        }

                        $inserted_options .= " <option value='" . $text . "' $selected >$text</option>";
                    }

                    // echo 'Options will be generated from the inserted data ';
                    //searchable multiple text input from previous data

                    echo "<select class='text_options' name='" . $name . "[]' multiple='multiple'>
                            $inserted_options
                        </select>";
                }

                break;

            default:
                _e( 'Select Product Attribute First', 'wp-product-advisor' );
                break;
        }

    }

    public function settingsTextboxField($args)
    {
        $name = $args['label_for'];
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        $option_name = $args['option_name'];
        $value = '';
        if (isset($_POST['edit'])) {

            $value = (isset(get_option($option_name)[$_POST['edit']][$name])) ? get_option($option_name)[$_POST['edit']][$name] : false;
        }
        echo '<input type="text" value="' . $value . '" name="' . $option_name . '[' . $name . ']" placeholder="' . $placeholder . '" />';

    }

    public function settingsCheckboxField($args)
    {
        $name = $args['label_for'];

        $classes = $args['classes'];

        //value after submit
        $option_name = $args['option_name'];

        $checked = false;

        if (isset($_POST['edit'])) {
            $checked = (isset(get_option($option_name)[$_POST['edit']][$name])) ?: false;
        }

        echo '<div class="' . $classes . '"><input type="" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="" ' . ($checked ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
    }

    public function settingsSelectField($args)
    {
        $name = $args['label_for'];
        $option_name = $args['option_name'];

        if (isset($args['get_options'])) {
            $function = $args['get_options'][0];
            $f_args = (isset($args['get_options'][1])) ? [$args['get_options'][1]] : [];
            $options = call_user_func_array($function, $f_args);

            if (isset($args['disabledoption'])) {
                $disabledOption = $args['disabledoption'];
            }

        } else {

            $options = $args['options'];
        };

        $value = '';
        $classes = '';

        $disabled = '';

        if (isset($_POST['edit'])) {

            $value = (isset(get_option($option_name)[$_POST['edit']][$name])) ? get_option($option_name)[$_POST['edit']][$name] : false;

            $disabled = 'readonly="readonly"';

        } else if (isset($_POST['pro_attr'])) {

            $value = $_POST['pro_attr'];
        }
        $id = $name;

        $output = "<select  $disabled  name='$option_name" . "[$id]' id='$id'  class='form-control $classes' style='min-width: 100px' >";

        if (isset($disabledOption)) {
            $output .= "<option value='' >" . $disabledOption . "</option>";
        }
        foreach ($options as $option_id => $option_title) {
            $selected = ($option_id == $value) ? 'selected' : '';
            $output .= "<option value='$option_id' $selected >$option_title</option>";
        }
        $output .= "</select>";

        echo $output;
    }

    public function frontFilterAction()
    {

        $ques_option = get_option($this->option_name_questions, null);

        $questions = [];

        $data = '';

        // $this->startSession();
        if (session_status() == PHP_SESSION_NONE) {

            session_start();
        }

        if (!isset($_SESSION[$this->session_attr_filter_data])) {

            $_SESSION[$this->session_attr_filter_data] = [];
        }

        //........Question section

        // update question data in session
        if (isset($_POST['data']) && !is_null($_POST['data'])) {

            $data = json_decode(stripslashes($_POST['data']));

            if (is_object($data)) {

                if (isset($data->unset)) {

                    if (!empty($_SESSION[$this->session_attr_filter_data])) {

                        $lastObj = $_SESSION[$this->session_attr_filter_data];
                        end($lastObj);
                        $last_key = key($lastObj);

                        unset($_SESSION[$this->session_attr_filter_data][$last_key]);

                    }

                } else {

                    $question_id = $data->question_id;
                    $selected_option = $data->selected_option;

                    $_SESSION[$this->session_attr_filter_data][$question_id] = $selected_option;

                }

            }

        }

        $c = 0;
        $output_question = '';
        foreach ($ques_option as $key => $value) {

            if (!array_key_exists($value['id'], $_SESSION[$this->session_attr_filter_data])) {

                $output_question = $this->getFrontQuesHtml($value);
                break;
            }
            $c++;
        }
        $progress = ($c / count($ques_option)) * 100;

        echo "<div class='row-fluid'>
                <div class='col-sm-12 shadow questions_area' id='question_area' data-progress='$progress' >";

        //var_dump($_SESSION[$this->session_attr_filter_data]);

        echo $output_question;

        $previous_button = " <div class='input-group answerbuttons'><button  class='btn backtolast btn-warning' data-unset='previous'  onclick='callAjaxUnset(this)' style='width:100%;' type='button' target='2'>Previous</button></div>";

        echo $previous_button;

        echo "</div>";

        echo "  <div id='' class='col-sm-12 list-product shadow' >";
        $question_other = get_option($this->option_name_questions_other, []);
        $title = (isset($question_other['filter_page_title']))? $question_other['filter_page_title'] : "Search Result";
        echo "<h1 class='wppaTitle'>$title</h1>";

        // $pro_attr_option = get_option($this->option_name_product_attributes, []);

        $product_meta_arg = [];

        foreach ($_SESSION[$this->session_attr_filter_data] as $key => $value) {

            if ($value != 'null') { //question is not skiped

                $q = $ques_option[$key];

                $key = $q['pro_attr'];
                $val = $q['options'][intval($value)]['value'];
                $compare = (isset($q['options'][intval($value)]['operator'])) ? $q['options'][intval($value)]['operator'] : ' = ';

                if (is_numeric($val)) {

                    $product_meta_arg[] = [

                        'key' => $key,
                        'value' => $val,
                        'compare' => $compare,
                        'type' => 'NUMERIC',
                    ];

                } else {
                    $product_meta_arg[] = [

                        'key' => $key,
                        'value' => $val,
                        'compare' => $compare,
                    ];
                }

            }
        }

        echo $this->getProductHtml($product_meta_arg);
        echo "  </div>
        </div>";

        wp_die(); // this is required to terminate immediately and return a proper response

    }

    /**
     * Helping functions
     */

    public function getProductAttributesArray($args = null)
    {
        $option_name = $this->option_name_product_attributes;

        $attributes = get_option($option_name);

        if (!$attributes || !is_array($attributes)) {
            $attributes = [];
        }

        $result = [];

        foreach ($attributes as $attr) {
            $id = (isset($attr['id'])) ? $attr['id'] : "";
            $name = (isset($attr['name'])) ? $attr['name'] : "";

            $result[$id] = $name;
        }

        return $result;

    }

    public function getAttributeId(String $name)
    {
        $id = preg_replace('/[^\p{L}\p{N}\s]/u', '', $name);
        $id = strtolower($id);
        $id = preg_replace('/\s+/', '_', $id);
        $id = "opa_" . $id;
        return $id;

    }

    public function getFilterTemplate()
    {
        ob_start();
        include $this->plugin_path . '/templates/shortcode/online_product_advisor.php';
        return ob_get_clean();
    }

    public function getAttributeType(String $pro_attr)
    {
        $product_attributes = get_option($this->option_name_product_attributes);

        if (isset($product_attributes[$pro_attr]['type'])) {
            return $product_attributes[$pro_attr]['type'];
        }
        return false;
    }

    private function getFrontQuesHtml($value)
    {
        $q_body = $value['ques_body'];
        $q_options = isset($value['options']) ? $value['options'] : [];
        $id = $value['id'];
        $pro_attr = $value['pro_attr'];
        $output = "<section class='answer-body' style='display:block'>
                    <h2 class='questionBody' id='ques_body' > $q_body </h2>";
        $c = 0;
        foreach ($q_options as $opt) {
            $value = $opt['value'];
            $name = isset($opt['name']) ? $opt['name'] : $value;

            $output .= "<div class='input-group answerbuttons' style='width:100%;'>
                        <button data-value='$c' data-question_id='$id'  onclick='callAjax(this)'  class='btn btn-primary opa_option' style='width:100%;' type='button' > $name </button>
                        <!-- <div class='input-group-btn'>
                            <button type='button'  class='btn btn-info information' data-toggle='popover' title='ab 200 euro.' data-content='Dulli Soundbars.' data-original-title='Mehr Infos'><i class='fa fa-question-circle'></i>
                            </button>
                        </div> -->
                    </div>";
            $c++;
        }

        $locale = get_locale();
        if ($locale == 'de_DE') {

            $output .= "<div class='input-group' style='width:100%;'>
                                <button data-value='null' data-question_id='$id'  onclick='callAjax(this)' class='btn   btn-primary' style='width:100%;' type='button' >Egal / Frage Übespringen</button>
                            </div>
                    </section>
                    ";
        } else {

            $output .= "<div class='input-group' style='width:100%;'>
                            <button data-value='null' data-question_id='$id'  onclick='callAjax(this)' class='btn   btn-primary' style='width:100%;' type='button' >Skip question</button>
                        </div>
                </section>
                ";

        }

        return $output;

    }

    private function getProductHtml($product_meta_arg)
    {

        $output = '';

        $args = [
            'post_type' => 'product',
            'meta_query' => $product_meta_arg,

        ];
        // The search operator. Possible values are '=', '!=', '>', '>=', '<', '<='. Default value is '='.

        $the_query = new \WP_Query($args);

        if ($the_query->have_posts()) {

            $count = 0;
            while ($the_query->have_posts()) {

                $the_query->the_post();

                $count++;
                $model = get_post_meta(get_the_id(), 'opa_model', true);
                $default_product_image = $this->plugin_url . 'assets/images/product-default.png';
                $image = (!is_null(get_the_post_thumbnail_url())) ? get_the_post_thumbnail_url() : $default_product_image;
                $brand = get_post_meta(get_the_id(), 'opa_brand', true);
                $product_url = get_post_permalink();
                $external_link = get_post_meta(get_the_id(), 'opa_external_link', true);

                // var_dump(get_post_meta(get_the_id()));

                $meta = get_post_meta(get_the_id());
                //var_dump($meta);
                $option_product = get_option($this->option_name_product_attributes);

                /*

                $meta_table = "<table>";

                foreach ($option_product as $key => $value) {

                    $meta_table .= "<tr>";

                    $meta_table .= "<td class='optionName'>" . $value['name'] . "</td>";

                    $v = (isset($meta[$key])) ? $meta[$key][0] : ' - ';

                    $meta_table .= "<td class='optionValue'>" . $v . "</td>";

                    $meta_table .= "</tr>";

                }
                $meta_table .= "</table>";
                */

                $output .= "
                <div class='col-xs-12 col-sm-6 col-md-3 filter_product'>
                    <div class='product-one text-center shadow'>
                        <div class='row productBox'>
                            <div class='col-sm-12 col-xs-12'>
                                <div class='rang'> $count. </div>
                            </div>
                            <div class='col-sm-12 col-xs-12'>
                                <div class='img-holder'>
                                    <a target='_blank' rel='nofollow' class='tl' data-meta='assistant-details-img' href='$product_url'><center><img title='$model' alt='$model' src='$image' style='height:100px;' class='img-responsive product-image-227'></center></a>
                                </div>
                            </div>
                            <div class='col-sm-12 col-xs-12'>
                                <strong>$brand $model </strong>
                                <div class='content'>
                                    <table class='table' style='text-align:center;'>
                                        <tbody>
                                            <!--
                                            <tr>
                                                <td>
                                                    Rating:
                                                </td>
                                                <td>
                                                    <i class='fa fa-star'></i>
                                                    <i class='fa fa-star'></i>
                                                    <i class='fa fa-star'></i>
                                                    <i class='fa fa-star'></i>
                                                    <i class='fa fa-star-half-alt'>
                                                    </i>
                                                </td>
                                            </tr>
                                            -->
                                            <tr>
                                                <td colspan='2'>
                                                    <a href='$product_url'><center>View</center></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan='2'>
                                                    <center>
                                                        <a target='_blank' rel='nofollow' data-meta='assistant-details' class='tl btn btn-success fw' href='$external_link'>
                                                            <i class='fa fa-amazon'></i> Check price
                                                        </a>
                                                    </center>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    $meta_table
                                </div>
                            </div>
                            <!--
                            <div class='hidden-xs hidden-sm col-xs-12 productlist'>
                                <button class='btn btn-primary product-action add-product product-227' data-product='227'><i class='fa fa-plus'></i> Compare</button>
                            </div>
                            -->
                        </div>
                    </div>
                </div> ";
            };

            wp_reset_postdata();

        } else {
            $output .= "
                <div class='row'>
                    <div class='col-md-12 text-center'>
                        <div class='alert alert-warning'>
                            Sorry, we can't find any products for that combination.
                        </div>
                    </div>
                </div>
                ";
        }

        return $output;
    }

    private function get_meta_unique_values($key = '', $type = 'post', $status = 'publish')
    {

        global $wpdb;

        if (empty($key)) {
            return;
        }

        $r = $wpdb->get_col($wpdb->prepare("
            SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
            LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = '%s'
            AND p.post_status = '%s'
            AND p.post_type = '%s'
        ", $key, $status, $type));

        return $r;
    }

}
