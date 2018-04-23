<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\TestRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;


class ApiController extends Controller
{
    public function __construct(
    ){
        $this->middleware('api');
    }

    public function checknotexists(Request $request){
        $username = $request->input('username', '');
        if($username == "hailt"){
            return Response::json(array(
                'success' => false,
                "message" => "username đã tồn tại."
            ), 200);
        }else{
            return Response::json(array(
                'success' => true
            ), 200);
        }

    }

    public function checkexists(Request $request){
        $email = $request->input('email', '');
        if($email != "test@gmail.com"){
            return Response::json(array(
                'success' => false,
                "message" => "Vui lòng kiểm tra lại thông tin tài khoản."
            ), 200);
        }else{
            return Response::json(array(
                'success' => true
            ), 200);
        }
    }

    public function wevnalOnlineEnglishQuestion(Request $request){
        $q1 = $request->input('answer1', '');
        $q2 = $request->input('answer2', '');
        $q3 = $request->input('answer3', '');
        $q4 = $request->input('answer4', '');
        $q5 = $request->input('answer5', '');

//        $inputs = $request->all();

        $cnt = 0;

        if($q1 == "効果的でない"){
            $cnt++;
        }
        if($q2 == "効果的"){
            $cnt++;
        }
        if($q3 == "効果的"){
            $cnt++;
        }
        if($q4 == "異文化に配慮していない"){
            $cnt++;
        }
        if($q5 == "適切でない"){
            $cnt++;
        }
        $a0 = "全問不正解です。。。もしこれで英語を使う環境が必須であればすぐに英語力をアップしていきましょう。\n詳しい診断テストの解説はこちらです。";
        $a1 = "1問正解です。これではビジネスの環境で使うにはまだまだです・・・早速、詳しい診断テストの解説を見て、英語力アップを目指しましょう。";
        $a2 = "2問正解です。これではビジネスの環境で使うにはまだまだです・・・早速、詳しい診断テストの解説を見て、英語力アップを目指しましょう。";
        $a3 = "3問正解です！かなり英語に慣れている方だとお見受けします。ただまだまだ貴方の英語力の向上の余地はありそうです。 \n早速、詳しい診断テストの解説を見て、英語力アップを目指しましょう。";
        $a4 = "おしい！後1問で全問正解でした。あなたはかなり英語に親しんでおり、ビジネスの環境でも使えていると感じます。\n早速、詳しい診断テストの解説を見て、さらなる英語力アップを目指しましょう。";
        $a5 = "全問正解です！かなり英語力かつビジネスでも英語を活用された経験がアリの方だと感じます。\n更なる向上を目指したい方は診断テストの解説を見て、無料体験レッスンをお試しください♪";

        $tmp = "a" . $cnt;
        if($cnt == 5){
            return Response::json(array(
                'success' => 200,
                'message' => array("text" => @$$tmp . "\n https://goo.gl/iAiGPT")
            ), 200);
        }else{
            return Response::json(array(
                'success' => 200,
                'message' => array("text" => @$$tmp . "\n https://goo.gl/iAiGPT" . "\n無料体験レッスンもやってるのでご気軽にどうぞ♪")
            ), 200);
        }

    }

    public function wevnalOnlineEnglishQuestionNew(Request $request){
        $q1 = $request->input('answer1', '');
        $q2 = $request->input('answer2', '');
        $q3 = $request->input('answer3', '');
        $q4 = $request->input('answer4', '');
        $q5 = $request->input('answer5', '');

//        $inputs = $request->all();

        $cnt = 0;

        if($q1 == "効果的でない"){
            $cnt++;
        }
        if($q2 == "効果的"){
            $cnt++;
        }
        if($q3 == "効果的"){
            $cnt++;
        }
        if($q4 == "異文化に配慮していない"){
            $cnt++;
        }
        if($q5 == "適切でない"){
            $cnt++;
        }
        $a0 = "全問不正解です。。。もしこれで英語を使う環境が必須であればすぐに英語力をアップしていきましょう。\n早速、以下をクリックして診断テストの解説を見てみましょう。\nまずはご気軽に無料体験レッスンから♪";
        $a1 = "1問正解です。これではビジネスの環境で使うにはまだまだです・・・早速、以下をクリックして診断テストの解説を見てみましょう。\n英語力アップを目指して、まずはご気軽に無料体験レッスンから♪";
        $a2 = "2問正解です。これではビジネスの環境で使うにはまだまだです・・・早速、以下をクリックして診断テストの解説を見てみましょう。\n英語力アップを目指して、まずはご気軽に無料体験レッスンから♪";
        $a3 = "3問正解です！かなり英語に慣れている方だとお見受けします。ただまだまだ貴方の英語力の向上の余地はありそうです。 早速、以下をクリックして診断テストの解説を見てみましょう。\n英語力アップを目指して、まずはご気軽に無料体験レッスンから♪";
        $a4 = "おしい！後1問で全問正解でした。あなたはかなり英語に親しんでおり、ビジネスの環境でも使えていると感じます。早速、以下をクリックして診断テストの解説を見てみましょう。\n英語力アップを目指して、まずはご気軽に無料体験レッスンから♪";
        $a5 = "全問正解です！かなりの英語力かつビジネスでも英語を活用された経験アリの方だと感じます。以下をクリックして診断テストの解説を見てみましょう。\n更なる向上を目指し、まずはご気軽に無料体験レッスンから♪";

        $tmp = "a" . $cnt;
        if($cnt == 5){
            return Response::json(array(
                'success' => 200,
                'message' => array("text" => @$$tmp)
            ), 200);
        }else{
            return Response::json(array(
                'success' => 200,
                'message' => array("text" => @$$tmp)
            ), 200);
        }

    }

    public function random(Request $request){
        $max = $request->input('max', 0);
        $ret = $request->input('ret', null);
        if($ret && intval($max) >= 0){
            $random = mt_rand(0, $max);
            return Response::json(array(
                $ret => $random
            ), 200);
        }
        return Response::json(null, 200);
    }
    public function test(Request $request){
        $validator = Validator::make(
            $request->all(),
            array(
                'email' => 'required|email',
                'password' => 'required'
            )
        );

// If validation fails, redirect to the settings page and send the errors
        if ($validator->fails())
        {
            dd($validator->errors()->getMessages());
        }
    }
}
