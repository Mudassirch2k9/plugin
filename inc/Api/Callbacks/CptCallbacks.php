<?php


/**
 * @package  OPA Plugin
 */


namespace OPA\Inc\Api\Callbacks;

use OPA\Inc\Api\FormElementApi;
use OPA\Inc\Base\BaseController;

class CptCallbacks extends BaseController
{
    public $form_element_api;

    function __construct()
    {
        parent::__construct();
        $this->form_element_api =  new FormElementApi();
    }

    //...metabox callback function

     /**
     * Outputs the content of the meta box
     */

    public function createMetaFields($post, $mataBox)
    {
        $codeSnippets = [];

        $args = $mataBox['args'];

        wp_nonce_field(basename(__FILE__), 'prfx_nonce');

        $post_meta = get_post_meta($post->ID);

        echo "<table class='form-table'>
            <tbody>";

        foreach ($args['fields'] as $field) {
            echo "<tr>";

            $id = $field['id'];
            $value = (isset($post_meta[$id])) ? $post_meta[$id][0] : null;

            if ($field['type'] == 'text'){
                echo $this->form_element_api->textBoxField($field, $value);

            } else if ($field['type'] == 'number'){
                echo $this->form_element_api->numberBoxField($field, $value);

            } else if ($field['type'] == 'select'){
                echo $this->form_element_api->selectBoxField($field, $value);

            } else if ($field['type'] == 'date'){
                echo $this->form_element_api->dateBoxField($field, $value);

            } else if ($field['type'] == 'checkbox'){
                
                echo $this->form_element_api->checkBoxField($field, $value);

            } else if ($field['type'] == 'codeSnippet'){
                
                $function = $field['method'];
                $arguments = (isset($field['args']))?[$field['args']]: [];

                // store function name and args to call end of making this table
                $codeSnippets[] = [ 
                    'function'=>$function ,
                    'args'  =>  $arguments
                ];
                
            }

            echo "</tr>";
        }
        echo "
            </tbody>
        </table>";

        foreach($codeSnippets as $codeSnippet){

            echo call_user_func_array($codeSnippet['function'], $codeSnippet['args']);
        }
    }

    //...section, cpt sanitizer , textbox, checkbox
    public function cptSection()
    {
        echo "Give product attribute info";
    }

    public function cptSanitizer($input)
    {
        $option_name = $this->option_name_product_attributes;

        $output = get_option($option_name);

        if (!$output || !is_array($output)) 
        {
            $output = [];
        }

        if (isset($_POST['remove'])) 
        {
            unset($output[$_POST['remove']]);

            return $output;
        }

        $id = $this->getAttributeId( $input['name']);

        $input['id'] = $id;
        
        $output[$id] = $input;

        // must initialize an empty array        
        return $output;
    }


    //...form elements
    
    public function settingsTextboxField($args)
    {
        $name = $args['label_for'];
        $placeholder = $args['placeholder'];
        $option_name = $args['option_name'];
        $value = '';
        if (isset($_POST['edit'])) {
            $value = (isset(get_option($option_name)[$_POST['edit']][$name])) ? get_option($option_name)[$_POST['edit']][$name]: false;
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

        if (isset($_POST['edit'])) 
        {
            $checked = (isset(get_option($option_name)[$_POST['edit']][$name]))?: false;
        }
        
        echo '<div class="' . $classes . '"><input type="checkbox" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="" ' . ($checked ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
        
    }

    public function settingsSelectField($args)
    {
        $name = $args['label_for'];
        $option_name = $args['option_name'];
        $options = $args['options'];
        $value = '';
        $classes = '';
        if (isset($_POST['edit'])) {
            $value = (isset(get_option($option_name)[$_POST['edit']][$name])) ? get_option($option_name)[$_POST['edit']][$name]: false;
        }
        $id = $name;

        $output = "<select name='$option_name"."[$id]' id='$id'  class='form-control $classes' style='min-width: 100px' >";
 
         if(isset($disabledOption)){
             $output.= "<option value='' >".$disabledOption."</option>";
         }
         foreach ($options as $option_id => $option_title) {
             $selected = ($option_id == $value) ? 'selected' : '';
             $output .= "<option value='$option_id' $selected >$option_title</option>";
         }
         $output.= "</select>";

        echo $output;
    }


     // helping function

     public function getPostArray( $args)
     {        
         $result =[];
 
         $query = new \WP_Query($args);
         
         if($query->have_posts())
         {
             foreach($query->posts as $post) // can't use WP default loop, calling the_post() changes permalink
             {
                 $id = $post->ID;
                 $result[$id] = $post->post_title;
             }
         }
 
         return $result;
 
     }
 
     public function getScriptInitSelect2()
     {
         $script ="
             <script>
             jQuery(document).ready(function($) {
                 $('.searchable').each( function() {
                     $(this).select2();
                 });
             });
 
         </script>
         ";
         return $script;
     }
     
    public function getAttributeId(String $name)
    {
        $id = preg_replace('/[^\p{L}\p{N}\s]/u', '', $name);
        $id = strtolower($id);
        $id = preg_replace('/\s+/', '_', $id);
        $id = "opa_".$id;
        return $id;

    }

    

}
