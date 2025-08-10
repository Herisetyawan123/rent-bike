<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JwksController extends Controller
{
    public function index()
    {
        // Load public key dari file
        $publicKeyPem = file_get_contents(public_path('public.pem'));
        
        // Ambil detail key
        $keyDetails = openssl_pkey_get_details(openssl_pkey_get_public($publicKeyPem));

        // Ambil modulus & exponent
        $n = rtrim(strtr(base64_encode($keyDetails['rsa']['n']), '+/', '-_'), '=');
        $e = rtrim(strtr(base64_encode($keyDetails['rsa']['e']), '+/', '-_'), '=');

        return response()->json([
            'keys' => [[
                'kty' => 'RSA',
                'use' => 'sig',
                'kid' => '98673-23142387-234', // bisa pakai UUID
                'alg' => 'RS256',
                'n'   => $n,
                'e'   => $e
            ]]
        ]);
    }
}