<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-08
 * Time: 09:44
 */

namespace RW\Tests\ACH;


use PHPUnit\Framework\TestCase;
use RW\ACH\Batch;
use RW\ACH\BatchHeaderRecord;
use RW\ACH\EntryDetailRecord;
use RW\ACH\FileControlRecord;
use RW\ACH\FileHeaderRecord;

class FileControlRecordTest extends TestCase
{
    private const VALID_FILE_HEADER_DATA = [
        FileHeaderRecord::IMMEDIATE_DESTINATION => ' 123456789',
        FileHeaderRecord::IMMEDIATE_ORIGIN      => '0123456789',
        FileHeaderRecord::DESTINATION_NAME      => 'abcdefg0123456789',
        FileHeaderRecord::ORIGIN_NAME           => 'abcdefg9876543210',
    ];

    /**
     * @return array
     * @throws \RW\ACH\ValidationException
     */
    public function validInputsProvider()
    {
        $validBatchHeaderData = [
            BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
            BatchHeaderRecord::COMPANY_NAME              => 'A Real Company',
            BatchHeaderRecord::DISCRETIONARY_DATA        => 'A Real Description',
            BatchHeaderRecord::COMPANY_ID                => '0123456789',
            BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => 'PPD',
            BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => 'Payroll',
            BatchHeaderRecord::ORIGINATING_DFI_ID        => '87654321',
        ];
        $validEntryDetailData1 = [
            EntryDetailRecord::TRANSACTION_CODE   => EntryDetailRecord::SAVINGS_CREDIT_DEPOSIT,
            EntryDetailRecord::TRANSIT_ABA_NUMBER => '113000023',
            EntryDetailRecord::DFI_ACCOUNT_NUMBER => '01234567891011',
            EntryDetailRecord::AMOUNT             => '11.00',
            EntryDetailRecord::INDIVIDUAL_NAME    => 'A Valid Company Name',
            EntryDetailRecord::TRACE_NUMBER       => '87654321',
        ];
        $validEntryDetailData2 = [
            EntryDetailRecord::TRANSACTION_CODE   => EntryDetailRecord::SAVINGS_DEBIT_PAYMENT,
            EntryDetailRecord::TRANSIT_ABA_NUMBER => '113000023',
            EntryDetailRecord::DFI_ACCOUNT_NUMBER => '01234567891011',
            EntryDetailRecord::AMOUNT             => '12.00',
            EntryDetailRecord::INDIVIDUAL_NAME    => 'A Valid Company Name',
            EntryDetailRecord::TRACE_NUMBER       => '87654321',
        ];

        $batches = [];
        $batchNumber = 1;
        while ($batchNumber < 4) {
            $validBatchHeaderData[BatchHeaderRecord::BATCH_NUMBER] = $batchNumber;
            $batch                                                 = new Batch(new BatchHeaderRecord($validBatchHeaderData));
            $batch->addEntryDetailRecord(new EntryDetailRecord($validEntryDetailData1, 1));
            $batch->addEntryDetailRecord(new EntryDetailRecord($validEntryDetailData2, 2));
            $batch->close();
            $batches[] = $batch;
            $batchNumber++;
        }

        return [
            [
                [],
                '9000000000001000000000000000000000000000000000000000000                                       ',
            ],
            [
                [$batches[0]],
                '9000001000001000000020022600004000000001200000000001100                                       ',
            ],
            [
                [
                    $batches[0],
                    $batches[1],
                    $batches[2],
                ],
                '9000003000002000000060067800012000000003600000000003300                                       ',
            ],
        ];
    }

    /**
     * @param $input
     * @param $output
     * @throws \RW\ACH\ValidationException
     * @dataProvider validInputsProvider
     */
    public function testValidInputGeneratesCorrectFileControlRecord($input, $output)
    {
        $entryAndAddendaCount = 0;
        $transitSum = 0;
        $debitDollarSum = 0;
        $creditDollarSum = 0;
        /** @var Batch $batch */
        foreach ($input as $batch) {
            $entryAndAddendaCount += $batch->getEntryAndAddendaCount();
            $transitSum += $batch->getSumOfTransitNumbers();
            $debitDollarSum = bcadd($batch->getEntryDollarSum(EntryDetailRecord::DEBIT_TRANSACTION_CODES), $debitDollarSum);
            $creditDollarSum = bcadd($batch->getEntryDollarSum(EntryDetailRecord::CREDIT_TRANSACTION_CODES), $creditDollarSum);
        }
        
        $batchCount = (string) count($input);
        $blockCount = (string) (ceil((2 + (2 * count($input)) + $entryAndAddendaCount) / 10) * 10) / 10;

        $fileControlRecord = FileControlRecord::buildFromBatchData(
            $batchCount,
            $blockCount,
            "$entryAndAddendaCount",
            $transitSum,
            $debitDollarSum,
            $creditDollarSum
        );

        $this->assertEquals($output, $fileControlRecord->toString());
    }

    /**
     * @throws \RW\ACH\ValidationException
     */
    public function testValidStringInputGeneratesValidFileControlRecord()
    {
        $input = '9000000000001000000000000000000000000000000000000000000                                       ';
        $fhr   = FileControlRecord::buildFromString($input);
        $this->assertEquals($input, $fhr->toString());
    }
}
