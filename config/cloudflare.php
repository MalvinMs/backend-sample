<?php

return [

  /*
    |--------------------------------------------------------------------------
    | Cloudflare Zero Trust Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Cloudflare Access JWT verification.
    | Get these values from your Cloudflare Access application settings.
    |
    */

  /**
   * Your Cloudflare team domain
   * Example: myteam.cloudflareaccess.com
   */
  'team_domain' => env('CLOUDFLARE_TEAM_DOMAIN'),

  /**
   * Application Audience (AUD) tag
   * Found in your Cloudflare Access application settings
   * Example: abc123def456ghi789jkl012mno345pqr678stu901vwx234yz
   */
  'audience' => env('CLOUDFLARE_AUDIENCE'),

  /**
   * Enable/disable Cloudflare Zero Trust authentication
   */
  'enabled' => env('CLOUDFLARE_AUTH_ENABLED', false),

  /**
   * Cache duration for JWKs in seconds (default: 1 hour)
   */
  'jwks_cache_duration' => env('CLOUDFLARE_JWKS_CACHE', 3600),

];
