<?php

//Option name

define('PIB_CATEGORY_FIELDS', 'pib_category_fields_option');

//Add Checkbox to Category Edit Screen 

add_action('edit_category_form_fields', 'pib_category_fields');

function pib_category_fields($tag) {
	$t_id = $tag->term_id;
    $tag_extra_fields = get_option(PIB_CATEGORY_FIELDS);
	
    if ( $tag_extra_fields[$t_id]['checkbox'] == true)
        $pib_category_checked = '';
    else
        $pib_category_checked = 'checked="checked"';

    ?>
		
    <table class="form-table">
        <tr class="form-field">
            <th scope="row" valign="top">
                <h3>"Pin It" Button Settings</h3>
            </th>
        </tr>
        <tbody>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="pib_category_field">Show "Pin It" Button</label>
                </th>
                <td>
                    <input name="pib_category_field" id="pib_category_field" type="checkbox" value="true" style="width: auto;"
                        <?php echo $pib_category_checked; ?> />
                    <p class="description">
                        If checked displays the button for this category (if <strong>Archives</strong> also checked in
                        <a href="<?php echo admin_url( 'admin.php?page=' . PIB_PLUGIN_BASENAME ); ?>">"Pin It" Button Settings</a>).
                        If unchecked the button will <strong>always</strong> be hidden for this category.
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    
    <?php
}

// when the form gets submitted, and the category gets updated (in your case the option will get updated with the values of your custom fields above

add_action('edited_category', 'update_pib_category_fields');

function update_pib_category_fields($term_id) {
    if ( $_POST['taxonomy'] == 'category' ) {
        $tag_extra_fields = get_option(PIB_CATEGORY_FIELDS);
        $tag_extra_fields[$term_id]['checkbox'] = strip_tags($_POST['pib_category_field']);

        if ( $_POST['pib_category_field'] != true ) {
            $tag_extra_fields[$term_id]['checkbox'] = true;
            update_option( PIB_CATEGORY_FIELDS, $tag_extra_fields );
        }
        if ( $_POST['pib_category_field'] == true ) {
            $tag_extra_fields[$term_id]['checkbox'] = "";
            update_option( PIB_CATEGORY_FIELDS, $tag_extra_fields );
        }
    }
}
