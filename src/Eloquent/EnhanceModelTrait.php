<?php

namespace Junichimura\LaravelEnhancePackage\Eloquent;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Junichimura\LaravelEnhancePackage\Eloquent\Exceptions\InvalidArgumentException;

trait EnhanceModelTrait
{

    /**
     * 指定IDに対応するモデルの取得を試みるが、存在しない場合にはNotFoundHttpExceptionをthrowする。
     *
     * @param $id
     * @param $throwMessage
     * @param array $headers
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public static function findOr404($id, $throwMessage, array $headers = [])
    {
        self::findOrAbort($id, null, $throwMessage, $headers);
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
     * モデルインスタンスにリクエストの内容をそのまま投入する。
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
            throw new InvalidArgumentException("引数は連想配列にする必要があります。");
        }

        foreach ($array as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }
}