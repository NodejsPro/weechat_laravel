<?php

namespace App\Exceptions;

use App\Repositories\ExceptionRepository;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */

    protected $repException;

    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    public function __construct(
        Container $container,
        ExceptionRepository $exceptionRepository
    )
    {
        parent::__construct($container);
        $this->repException = $exceptionRepository;
    }

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        Log::info('report');
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        Log::info('render');
        if ($request->expectsJson()) {
            Log::info('1');
            return Response::make(trans('auth.failed'), 404);
        }

        if ($exception instanceof TokenMismatchException) {
            Log::info('2');
            if ($request->ajax() || $request->wantsJson()) {
                Log::info('3');
                return response()->json(['error' => 'TokenMismatchException.'], 500);
            }
            Log::info('4');
            return response()->view('errors.500', array(), 500);
        }
        Log::info('5');
        return parent::render($request, $exception);
    }

    /**
     * Prepare response containing exception render.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function prepareResponse($request, Exception $e)
    {
        if ($this->isHttpException($e)) {
            return $this->toIlluminateResponse($this->renderHttpException($e), $e);
        } else {
            try {
                $inputs = [
                    'err' => [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'url' => $request->fullUrl(),
                        'type' => '001',
                        'push_facebook_flg' => 1,
                    ],
                ];
                $this->sendExceptionToFacebook($inputs);
                $this->repException->store($inputs);
            } catch (\Exception $error) {
                Log::info($e);
            }
            return response()->view('errors.500', array(), 500);
//            return $this->toIlluminateResponse($this->convertExceptionToResponse($e), $e);
        }
    }

    private function sendExceptionToFacebook($error){
        $token = config('facebook.fb_token');
        $uid = config('facebook.fb_uid');
        if(!isset($uid)){
            return;
        }
        $post_message = config('facebook.PostMessage');
        $post_message = str_replace(':access_token', $token, $post_message);

        try{
            $client = new Client();
            $res = $client->post($post_message,
                array(
                    'headers' => array(
                        'Content-Type'  => 'application/json',
                    ),
                    'form_params' => [
                        'messaging_type' => 'RESPONSE',
                        'recipient' => [
                            'id' => $uid,
                        ],
                        'message' => [
                            'text' => "-------------------error-------------------------\n" . json_decode($error)
                        ]
                    ]
                )
            );
        }catch (\Exception $e) {
            Log::info($e->getMessage());
        }
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        Log::info('unauthenticated');
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}
