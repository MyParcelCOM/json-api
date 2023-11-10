<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Traits;

use Carbon\Carbon;

/**
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */
trait TimestampsTrait
{
    public function getCreatedAt(): Carbon
    {
        return $this->created_at ? $this->created_at->copy() : Carbon::now();
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->updated_at ? $this->updated_at->copy() : Carbon::now();
    }
}
