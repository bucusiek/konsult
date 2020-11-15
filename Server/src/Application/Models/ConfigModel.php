<?php


namespace App\Application\Models;

class ConfigModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $day_of_week;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getDayOfWeek(): int
    {
        return $this->day_of_week;
    }

    /**
     * @param int $day_of_week
     */
    public function setDayOfWeek(int $day_of_week): void
    {
        $this->day_of_week = $day_of_week;
    }

}