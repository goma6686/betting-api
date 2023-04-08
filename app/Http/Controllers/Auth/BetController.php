<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

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

    public function ping(Request $request){
        $xmlData = $request->getContent();
        $xml = simplexml_load_string($xmlData);

        $method = (string) $xml->method;
        $token = (string) $xml->token;
        $requestId = (string) $xml->request_id;
        $time = (string) $xml->time;
        $signature = (string) $xml->signature;
        $params = (array) $xml->params;

        $sig = hash_hmac('sha256', $requestId, 'CCHWS-ZIFJV-HEAOB-DV336');

        $xmlResponse = new \SimpleXMLElement('<root/>');
        $xmlResponse->addChild('response', $sig);
        return response($xmlResponse->asXML())->header('Content-Type', 'application/xml');
        /*if () {
            $success = '1';
            $error_code = '0';
            $error_text = '';
        } else {
            $response = '<error>unknown method</error>';
        }

        $sig = hash_hmac('sha256', $requestId, 'CCHWS-ZIFJV-HEAOB-DV336');

        $xmlResponse = new \SimpleXMLElement('<root/>');
        $xmlResponse->addChild('response', $response);
        
        return response($xmlResponse->asXML())->header('Content-Type', 'application/xml');*/
    }
}
