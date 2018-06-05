<?php



namespace Leven\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * The user.
     *
     * @var Leven\Models\User
     */
    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(UserModel::class)->create();
        $this->user->sendNotifyMessage('test', '1111');
        $this->user->sendNotifyMessage('test', '2222');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard(): Guard
    {
        return Auth::guard('api');
    }

    /**
     * 通知列表.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testGetNotifications()
    {
        $token = $this->guard()->login($this->user);

        $response = $this->getJson('/api/v2/user/notifications', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200)->assertHeader('Unread-Notification-Limit');

        $single = $response->json()[0];

        $this->assertArrayHasKey('id', $single);
        $this->assertArrayHasKey('read_at', $single);
        $this->assertArrayHasKey('data', $single);
    }

    /**
     * 测试阅读一条通知.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testReadNotifications()
    {
        $token = $this->guard()->login($this->user);

        $getNotification = $this->getJson('/api/v2/user/notifications', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $single = $getNotification->json()[0];

        // 未读时read_at为null
        $this->assertNull($single['read_at']);

        $response = $this->getJson('/api/v2/user/notifications/'.$single['id'], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $readedData = $response->json();

        // 已读时read_at不为null
        $this->assertTrue($readedData['read_at'] !== null);
    }

    /**
     * 测试单条设置已读.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testMarkNotificationsReadBySingle()
    {
        $token = $this->guard()->login($this->user);

        $getNotification = $this->getJson('/api/v2/user/notifications', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $notifications = $getNotification->json();
        collect($notifications)->map(function ($single) {
            $this->assertNull($single['read_at']);
        });

        $markResponse = $this->patch('/api/v2/user/notifications?notification='.$notifications[0]['id'], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $markResponse->assertStatus(201);

        $finalResponse = $this->getJson('/api/v2/user/notifications', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $this->assertTrue($finalResponse->json()[0]['read_at'] !== null);
        $this->assertTrue($finalResponse->json()[1]['read_at'] === null);
    }

    /**
     * 测试多条设置已读.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testMarkNotificationsReadByMultiple()
    {
        $token = $this->guard()->login($this->user);

        $getNotification = $this->getJson('/api/v2/user/notifications', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $notifications = $getNotification->json();
        collect($notifications)->map(function ($single) {
            $this->assertNull($single['read_at']);
        });

        $markResponse = $this->patch('/api/v2/user/notifications?notification='.$notifications[0]['id'].','.$notifications[1]['id'], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $markResponse->assertStatus(201);

        $finalResponse = $this->getJson('/api/v2/user/notifications', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $this->assertTrue($finalResponse->json()[0]['read_at'] !== null);
        $this->assertTrue($finalResponse->json()[1]['read_at'] !== null);
    }

    /**
     * 测试全部设为已读.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testMarkNotificationsReadByall()
    {
        $token = $this->guard()->login($this->user);

        $getNotification = $this->getJson('/api/v2/user/notifications', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $notifications = $getNotification->json();
        collect($notifications)->map(function ($single) {
            $this->assertNull($single['read_at']);
        });

        $markResponse = $this->put('/api/v2/user/notifications/all', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $markResponse->assertStatus(201);

        $finalResponse = $this->getJson('/api/v2/user/notifications', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $this->assertTrue($finalResponse->json()[0]['read_at'] !== null);
        $this->assertTrue($finalResponse->json()[1]['read_at'] !== null);
    }

    protected function tearDown()
    {
        $this->user->forceDelete();

        parent::tearDown();
    }
}
