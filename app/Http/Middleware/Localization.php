<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    protected array $supportedLocales = ["nl", "en"];
    protected string $defaultLocale = "nl";

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');

        if (!$locale) $locale = $request->getPreferredLanguage($this->supportedLocales);

        if (!in_array($locale, $this->supportedLocales)) $locale = $this->defaultLocale;

        App::setLocale($locale);

        return $next($request);
    }
}
