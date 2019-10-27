<?php 

if (! defined('API_HOST')) {
    define("API_HOST", getenv('APP_URL'));
}

if ( ! function_exists('get_json_response') ) {
    function get_json_response($params = null, $status = 200, array $headers = [], $options = 0) {
        $responses = [
            'status' => $status,
            'success' => isset($params['success']) ? $params['success'] : true,
            'message' => isset($params['message']) ? $params['message'] : null,
            'data' => isset($params['data']) ? $params['data'] : null,
            'exceptions' => isset($params['exceptions']) ? $params['exceptions'] : null
        ];

        if (isset($params['metadata'])) {
            $responses['metadata'] = $params['metadata'];
        }

        return response()->json($responses, 200);
    }
}

if ( ! function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if ( ! function_exists('bcrypt')) {
    /**
     * Generate a hash of the secret string
     *
     * @param string $secret
     * @return mixed
     */
    function bcrypt($secret = '')
    {
        return app('hash')->make($secret);
    }
}

if ( ! function_exists('debug')) {
    function debug($params, $withDie = true)
    {
        echo "<pre>";
        print_r($params);
        echo "</pre>";

        if ($withDie == true) {
            die;
        }
    }
}

if ( ! function_exists('dump')) {
    function dump($params, $withDie = true)
    {
        echo "<pre>";
        var_dump($dump);
        echo "</pre>";

        if ($withDie == true) {
            die;
        }
    }
}

if ( ! function_exists('sanitizeString')) {
    function sanitizeString($string, $with = '-')
    {
        // allow only alphanumeric
        $res = preg_replace("/[^a-zA-Z0-9\.]/", "", $string);

        // trim what's left to 50 chars
        $res = substr($res, 0, 50);

        // return
        return strtolower($res);
    }
}

if (!function_exists('urlGenerator')) {
    /**
     * @return \Laravel\Lumen\Routing\UrlGenerator
     */
    function urlGenerator() {
        return new \Laravel\Lumen\Routing\UrlGenerator(app());
    }
}

if (!function_exists('asset')) {
    /**
     * @param $path
     * @param bool $secured
     *
     * @return string
     */
    function asset($path, $secured = false) {
        return urlGenerator()->asset($path, $secured);
    }
}
