<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Log;
use Zalo\Zalo;
use Zalo\ZaloClient;
use Zalo\ZaloRequest;
use Zalo\ZaloResponse;
use Zalo\Builder\MessageBuilder;
use Zalo\ZaloEndPoint;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('posts', PostController::class);
Route::post('/zalo-webhook', function(Request $request){
    // b1: public 1 API return status code = 200 với phương thức post.;

    // b2: Đăng ký webhook với api public vừa tạo qua ngrok

    // b3: Trong webhook của ứng dụng https://developers.zalo.me/app/4249818176355886832/webhook bật các API người dùng gửi tin nhắn
    // mục đích là đăng ký các sự kiện, để khi sự kiện xảy ra thì zalo sẽ tự động gửi 1 request method là POST đến Webhook Url đã đăng ký.ngrok

    // b4. Cài đặt sdk zalo cung cấp của java hoặc php https://github.com/zaloplatform/zalo-php-sdk

    // b5. Bắt các sự kiện mà người dùng thao tác trên zalo OA: Gửi tin nhắn text, gửi vị trí, gửi file, gửi ảnh ...
    // tại api public theo code dưới đây. để phân biệt các sự kiện qua trường event_name : https://developers.zalo.me/docs/api/official-account-api/webhook/su-kien-nguoi-dung-gui-tin-nhan-post-3720

    // B6. Sau khi bắt được sự kiện, cần trả lời các sự kiện đó. Ex: gửi tin nhắn cho người dùng.
    // Việc này cần có access_token của OA khi gửi tin, có 3 cách để lấy access_token, mình sẽ sử dụng các thứ 3 theo link:
    // https://developers.zalo.me/docs/api/official-account-api/xac-thuc-va-uy-quyen/cach-3-quy-trinh-lay-oa-access-token-rut-gon-neu-admin-cua-app-cung-la-admin-cua-oa/flow-rut-gon-cac-buoc-yeu-cau-cap-moi-oa-access-token-post-6487

    // Cần tạo bảng trong csdl lưu lại access_token và refresh_token, expires_in của token đó.
    // dữ liệu đầu tiên của bảng lấy từ link https://developers.zalo.me/tools/explorer/4249818176355886832

    // b7. Khi sử dụng các API của Zalo, cần lấy thông tin của token từ csdl, kiểm tra token đã hết hạn chưa,
    // nếu token đã hết hạn thì gọi API lấy token mới : https://oauth.zaloapp.com/v4/oa/access_token

    // b8: Khi có token mới thì gọi các api zalo để hoàn thành nhiệm vụ.

    $bodyContent = $request->getContent();
    error_log($bodyContent);
    $data  = json_decode($bodyContent);
    error_log($data->sender->id);
    $sender = $data->sender->id;

    $config = array(
        'app_id' => '1234567890987654321',
        'app_secret' => 'UiZo1zM4X732Q3IZ7tLN',
        'callback_url' => 'https://8133-113-168-135-133.ngrok.io'
    );
    $zalo = new Zalo($config);
    // "user_id_by_app":"1158293772516759202"

    $accessToken = 'clbOQwPVOc-MyLTihLjWOPtoMcUdGm0dzgXtVCrq5pYXWpX9pLLuFvE1FbZMBaefZUGdICetIqMKwn0wy21bHBti00l59qb8uF086h4wR4RKu1iLcnD_H-3f31-o0bbWtjaw8eGsQbB9uJaGaoj8KjFtFWov8ajuw-mZBBKNLNhe-JGckGfcQi7NNJMBCZHLvVnL8ReE8ttjn7OcrZqVPv_HUn7IUIndzxvx9vHj6tVff6blfLqyCUYGK62hM1eMyPDAKh903otvbsjUgM4z2S-k2Ko9N6aDxvGuLzP4SJIJx4D9rn45ExRZSJRJ9I9ojjbJ2SW77tBpttXsD0YV0ZMcI6vi';
    $refresh_token = '';

    // lấy thông tin của accessToken nếu token hết hạn.

    /*$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://oauth.zaloapp.com/v4/oa/access_token");
    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
        'secret_key: UiZo1zM4X732Q3IZ7tLN')
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS,"app_id=4249818176355886832&grant_type=refresh_token&refresh_token=".$refresh_token);
    // Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    curl_close ($ch);

    if ($server_output) {
        error_log('Thinklabs Success');
        error_log(gettype($server_output));
        error_log($server_output);
        $dataToken = json_decode($server_output);
        if($dataToken->access_token){
            $accessToken=$dataToken->access_token;
        }
    }
    error_log('access_token: '.$accessToken);*/

    // Kết thúc lấy thông tin của token



     // lấy thông tin người quan tâm : đã ok.
     //$data = ['data' => json_encode(array('user_id' => '7709184099385633990'))];
     //$response = $zalo->get(ZaloEndpoint::API_OA_GET_USER_PROFILE, $accessToken, $data);
     //$result = $response->getDecodedBody(); // result
    // ----------------------- Kết thúc lấy thông tin người quan tâm.



   // Gửi tin nhắn text
   // build data
   $msgBuilder = new MessageBuilder('text');
   $msgBuilder->withUserId($sender);
   $msgBuilder->withText('Thinklabs JSC, Tin nhắn trả lời từ động từ OA');

   $msgText = $msgBuilder->build();
   // send request
   $response = $zalo->post(ZaloEndpoint::API_OA_SEND_MESSAGE, $accessToken, $msgText);
   $result = $response->getDecodedBody(); // result
   error_log(gettype($result));
   error_log(json_encode($result));


   // ------------------ Kết thúc gửi tin nhắn text. -------------------------
    //$params = ['message' => 'put_your_message_here', 'to' => '7709184099385633990'];
    //$response = $zalo->post(ZaloEndpoint::API_GRAPH_APP_REQUESTS, $accessToken, $params);
    //$result = $response->getDecodedBody(); // result
    //error_log($accessToken);
    //$params = ['message' => 'OA Auto', 'to' => '7709184099385633990'];
    //$response = $zalo->post(ZaloEndpoint::API_GRAPH_MESSAGE, $accessToken, $params);
    //error_log(ZaloEndpoint::API_GRAPH_MESSAGE);
    //$result = $response->getDecodedBody(); // result
    //error_log(gettype($result));
    //error_log(json_encode($result));
    return response()->json([], 200);
});
Route::get('/hello', function(Request $request){
    //return [1, 2, 3];
    //return response('Hello World', 200)->header('Content-Type', 'text/plain');
    //return response()->json(['name' => 'Abigail','state' => 'CA']); //{"name": "Abigail","state": "CA"}
    return response()
                ->json(['name' => 'Thinklabs', 'state' => 'VN'])
                ->withCallback($request->input('callback'));

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


