<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

abstract class Request extends FormRequest
{
    //
    protected $forceJsonResponse = true;

    public function response(array $errors)
	{
	    Log::info('check request ressponve');
	    if ($this->ajax() || $this->wantsJson()) {
	        return new JsonResponse($errors, 422);
	    }

	    return $this->redirector->to($this->getRedirectUrl())
	                                    ->withInput($this->except($this->dontFlash))
	                                    ->withErrors($errors, $this->errorBag);
	}
}
