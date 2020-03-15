<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * tips
     * 下記を追加した理由:
     * トレイトであるIlluminate\Foundation\Auth\AuthenticatesUsersを参照
     * loginメソッドでログイン成功時にsendLoginResponseメソッドが呼ばれる
     * sendLoginResponseメソッドのreturn文でレスポンスが決まる
     * return 文では実装が空の authenticated メソッドが呼ばれて、戻り値が偽であった場合にリダイレクトレスポンスが返されてる
     * なので下記でauthenticated メソッドをオーバーライドすればレスポンスをカスタマイズできる
     */
    protected function authenticated(Request $request, $user)
    {
        return $user;
    }
    /**
     * tips
     * 下記を追加した理由: ログインと同じ理由
     */
    protected function loggedOut(Request $request)
    {
        
        // セッションIDの再生成
        // 悪意のあるユーザからの、アプリケーションに対するsession fixation攻撃(セッション固定攻撃)を防ぐために行う
        // Laravelに組み込まれているLoginControllerを使用していれば、認証中にセッションIDは自動的に再生性される
        // セッションIDを任意に再生成する必要がある場合は、下記のようにregenerateメソッドを実行する
        $request->session()->regenerate();
    
        return response()->json();
    }
}
