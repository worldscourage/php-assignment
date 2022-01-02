<?php

declare(strict_types = 1);

namespace Tests\unit;

use PHPUnit\Framework\TestCase;
use SocialPost\Dto\SocialPostTo;
use Statistics\Builder\ParamsBuilder;
use Statistics\Calculator\AveragePostNumberPerMonthPerUserCalculator;
use Statistics\Dto\ParamsTo;

/**
 * Class ATestTest
 *
 * @package Tests\unit
 */
class AveragePostNumberPerUserPerMonthCalculatorTest extends TestCase
{
    /**
     * @test
     */
    public function testSomething(): void
    {
        $calculator = new AveragePostNumberPerMonthPerUserCalculator();
        $calculator->setParameters($this->getParams());
        foreach ($this->getPosts() as $post) {
            $calculator->accumulateData($post);
        }
        $stats = $calculator->calculate();
        $this->assertCount(2, $stats->getChildren());
        $this->assertEquals(3/2, $stats->getChildren()[0]->getValue());

        $this->assertTrue(true);
    }

    private function getParams(): ParamsTo
    {
        $params = new ParamsTo();
        $date = new \DateTime('November 2021');
        $params = ParamsBuilder::reportStatsParams($date);
        return $params[3];
    }

    private function getPosts(): array
    {
        return [
            (new SocialPostTo())
                ->setId('1')
                ->setDate(new \DateTime('12 November 2021 21:17:52 +0000'))
                ->setAuthorId('1')
                ->setAuthorName('Pavel')
                ->setText('qwer'),
            (new SocialPostTo())
                ->setId('2')
                ->setDate(new \DateTime('12 November 2021 21:17:52 +0000'))
                ->setAuthorId('1')
                ->setAuthorName('Pavel')
                ->setText('asdf'),
            (new SocialPostTo())
                ->setId('4')
                ->setDate(new \DateTime('25 November 2021 21:17:52 +0000'))
                ->setAuthorId('1')
                ->setAuthorName('Pavel')
                ->setText('asdf'),
            (new SocialPostTo())
                ->setId('3')
                ->setDate(new \DateTime('12 November 2021 21:17:52 +0000'))
                ->setAuthorId('2')
                ->setAuthorName('Kazbek')
                ->setText('zxcv'),
        ];
    }
}
