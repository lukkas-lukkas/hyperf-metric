<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf + OpenCodeCo
 *
 * @link     https://opencodeco.dev
 * @document https://hyperf.wiki
 * @contact  leo@opencodeco.dev
 * @license  https://github.com/opencodeco/hyperf-metric/blob/main/LICENSE
 */
namespace Hyperf\Metric\Adapter\RemoteProxy;

use Hyperf\Metric\Contract\GaugeInterface;
use Hyperf\Process\ProcessCollector;

class Gauge implements GaugeInterface
{
    protected const TARGET_PROCESS_NAME = 'metric';

    /**
     * @var string[]
     */
    public array $labelValues = [];

    public ?float $delta;

    public ?float $value;

    public function __construct(public string $name, public array $labelNames)
    {
    }

    public function with(string ...$labelValues): static
    {
        $this->labelValues = $labelValues;
        return $this;
    }

    public function set(float $value): void
    {
        $this->value = $value;
        $this->delta = null;
        $process = ProcessCollector::get(static::TARGET_PROCESS_NAME)[0];
        $process->write(serialize($this));
    }

    public function add(float $delta): void
    {
        $this->delta = $delta;
        $this->value = null;
        $process = ProcessCollector::get(static::TARGET_PROCESS_NAME)[0];
        $process->write(serialize($this));
    }
}
