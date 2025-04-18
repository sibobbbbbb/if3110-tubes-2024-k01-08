<?php
namespace src\middlewares;

use src\core\Request;
use src\core\Response;

class RateLimitMiddleware extends Middleware
{
    private int $max;    // max attempts
    private int $decay;  // window period in seconds

    public function __construct(int $maxAttempts = 5, int $decaySeconds = 60)
    {
        $this->max   = $maxAttempts;
        $this->decay = $decaySeconds;
        if (!session_id()) {
            session_start();
        }
    }

    public function handle(Request $req, Response $res): void
    {
        $ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key  = "rl:{$ip}:" . $req->getPath();
        $now  = time();

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'reset' => $now + $this->decay];
        }
        $data = &$_SESSION[$key];

        if ($now > $data['reset']) {
            $data['count'] = 0;
            $data['reset'] = $now + $this->decay;
        }
        $data['count']++;

        if ($data['count'] > $this->max) {
            $retryAfter = $data['reset'] - $now;
            $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
            if (strpos($accept, 'application/json') === false) {
                $_SESSION['rate_limit_error'] =
                  "Too many attempts. Try again in {$retryAfter}s";
                $res->redirect('/auth/sign-in');
                exit;
            }
            $res->json(429, [
                'status'  => 'error',
                'message' => "Too many attempts. Try again in {$retryAfter}s"
            ]);
            exit;
        }
    }
}