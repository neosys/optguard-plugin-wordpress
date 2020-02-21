<?php

	/**
	 * Theme Options v1.1.0
	 * Adjust theme settings from the admin dashboard.
	 * 
	 * TO-DO: Pull Account API data from optGuard automatically (support limit checking/reporting, log reporting, account level, etc)
	 */

	/**
	 * Get data from the optGuard API
	 * @param  string $access_key The account access_key
	 * @return array         Data from the MailChimp API
	 */
	function optguard_api_get_account_data($access_key = null)
	{

		// $options = optguard_api_get_options();

		if (empty($options['optguard_access_key'])) return;

		// Create API call
		$url = 'https://api.optguard.com/v1/account?access_key=' . $options['optguard_access_key'];

		// Get data from  optGuard
		$request = wp_remote_get($url, $params);
		$response = wp_remote_retrieve_body($request);
		$data = json_decode($response, true);

		return $data;
	}

	/**
	 * Theme Options Menu
	 * Each option field requires its own add_settings_field function.
	 * 
	 * https://developer.wordpress.org/plugins/settings/custom-settings-page/
	 */

	/**
	 * @internal never define functions inside callbacks.
	 * these functions could be run multiple times; this would result in a fatal error.
	 */

	/**
	 * custom option and settings
	 */
	function optguard_settings_init()
	{
		// register a new setting for "optguard" page
		register_setting('optguard', 'optguard_options');

		// register a new section in the "optguard" page
		add_settings_section(
			'optguard_section_credentials',
			__('Enter optGuard Account Credentials', 'optguard'),
			'optguard_section_credentials_cb',
			'optguard'
		);

		// register a new field in the "optguard_section_credentials" section, inside the "optguard" page
		add_settings_field(
			'optguard_field_access_key', // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			__('Access Key', 'optguard'),
			'optguard_field_access_key_cb',
			'optguard',
			'optguard_section_credentials',
			[
				'label_for' => 'optguard_field_access_key',
				'class' => 'optguard_row',
				'optguard_custom_data' => 'custom',
			]
		);

		// register a new field in the "optguard_section_credentials" section, inside the "optguard" page
		add_settings_field(
			'optguard_field_secret_key', // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			__('Access Key', 'optguard'),
			'optguard_field_secret_key_cb',
			'optguard',
			'optguard_section_credentials',
			[
				'label_for' => 'optguard_field_secret_key',
				'class' => 'optguard_row',
				'optguard_custom_data' => 'custom',
			]
		);
	}

	/**
	 * register our optguard_settings_init to the admin_init action hook
	 */
	add_action('admin_init', 'optguard_settings_init');

	/**
	 * custom option and settings:
	 * callback functions
	 */

	// credentials section cb

	// section callbacks can accept an $args parameter, which is an array.
	// $args have the following keys defined: title, id, callback.
	// the values are defined at the add_settings_section() function.
	function optguard_section_credentials_cb($args)
	{
	?>
		<p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Follow the white rabbit.', 'optguard'); ?></p>
	<?php
	}

	// access key field cb

	// field callbacks can accept an $args parameter, which is an array.
	// $args is defined at the add_settings_field() function.
	// wordpress has magic interaction with the following keys: label_for, class.
	// the "label_for" key value is used for the "for" attribute of the <label>.
	// the "class" key value is used for the "class" attribute of the <tr> containing the field.
	// you can add custom key value pairs to be used inside your callbacks.
	function optguard_field_access_key_cb($args)
	{
		// get the value of the setting we've registered with register_setting()
		$options = get_option('optguard_options');
		// output the field
	?>
		<input type="text" value="<?php echo get_option(esc_attr($args['label_for']));?>" id="<?php echo esc_attr($args['label_for']); ?>" data-custom="<?php echo esc_attr($args['optguard_custom_data']); ?>" name="optguard_options[<?php echo esc_attr($args['label_for']); ?>]">

		<p class="description">
			<?php esc_html_e('Enter your optGuard account Access Key', 'optguard'); ?>
		</p>
	<?php
	}

	function optguard_field_secret_key_cb($args)
	{
		// get the value of the setting we've registered with register_setting()
		$options = get_option('optguard_options');
		// output the field
	?>
		<input type="text" value="<?php echo get_option(esc_attr($args['label_for']));?>" id="<?php echo esc_attr($args['label_for']); ?>" data-custom="<?php echo esc_attr($args['optguard_custom_data']); ?>" name="optguard_options[<?php echo esc_attr($args['label_for']); ?>]">

		<p class="description">
			<?php esc_html_e('Enter your optGuard account Secret Key', 'optguard'); ?>
		</p>
	<?php
	}

	/**
	 * top level menu
	 */
	function optguard_options_page()
	{
		// add top level menu page
		add_menu_page(
			'optGuard Anti-Fraud',
			'optGuard Options',
			'manage_options',
			'optguard',
			'optguard_options_page_html'
		);
	}

	/**
	 * register our optguard_options_page to the admin_menu action hook
	 */
	add_action('admin_menu', 'optguard_options_page');

	/**
	 * top level menu:
	 * callback functions
	 */
	function optguard_options_page_html()
	{
		// check user capabilities
		if (!current_user_can('manage_options')) {
			return;
		}

		// add error/update messages

		// check if the user have submitted the settings
		// wordpress will add the "settings-updated" $_GET parameter to the url
		if (isset($_GET['settings-updated'])) {
			// add settings saved message with the class of "updated"
			add_settings_error('optguard_messages', 'optguard_message', __('Settings Saved', 'optguard'), 'updated');
		}

		// show error/update messages
		settings_errors('optguard_messages');
	?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "optguard"
				settings_fields('optguard');
				// output setting sections and their fields
				// (sections are registered for "optguard", each field is registered to a specific section)
				do_settings_sections('optguard');
				// output save settings button
				submit_button('Save Settings');
				?>
			</form>
		</div>
	<?php
	}
