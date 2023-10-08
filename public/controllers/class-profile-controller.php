<?php

class AP_Profile_Controller extends AP_Base_Controller
{
    public function __construct()
    {
        $this->layout = 'account';
    }

    public function index($username)
    {
        $user = get_user_by('login', $username);
        if (!$user) {
            return ap_abort();
        }
        $title = $user->get('display_name');

        return $this->view('profile/index', compact('user', 'title'));
    }

    public function profile()
    {
        $title = 'Profile';
        $user = wp_get_current_user();

        return $this->view('profile/index', compact('user', 'title'));
    }

    public function edit()
    {
        $title = 'Edit Profile';
        $user = wp_get_current_user();
        $countries = json_decode(file_get_contents(plugin_dir_path(__DIR__) . 'data/countries.json'), true);

        return $this->view('profile/edit', compact('user', 'title', 'countries'));
    }

    public function update()
    {
        $user = wp_get_current_user();

        if ($user->get('user_login') != request('username')) {
            return $this->redirectWith(ap_route('profile.edit'), 'Invalid request sent', 'error');
        }

        $data = [
            'display_name' => request('name'),
            'user_nicename' => request('nickname'),
            'country' => request('country'),
            'profession_title' => request('profession_title'),
            'skills' => request('skills'),
            'languages' => request('languages'),
            'about' => htmlspecialchars(request('about'))
        ];

        if (request('email') != $user->get('user_email')) {
            $data['user_email'] = request('email');
            $data['user_status'] = 0;
        }

        global $wpdb;
        $wpdb->update(
            $wpdb->users,
            $data,
            ['ID' => get_current_user_id()]
        );

        return $this->redirectWith(ap_route('profile.edit'), 'success', 'Profile updated successfully');
    }
}
