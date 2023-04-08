<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class BetController extends Controller
{
    public function index (){
        if (Auth::user()){
            return view('betgames', ['token' => $this->issuetoken(Auth::user())]);
        } else {
            return view('betgames', ['token' => '-']);
        }
    }

    public function issuetoken (User $user){
        return $user->createToken('token')->plainTextToken;
    }

    public function test(){
        $response['status'] = 'ok';
        return response()->json($response);
    }

    public function ping(Request $request){
        $xmlData = $request->getContent();
        $xml = simplexml_load_string($xmlData);

        $method = (string) $xml->method;
        $token = (string) $xml->token;
        $time = (string) $xml->time;
        $params = (array) $xml->params;
        $signature = (string) $xml->signature;

        $secret = "CCHWS-ZIFJV-HEAOB-DV336";
        $calc_string = "method" . $method . "token" . $token . "time" . $time . $secret;
        if (md5($calc_string) === $signature){
            $success = '1';
            $error_code = '0';
            $error_text = '';
            $time = time();
            //$time = '1423124663';
            $signature = md5("method" . $method . "token" . $token . "success" . $success . "error_code" . $error_code . "error_texttime" . $time . $secret);
        } else {
            $success = '0';
            $error_code = '1';
            $error_text = 'wrong_signature';
            $time = time();
            //$time = '1423124663';
            $signature = md5("method" . $method . "token" . $token . "success" . $success . "error_code" . $error_code . "error_texttime" . $time . $secret);
        }

        $xmlResponse = new \SimpleXMLElement('<root/>');
        $xmlResponse->addChild('method', $method);
        $xmlResponse->addChild('token', $token);
        $xmlResponse->addChild('success', $success);
        $xmlResponse->addChild('error_code', $error_code);
        $xmlResponse->addChild('error_text', $error_text);
        $xmlResponse->addChild('time', $time);
        $xmlResponse->addChild('signature', $signature);

        return response($xmlResponse->asXML())->header('Content-Type', 'application/xml');
    }
}
