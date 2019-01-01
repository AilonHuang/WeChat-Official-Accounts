<?php

namespace App\Wechat;

use Illuminate\Http\Request;

class CheckSignature
{
    public function __construct(Request $request)
    {$signature = $request->signature;
        $timestamp = $request->timestamp;
        $nonce = $request->nonce;

        $token = env('WECHAT_TOKEN');
        $tmpArr = [$token, $timestamp, $nonce];
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
}