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


}