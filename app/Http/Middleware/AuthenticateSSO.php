namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class AuthenticateSSO
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        $response = Http::withHeaders([
            'Referer' => env('SSO_SERVER')
        ])->get(env('SSO_SERVER') . '/check-auth');

        if ($response->successful() && $response->json('authenticated')) {
            Auth::loginUsingId($response->json('user')['id']);
            return $next($request);
        }

        return redirect()->route('login');
    }
}
