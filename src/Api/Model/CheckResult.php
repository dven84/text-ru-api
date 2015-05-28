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
     * Constructor.
     *
     * @param string $textId
     * @param float $uniquePercent
     */
    public function __construct($textId, $uniquePercent)
    {
        $this->textId = $textId;
        $this->uniquePercent = $uniquePercent;
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
}