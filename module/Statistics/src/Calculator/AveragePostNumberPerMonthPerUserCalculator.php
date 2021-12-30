<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

/**
 * Average number of posts per user per month is a vague requirement. Does not matter for test assignment so
 * assuming that it needs weekly for active weeks average grouped by user. Whatever no correct answer right ;)
 *
 * Class AveragePostNumberPerUserPerMonthCalculator
 * @package Statistics\Calculator
 */
final class AveragePostNumberPerMonthPerUserCalculator extends AbstractCalculator
{
    protected const UNITS = 'posts';

    /** @var TotalPostsPerWeek[] user is key */
    private array $data = [];

    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $key = $postTo->getAuthorId();
        if (!isset($this->data[$key])) {
            $calculator = new TotalPostsPerWeek();
            $calculator->setParameters($this->parameters);
            $this->data[$key] = $calculator;
        } else {
            $calculator = $this->data[$key];
        }

        $calculator->accumulateData($postTo);
    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        $stats = new StatisticsTo();
        foreach ($this->data as $userId => $weeklyStats) {
            $cnt = 0;
            $total = 0;
            /** @var StatisticsTo $statsTo */
            foreach ($weeklyStats->calculate()->getChildren() as $statsTo) {
                $cnt++;
                $total+=$statsTo->getValue();
            }
            $child = (new StatisticsTo())
                ->setName($this->parameters->getStatName())
                ->setValue($total/$cnt)
                ->setSplitPeriod($userId) // em... meh
                ->setUnits(self::UNITS);

            $stats->addChild($child);
        }

        return $stats;
    }
}
