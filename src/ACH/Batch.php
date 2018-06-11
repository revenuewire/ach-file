<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-08
 * Time: 10:22
 */

namespace RW\ACH;


class Batch extends ComponentCollection
{
    /** @var BatchHeaderRecord */
    protected $headerRecord;
    /** @var EntryDetailRecord[] */
    protected $collection = [];

    /**
     * @return FileComponent
     * @throws ValidationException
     */
    protected function getControlRecord(): FileComponent
    {
        return new BatchControlRecord(
            $this->headerRecord,
            "{$this->getEntryAndAddendaCount()}",
            $this->getSumOfTransitNumbers(),
            $this->getEntryDollarSum(EntryDetailRecord::DEBIT_TRANSACTION_CODES),
            $this->getEntryDollarSum(EntryDetailRecord::CREDIT_TRANSACTION_CODES)
        );
    }

    /**
     * @return int
     */
    public function getEntryAndAddendaCount(): int
    {
        $count = 0;
        foreach ($this->collection as $entryDetailRecord) {
            $count += $entryDetailRecord->getBlockCount();
        }

        return $count;
    }

    /**
     * @return int
     */
    public function getSumOfTransitNumbers(): int
    {
        $transitSum = 0;
        /** @var EntryDetailRecord $entryDetailRecord */
        foreach ($this->collection as $entryDetailRecord) {
            $transitSum += (int) $entryDetailRecord->getTransitAbaNumber();
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
        foreach ($this->collection as $entryDetailRecord) {
            if (in_array($entryDetailRecord->getTransactionCode(), $validTransactionCodes)) {
                // Amounts should always be retrieved without decimals ($11.35 = '1135')
                $dollarSum = bcadd($entryDetailRecord->getAmount(), $dollarSum, 0);
            }
        }

        return "$dollarSum";
    }

    public function getBlockCount(): int
    {
        if ($this->isOpen) {
            throw new \BadMethodCallException('Unable to obtain the block count of an open batch');
        }

        return $this->headerRecord->getBlockCount() +
            $this->getEntryAndAddendaCount() +
            $this->controlRecord->getBlockCount();
    }
}