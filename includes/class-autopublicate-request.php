<?php

class Autopublicate_Request
{
    private static $data;

    public function __construct()
    {
        $post = $_POST;
        $get = $_GET;
        self::$data = array_merge($get, $post);

        foreach (self::$data as $key => $value) {
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
    public static function validate($rules)
    {
        $data = self::$data;
        $validate = true;

        foreach ($data as $index => $dt) {

            $has_rule = isset($rules[$index]) ? $rules[$index] : null;
            if (!$has_rule)
                continue;

            $explode_rules = explode('|', $has_rule);

            foreach ($explode_rules as $rule) {
                $check = static::checkValid($rule, $dt, $index);

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
    public static function checkValid($rule, $data, $key)
    {
        if ($rule == 'numeric' && !is_numeric($data))
            return static::message(false, "$key must be an integer value");

        if ($rule == 'string' && !is_string($data))
            return static::message(false, "$key must be a string value");

        $explode_rule = explode(':', $rule);
        if (count($explode_rule) > 1) {

            $min = intval(end($explode_rule));
            if (reset($explode_rule) == 'min' && intval($data) < $min)
                return static::message(false, "$key should not be less than " . $min . " characters");

            $max = intval(end($explode_rule));
            if (reset($explode_rule) == 'max' && intval($data) > $max)
                return static::message(false, "$key should not be grater than " . $max . " characters");
        }

        $has_in = explode(':', $rule);
        if (reset($has_in) == 'in') {
            $allowed_values = explode(',', end($has_in));

            if (!in_array($data, $allowed_values))
                return static::message(false, "$key allowed values are: " . end($has_in));
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
}
