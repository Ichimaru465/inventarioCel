<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetStoreConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        $store = $request->session()->get('store');
        $connection = $store['connection'] ?? null;

        if (! $connection || ! array_key_exists($connection, config('database.connections', []))) {
            Auth::logout();
            $request->session()->forget('store');

            return redirect()->route('store.select')
                ->withErrors(['store' => 'Selecciona la tienda antes de iniciar sesion.']);
        }

        Config::set('database.default', $connection);
        DB::purge($connection);
        DB::reconnect($connection);

        return $next($request);
    }
}
