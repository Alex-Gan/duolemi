<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-06-05
 * Time: 15:43
 */
namespace App\Http\Middleware;

use Closure;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //获取session的值
        $session_admin = $request->session()->get('sess_admin_user_key');

        if (empty($session_admin)) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}