<?php

namespace MageSuite\OrderFailureNotification\Test\Unit\Service;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\OrderFailureNotification\Service\Configuration
     */
    protected $instance;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $scopeConfigMock;

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
            ->getMock();

        $this->instance = new \MageSuite\OrderFailureNotification\Service\Configuration($this->scopeConfigMock);
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(\MageSuite\OrderFailureNotification\Service\Configuration::class, $this->instance);
    }

    public function testItImplementsConfigProviderInterface()
    {
        $this->assertInstanceOf(
            \MageSuite\OrderFailureNotification\Service\ConfigProviderInterface::class,
            $this->instance
        );
    }

    /**
     * @param bool $notificationEnabled
     * @dataProvider notificationEnabledProvider
     */
    public function testIsNotificationEnabledMethodReturnsBoolValue($notificationEnabled)
    {
        $this->scopeConfigMock
            ->method('isSetFlag')
            ->with(\MageSuite\OrderFailureNotification\Service\Configuration::XML_PATH_NOTIFICATION_ENABLED)
            ->willReturn($notificationEnabled);

        $callResult = $this->instance->isNotificationEnabled();
        $this->assertIsTypeBool($callResult);
        $this->assertEquals($notificationEnabled, $callResult);
    }

    /**
     * @param string $rawConfigValue
     * @param array $expectedResult
     * @dataProvider notificationRecipientsProvider
     */
    public function testGetNotificationRecipientsMethodReturnsArray($rawConfigValue, $expectedResult)
    {
        $this->scopeConfigMock
            ->method('getValue')
            ->with(\MageSuite\OrderFailureNotification\Service\Configuration::XML_PATH_NOTIFICATION_EMAIL_ADDRESS)
            ->willReturn($rawConfigValue);

        $callResult = $this->instance->getNotificationRecipients();
        $this->assertIsTypeArray($callResult);
        $this->assertEquals($expectedResult, $callResult);
    }

    /**
     * @return bool[]
     */
    public function notificationEnabledProvider()
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * @return array
     */
    public function notificationRecipientsProvider()
    {
        return [
            ['', []],
            [null, []],
            ['john.doe@example.com', ['john.doe@example.com']],
            ['john.doe+shop@example.com', ['john.doe+shop@example.com']],
            ['john.doe@example.com, jan.kowalski@example.com', ['john.doe@example.com', 'jan.kowalski@example.com']],
            ['john.doe@example.com,jan.kowalski@example.com', ['john.doe@example.com', 'jan.kowalski@example.com']],
        ];
    }

    private function assertIsTypeBool($value)
    {
        if (method_exists($this, 'assertInternalType')) {
            return $this->assertInternalType('bool', $value);
        }

        return $this->assertIsBool($value);
    }

    private function assertIsTypeArray($value)
    {
        if (method_exists($this, 'assertInternalType')) {
            return $this->assertInternalType('array', $value);
        }

        return $this->assertIsArray($value);
    }
}
