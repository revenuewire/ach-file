<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-08
 * Time: 10:19
 */

namespace RW\Tests\ACH;


use PHPUnit\Framework\TestCase;
use RW\ACH\Batch;
use RW\ACH\BatchHeaderRecord;
use RW\ACH\EntryDetailRecord;

class BatchTest extends TestCase
{
    private $validBatchHeaderRecord;
    private $validEntryDetailData = [
        EntryDetailRecord::TRANSACTION_CODE   => EntryDetailRecord::SAVINGS_CREDIT_DEPOSIT,
        EntryDetailRecord::TRANSIT_ABA_NUMBER => '113000023',
        EntryDetailRecord::DFI_ACCOUNT_NUMBER => '987654321234567',
        EntryDetailRecord::AMOUNT             => '11.00',
        EntryDetailRecord::INDIVIDUAL_NAME    => 'A Valid Company Name',
        EntryDetailRecord::TRACE_NUMBER       => '87654321',
        EntryDetailRecord::ID_NUMBER          => 'AF34B52',
    ];

    /**
     * @throws \RW\ACH\ValidationException
     */
    public function setUp()
    {
        $this->validBatchHeaderRecord = new BatchHeaderRecord([
            BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
            BatchHeaderRecord::COMPANY_NAME              => 'A Real Company',
            BatchHeaderRecord::DISCRETIONARY_DATA        => 'A Real Description',
            BatchHeaderRecord::COMPANY_ID                => 'FMSPWAJM99',
            BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => 'PPD',
            BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => 'Payroll',
            BatchHeaderRecord::ORIGINATING_DFI_ID        => '87654321',
            BatchHeaderRecord::BATCH_NUMBER              => '1',
            BatchHeaderRecord::EFFECTIVE_ENTRY_DATE      => new \DateTime('2018-05-29 01:02:03'),
        ]);
    }

    public function validInputsProvider()
    {
        $noEntryOutput = <<<OUTPUT
5200A REAL COMPANY  A REAL DESCRIPTION  FMSPWAJM99PPDPAYROLL         180529   1876543210000001
82000000000000000000000000000000000000000000FMSPWAJM99                         876543210000001
OUTPUT;

        $singleEntryOutput = <<<OUTPUT
5200A REAL COMPANY  A REAL DESCRIPTION  FMSPWAJM99PPDPAYROLL         180529   1876543210000001
632113000023987654321234567  0000001100AF34B52        A VALID COMPANY NAME    0876543210000001
82000000010011300002000000000000000000001100FMSPWAJM99                         876543210000001
OUTPUT;

        $multiEntryOutput = <<<OUTPUT
5200A REAL COMPANY  A REAL DESCRIPTION  FMSPWAJM99PPDPAYROLL         180529   1876543210000001
632113000023987654321234567  0000001100AF34B52        A VALID COMPANY NAME    0876543210000001
632113000023987654321234567  0000001100AF34B52        A VALID COMPANY NAME    0876543210000002
632113000023987654321234567  0000001100AF34B52        A VALID COMPANY NAME    0876543210000003
82000000030033900006000000000000000000003300FMSPWAJM99                         876543210000001
OUTPUT;

        return [
            'No Entry' => [
                [],
                $noEntryOutput,
            ],
            'Single Entry' => [
                [$this->validEntryDetailData],
                $singleEntryOutput,
            ],
            'Multi-Entry' => [
                [
                    $this->validEntryDetailData,
                    $this->validEntryDetailData,
                    $this->validEntryDetailData,
                ],
                $multiEntryOutput,
            ],
        ];
    }

    public function testUnableToGetContentFromOpenBatch()
    {
        $e = null;
        try {
            (new Batch($this->validBatchHeaderRecord))->toString();
        } catch (\BadMethodCallException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(\BadMethodCallException::class, get_class($e));
    }

    /**
     * @throws \RW\ACH\ValidationException
     */
    public function testUnableToAddEntryToClosedBatch()
    {
        $e = null;
        try {
            $batch = new Batch($this->validBatchHeaderRecord);
            $batch->close();
            $batch->addEntryDetailRecord(new EntryDetailRecord($this->validEntryDetailData, 1));
        } catch (\BadMethodCallException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(\BadMethodCallException::class, get_class($e));
    }

    /**
     * @param $input
     * @param $output
     * @throws \RW\ACH\ValidationException
     * @dataProvider validInputsProvider
     */
    public function testEntryCountIsAccurate($input, $output)
    {
        $batch = new Batch($this->validBatchHeaderRecord);
        $output = 0;
        foreach ($input as $k => $entryDetailRecordData) {
            $output++;
            $batch->addEntryDetailRecord(new EntryDetailRecord($entryDetailRecordData, $k + 1));
        }

        $this->assertEquals($output, $batch->getEntryAndAddendaCount());
    }

    /**
     * @param $input
     * @param $output
     * @throws \RW\ACH\ValidationException
     * @dataProvider validInputsProvider
     */
    public function testEntryDollarSumIsAccurate($input, $output)
    {
        $batch     = new Batch($this->validBatchHeaderRecord);
        $debitSum  = '0.00';
        $creditSum = '0.00';
        foreach ($input as $k => $entryDetailRecordData) {
            if (in_array($entryDetailRecordData[EntryDetailRecord::TRANSACTION_CODE], EntryDetailRecord::DEBIT_TRANSACTION_CODES)) {
                $debitSum = bcadd($entryDetailRecordData[EntryDetailRecord::AMOUNT], $debitSum);
            } elseif (in_array($entryDetailRecordData[EntryDetailRecord::TRANSACTION_CODE], EntryDetailRecord::CREDIT_TRANSACTION_CODES)) {
                $creditSum = bcadd($entryDetailRecordData[EntryDetailRecord::AMOUNT], $creditSum);
            }

            $batch->addEntryDetailRecord(new EntryDetailRecord($entryDetailRecordData, $k + 1));
        }
        $debitSum = bcmul($debitSum, '100', 0);
        $creditSum = bcmul($creditSum, '100', 0);

        $this->assertEquals($debitSum, $batch->getEntryDollarSum(EntryDetailRecord::DEBIT_TRANSACTION_CODES));
        $this->assertEquals($creditSum, $batch->getEntryDollarSum(EntryDetailRecord::CREDIT_TRANSACTION_CODES));
    }

    /**
     * @param $input
     * @param $output
     * @throws \RW\ACH\ValidationException
     * @dataProvider validInputsProvider
     */
    public function testValidInputGeneratesCorrectBatch($input, $output)
    {
        $batch  = new Batch($this->validBatchHeaderRecord);
        foreach ($input as $k => $entryDetailRecordData) {
            $batch->addEntryDetailRecord(new EntryDetailRecord($entryDetailRecordData, $k + 1));
        }
        $batch->close();

        $this->assertEquals($output, $batch->toString());
    }
}
