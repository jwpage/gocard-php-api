<?php

namespace Jwpage\Test;

use Jwpage\GoCard;
use Guzzle\Plugin\Log\LogPlugin;
use Guzzle\Plugin\Mock\MockPlugin;

class GoCardTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->goCard = new GoCard(GOCARD_TEST_NUMBER, GOCARD_TEST_PASSWORD);
        $this->mockPlugin = new MockPlugin();
        $this->goCard->getClient()->getClient()->addSubscriber($this->mockPlugin);
        #$this->goCard->getClient()->getClient()->addSubscriber(LogPlugin::getDebugPlugin());
    }

    public function testLoginSuccess()
    {
        $this->addMock('login_success.txt');
        $this->assertTrue($this->goCard->login());
    }

    public function testLoginFailure()
    {
        $this->addMock('login_failure.txt');
        $this->assertFalse($this->goCard->login());
    }

    public function testGetBalance()
    {
        $this->addMock('login_success.txt');
        $this->assertEquals('51.18', $this->goCard->getBalance());
    }

    private function addMock($path)
    {
        $this->mockPlugin->addResponse(MockPlugin::getMockFile(MOCK_BASE_PATH.'/'.$path));
    }
}