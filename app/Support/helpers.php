<?php

if (!function_exists('api_response')) {

    /**
     * json返回
     * @param $data
     * @param string $code
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    function api_response($data, $code = '0', $msg = 'ok')
    {
        $json = [
            'data' => $data,
            'code' => $code,
            'message' => $msg,
            'time' => (string)time(),
            '_ut' => (string)round(microtime(TRUE) - $_SERVER['REQUEST_TIME_FLOAT'], 5),
        ];

        $headers = implode(',', config('domain.request.headers'));

        return response()->json($json)->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Content-Type', 'application/json')
            ->header('charset', 'UTF-8')
            ->header('Access-Control-Allow-Headers', $headers);
    }
}

if (!function_exists('api_error')) {

    function api_error($code = 0, $message = null, Throwable $previous = null)
    {
        if ($message === null) {
            $message = \App\Core\Enums\ErrorCode::getMessage($code);
            return api_response([], $code, $message);
        }
    }
}

/**
 * 记录日志
 * @param $loggerName
 * @param $body
 */
function el_journal($loggerName, $body)
{
    $body['ip'] = request()->getClientIp();
    $job = new \App\Jobs\ElasticSearch\ElasticSearchJournal($loggerName, $body);
    $name = env('APP_ENV', 'default');
    $job->onQueue($name);
    dispatch($job);
}
