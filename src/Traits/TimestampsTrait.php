<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Traits;

use Illuminate\Support\Carbon;

trait TimestampsTrait
{
    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updated_at;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }
}
