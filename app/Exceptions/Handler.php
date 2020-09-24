<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }
        if ($exception instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("Does not exists any {$modelName} with the specified identificator", 404);
        }
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }
        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse($exception->getMessage(), 403);
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse("The specified method is ivalid for this url", 405);
        }
        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse("The specific url cannot be found", 404);
        }
        if($exception instanceof HttpException)
        {
            return $this->errorResponse($exception->getMessage(),$exception->getStatusCode());
        }
        if($exception instanceof QueryException)
        {
            $errorCode = $exception->errorInfo[1];

            if($errorCode ==1451)
            {
                return $this->errorResponse('Cannot remove this resource permently.It is related to any other resources.',409);
            }
        }

        if($exception instanceof TokenMismatchException)
        {
            return redirect()->back()->withInput($request->input());
        }

        if(config('app.debug')){
            return parent::render($request, $exception);
        }
        return $this->errorResponse('Unexpected exception. Please try again later!',500);
    }


    //overriding method for validation exception
    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $error = $e->validator->errors()->getMessages();
        if($this->isFrontend($request))
        {
           return $request->ajax() ? response()->json($error, 422) : redirect()
           ->back()
           ->withInput()
           ->withErrors($error);
        }
        return $this->errorResponse($error, 422);
    }
    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if($this->isFrontend($request))
        {
            return redirect()->guest('login');
        }
        return $this->errorResponse("unauthenticated", 401);
    }

    private function isFrontend($request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}
