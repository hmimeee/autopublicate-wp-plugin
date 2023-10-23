<?php

class AP_Profile_Controller extends AP_Base_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layout = 'account';
        $this->user = wp_get_current_user();
    }

    public function index($username)
    {
        $this->user = get_user_by('login', $username);
        if (!$this->user) {
            return ap_abort();
        }
        $title = $this->user->get('display_name');

        return $this->view('profile/index', compact('title'));
    }

    public function profile()
    {
        $title = 'Profile';

        return $this->view('profile/index', compact('title'));
    }

    public function edit()
    {
        $title = 'Edit Profile';
        $countries = json_decode(file_get_contents(plugin_dir_path(__DIR__) . 'data/countries.json'), true);

        return $this->view('profile/edit', compact('title', 'countries'));
    }

    public function update()
    {
        $validate = request()->validate([
            'image' => 'nullable|file|max:256',
            'name' => 'required|string|min:5',
            'profession_title' => 'required|string|min:5'
        ]);

        if (!$validate['status']) {
            return $this->redirectWith(ap_route('profile.edit'), $validate['message'], 'error');
        }

        if ($this->user->get('user_login') != request('username')) {
            return $this->redirectWith(ap_route('profile.edit'), 'Invalid request sent', 'error');
        }

        if (!request('name')) {
            return $this->redirectWith(ap_route('profile.edit'), 'Full name field is required', 'error');
        }

        if (!is_email(request('email'))) {
            return $this->redirectWith(ap_route('profile.edit'), 'Invalid email address', 'error');
        }

        $data = [
            'display_name' => request('name'),
            'user_nicename' => request('nickname'),
            'country' => request('country'),
            'profession_title' => request('profession_title'),
            'skills' => request('skills'),
            'languages' => request('languages'),
            'professional_description' => request('professional_description') ? htmlentities(stripslashes(request('professional_description'))) : null,
            'about' => request('about') ? strip_tags(stripslashes(request('about'))) : null
        ];

        if (request('image')['tmp_name']) {
            $upload = request()->file('image')->store();
            if (!$upload['status']) {
                return $this->redirectWith(ap_route('profile.edit'), $upload['message'], 'error');
            }
            
            $data['image'] = $upload['data'];
        }

        if (request('email') != $this->user->get('user_email')) {
            $exists = get_user_by('email', request('email'));

            if ($exists->get('ID') != get_current_user_id()) {
                return $this->redirectWith(ap_route('profile.edit'), 'The email address is already associated with an user', 'error');
            }

            $data['user_email'] = request('email');
            $data['user_status'] = 0;
        }

        global $wpdb;
        $wpdb->update(
            $wpdb->users,
            $data,
            ['ID' => get_current_user_id()]
        );

        return $this->redirectWith(ap_route('profile'), 'Profile updated successfully');
    }
}
