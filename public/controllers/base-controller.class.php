<?php

class Base_Controller
{
    public function view($file, $params)
    {
        status_header(200);

        add_filter('document_title_parts', function ($parts) use ($file, $params) {
            $parts['title'] = __($params['title'] ?? ucfirst(basename($file)));
            return $parts;
        }, 10000);

        add_action('elementor/theme/before_do_single', function ($location) use ($file, $params) {
            autopublicate_file_loader('public/views/' . $file . '.php', $params);

            get_footer();
            exit;
        });
    }
}
