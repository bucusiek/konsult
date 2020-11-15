<?php


namespace App\Application\Models;


use DateTime;

class ConsultationModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var \DateTime
     */
    public $dateStart;

    /**
     * @var int
     */
    public $duration;

    /**
     * @var int
     */
    public $id_user;

    /**
     * @var int
     */
    public $id_type;

    /**
     * @var bool
     */
    public $isAccepted = false;

    /**
     * @var string
     */
    public $applicant;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param DateTime $dateStart
     */
    public function setDateStart($dateStart): void
    {
        $this->dateStart = $dateStart;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return int
     */
    public function getIdUser(): int
    {
        return $this->id_user;
    }

    /**
     * @param int $id_user
     */
    public function setIdUser($id_user): void
    {
        $this->id_user = $id_user;
    }

    /**
     * @return int
     */
    public function getIdType(): int
    {
        return $this->id_type;
    }

    /**
     * @param int $id_type
     */
    public function setIdType($id_type): void
    {
        $this->id_type = $id_type;
    }

    /**
     * @return bool
     */
    public function isAccepted(): bool
    {
        return $this->isAccepted;
    }

    /**
     * @param bool $isAccepted
     */
    public function setIsAccepted(string $isAccepted): void
    {
        if($isAccepted === "1"){ $this->isAccepted = true; }
        else $this->isAccepted = false;
    }

    /**
     * @return string
     */
    public function getApplicant(): string
    {
        return $this->applicant;
    }

    /**
     * @param string $applicant
     */
    public function setApplicant(string $applicant): void
    {
        $this->applicant = $applicant;
    }
}