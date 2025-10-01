<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleSpaRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // If this is an SPA request and we have a view response
        if ($request->header('X-SPA-Request') && method_exists($response, 'getOriginalContent')) {
            $view = $response->getOriginalContent();
            
            // If it's a view instance, we can modify it
            if ($view instanceof \Illuminate\View\View) {
                // Mark this as an SPA request for the view
                $view->with('isSpaRequest', true);
            }
        }
        
        return $response;
    }
}