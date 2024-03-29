<?php

namespace Services\Telegram;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class TelegramBotApiTest extends TestCase
{
    /**
     * @test
     */
    public function it_send_message_success(): void
    {
        Http::fake([
            TelegramBotApi::HOST.'*' => Http::response(['ok'=>true])
        ]);

        $result = TelegramBotApi::sendMessage('',1,'Testing');

        $this->assertTrue($result);
    }
}
