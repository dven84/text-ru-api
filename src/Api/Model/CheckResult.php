<?php

namespace TextParams\TextRu\Api\Model;

class CheckResult 
{
    /**
     * @var string
     */
    private $textId;

    /**
     * @var float
     */
    private $uniquePercent;

    /**
     * @var float
     */
    private $waterPercent;

    /**
     * Constructor.
     *
     * @param string $textId
     * @param float $uniquePercent
     * @param float $waterPercent
     */
    public function __construct($textId, $uniquePercent, $waterPercent)
    {
        $this->textId = $textId;
        $this->uniquePercent = $uniquePercent;
        $this->waterPercent = $waterPercent;
    }

    /**
     * @return string
     */
    public function getTextId()
    {
        return $this->textId;
    }

    /**
     * @return float
     */
    public function getUniquePercent()
    {
        return $this->uniquePercent;
    }

    /**
     * @return float
     */
    public function getWaterPercent()
    {
        return $this->waterPercent;
    }
}