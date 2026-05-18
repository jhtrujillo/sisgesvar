<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTFactory;


// controlador para autenticación de usuario
class JWTAuthController extends Controller
{
    /**
     * Create a new JWTAuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['usuario', 'clave']);

        $cred = array(
                'lgin' => $credentials["usuario"],
                'clve'=> $credentials["clave"]
            );
            
        if (!$token = auth('api')->attempt($cred)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
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
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTFactory::getTTL() * 60
        ]);
    }
    /**
     * @OA\Post(
     *   path="/auth/login",
     *   tags={"Autenticación"},
     *   summary="Login",
     *   description="Genera el token de autenticación",
     *   operationId="login",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *      )
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="usuario",
     *     description="E-Mail",
     *     required=true,
     *     @OA\Schema(
     *             type="string"
     *         )
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="clave",
     *     description="Contraseña",
     *     required=true,
     *     @OA\Schema(
     *             type="string",
     *             format = "password"
     *         )
     *   ),
     *   @OA\Response(
     *                  response="200",
     *                  description="El login se realizó correctamente "
     *   ),
     *   @OA\Response(
     *                  response="400",
     *                  description="Petición no valida"
     *   ),
     *   @OA\Response(
     *                  response="401",
     *                  description="Usuario no autorizado"
     *   ),
     * )
     */

    /**
     * @OA\Post(path="/auth/logout",
     *   tags={"Autenticación"},
     *   summary="Logout",
     *   description="Invalida el token",
     *   operationId="logout",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *      )
     *   ),
     *   security={
     *     {"jwt_key": {}},
     *   },
     *   @OA\Response(
     *                  response="200",
     *                  description="El login se realizó correctamente "
     *   ),
     *   @OA\Response(
     *                  response="400",
     *                  description="Petición no valida"
     *   ),
     *   @OA\Response(
     *                  response="401",
     *                  description="Usuario no autorizado"
     *   ),
     * )
     */

    /**
     * @OA\Post(path="/auth/refresh",
     *   tags={"Autenticación"},
     *   summary="Refresca el token de autenticación",
     *   description="Invalida el token actual y genera uno nuevo",
     *   operationId="refresh",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *      )
     *   ),
     *   security={
     *     {"jwt_key": {}},
     *   },
     *   @OA\Response(
     *                  response="200",
     *                  description="El login se realizó correctamente "
     *   ),
     *   @OA\Response(
     *                  response="400",
     *                  description="Petición no valida"
     *   ),
     *   @OA\Response(
     *                  response="401",
     *                  description="Usuario no autorizado"
     *   ),
     * )
     */

    /**
     * @OA\Post(path="/auth/me",
     *   tags={"Autenticación"},
     *   summary="Obtiene la información del usuaro autenticado",
     *   description="Obtiene la información del usuaro autenticado",
     *   operationId="me",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *      )
     *   ),
     *   security={
     *     {"jwt_key": {}},
     *   },
     *   @OA\Response(
     *                  response="200",
     *                  description="El login se realizó correctamente "
     *   ),
     *   @OA\Response(
     *                  response="400",
     *                  description="Petición no valida"
     *   ),
     *   @OA\Response(
     *                  response="401",
     *                  description="Usuario no autorizado"
     *   ),
     * )
     */
}
