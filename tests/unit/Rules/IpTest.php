<?php

/*
 * This file is part of Respect/Validation.
 *
 * (c) Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Respect\Validation\Rules;

use Respect\Validation\Test\TestCase;

/**
 * @group  rule
 * @covers \Respect\Validation\Exceptions\IpException
 * @covers \Respect\Validation\Rules\Ip
 *
 * @author Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 * @author Gabriel Caruso <carusogabriel34@gmail.com>
 * @author Henrique Moody <henriquemoody@gmail.com>
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class IpTest extends TestCase
{
    /**
     * @dataProvider providerForIp
     *
     * @test
     */
    public function validIpsShouldReturnTrue($input, $options = null): void
    {
        $ipValidator = new Ip($options);
        self::assertTrue($ipValidator->__invoke($input));
        $ipValidator->assert($input);
        $ipValidator->check($input);
    }

    /**
     * @dataProvider providerForIpBetweenRange
     *
     * @test
     */
    public function ipsBetweenRangeShouldReturnTrue($input, $networkRange): void
    {
        $ipValidator = new Ip($networkRange);
        self::assertTrue($ipValidator->__invoke($input));
        $ipValidator->assert($input);
        $ipValidator->check($input);
    }

    /**
     * @dataProvider providerForNotIp
     * @expectedException \Respect\Validation\Exceptions\IpException
     *
     * @test
     */
    public function invalidIpsShouldThrowIpException($input, $options = null): void
    {
        $ipValidator = new Ip($options);
        self::assertFalse($ipValidator->__invoke($input));
        $ipValidator->assert($input);
    }

    /**
     * @dataProvider providerForIpOutsideRange
     * @expectedException \Respect\Validation\Exceptions\IpException
     *
     * @test
     */
    public function ipsOutsideRangeShouldReturnFalse($input, $networkRange): void
    {
        $ipValidator = new Ip($networkRange);
        self::assertFalse($ipValidator->__invoke($input));
        $ipValidator->assert($input);
    }

    public function providerForIp()
    {
        return [
            ['127.0.0.1'],
        ];
    }

    public function providerForIpBetweenRange()
    {
        return [
            ['127.0.0.1', '127.*'],
            ['127.0.0.1', '127.0.*'],
            ['127.0.0.1', '127.0.0.*'],
            ['192.168.2.6', '192.168.*.6'],
            ['192.168.2.6', '192.*.2.6'],
            ['10.168.2.6', '*.168.2.6'],
            ['192.168.2.6', '192.168.*.*'],
            ['192.10.2.6', '192.*.*.*'],
            ['192.168.255.156', '*'],
            ['192.168.255.156', '*.*.*.*'],
            ['127.0.0.1', '127.0.0.0-127.0.0.255'],
            ['192.168.2.6', '192.168.0.0-192.168.255.255'],
            ['192.10.2.6', '192.0.0.0-192.255.255.255'],
            ['192.168.255.156', '0.0.0.0-255.255.255.255'],
            ['220.78.173.2', '220.78.168/21'],
            ['220.78.173.2', '220.78.168.0/21'],
            ['220.78.173.2', '220.78.168.0/255.255.248.0'],
        ];
    }

    public function providerForNotIp()
    {
        return [
            [''],
            [null],
            ['j'],
            [' '],
            ['Foo'],
            ['192.168.0.1', FILTER_FLAG_NO_PRIV_RANGE],
        ];
    }

    public function providerForIpOutsideRange()
    {
        return [
            ['127.0.0.1', '127.0.1.*'],
            ['192.168.2.6', '192.163.*.*'],
            ['192.10.2.6', '193.*.*.*'],
            ['127.0.0.1', '127.0.1.0-127.0.1.255'],
            ['192.168.2.6', '192.163.0.0-192.163.255.255'],
            ['192.10.2.6', '193.168.0.0-193.255.255.255'],
            ['220.78.176.1', '220.78.168/21'],
            ['220.78.176.2', '220.78.168.0/21'],
            ['220.78.176.3', '220.78.168.0/255.255.248.0'],
        ];
    }

    /**
     * @dataProvider providerForInvalidRanges
     * @expectedException \Respect\Validation\Exceptions\ComponentException
     *
     * @test
     */
    public function invalidRangeShouldRaiseException($range): void
    {
        $o = new Ip($range);
    }

    public function providerForInvalidRanges()
    {
        return [
            ['192.168'],
            ['asd'],
            ['192.168.0.0-192.168.0.256'],
            ['192.168.0.0-192.168.0.1/4'],
            ['192.168.0/1'],
            ['192.168.2.0/256.256.256.256'],
            ['192.168.2.0/8.256.256.256'],
        ];
    }
}
