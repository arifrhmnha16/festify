<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectApexDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getHost() !== 'festify.my.id') {
            return $next($request);
        }

        $target = 'https://www.festify.my.id'.$request->getRequestUri();

        return redirect()->away($target, 301);
    }
}
