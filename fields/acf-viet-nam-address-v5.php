<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_field_viet_nam_address') ) :


class acf_field_viet_nam_address extends acf_field {


	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct( $settings ) {

		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/

		$this->name = 'viet-nam-address';


		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/

		$this->label = __('Viet Nam Address', 'acf-viet-nam-address');


		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/

		$this->category = 'basic';


		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/

		$this->defaults = array(
            'enable_district'	=> 1,
            'enable_village'	=> 1,
            'return_format'	    => 'array',
            'text_format'	    => '{{village}} - {{district}} - {{city}}',
		);


		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('viet-nam-address', 'error');
		*/

		$this->l10n = array(
			'error'	=> __('Error! Please enter a higher value', 'acf-viet-nam-address'),
		);


		/*
		*  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		*/

		$this->settings = $settings;


		// do not delete!
    	parent::__construct();

	}


	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field_settings( $field ) {

		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/
		acf_render_field_setting( $field, array(
            'label'			=> __('Quận/huyện','acf-viet-nam-address'),
            'instructions'	=> '',
            'type'			=> 'true_false',
            'name'			=> 'enable_district',
            'ui'			=> 1,
        ));

        acf_render_field_setting( $field, array(
            'label'			=> __('Xã/phường/thị trấn','acf-viet-nam-address'),
            'instructions'	=> '',
            'type'			=> 'true_false',
            'name'			=> 'enable_village',
            'ui'			=> 1,
        ));

        acf_render_field_setting( $field, array(
            'label'			=> __('Định dạng trả về','acf-viet-nam-address'),
            'instructions'	=> '',
            'type'			=> 'radio',
            'name'			=> 'return_format',
            'choices'		=> array(
                'array'	    	=> __("Array(ID and Name)",'acf-viet-nam-address'),
                'id'			=> __("ID",'acf-viet-nam-address'),
                'text'			=> __("Địa chỉ",'acf-viet-nam-address'),
            ),
            'layout'	=>	'horizontal',
        ));

        acf_render_field_setting( $field, array(
            'label'			=> __('Định dạng địa chỉ','acf-viet-nam-address'),
            'instructions'	=> __('{{village}}: Xã phường <br />{{district}}: Quận/huyện <br />{{city}}: Tỉnh/thành phố','acf-viet-nam-address'),
            'type'			=> 'text',
            'name'			=> 'text_format',
        ));
	}



	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field( $field ) {
	    $enable_city = 1;
        $enable_district = isset($field['enable_district']) ? intval($field['enable_district']) : 0;
        $enable_village = isset($field['enable_village']) ? intval($field['enable_village']) : 0;
        $required = isset($field['required']) ? intval($field['required']) : 0;
        $city_value = isset($field['value']['city']) ? wc_clean(wp_unslash($field['value']['city'])) : '';
        $district_value = isset($field['value']['district']) ? sprintf("%03d", intval($field['value']['district'])) : '';
        $village_value = isset($field['value']['village']) ? sprintf("%05d", intval($field['value']['village'])) : '';

        $list_cities = new acf_plugin_viet_nam_address();
        $cities = $list_cities->get_all_cities();
		?>
        <div class="acf_vietnam_address acf-input">
            <?php if($enable_city && is_array($cities) && $cities):?>
                <div class="acf_vietnam_address_input">
                <select name="<?php echo esc_attr($field['name'])?>[city]" data-ui="1" class="acf_viet_nam_select2 acf_vietnam_city" data-placeholder="<?php _e('Chọn tỉnh/thành phố','acf-viet-nam-address')?>">
                    <option value="" selected="selected"><?php _e('Chọn tỉnh/thành phố','acf-viet-nam-address')?></option>
                    <?php foreach ($cities as $k=>$v):?>
                    <option value="<?php echo $k;?>" <?php selected($k, $city_value, true)?>><?php echo $v;?></option>
                    <?php endforeach;?>
                </select>
                </div>
                <?php if($enable_district):?>
                    <div class="acf_vietnam_address_input">
                    <select name="<?php echo esc_attr($field['name'])?>[district]" data-ui="1" class="acf_viet_nam_select2 acf_vietnam_district" data-placeholder="<?php _e('Chọn quận/huyện','acf-viet-nam-address')?>">
                        <option value="" selected="selected"><?php _e('Chọn quận/huyện','acf-viet-nam-address')?></option>
                        <?php
                        if($district_value):
                        $districts = $list_cities->get_list_district($city_value);
                        foreach ($districts as $v){
                            $maqh = (isset($v['maqh']))?$v['maqh']:'';
                            $tenqh = (isset($v['name']))?$v['name']:'';
                            ?>
                                <option value="<?php echo $maqh;?>" <?php selected($district_value, $maqh, true)?>><?php echo $tenqh;?></option>
                            <?php
                        }
                        endif;
                        ?>
                    </select>
                    </div>
                    <?php if($enable_village):?>
                    <div class="acf_vietnam_address_input">
                    <select name="<?php echo esc_attr($field['name'])?>[village]" data-ui="1" class="acf_viet_nam_select2 acf_vietnam_village" data-placeholder="<?php _e('Chọn xã/phường','acf-viet-nam-address')?>">
                        <option value="" selected="selected"><?php _e('Chọn xã/phường','acf-viet-nam-address')?></option>
                        <?php
                        if($village_value):
                            $villages = $list_cities->get_list_village($district_value);
                            foreach ($villages as $v){
                                $maqh = (isset($v['xaid']))?$v['xaid']:'';
                                $tenqh = (isset($v['name']))?$v['name']:'';
                                ?>
                                <option value="<?php echo $maqh;?>" <?php selected($village_value, $maqh, true)?>><?php echo $tenqh;?></option>
                                <?php
                            }
                        endif;
                        ?>
                    </select>
                    </div>
                    <?php endif;?>
                <?php endif;?>
            <?php endif;?>
        </div>
		<?php
	}

    function field_group_admin_enqueue_scripts() {
        $this->enqueue_assets();
    }

	function input_admin_enqueue_scripts() {
        $this->enqueue_assets();
	}

    function enqueue_assets() {

        // globals
        global $wp_scripts, $wp_styles;


        // vars
        $version = '3.5.2';
        $lang = get_locale();
        $min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        //$lang = 'fr';


        // v4
        /*
                wp_enqueue_script('select2', acf_get_dir("assets/inc/select2/dist/js/select2.full.js"), array('jquery'), '4.0', true );
                wp_enqueue_style('select2', acf_get_dir("assets/inc/select2/dist/css/select2{$min}.css"), '', '4.0' );
                return;
        */

        // register script
        if( !isset($wp_scripts->registered['select2']) ) {

            // scripts
            wp_register_script('select2', acf_get_dir("assets/inc/select2/select2{$min}.js"), array('jquery'), $version );


            // translation
            if( $lang ) {

                // vars
                $lang = str_replace('_', '-', $lang);
                $lang_code = substr($lang, 0, 2);
                $lang_src = '';


                // attempt 1
                if( file_exists(acf_get_path("assets/inc/select2/select2_locale_{$lang_code}.js")) ) {

                    $lang_src = acf_get_dir("assets/inc/select2/select2_locale_{$lang_code}.js");

                    // attempt 2
                } elseif( file_exists(acf_get_path("assets/inc/select2/select2_locale_{$lang}.js")) ) {

                    $lang_src = acf_get_dir("assets/inc/select2/select2_locale_{$lang}.js");

                }


                // enqueue
                if( $lang_src ) {

                    wp_enqueue_script('select2-l10n', $lang_src, array('select2'), $version );

                }

            }
            // end translation

        }


        // register style
        if( !isset($wp_styles->registered['select2']) ) {

            wp_register_style('select2', acf_get_dir('assets/inc/select2/select2.css'), '', $version );

        }


        // enqueue
        wp_enqueue_script('select2');
        wp_enqueue_style('select2');

        // vars
        $url = $this->settings['url'];
        $version = $this->settings['version'];


        // register & include JS
        wp_register_script( 'acf-vietnam-address', "{$url}assets/js/input.js", array('acf-input', 'jquery'), $version );
        $php_array = array(
            'admin_ajax'		=>	admin_url( 'admin-ajax.php'),
            'home_url'			=>	home_url(),
            'none_acfvn'        =>  wp_create_nonce('acf_vn_nonce')
        );
        wp_localize_script( 'acf-vietnam-address', 'devvn_acf_vn', $php_array );
        wp_enqueue_script('acf-vietnam-address');


        // register & include CSS
        wp_register_style( 'acf-vietnam-address', "{$url}assets/css/input.css", array('acf-input'), $version );
        wp_enqueue_style('acf-vietnam-address');


    }

	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function input_admin_head() {



	}

	*/


	/*
   	*  input_form_data()
   	*
   	*  This function is called once on the 'input' page between the head and footer
   	*  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and
   	*  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
   	*  seen on comments / user edit forms on the front end. This function will always be called, and includes
   	*  $args that related to the current screen such as $args['post_id']
   	*
   	*  @type	function
   	*  @date	6/03/2014
   	*  @since	5.0.0
   	*
   	*  @param	$args (array)
   	*  @return	n/a
   	*/

   	/*

   	function input_form_data( $args ) {



   	}

   	*/


	/*
	*  input_admin_footer()
	*
	*  This action is called in the admin_footer action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_footer)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function input_admin_footer() {



	}

	*/


	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function field_group_admin_head() {

	}

	*/


	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/

	/*

	function load_value( $value, $post_id, $field ) {

		return $value;

	}

	*/


	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/

	/*

	function update_value( $value, $post_id, $field ) {

		return $value;

	}

	*/


	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/

	function format_value( $value, $post_id, $field ) {
		if( empty($value) || !is_array($value)) {
			return false;
		}
		$dghc = new acf_plugin_viet_nam_address();
		$city       = (isset($value['city']))?$value['city']:'';
        $district   = (isset($value['district']))?$value['district']:'';
        $village    = (isset($value['village']))?$value['village']:'';
        $nameCity       = $dghc->get_name_city($city);
        $nameDistrict   = $dghc->get_name_district($district);
        $nameVillage    = $dghc->get_name_village($village);
        if( $field['return_format'] == 'array' ) {
            $new_array = array();
		    $new_array['city'] = array(
                'id'    =>  $city,
                'name'  =>  $nameCity
            );
		    $new_array['district'] = array(
                'id'    =>  $district,
                'name'  =>  $nameDistrict
            );
		    $new_array['village'] = array(
                'id'    =>  $village,
                'name'  =>  $nameVillage
            );
            $value = $new_array;
		    return $value;
		}elseif ($field['return_format'] == 'text'){
            $text_format = (isset($field['text_format']) && $field['text_format'])?$field['text_format']:$this->defaults['text_format'];
            $value = str_replace("{{city}}", $nameCity, $text_format);
            $value = str_replace("{{district}}", $nameDistrict, $value);
            $value = str_replace("{{village}}", $nameVillage, $value);
            return $value;
        }elseif ($field['return_format'] == 'id'){
            return $value;
        }
		return $value;
	}

	function validate_value( $valid, $value, $field, $input ){
	    if($field['required']) {
            if ($value['city'] == '') {
                $valid = __('Chọn tỉnh/thành phố', 'acf-viet-nam-address');
            } elseif ($value['district'] == '') {
                $valid = __('Chọn quận/huyện', 'acf-viet-nam-address');
            } elseif ($value['village'] == '') {
                $valid = __('Chọn xã/phường', 'acf-viet-nam-address');
            }
        }else{
	        $valid = true;
        }
		return $valid;

	}
	/*
	*  delete_value()
	*
	*  This action is fired after a value has been deleted from the db.
	*  Please note that saving a blank value is treated as an update, not a delete
	*
	*  @type	action
	*  @date	6/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (mixed) the $post_id from which the value was deleted
	*  @param	$key (string) the $meta_key which the value was deleted
	*  @return	n/a
	*/

	/*

	function delete_value( $post_id, $key ) {



	}

	*/


	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/

	/*

	function load_field( $field ) {

		return $field;

	}

	*/


	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/

	/*

	function update_field( $field ) {

		return $field;

	}

	*/


	/*
	*  delete_field()
	*
	*  This action is fired after a field is deleted from the database
	*
	*  @type	action
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	n/a
	*/

	/*

	function delete_field( $field ) {



	}

	*/


}


// initialize
new acf_field_viet_nam_address( $this->settings );


// class_exists check
endif;

?>