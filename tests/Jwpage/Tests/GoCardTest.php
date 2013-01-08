<?php

namespace Jwpage\Test;

use Jwpage\GoCard;
use Guzzle\Plugin\Log\LogPlugin;
use Guzzle\Plugin\Mock\MockPlugin;

class GoCardTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->goCard = new GoCard('cardNumber', 'password');
        $this->mockPlugin = new MockPlugin();
        $this->goCard->getClient()->getClient()->addSubscriber($this->mockPlugin);
    }

    public function testLoginFailure()
    {
        $this->addMock('login_failure.txt');
        $this->goCard->login();
        print_r($this->goCard->getClient()->getResponse());
        $this->assertFalse($this->goCard->login());
    }

    private function addMock($path)
    {
        $this->mockPlugin->addResponse(MockPlugin::getMockFile(MOCK_BASE_PATH.'/'.$path));
    }
}