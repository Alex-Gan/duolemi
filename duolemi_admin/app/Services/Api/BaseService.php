<?php
namespace App\Services\Api;

class BaseService
{
    /**
     * 输出结果格式化
     *
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function formatResponse(int $code = 0, $msg = 'success', $data = [])
    {
        $response_data = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];

        if (empty($data)) {
            unset($response_data['data']);
        }

        $response = response()->json($response_data);

        return $response;
    }
}