<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-11
 * Time: 09:49
 */

namespace RW\ACH;


abstract class ComponentCollection
{
    /** @var bool */
    protected $isOpen = true;
    /** @var FileComponent */
    protected $headerRecord;
    /** @var FileComponent[] */
    protected $collection = [];
    /** @var FileComponent */
    protected $controlRecord;

    protected static abstract function buildFromResource($handle, &$count = null);

    /**
     * Batch constructor.
     *
     * @param FileComponent $headerRecord
     */
    public function __construct($headerRecord)
    {
        $this->headerRecord = $headerRecord;
    }

    /**
     * @return int
     */
    public function getEntryAndAddendaCount(): int
    {
        $entryAndAddendaCount = 0;
        foreach ($this->collection as $component) {
            if ($component instanceof ComponentCollection) {
                $entryAndAddendaCount += $component->getEntryAndAddendaCount();
            } else {
                $entryAndAddendaCount += $component->getBlockCount();
            }
        }

        return $entryAndAddendaCount;
    }

    /**
     * @return int
     */
    public function getBlockCount(): int
    {
        if ($this->isOpen) {
            throw new \BadMethodCallException('Unable to obtain the block count of an open ' . static::class);
        }

        // Every component collection type should have a header record, a control record, and some number of
        // components >= 0. Because the control record may require this call as part of the constructor, we
        // don't make the call on the header and control records directly - the control record may not exist yet!
        $blockCount = 2;
        foreach ($this->collection as $component) {
            $blockCount += $component->getBlockCount();
        }

        return $blockCount;
    }

    /**
     * @return int
     */
    public function getSumOfTransitNumbers(): int
    {
        $transitSum = 0;
        // Collection items should always drill down to an Entry Detail Record eventually
        /** @var EntryDetailRecord $component */
        foreach ($this->collection as $component) {
            if ($component instanceof ComponentCollection) {
                $transitSum += $component->getSumOfTransitNumbers();
            } else {
                $transitSum += (int) $component->getField(EntryDetailRecord::TRANSIT_ABA_NUMBER);
            }
        }

        return $transitSum;
    }

    /**
     * @param array $validTransactionCodes
     * @return string
     */
    public function getEntryDollarSum($validTransactionCodes): string
    {
        $dollarSum = '0';
        // Collection items should always drill down to an Entry Detail Record eventually
        /** @var EntryDetailRecord $component */
        foreach ($this->collection as $component) {
            if ($component instanceof ComponentCollection) {
                $dollarSum = bcadd($component->getEntryDollarSum($validTransactionCodes), $dollarSum, 0);
            } elseif (in_array($component->getField(EntryDetailRecord::TRANSACTION_CODE), $validTransactionCodes)) {
                // Amounts should always be retrieved without decimals ($11.35 = '1135')
                $dollarSum = bcadd($component->getField(EntryDetailRecord::AMOUNT), $dollarSum, 0);
            }
        }

        return "$dollarSum";
    }

    protected abstract function getControlRecord(): FileComponent;

    /**
     * @param ComponentCollection|FileComponent $component
     * @return static for clean method chaining.
     */
    public function addComponent($component)
    {
        if (!$this->isOpen) {
            throw new \BadMethodCallException('Unable to add entries to a closed collection');
        }

        $this->collection[] = $component;

        return $this;
    }

    /**
     * Finalize the batch and generate the Batch Control Record
     *
     * @return static for clean method chaining
     */
    public function close()
    {
        $this->isOpen = false;
        $this->controlRecord = $this->getControlRecord();

        return $this;
    }

    public function toString()
    {
        if ($this->isOpen) {
            throw new \BadMethodCallException('Unable to convert an open batch to a string');
        }

        $content = "{$this->headerRecord->toString()}\n";
        foreach ($this->collection as $component) {
            $content .= "{$component->toString()}\n";
        }
        // Don't add an extra new line here, let the caller decide if it is required.
        $content .= "{$this->controlRecord->toString()}";

        return $content;
    }
}
