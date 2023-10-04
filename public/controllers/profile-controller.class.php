<?php

class Profile_Controller extends Base_Controller
{
    public function index($username)
    {
        $user = get_user_by('login', $username);
        if (!$user) {
            return abort();
        }
        $title = $user->get('display_name');

        return $this->view('profile', compact('user', 'title'));
    }

    public function profile()
    {
        $title = 'Profile';
        $user = wp_get_current_user();

        return $this->view('profile', compact('user', 'title'));
    }
}
