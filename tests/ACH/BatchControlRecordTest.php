<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-01
 * Time: 16:32
 */

namespace RW\Tests\ACH;


use PHPUnit\Framework\TestCase;
use RW\ACH\BatchControlRecord;
use RW\ACH\BatchHeaderRecord;
use RW\ACH\EntryDetailRecord;

class BatchControlRecordTest extends TestCase
{
    private const VALID_TRANSIT_ABA_NUMBER = '123456789';
    private const VALID_DFI_ACCOUNT_NUMBER = '01234-567-891011';
    private const VALID_AMOUNT             = '11.00';
    private const VALID_INDIVIDUAL_NAME    = 'A Valid Company Name';
    private const VALID_TRACE_NUMBER       = '12345678';

    private const VALID_COMPANY_NAME              = 'A Real Company';
    private const VALID_DISCRETIONARY_DATA        = 'A Real Description';
    private const VALID_COMPANY_IDENTIFICATION    = '0123456789';
    private const VALID_STANDARD_ENTRY_CLASS_CODE = 'PPD';
    private const VALID_COMPANY_ENTRY_DESCRIPTION = 'Payroll';
    private const VALID_ORIGINATING_DFI_ID        = '87654321';
    private const VALID_BATCH_NUMBER              = '1';

    /**
     * @return array
     * @throws \RW\ACH\ValidationException
     */
    public function validInputsProvider()
    {
        return [
            [
                // No Entry Detail Records
                [
                    BatchHeaderRecord::class => new BatchHeaderRecord([
                        BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                        BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                        BatchHeaderRecord::DISCRETIONARY_DATA        => self::VALID_DISCRETIONARY_DATA,
                        BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                        BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                        BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                        BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                        BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                        BatchHeaderRecord::ENTRY_DATE_OVERRIDE       => new \DateTime('2018-05-29 01:02:03'),
                    ]),
                    EntryDetailRecord::class => [],
                ],
                '820000000000000000000000000000000000000000000123456789                         876543210000001',
            ],
            [
                // Single Entry Detail Record
                [
                    BatchHeaderRecord::class => new BatchHeaderRecord([
                        BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                        BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                        BatchHeaderRecord::DISCRETIONARY_DATA        => self::VALID_DISCRETIONARY_DATA,
                        BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                        BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                        BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                        BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                        BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                        BatchHeaderRecord::ENTRY_DATE_OVERRIDE       => new \DateTime('2018-05-29 01:02:03'),
                    ]),
                    EntryDetailRecord::class => [
                        new EntryDetailRecord([
                            EntryDetailRecord::TRANSACTION_CODE   => EntryDetailRecord::SAVINGS_CREDIT_DEPOSIT,
                            EntryDetailRecord::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                            EntryDetailRecord::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                            EntryDetailRecord::AMOUNT             => self::VALID_AMOUNT,
                            EntryDetailRecord::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                            EntryDetailRecord::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                        ], 1),
                    ],
                ],
                '820000000100123456780000000000000000000011000123456789                         876543210000001',
            ],
            [
                // Multiple Entry Detail Records
                [
                    BatchHeaderRecord::class => new BatchHeaderRecord([
                        BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                        BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                        BatchHeaderRecord::DISCRETIONARY_DATA        => self::VALID_DISCRETIONARY_DATA,
                        BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                        BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                        BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                        BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                        BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                        BatchHeaderRecord::ENTRY_DATE_OVERRIDE       => new \DateTime('2018-05-29 01:02:03'),
                    ]),
                    EntryDetailRecord::class => [
                        new EntryDetailRecord([
                            EntryDetailRecord::TRANSACTION_CODE   => EntryDetailRecord::SAVINGS_CREDIT_DEPOSIT,
                            EntryDetailRecord::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                            EntryDetailRecord::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                            EntryDetailRecord::AMOUNT             => self::VALID_AMOUNT,
                            EntryDetailRecord::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                            EntryDetailRecord::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                        ], 1),
                        new EntryDetailRecord([
                            EntryDetailRecord::TRANSACTION_CODE   => EntryDetailRecord::SAVINGS_CREDIT_DEPOSIT,
                            EntryDetailRecord::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                            EntryDetailRecord::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                            EntryDetailRecord::AMOUNT             => self::VALID_AMOUNT,
                            EntryDetailRecord::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                            EntryDetailRecord::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                        ], 2),
                    ],
                ],
                '820000000200246913560000000000000000000022000123456789                         876543210000001',
            ],
        ];
    }

    /**
     * @param $input
     * @param $output
     * @throws \RW\ACH\ValidationException
     * @dataProvider validInputsProvider
     */
    public function testValidInputGeneratesCorrectBatchControlRecord($input, $output)
    {
        $batchHeaderRecord  = $input[BatchHeaderRecord::class];
        $entryDetailRecords = $input[EntryDetailRecord::class];

        $transitSum = 0;
        /** @var EntryDetailRecord $entryDetailRecord */
        foreach ($entryDetailRecords as $entryDetailRecord) {
            $transitSum += (int) $entryDetailRecord->getTransitAbaNumber();
        }

        $debitDollarSum = 0;
        /** @var EntryDetailRecord $entryDetailRecord */
        foreach ($entryDetailRecords as $entryDetailRecord) {
            if (in_array($entryDetailRecord->getTransactionCode(), EntryDetailRecord::DEBIT_TRANSACTION_CODES)) {
                $debitDollarSum += (int) $entryDetailRecord->getAmount();
            }
        }

        $creditDollarSum = 0;
        /** @var EntryDetailRecord $entryDetailRecord */
        foreach ($entryDetailRecords as $entryDetailRecord) {
            if (in_array($entryDetailRecord->getTransactionCode(), EntryDetailRecord::CREDIT_TRANSACTION_CODES)) {
                $creditDollarSum += (int) $entryDetailRecord->getAmount();
            }
        }

        $batchControlRecord = new BatchControlRecord(
            $batchHeaderRecord,
            count($entryDetailRecords),
            $transitSum,
            $debitDollarSum,
            $creditDollarSum
        );
        $this->assertEquals($output, $batchControlRecord->toString());
    }
}