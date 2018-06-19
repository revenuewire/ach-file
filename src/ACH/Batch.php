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
     * @param resource $handle
     * @param int      $count
     * @return Batch|bool
     * @throws ValidationException
     */
    protected static function buildFromResource($handle, &$count = null)
    {
        $count = $count ?: 0;
        $initialPosition = ftell($handle);

        $record = rtrim(fgets($handle), "\n");
        // If this isn't the start of a batch, reset the internal file pointer and return false
        if (mb_substr($record, 0, 1) !== BatchHeaderRecord::FIXED_RECORD_TYPE_CODE) {
            fseek($handle, $initialPosition);

            return false;
        }

        $count++;
        $batch = new Batch(BatchHeaderRecord::buildFromString($record));

        // Iterate over and generate each entry detail record
        while (
            ($record = rtrim(fgets($handle), "\n"))
            && (mb_substr($record, 0, 1) === EntryDetailRecord::FIXED_RECORD_TYPE_CODE)
        ) {
            $count++;
            // Build the addenda from the string if required
            $entryDetailRecord = EntryDetailRecord::buildFromString($record);
            if ($entryDetailRecord->hasAddendaRecord()) {
                $record = rtrim(fgets($handle), "\n");
                $count++;
                if (mb_substr($record, 0, 1) !== AddendaRecord::FIXED_RECORD_TYPE_CODE) {
                    throw new ValidationException('Found record type code ' . mb_substr($record, 0, 1) . ', expected ' . AddendaRecord::FIXED_RECORD_TYPE_CODE . " (Addenda Record) on line {$count}");
                }
                $entryDetailRecord->setAddendaRecord(AddendaRecord::buildFromString($record));
            }
            $batch->addComponent($entryDetailRecord);
            // TODO: add addenda processing
        }

        // Pull batch control record
        $count++;
        if (mb_substr($record, 0, 1) !== BatchControlRecord::FIXED_RECORD_TYPE_CODE) {
            throw new ValidationException('Found record type code ' . mb_substr($record, 0, 1) . ', expected ' . BatchControlRecord::FIXED_RECORD_TYPE_CODE . " (Batch Control) on line {$count}");
        }
        $batch->setControlRecord(BatchControlRecord::buildFromString($record))
              ->close();

        return $batch;
    }

    /**
     * @return FileComponent
     * @throws ValidationException
     */
    protected function getControlRecord(): FileComponent
    {
        if (!isset($this->controlRecord)) {
            if ($this->isOpen) {
                throw new \BadMethodCallException('Unable to generate the control record of an open batch');
            }

            $this->controlRecord = BatchControlRecord::buildFromBatchData(
                $this->headerRecord,
                "{$this->getEntryAndAddendaCount()}",
                $this->getSumOfTransitNumbers(),
                $this->getEntryDollarSum(EntryDetailRecord::DEBIT_TRANSACTION_CODES),
                $this->getEntryDollarSum(EntryDetailRecord::CREDIT_TRANSACTION_CODES)
            );
        }

        return $this->controlRecord;
    }

    /**
     * @param BatchControlRecord $input
     * @return Batch
     */
    public function setControlRecord($input): Batch
    {
        $this->controlRecord = $input;

        return $this;
    }
}
