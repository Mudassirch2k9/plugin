<?php
use OPA\Inc\Base\BaseController;

$baseController = new BaseController();
?>
<div class="wrap">

    <h1>Filter Questions</h1>

	<?php settings_errors();?>

	<ul class="nav nav-tabs">

		<li class="<?php echo isset($_POST['edit']) ?: 'active'; ?>"><a href="#tab-1">Options</a></li>


	</ul>

	<div class="tab-content">

		<div id="tab-1" class="tab-pane active">

            
        
			<form method="post" action="options.php" id="question_form_other">
        <?php
            settings_fields($this->option_group_filter_question_other);

            do_settings_sections($this->page_slug_filter_question_other);

            submit_button();

            ?>

            </form>

        </div>

		<!-- <div id="tab-2" class="tab-pane">

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
		</div> -->
	</div>
</div>