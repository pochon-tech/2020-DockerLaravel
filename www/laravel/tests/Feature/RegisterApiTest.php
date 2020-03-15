<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_新規にユーザを作成して返却()
    {
        $data = [
            'name' => 'Yamda Tarou',
            'email' => 'yamada@email.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $response = $this->json('POST', route('register'), $data);
        
        $user = User::first();
        
        $this->assertEquals($data['name'], $user->name);

        /**
         *  tips
         *  assertStatus:
         *    クライアントのレスポンスが指定したHTTPステータスコードであることを宣言
         *  assertJson:
         *    レスポンスを配列へ変換
         *    PHPUnit::assertArraySubsetを使用しアプリケーションへ戻ってきたJSONレスポンスの中に、指定された配列が含まれているかを確認
         */
        $response->assertStatus(201)->assertJson(['name' => $user->name]);
    }
}
