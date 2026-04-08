<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle mail transport exceptions gracefully (e.g. SMTP connection failures)
        $this->renderable(function (\Symfony\Component\Mailer\Exception\TransportException $e, Request $request) {
            \Illuminate\Support\Facades\Log::error('Mail transport error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Email service is temporarily unavailable. Please try again later.'], 503);
            }

            return back()->withErrors([
                'email' => 'We could not send the email at this time. Please check your email address or try again later.',
            ])->withInput();
        });
    }
}
