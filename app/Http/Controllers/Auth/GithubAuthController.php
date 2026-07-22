<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthenticateWithGithubAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class GithubAuthController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     */
    public function redirect(): RedirectResponse
    {
        $clientId = config('services.github.client_id');
        $clientSecret = config('services.github.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->with(
                'error',
                'As credenciais do GitHub OAuth (GITHUB_CLIENT_ID e GITHUB_CLIENT_SECRET) não foram configuradas no .env.'
            );
        }

        try {
            return Socialite::driver('github')->redirect();
        } catch (Throwable $e) {
            Log::error('Erro ao redirecionar para GitHub OAuth: ' . $e->getMessage(), ['exception' => $e]);

            return redirect()->route('login')->with(
                'error',
                'Erro ao iniciar autenticação com o GitHub: ' . $e->getMessage()
            );
        }
    }

    /**
     * Obtain user information from GitHub callback.
     */
    public function callback(AuthenticateWithGithubAction $action): RedirectResponse
    {
        try {
            try {
                $githubUser = Socialite::driver('github')->user();
            } catch (InvalidStateException $e) {
                $githubUser = Socialite::driver('github')->stateless()->user();
            }

            $action->execute([
                'name' => $githubUser->getName() ?? $githubUser->getNickname() ?? 'Usuário GitHub',
                'email' => $githubUser->getEmail(),
                'github_id' => (string) $githubUser->getId(),
                'avatar' => $githubUser->getAvatar(),
            ]);

            return redirect()->route('dashboard')->with('status', 'Autenticado com sucesso via GitHub!');
        } catch (Throwable $e) {
            Log::error('Erro no callback do GitHub OAuth: ' . $e->getMessage(), ['exception' => $e]);

            return redirect()->route('login')->with(
                'error',
                'Falha na autenticação via GitHub. Por favor, tente novamente.'
            );
        }
    }

    /**
     * Local development mock login bypass.
     */
    public function mockLogin(AuthenticateWithGithubAction $action): RedirectResponse
    {
        if (!app()->environment(['local', 'testing'])) {
            abort(403, 'Acesso não permitido em ambiente de produção.');
        }

        $action->execute([
            'name' => 'Desenvolvedor LeadSpect',
            'email' => 'dev@leadspect.com',
            'github_id' => 'mock_github_id_12345',
            'avatar' => 'https://ui-avatars.com/api/?name=LeadSpect+Dev&background=18181b&color=ffffff',
        ]);

        return redirect()->route('dashboard')->with('status', 'Login de desenvolvimento (GitHub mock) realizado!');
    }
}
