<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-05-28
 * Time: 11:54
 */

namespace RW\ACH;


class File extends ComponentCollection
{
    const BLOCKING_FILE_CONTROL_RECORD = '9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999';

    /** @var FileHeaderRecord */
    protected $headerRecord;
    /** @var Batch[] */
    protected $collection = [];

    /**
     * Build an ACH file from a resource
     *
     * @param resource $handle
     * @param int      $count an optional variable to hold the number of records added
     * @return File
     * @throws ValidationException
     */
    public static function buildFromResource($handle, &$count = null)
    {
        // Pull file header record and initialize the payment file object
        $record = rtrim(fgets($handle), "\n");
        $count  = 1;
        if (mb_substr($record, 0, 1) !== FileHeaderRecord::FIXED_RECORD_TYPE_CODE) {
            throw new ValidationException('Found record type code ' . mb_substr($record, 0, 1) . ', expected ' . FileHeaderRecord::FIXED_RECORD_TYPE_CODE . " (File Header) on line {$count}");
        }
        $paymentFile = new File(FileHeaderRecord::buildFromString($record));

        // Iterate over and generate each batch
        while ($batch = Batch::buildFromResource($handle, $count)) {
            $paymentFile->addComponent($batch);
        }

        // Pull the file control record
        $record = rtrim(fgets($handle), "\n");
        $count++;
        if (mb_substr($record, 0, 1) !== FileControlRecord::FIXED_RECORD_TYPE_CODE) {
            throw new ValidationException('Found record type code ' . mb_substr($record, 0, 1) . ', expected ' . FileControlRecord::FIXED_RECORD_TYPE_CODE . " (File Control) on line {$count}");
        }
        $paymentFile->setControlRecord(FileControlRecord::buildFromString($record))
            ->close();

        return $paymentFile;
    }

    /**
     * Build a list of ACH files from a resource that could contain multiple files, possibly separated by blocking records
     *
     * @param resource $handle
     * @param int      $count an optional variable to hold the number of records added
     * @return array
     */
    public static function buildFromStackedResource($handle, &$count = null)
    {
        $files = [];
        try {
            while (!feof($handle)) {
                $files[] = self::buildFromResource($handle, $count);
                $handle = self::clearBlockingFileControlRecords($handle);
            }
        } catch (ValidationException $e) {
            // noop - this should indicate that we're at the end of the file
        }

        return $files;
    }

    /**
     * Add a batch to the file
     *
     * @param $batch
     * @return File for easy method chaining
     */
    public function addBatch($batch)
    {
        return $this->addComponent($batch);
    }

    /**
     * Get an array of batches belonging to the ACH file
     *
     * @return array|ComponentCollection[]
     */
    public function getBatches()
    {
        return parent::getCollection();
    }

    /**
     * Get the string representation of the ACH File. Includes a trailing newline character.
     *
     * @return string
     */
    public function toString()
    {
        // Add a new line to the end of the file.
        return parent::toString() . "\n";
    }

    /**
     * Get the block count for the file control record
     *
     * The block count is each complete and partial collection of 10 lines as 1 block
     * Block count is equal to all records in the file, rounded up to the nearest multiple of 10, and divided by 10
     *
     * File header + file control = 2
     * Batches = 2 * batch count
     * Entries = entry count
     *  
     * @return int
     */
    public function getBlockCount(): int
    {
        return (ceil((2 + (2 * count($this->collection)) + $this->getEntryAndAddendaCount()) / 10) * 10) / 10;
    }

    /**
     * @return FileComponent
     * @throws ValidationException
     */
    protected function getControlRecord(): FileComponent
    {
        if (!isset($this->controlRecord)) {
            if ($this->isOpen) {
                throw new \BadMethodCallException('Unable to generate the control record of an open payment file');
            }

            $this->controlRecord = FileControlRecord::buildFromBatchData(
                (string) count($this->collection),
                (string) self::getBlockCount(),
                "{$this->getEntryAndAddendaCount()}",
                $this->getSumOfTransitNumbers(),
                $this->getEntryDollarSum(EntryDetailRecord::DEBIT_TRANSACTION_CODES),
                $this->getEntryDollarSum(EntryDetailRecord::CREDIT_TRANSACTION_CODES)
            );
        }

        return $this->controlRecord;
    }

    /**
     * Set the File Control record.
     *
     * @param FileControlRecord $v
     * @return File
     */
    private function setControlRecord($v): File
    {
        $this->controlRecord = $v;

        return $this;
    }

    /**
     * @param resource $handle
     * @return resource
     */
    private static function clearBlockingFileControlRecords($handle)
    {
        // Consume any blocking records
        do {
            $initialPosition = ftell($handle);
            $record          = rtrim(fgets($handle));
        } while ($record === self::BLOCKING_FILE_CONTROL_RECORD);

        // Rewind to the start of the line, in case there's additional files to process
        fseek($handle, $initialPosition);

        // This is just to explicitly show that $handle has mutated
        return $handle;
    }
}
