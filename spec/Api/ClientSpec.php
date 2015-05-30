<?php

namespace spec\TextParams\TextRu\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TextParams\TextRu\Api\Model\CheckResult;

class ClientSpec extends ObjectBehavior
{
    function let(ClientInterface $client)
    {
        $this->beAnInstanceOf('TextParams\TextRu\Api\Client');
        $this->beConstructedWith('abc', $client);
    }

    function it_should_initiate_text_check_and_return_text_unique_identifier(ClientInterface $client)
    {
        $data = [
            'text' => 'Some text',
            'userkey' => 'abc',
            'callback' => 'http://test.com/process-result',
            'copying' => 'noadd',
            'visible' => 'vis_on',
            'exceptdomain' => 'test.com mail.ru'
        ];
        $client
            ->post('http://api.text.ru/post', ['body' => $data])
            ->willReturn(new Response(200, [], Stream::factory('{"text_uid":"123"}')))
        ;

        $this
            ->check('Some text', 'http://test.com/process-result', true, false, ['test.com', 'mail.ru'])
            ->shouldBe('123')
        ;
    }

    function it_should_return_available_symbols(ClientInterface $client)
    {
        $client
            ->post('http://api.text.ru/account', ['body' => ['method' => 'get_packages_info', 'userkey' => 'abc']])
            ->willReturn(new Response(200, [], Stream::factory('{"size":115}')))
        ;

        $this->availableSymbols()->shouldBe(115);
    }

    function it_should_return_result_by_text_unique_identifier(ClientInterface $client)
    {
        $client
            ->post('http://api.text.ru/post', ['body' => ['uid' => '123', 'userkey' => 'abc', 'jsonvisible' => 'detail']])
            ->willReturn(new Response(200, [], Stream::factory('{"text_unique":3.43, "seo_check": "{\"water_percent\": 12}"}')))
        ;

        $this->tryGetResult(123)->shouldBeLike(new CheckResult(123, 3.43, 12));
    }
}
