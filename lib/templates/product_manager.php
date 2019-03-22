<?php
    use OPA\Inc\Base\BaseController;

    $baseController = new BaseController();
?>
<div class="wrap">

    <h1>Product Manager</h1>

	<?php settings_errors();?>

	<ul class="nav nav-tabs">

		<li class="<?php echo isset($_POST['edit']) ?: 'active'; ?>"><a href="#tab-1">Your Product Attributes</a></li>

        <li class=" <?php echo isset($_POST['edit']) ? 'active' : ''; ?>"><a href="#tab-2"><?php echo isset($_POST['edit']) ? 'Update ' : 'Add '; ?> Product Attribute</a></li>

	</ul>

	<div class="tab-content">

		<div id="tab-1" class="tab-pane <?php echo isset($_POST['edit']) ?: 'active'; ?>">

             <table class="cpt-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Placeholder</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
<?php
$attributes = (get_option($baseController->option_name_product_attributes)) ?: [];

foreach ($attributes as $attr) {
    $id         =   (isset($attr['id'])) ? $attr['id']: "";
    $name       =   (isset($attr['name'])) ? $attr['name']: "";
    $type       =   (isset($attr['type'])) ? $attr['type']: "";
    $placeholder =  (isset($attr['placeholder'])) ? $attr['placeholder']: "";

    echo "<tr scope='row'>";

    echo "<td>$id </td>";

    echo "<td>$name </td>";

    echo "<td>$type</td>";

    echo "<td>$placeholder</td>";

    echo '<td>';

    echo '</form> ';

    echo ' <form method="post" action="options.php" class="inline-block">';

    settings_fields($baseController->option_group_product_manager);

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

			<form method="post" action="options.php">
<?php

settings_fields($baseController->option_group_product_manager);

do_settings_sections($baseController->page_slug_product_manager);

submit_button();

?>
            </form>

		</div>

		<div id="tab-3" class="tab-pane">

            <h3>About</h3>

		</div>
	</div>
</div>