<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-05-28
 * Time: 11:54
 */

namespace RW\ACH;


class PaymentFile extends ComponentCollection
{
    /** @var FileHeaderRecord */
    protected $headerRecord;
    /** @var Batch[] */
    protected $collection = [];

    /**
     * @return FileComponent
     * @throws ValidationException
     */
    protected function getControlRecord(): FileComponent
    {
        return new FileControlRecord(
            $this->headerRecord,
            count($this->collection),
            $this->getBlockCount(),
            "{$this->getEntryAndAddendaCount()}",
            $this->getSumOfTransitNumbers(),
            $this->getEntryDollarSum(EntryDetailRecord::DEBIT_TRANSACTION_CODES),
            $this->getEntryDollarSum(EntryDetailRecord::CREDIT_TRANSACTION_CODES)
        );
    }

    public function toString()
    {
        // Add a new line to the end of the file.
        return parent::toString() . "\n";
    }
}
