<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CloudflareJwtService
{
  /**
   * Verify Cloudflare Zero Trust JWT token
   *
   * @param string $jwt
   * @return object Decoded JWT payload
   * @throws \Exception
   */
  public function verify(string $jwt): object
  {
    $teamDomain = config('cloudflare.team_domain');

    if (!$teamDomain) {
      throw new \Exception('Cloudflare team domain not configured');
    }

    // Get JWKs (JSON Web Key Set) from Cloudflare
    $jwks = $this->getJwks($teamDomain);

    try {
      // JWK::parseKeySet returns an associative array of kid to Key objects
      $decoded = JWT::decode($jwt, JWK::parseKeySet($jwks));

      // Additional validation
      $this->validateAudience($decoded);
      $this->validateIssuer($decoded, $teamDomain);

      return $decoded;
    } catch (\Exception $e) {
      throw new \Exception('JWT verification failed: ' . $e->getMessage());
    }
  }

  /**
   * Get JWKs from Cloudflare
   *
   * @param string $teamDomain
   * @return array
   */
  protected function getJwks(string $teamDomain): array
  {
    $certsUrl = "https://{$teamDomain}/cdn-cgi/access/certs";

    $response = Http::timeout(5)->get($certsUrl);

    if (!$response->successful()) {
      throw new \Exception('Failed to fetch Cloudflare JWKs');
    }

    return $response->json();
  }

  /**
   * Validate JWT audience claim
   *
   * @param object $decoded
   * @throws \Exception
   */
  protected function validateAudience(object $decoded): void
  {
    $expectedAudience = config('cloudflare.audience');

    if (!$expectedAudience) {
      return; // Skip if not configured
    }

    $audiences = is_array($decoded->aud ?? null) ? $decoded->aud : [$decoded->aud ?? null];

    if (!in_array($expectedAudience, $audiences)) {
      throw new \Exception('Invalid audience claim');
    }
  }

  /**
   * Validate JWT issuer claim
   *
   * @param object $decoded
   * @param string $teamDomain
   * @throws \Exception
   */
  protected function validateIssuer(object $decoded, string $teamDomain): void
  {
    $expectedIssuer = "https://{$teamDomain}";

    if (($decoded->iss ?? '') !== $expectedIssuer) {
      throw new \Exception('Invalid issuer claim');
    }
  }

  /**
   * Extract JWT from request header
   *
   * @param \Illuminate\Http\Request $request
   * @return string|null
   */
  public function extractToken($request): ?string
  {
    // Cloudflare Access sends JWT in Cf-Access-Jwt-Assertion header
    $token = $request->header('Cf-Access-Jwt-Assertion');

    if ($token) {
      return $token;
    }

    // Fallback to Authorization header
    $authHeader = $request->header('Authorization');

    if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
      return $matches[1];
    }

    return $request->header('Cf-Access-Jwt-Assertion');
  }

  /**
   * Get user info from decoded JWT
   *
   * @param object $decoded
   * @return array
   */
  public function getUserInfo(object $decoded): array
  {
    return [
      'email' => $decoded->email ?? null,
      'name' => $decoded->name ?? null,
      'sub' => $decoded->sub ?? null,
      'groups' => $decoded->groups ?? [],
      'country' => $decoded->country ?? null,
    ];
  }
}
