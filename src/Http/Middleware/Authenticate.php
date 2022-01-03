<?php

namespace Antoniputra\Reviz\Http\Middleware;

use Laravel\Horizon\Horizon;

class Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|null
     */
    public function handle($request, $next)
    {
        $authUser = $request->user();
        if ($authorizedEmails = config('reviz.ui.authorized_emails')) {
            if (! $authUser || ! in_array($authUser->email, $authorizedEmails)) {
                return abort(403);
            }
        }

        return $next($request);
    }
}
