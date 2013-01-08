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

    public function testGetHistory()
    {
        $this->addMock('history.txt');
        $results = $this->goCard->getHistory(
            new \DateTime('2012-08-01'), 
            new \DateTime('2012-09-01')
        );
        $this->assertEquals(2, count($results));
        $this->assertEquals('Indooroopilly', $results[0]['startLocation']);
        $this->assertEquals('Toowong', $results[0]['endLocation']);
        $this->assertEquals('3.28', $results[0]['cost']);
        $this->assertEquals(new \DateTime('2013-01-08 08:12:00'), $results[0]['startTime']);
        $this->assertEquals(new \DateTime('2013-01-08 08:23:00'), $results[0]['endTime']);
    }

    public function testLogout()
    {
        $this->addMock('logout_success.txt');
        $this->assertTrue($this->goCard->logout());
    }

    private function addMock($path)
    {
        $this->mockPlugin->addResponse(MockPlugin::getMockFile(MOCK_BASE_PATH.'/'.$path));
    }
}