<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\Like;
use App\Models\Product;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Evaluation;
use App\Models\Profile;

class FleaMarketTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //登録処理のテスト
    public function test_register_name()
    {
        $response = $this->post('/register', [
            // 'name' => 'taro',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_comfirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_register_email()
    {
        $response = $this->post('/register', [
            'name' => 'taro',
            // 'email' => 'test@example.com',
            'password' => 'password123',
            'password_comfirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_register_password()
    {
        $response = $this->post('/register', [
            'name' => 'taro',
            'email' => 'test@example.com',
            // 'password' => 'password123',
            'password_comfirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_less_password()
    {
        $response = $this->post('/register', [
            'name' => 'taro',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_comfirmation' => 'pass',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_mismatch_password()
    {
        $response = $this->post('/register', [
            'name' => 'taro',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_comfirmation' => 'password456',
        ]);

        $response->assertSessionHasErrors('password', 'password_confirmation');
    }

    use RefreshDatabase;

    public function test_register_success()
    {

        $this->seed(DatabaseSeeder::class);

        $formData = [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
    
        $response = $this->post('/register', $formData);
    
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/mypage/profile');
    }

    //ログイン処理のテスト
    public function test_login_email()
    {
        $response = $this->post('/login', [
            //'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_password()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            //'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_login_error()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'nopassword',
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }


    // 検索機能
    public function test_serch()
    {
        $condition = Condition::factory()->create();   
        
        $user = User::create([
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        Product::factory()->create([
            'name' => '目覚まし時計',
            'user_id' => $user->id,
            'condition_id' => $condition->id,
        ]);

        Product::factory()->create([
            'name' => '運動靴',
            'user_id' => $user->id,
            'condition_id' => $condition->id,
        ]);

        $response = $this->get('/?keyword=時計');

        $response->assertStatus(200);
        $response->assertSee('目覚まし時計');
        $response->assertDontSee('運動靴');
    }

    public function test_search_mylist()
    {
        $condition = Condition::factory()->create();

        $user = User::create([
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $matchedProduct = Product::factory()->create([
            'name' => '玉ねぎ3束',
            'user_id' => $user->id,
            'condition_id' => $condition->id,
        ]);

        $otherProduct = Product::factory()->create([
            'name' => '革靴',
            'user_id' => $user->id,
            'condition_id' => $condition->id,
        ]);

        Like::create(['user_id' => $user->id, 'product_id' => $matchedProduct->id]);
        Like::create(['user_id' => $user->id, 'product_id' => $otherProduct->id]);

        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=玉');

        $response->assertStatus(200);
        $response->assertSee('玉ねぎ3束');
        $response->assertDontSee('革靴');
    }

    //商品詳細
   public function test_display_information()
    {
        $category = \App\Models\Category::create(['category' => '家電']);
        $condition = Condition::factory()->create();

        $user = \App\Models\User::factory()->create();

        $product = \App\Models\Product::factory()->create([
            'user_id' => $user->id,
            'name' => '炊飯器',
            'brand_name' => 'test',
            'description' => '高機能な炊飯器です',
            'price' => 10000,
            'image' => 'products/sample.png',
            //'category_id' => $category->id,
            'condition_id' => $condition->id,
        ]);

        $product->categories()->attach($category->id);

        \App\Models\Like::factory()->count(2)->create(['product_id' => $product->id]);

        $commentUser = \App\Models\User::factory()->create(['name' => 'コメントユーザー']);

        \App\Models\Evaluation::create([
            'product_id' => $product->id,
            'user_id' => $commentUser->id,
            'comment' => '使いやすいです'
        ]);

        \App\Models\Evaluation::create([
            'product_id' => $product->id,
            'user_id' => $commentUser->id,
            'comment' => '購入を検討しています'
        ]);

        $response = $this->get("/products/{$product->id}");

        $response->assertStatus(200);
        $response->assertSee('炊飯器');
        $response->assertSee('test');
        $response->assertSee('10000');
        $response->assertSee('高機能な炊飯器です');
        $response->assertSee('家電');
        $response->assertSee('良好');
        $response->assertSee('使いやすいです');
        $response->assertSee('購入を検討しています');
        $response->assertSee('コメントユーザー');  
        
        $response->assertSee('products/sample.png');

        $response->assertSee('2');
    }

    public function test_multiple_selected_categories_are_displayed()
    {
        $condition = Condition::factory()->create();

        $user = User::factory()->create();

        $product = Product::factory()->create([
            'user_id' => $user->id,
            'name' => 'テレビ',
            'condition_id' => $condition->id,
        ]);

        $category1 = Category::create(['category' => '家電']);
        $category2 = Category::create(['category' => '家具']);

        $product->categories()->attach([$category1->id, $category2->id]);

        $response = $this->get('/products/' . $product->id);

        $response->assertStatus(200);

        $response->assertSee('家電');
        $response->assertSee('家具');
    }

    // いいね機能
    public function test_good_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
    
        $response = $this->actingAs($user)->post('/like', [
            'product_id' => $product->id,
        ]);
    
        $response->assertStatus(200);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_change_color_good_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Like::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->get('/products/' . $product->id);

        $response->assertStatus(200);
        $response->assertSee('class="like-button liked"');
    }

    public function test_cancel_good_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Like::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->delete('/like', [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    // コメント
    public function post_comment_only_login_user()
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
        ]);

        $payload  = ['comment' => 'これはテストコメントです'];
        $response = $this->actingAs($user)
        ->post(route('products.comment', $product), $payload);

        $response->assertRedirect("/products/{$product->id}")
        ->assertSessionHasNoErrors();


        $this->assertDatabaseHas('evaluations', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'comment' => 'これはテストコメントです',
        ]);
    }

    public function test_cant_post_comment_guest()
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $product = Product::factory()->create([
            'condition_id' => $condition->id,
            'user_id' => $user->id,
        ]);

        $payload = ['comment' => 'エラー'];
        $response = $this->post("/products/{product->id}/comment", $payload);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('evaluations', [
            'product_id' => $product->id,
            'comment' => 'エラー',
        ]);
    }

    public function test_if_no_comment_is_entered()
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
        ]);

        $response = $this->actingAs($user)
        ->from("products/{$product->id}")
        ->post(route('products.comment', ['product' => $product->id]), [
            'comment' => '',
        ]);

        $response->assertRedirect("/products/{$product->id}");
        $response->assertSessionHasErrors('comment');

        $this->assertDatabaseMissing('evaluations', [
            'product_id' => $product->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_if_the_comment_is_longer_than_255_characters()
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
        ]);
    
        $longComment = str_repeat('あ', 256);
    
        $response = $this->actingAs($user)
            ->from("/products/{$product->id}")
            ->post(route('products.comment', ['product' => $product->id]), [
                'comment' => $longComment,
            ]);
    
        $response->assertRedirect("/products/{$product->id}");
        $response->assertSessionHasErrors('comment');
    
        $this->assertDatabaseMissing('evaluations', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'comment' => $longComment,
        ]);
    }

    // 商品購入機能
    public function test_buy()
    {
        $seller = User::factory()->create();    //出品者
        $buyer = User::factory()->create();    //購入者
        $condition = Condition::factory()->create();

        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'price' => 2500,
        ]);

        Profile::factory()->create([
            'user_id' => $buyer->id,
            'postcode' => '1234567',
            'address' => '東京都'
        ]);
        //購入処理
        $response = $this->actingAs($buyer)
        ->post('/buy', [
            'product_id' => $product->id,
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('sold_products', [
            'product_id' => $product->id,
            'user_id' => $buyer->id,
        ]);

    }

    public function test_sold_item_display_sold()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $condition = Condition::factory()->create();

        $soldProduct = Product::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '購入対象の商品',
        ]);

        Profile::factory()->create([
            'user_id' => $buyer->id,
            'postcode' => '1234567',
            'address' => '東京都',
        ]);    
        //購入処理
        $this->actingAs($buyer)
            ->post('/buy', ['product_id' => $soldProduct->id])
            ->assertRedirect('/');

        $response = $this->get('/');

        $response->assertSee('購入対象の商品');
        $response->assertSeeText('Sold');
    }

    public function test_list_of_purchased_items()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $condition = Condition::factory()->create();
    
        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '購入済み商品',
        ]);
    
        Profile::factory()->create([
            'user_id' => $buyer->id,
            'postcode' => '100-0001',
            'address' => '東京都千代田区1-1-1',
        ]);
        
        //購入処理
        $this->actingAs($buyer)
            ->post('/buy', ['product_id' => $product->id])
            ->assertRedirect('/');
    
        $response = $this->actingAs($buyer)
            ->get('/mypage?tab=buy');
    
        $response->assertStatus(200);
        $response->assertSee('購入済み商品');
    }    
    

    //支払方法選択機能
    public function test_Changes_are_reflected_immediately()
    {
    
    }

    // 配送先変更機能
    public function test_reflected_on_the_product_purchase_screen()
    {
        $user = User::factory()->create();

        // 初期プロフィール
        Profile::factory()->create([
            'user_id'  => $user->id,
            'postcode' => '1234567',
            'address'  => '東京都',
        ]);
    
        $product = Product::factory()->create();    
    
        // ログインして住所変更画面にPOST
        $response = $this->actingAs($user)
            ->post("/purchase/address/{$product->id}", [
                'postcode' => '2345678',
                'address'  => '神奈川県',
            ]);
        $response->assertRedirect("/purchase/{$product->id}");
    
        // 新しい住所が表示されているか確認
        $purchase = $this->actingAs($user)
            ->get("/purchase/{$product->id}");
    
        $purchase->assertStatus(200);
        $purchase->assertSee('2345678');
        $purchase->assertSee('神奈川県');    
    }

    public function test_linked_to_the_purchased_item_and_registered()
    {

    }

    // ユーザ情報取得
    public function test_can_get_information()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);
    
        Profile::factory()->create([
            'user_id' => $user->id,
            'postcode' => '1234567',
            'address' => '東京都',
            'img_url' => 'storage/profiles/test_avatar.png',
        ]);
    
        // 出品した商品
        $sellingProduct = Product::factory()->create([
            'user_id' => $user->id,
            'name' => '自分の出品商品',
        ]);
    
        // 購入した商品を作成
        $seller = User::factory()->create();
        $boughtProduct = Product::factory()->create([
            'user_id' => $seller->id,
            'name' => '購入した商品',
        ]);
    
        // sold_products に購入レコードを直接 INSERT
        DB::table('sold_products')->insert([
            'product_id' => $boughtProduct->id,
            'user_id' => $user->id,
            'postcode' => '2345678',
            'address' => '神奈川県',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // マイページへアクセス
        $response = $this->actingAs($user)->get('/mypage');
    
        //必要情報が表示されているか検証
        $response->assertSee('テストユーザー');
        $response->assertSee('storage/profiles/test_avatar.png');
        $response->assertSee('自分の出品商品');
        $response->assertSee('購入した商品');
    
        $response->assertStatus(200);    
    }

    // ユーザ情報変更
    public function test_displayed_as_the_initial_value()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);
    
        Profile::factory()->create([
            'user_id' => $user->id,
            'img_url' => 'storage/profiles/avatar_test.png',
            'postcode' => '1234567',
            'address' => '東京都',
        ]);
    
        //プロフィール編集画面へアクセス 
        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);
    
        //各入力欄に初期値が入っているか検証
        $response->assertSee('storage/profiles/avatar_test.png');
        $response->assertSee('value="テストユーザー"', false);
        $response->assertSee('value="1234567"', false);
        $response->assertSee('東京都');    
    }

    // 出品情報登録
    public function test_can_store_informaion()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $category1 = Category::factory()->create(['category' => '家電']);
        $category2 = Category::factory()->create(['category' => 'スポーツ']);
        $condition = Condition::factory()->create(['name' => '良好']);
        $image = UploadedFile::fake()->create('test.jpeg', 100, 'image/jpeg');

        //出品フォーム送信
        $payload = [
            'categories' => [$category1->id, $category2->id],
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'description' => 'これはテスト商品です',
            'price' => 100,
            'image' => $image,
        ];

        $response = $this->actingAs($user)
            ->post('/sell', $payload);

        $response->assertStatus(302);

        // 確認
        $this->assertDatabaseHas('products', [
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'description' => 'これはテスト商品です',
            'price' => 100,
        ]);   
        
        // 中間テーブル
        $this->assertDatabaseHas('category_product', [
            'category_id' => $category1->id,
        ]);

        $this->assertDatabaseHas('category_product', [
            'category_id' => $category2->id,
        ]);
    }
}
