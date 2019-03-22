<?php


/**
 * @package  OPA Plugin
*/

namespace OPA\Inc\Api;

class FormElementApi  
{
    
    public function dateBoxField($field, $value)
    {
        $id = $field['id'];
        $classes = ($field['classes']) ?: '';
        $title = $field['title'];
        $placeholder = (isset($field['placeholder'])) ? $field['placeholder'] : '';

        $output = "<th scope='row'><label for='$id' class=' $classes'>$title</label></th>"
            . "<td><input type='text' class='form-control' name='$id' id='$id' placeholder=' $placeholder '  value='$value' />
            <script>
                jQuery(document).ready(function($) {
                    $('#$id').datepicker({
                        changeMonth: true,
                        changeYear: true,
                        maxDate: '+0D',
                        yearRange : '-120yy:+0yy'
                    });
                });
            </script>
            </td>
            ";

        return $output;
    }
    
    public function selectBoxField($field, $value)
    {
        $id = $field['id'];
        $classes = ($field['classes']) ? $field['classes']: '';
        $title = $field['title'];
        $disabledOption = (isset($field['placeholder']))? $field['placeholder']:null;
        $options ='';

        if (isset($field['get_options'])) 
        {
            $function = $field['get_options'][0];
            $args = [$field['get_options'][1]];
            $options = call_user_func_array($function, $args);

        } else{

            $options = $field['options'];
        };

        $output = "
        <th> <label for='$id'>$title</label> </th>
        <td> ";
        
        $output .= $this->getSelectOptions($id, $options, $value, $classes, $disabledOption);
        $output .="</td>";

        return $output;
    }

    public function textBoxField($field, $value = null)
    {
        $id = $field['id'];
        $classes = ($field['classes']) ?: '';
        $title = $field['title'];
        $data_list = isset($field['data_list'])?$field['data_list']: false;
        $placeholder = (isset($field['placeholder'])) ? $field['placeholder'] : '';

        $output = "<th scope='row'><label for='$id' class='prfx-row-title $classes'>$title</label></th>"
            . "<td><input type='text' class='form-control' name='$id' id='$id' placeholder=' $placeholder '  value='$value' /></td>";

        return $output;
    }

    public function checkBoxField($field, $value = null)
    {
        $id = $field['id'];
        $classes = ($field['classes']) ?: '';
        $title = $field['title'];
        
        $cheacked = ( $value == true)?' checked': '';
        
        $output = "<th scope='row'><label for='$id' class='prfx-row-title $classes'>$title</label></th>"
            . "<td>
            <div class=' ui-toggle  $classes '>

            <input type='hidden' name='$id' value='0'  >
            <input type='checkbox' id=' $id ' name='$id' value='1' class=''  $cheacked >
                    <label for=' $id '><div></div></label>
            </div>
            
            
            </td>";
            
    
        return $output;

    }
    
    public function numberBoxField($field, $value = null)
    {
        $id = $field['id'];
        $classes = ($field['classes']) ?: '';
        $title = $field['title'];
        $placeholder = (isset($field['placeholder'])) ? $field['placeholder'] : '';

        $output = "<th scope='row'><label for='$id' class='prfx-row-title $classes'>$title</label></th>"
            . "<td><input type='number' step='0.01' class='form-control' name='$id' id='$id' placeholder=' $placeholder '  value='$value' /></td>";

        return $output;
    }



     // helping function

     public function getSelectOptions($id, $options,  $value = null, $classes = null, $disabledOption = null){
         
         $output = "<select name='$id' id='$id'  class='form-control $classes' style='min-width: 100px' >";
 
         if(isset($disabledOption)){
             $output.= "<option value='' >".$disabledOption."</option>";
         }
         foreach ($options as $option_id => $option_title) {
             $selected = ($option_id == $value) ? 'selected' : '';
             $output .= "<option value='$option_id' $selected >$option_title</option>";
         }
         $output.= "</select>";
 
         return $output;
     }

     
}
