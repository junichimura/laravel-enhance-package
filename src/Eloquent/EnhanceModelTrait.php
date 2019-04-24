<?php

namespace Fujijun\LaravelEnhancePackage\Eloquent;

class EnhanceModelTrait
{
    /**
     * @param $id
     * @param int $code
     * @param string $message
     * @param array $headers
     *
     * @return $this
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public static function findOrAbort($id, $code = Response::HTTP_NOT_FOUND, $message = '', array $headers = [])
    {
        return static::find($id) ?: abort($code, $message, $headers);
    }

    public static function withFindOrAbort($with, $id, $code = Response::HTTP_NOT_FOUND, $message = '', array $headers = [])
    {
        return static::with($with)->find($id) ?: abort($code, $message, $headers);
    }

    /**
     * @param $id
     * @param int $code
     * @param string $message
     * @param array $headers
     *
     * @return $this
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public static function findOrAbortWithTrashed($id, $code = Response::HTTP_NOT_FOUND, $message = '', array $headers = [])
    {
        return static::withTrashed()->find($id) ?: abort($code, $message, $headers);
    }

    /**
     * @param Request $request
     * @return static
     */
    public function setRequestValue(Request $request)
    {
        collect($request->all())
            ->filter(function ($value, $key) {
                return !str_is('_token', $key);
            })
            ->each(function ($value, $key) {
                $this->setAttribute($key, $value);
            });
        return $this;
    }

    /**
     * @param array $array
     * @return static
     */
    public function setArrayValue(array $array)
    {
        if (array_values($array) === $array) {
            throw new \RuntimeException("引数は連想配列にしてください。");
        }

        foreach ($array as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }
}