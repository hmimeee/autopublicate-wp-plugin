<?php

class Autopublicate_Request
{
    private $data;

    private $files;

    private $rules;

    public function __construct()
    {
        $post = $_POST;
        $get = $_GET;
        $file = $_FILES;
        $session = $_SESSION['_request'] ?? [];

        $this->data = array_merge($get, $post, $file, $session);

        foreach ($this->data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function server($key = null)
    {
        $server = $_SERVER;
        $data = [];

        foreach ($server as $index => $value) {
            $data[$index] = $value;
        }

        if ($key)
            return $data[$key];

        return (object) $data;
    }

    /**
     * Validation data parsing method
     * 
     * @param array $rule Variable must be array with associative rules
     * @return boolean
     */
    public function validate($rules)
    {
        $this->rules = $rules;
        $data = $this->data;

        foreach ($rules as $key => $rulesGroup) {
            $rulesGroup = explode('|', $rulesGroup);
            if (in_array('nullable', $rulesGroup) && (is_null($data[$key]) || (isset($data[$key]['tmp_name']) && ((is_array($data[$key]['tmp_name']) && !count($data[$key]['tmp_name'])) && (is_null($data[$key]['tmp_name'])) || $data[$key]['tmp_name'] == ''))))
                return static::message(true, "Validated successfully");

            foreach ($rulesGroup as $rule) {
                $dt = isset($data[$key]) ? $data[$key] : null;
                $check = $this->checkValid($rule, $dt, $key);

                if (!$check['status'])
                    return $check;
            }
        }

        return static::message(true, "Validated successfully");
    }

    /**
     * Check the data if valid or not.
     * 
     * @param string $rule Rule name to check the data
     * @param mixed $data Data varibale
     * @param string $key Key of the data
     * @return boolean
     */
    public function checkValid($rule, $data, $key)
    {
        $rawKey = $key;
        $key = 'The ' . str_replace('_', ' ', $key);
        $explode_rule = explode(':', $rule);

        if ($rule == 'required' && (is_null($data) || empty($data) || $data == ''))
            return static::message(false, "$key field is required");

        else if ($rule == 'numeric' && !is_numeric($data))
            return static::message(false, "$key must be a numeric value");

        else if ($rule == 'string' && !is_string($data))
            return static::message(false, "$key must be a string value");

        else if ($rule == 'date' && $data != date('Y-m-d', strtotime($data)))
            return static::message(false, "$key invalid date format");

        else if ($rule == 'file' && (!isset($data['tmp_name']) || (!is_uploaded_file($data['tmp_name']) && !is_uploaded_file(reset($data['tmp_name']))))) {
            return static::message(false, "$key must be a file");
        } else if (count($explode_rule) > 1) {

            if (reset($explode_rule) == 'date') {
                $format = end($explode_rule);
                if ($data != date($format, strtotime($data))) static::message(false, "$key field must have the date format '$format'");
            } else if (reset($explode_rule) == 'in') {
                $allowed_values = explode(',', end($explode_rule));

                if (!in_array($data, $allowed_values))
                    return static::message(false, "$key allowed values are: " . end($explode_rule));
            } else if (strrpos($this->rules[$rawKey], 'numeric')  && is_numeric($data)) {
                $min = intval(end($explode_rule));
                if (reset($explode_rule) == 'min' && intval($data) < $min)
                    return static::message(false, "$key must be grater than or equal to $min");

                $max = intval(end($explode_rule));
                if (reset($explode_rule) == 'max' && intval($data) > $max)
                    return static::message(false, "$key should not be grater than $max");
            } else if (is_string($data)) {
                $min = intval(end($explode_rule));
                if (reset($explode_rule) == 'min' && strlen($data) < $min)
                    return static::message(false, "$key should not be less than " . $min . " characters");

                $max = intval(end($explode_rule));
                if (reset($explode_rule) == 'max' && strlen($data) > $max)
                    return static::message(false, "$key should not be grater than " . $max . " characters");
            } else if (isset($data['tmp_name']) && (is_uploaded_file($data['tmp_name']) || is_uploaded_file(reset($data['tmp_name'])))) {
                $sizes = is_array($data['size']) ? $data['size'] : [$data['size']];
                $limit = intval(end($explode_rule));
                $convertedSize = $limit > 1024 ? $limit / 1024 . 'MB' : $limit . 'KB';

                foreach ($sizes as $size) {
                    if (reset($explode_rule) == 'max' && $size > ($limit * 1024))
                        return static::message(false, "$key maximum upload size is {$convertedSize}");

                    else if (reset($explode_rule) == 'min' && $size < ($limit * 1024))
                        return static::message(false, "$key minimum upload size is {$convertedSize}");
                }
            }
        }

        return static::message(true, "Validated successfully");
    }

    private static function message($status = true, $message = 'Invalid data')
    {
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    public function only(...$params)
    {
        return array_intersect_key($this->data, array_flip($params));
    }

    public function all()
    {
        return $this->data;
    }

    public function file($key)
    {
        $this->files = $this->data[$key];

        return $this;
    }

    public function save()
    {
        $attachments = [];
        if (is_array(reset($this->files))) {
            foreach ($this->files['name'] as $key => $file) {
                $file = array(
                    'name' => $this->files['name'][$key],
                    'type' => $this->files['type'][$key],
                    'tmp_name' => $this->files['tmp_name'][$key],
                    'error' => $this->files['error'][$key],
                    'size' => $this->files['size'][$key]
                );

                $file_key = $file['name'] . time();
                $_FILES[$file_key] = $file;
                $attachments[] = $this->upload($file_key);
            }
        } else {
            $file_key = $this->files['name'] . time();
            $_FILES[$file_key] = $this->files;
            $attachments[] = $this->upload($file_key);
        }

        return $attachments;
    }

    public function store()
    {
        if (is_array(reset($this->files))) return false;

        $time = current_time('mysql');
        $file = wp_handle_upload($this->files, array('test_form' => false), $time);

        if (isset($file['error'])) {
            return ['status' => false, 'message' => $file['error']];
        }

        return ['status' => true, 'data' => $file['url']];
    }

    private function upload($file)
    {
        // check to make sure its a successful upload
        if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) return false;

        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');

        return media_handle_upload($file, 0);
    }
}
