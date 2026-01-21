<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Auth;

use App\Brain\Auth\Processes\SlackAuthProcess;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class SlackAuthController extends Controller
{
    /**
     * Redirect to Slack OAuth authorization page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('slack')
            ->stateless()
            ->redirect();
    }

    /**
     * Handle Slack OAuth callback.
     */
    public function callback(): RedirectResponse
    {
        try {
            $slackUser = Socialite::driver('slack')->stateless()->user();

            SlackAuthProcess::dispatchSync([
                'slackId'      => $slackUser->getId(),
                'email'        => $slackUser->getEmail(),
                'name'         => $slackUser->getName(),
                'avatar'       => $slackUser->getAvatar(),
                'accessToken'  => $slackUser->token,
                'refreshToken' => $slackUser->refreshToken,
            ]);

            // Check if user needs to set password
            $user = auth()->user();

            if ($user && ! $user->password_set_at) {
                return redirect('/auth/set-password');
            }

            return redirect()->intended('/dashboard');
        } catch (InvalidStateException) {
            return redirect('/login')->with('error', 'Autenticação inválida. Por favor, tente novamente.');
        } catch (Exception $e) {
            Log::error('Slack OAuth error', ['exception' => $e]);

            return redirect('/login')->with('error', $e->getMessage() ?: 'Erro ao autenticar com Slack.');
        }
    }
}
