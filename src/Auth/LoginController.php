<?php

namespace Chestnut\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Chestnut\Auth\Events\WechatRegisterEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class LoginController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only("phone", "password");

        if (Auth::guard("chestnut")->attempt($credentials)) {
            $request->session()->regenerate();

            return ["msg" => "登陆成功。"];
        }

        return ["error" => "账号或密码错误，请确认后再尝试。"];
    }

    public function wechat_login(Request $request)
    { {
            if ($request->filled("code")) {
                $code = $request->code;
                $userinfo = $request->userinfo;

                $client = new \GuzzleHttp\Client(['base_uri' => 'https://api.weixin.qq.com/']);

                $response = $client->get(
                    'sns/jscode2session',
                    [
                        'query' => [
                            'appid'      => env("MICROAPP_ID"),
                            'secret'     => env("MICROAPP_SECRET"),
                            'js_code'    => $code,
                            'grant_type' => 'authorization_code',
                        ],
                    ]
                );

                $data = json_decode($response->getBody()->getContents());

                if ($user = User::where('openid', $data->openid)->first()) {
                    $user->session_key = $data->session_key;
                    $token             = $user->createToken($data->openid);

                    if ($request->phone !== null) {
                        $user->openid = $data->openid;
                    }

                    if (!empty($userinfo)) {
                        $user->name = $userinfo['nickName'];
                        $user->avatar = $userinfo['avatarUrl'];
                    }

                    $user->save();

                    event(new WechatRegisterEvent($user, $request->parent_code));

                    return $this->respondWithTokenForWechat($token->plainTextToken, $user->id);
                } else {
                    if ($request->phone !== null) {
                        return [
                            "code"    => -21,
                            "message" => "手机号码错误，找不到账号。",
                        ];
                    }

                    $data = [
                        'openid'      => $data->openid,
                        'session_key' => $data->session_key,
                    ];

                    if (!empty($userinfo)) {
                        $data['name'] = $userinfo['nickName'];
                        $data['avatar'] = $userinfo['avatarUrl'];
                    }

                    $user = User::create(
                        $data
                    );

                    $token             =
                        $user->createToken($data['openid']);

                    return $this->respondWithTokenForWechat($token->plainTextToken, $user->id);
                }

                return ["code" => $data->errcode, "errmsg" => $data->errmsg];
            }

            return [
                "code"    => -20,
                "message" => "login failed, code not found.",
            ];
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = $request->user("sanctum");

        $user->roles->makeHidden("pivot");

        $nuts = File::files(config("chestnut.dashboard.nutsIn"));

        $nutsLastModifiedAt = array_reduce($nuts, function ($prev, $nut) {
            if ($prev < $nut->getMTime()) {
                $prev = $nut->getMTime();
            }

            return $prev;
        }, 0);

        $clientModifiedAt = intval($request->cookie("chestnut_modified_at", 0));

        $data = ["profile" => $user];

        if ($clientModifiedAt < $nutsLastModifiedAt) {
            $settings = app("shell")->jsonSerialize();
            $data = array_merge($data, $settings);
        }

        return response()->json($data)->cookie("chestnut_modified_at", $nutsLastModifiedAt, 1440);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('chestnut')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     * 刷新token，如果开启黑名单，以前的token便会失效。
     * 值得注意的是用上面的getToken再获取一次Token并不算做刷新，两次获得的Token是并行的，即两个都可用。
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            return $this->respondWithToken(auth('chestnut')->refresh());
        } catch (TokenExpiredException $e) {
            return response("Token has expired and can no longer be refreshed", 403);
        }
    }

    /**
     * Refresh a token.
     * 刷新token，如果开启黑名单，以前的token便会失效。
     * 值得注意的是用上面的getToken再获取一次Token并不算做刷新，两次获得的Token是并行的，即两个都可用。
     * @return \Illuminate\Http\JsonResponse
     */
    public function wechat_refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            "errno"       => 0,
            'access_token'      => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithTokenForWechat($token, $shareId)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'share_code'   => $shareId,
        ]);
    }
}
