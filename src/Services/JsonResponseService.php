<?php

/*
 * This file is part of AWS Cognito Auth solution.
 *
 * (c) EllaiSys <support@ellaisys.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ellaisys\Cognito\Services;

use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\JsonResource;

use Mockery\Exception;


/**
 * Class JsonResponseService
 * @package Modules\Core\Services
 */
class JsonResponseService
{
    /**
     * @param array $resource
     * @param int $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($resource = [], $code = Response::HTTP_OK, string $message='success')
    {
        return $this->putAdditionalMeta($resource, 'success', null, $message)
            ->response()
            ->setStatusCode($code);
    } //Function end


    /**
     * @param array $resource
     * @param int $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fail($resource = [], $code = Response::HTTP_UNPROCESSABLE_ENTITY, string $message='fail')
    {
        $exception = null;
        if ($resource instanceof \Exception) {
            $exception = $resource;
            $resource = [];
        } //End if

        return $this->putAdditionalMeta($resource, 'fail', $exception, $message)
            ->response()
            ->setStatusCode($code);
    } //Function end


    /**
     * @param array $resource
     * @param int $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function noContent($resource = [], $code = Response::HTTP_NO_CONTENT)
    {
        return $this->putAdditionalMeta($resource, 'success')
            ->response()
            ->setStatusCode($code);
    } //Function end


    /**
     * @param $resource
     * @param $status
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    private function putAdditionalMeta($resource, $status, $e=null, string $message=null)
    {
        $meta   = [
            'message'        => $message,
            'status'         => $status,
            'execution_time' => number_format(microtime(true) - LARAVEL_START, 4),
        ];

        //Add exception message
        if (!empty($e)) {
            $meta = array_merge([
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ],

            ], $meta);            
        } //End if

        $merged = array_merge($resource->additional ?? [], $meta);

        if ($resource instanceof JsonResource) {
            return $resource->additional($merged);
        }

        if (is_array($resource)) {
            return (new JsonResource(collect($resource)))->additional($merged);
        }

        throw new Exception('Invalid type of resource.');
    } //Function end

} //Class end
