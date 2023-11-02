<?php
namespace App\Controllers;

use App\Auth;
use App\Models\User;
use App\Request;
use App\Response;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function register(Request $request)
    {
        $req = $request->request['body'];
        $user = User::create([
            'name' => $req['name'],
            'email' => $req['email'],
            'password' => password_hash($req['password'], PASSWORD_BCRYPT),
        ]);

        if (!$user) {
            return $this->response(Response::HTTP_INTERNAL_SERVER_ERROR, [
                'message' => 'Something went wrong!'
            ]);
        }

        return $this->response(Response::HTTP_CREATED, [
            'message' => 'User has been created!',
            'data' => $user
        ]);
    }

    public function login(Request $request)
    {
        $req = $request->request['body'];
        $user = User::where('email', $req['email'])->first();
        if (empty($user) || !password_verify($req['password'], $user->password)) {
            return $this->response(Response::HTTP_UNAUTHORIZED, [
                'message' => 'Your credential is wrong!'
            ]);                
        }
        
        $token = Auth::generateToken($user);

        return $this->response(Response::HTTP_OK, [
            'message' => 'You\'re logged in',
            'data' => [
                'token' => $token,
                'user' => $user
            ]
        ]);
    }

    public function oauth()
    {
        include __DIR__ . '/../OAuth.php';
    }

    public function oauthCallback(Request $request)
    {
        include __DIR__ . '/../OAuth.php';
    }
}