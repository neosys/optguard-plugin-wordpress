<?php
defined( 'WP_UNINSTALL_PLUGIN' ) || die( 'Cheatin&#8217; uh?' );

// Delete WP Rocket options.
delete_option( 'optguard_field_access_key' );
delete_option( 'optguard_field_secret_key' );
