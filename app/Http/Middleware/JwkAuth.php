<?php

namespace App\Http\Middleware;

use App\Services\CloudflareJwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwkAuth
{
  protected CloudflareJwtService $jwtService;

  public function __construct(CloudflareJwtService $jwtService)
  {
    $this->jwtService = $jwtService;
  }

  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    // Skip if Cloudflare auth is disabled
    if (!config('cloudflare.enabled')) {
      return $next($request);
    }

    // Extract JWT token from request
    $token = $this->jwtService->extractToken($request);

    if (!$token) {
      return response()->json([
        'status' => 'error',
        'message' => 'Cloudflare Access JWT token not found',
      ], 401);
    }

    // Allow mock token in non-production environments
    if (app()->environment('local', 'development') && $token === 'mock-token') {
      $request->attributes->set('cloudflare_user', [
        'email' => 'test@example.com',
        'name' => 'Test User',
        'sub' => 'mock-user-id',
        'groups' => ['developers'],
        'country' => 'US',
      ]);
      $request->attributes->set('cloudflare_jwt', (object) [
        'email' => 'test@example.com',
        'name' => 'Test User',
        'sub' => 'mock-user-id',
      ]);
      return $next($request);
    }

    try {
      // Verify JWT
      $decoded = $this->jwtService->verify($token);

      // Add user info to request for later use
      $request->attributes->set('cloudflare_user', $this->jwtService->getUserInfo($decoded));
      $request->attributes->set('cloudflare_jwt', $decoded);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Cloudflare Access authentication failed',
        'error' => $e->getMessage(),
      ], 401);
    }

    return $next($request);
  }
}
