<?php

class Short_Code_Service extends Base_Service
{
	public static function init()
	{
		$self = new self;
		$self->add_short_code('autorepublicate_profile', 'profile');
	}

	/**
	 * Central location to create all shortcodes.
	 */
	function profile()
	{
		$user = get_query_var( 'user' );
		echo $user;
		exit;
		$user = wp_get_current_user();
		autopublicate_file_loader('public/views/profile.php', compact('user'));
	}
}
