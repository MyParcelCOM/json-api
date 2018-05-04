<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Traits;

use DateTime;

trait TimestampsTrait
{
    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }
}
