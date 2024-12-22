<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;

class HandleValidationErrors
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response->status() === 422) {
            return back()->withErrors($response->exception->errors());
        }

        // If there are any validation errors in the session
        if (session()->has('errors')) {
            $errors = session()->get('errors');
            
            if ($errors instanceof ViewErrorBag && $errors->any()) {
                session()->flash('error', 'Validation failed. Please check the form and try again.');
            }
        }

        return $response;
    }
}
