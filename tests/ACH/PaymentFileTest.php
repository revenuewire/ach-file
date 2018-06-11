<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-05-28
 * Time: 13:32
 */

namespace RW\Tests\ACH;


use PHPUnit\Framework\TestCase;
use RW\ACH\Batch;
use RW\ACH\BatchHeaderRecord;
use RW\ACH\EntryDetailRecord;
use RW\ACH\FileHeaderRecord;
use RW\ACH\PaymentFile;

class PaymentFileTest extends TestCase
{
    /** @var FileHeaderRecord */
    private $validFileHeaderRecord;
    /** @var BatchHeaderRecord */
    private $validBatchHeaderRecord;
    /** @var array */
    private $validEntryDetailData;

    /**
     * @throws \RW\ACH\ValidationException
     */
    public function setUp()
    {
        $this->validFileHeaderRecord = new FileHeaderRecord([
            FileHeaderRecord::IMMEDIATE_DESTINATION => ' 123456789',
            FileHeaderRecord::IMMEDIATE_ORIGIN      => '0123456789',
            FileHeaderRecord::DESTINATION           => 'abcdefg0123456789',
            FileHeaderRecord::ORIGIN_NAME           => 'abcdefg9876543210',
            FileHeaderRecord::FILE_DATE_OVERRIDE    => new \DateTime('2018-05-29 15:19:45'),
        ]);
        $this->validBatchHeaderRecord = new BatchHeaderRecord([
            BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
            BatchHeaderRecord::COMPANY_NAME              => 'A Real Company',
            BatchHeaderRecord::DISCRETIONARY_DATA        => 'A Real Description',
            BatchHeaderRecord::COMPANY_ID                => '0123456789',
            BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => 'PPD',
            BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => 'Payroll',
            BatchHeaderRecord::ORIGINATING_DFI_ID        => '87654321',
            BatchHeaderRecord::BATCH_NUMBER              => '1',
            BatchHeaderRecord::ENTRY_DATE_OVERRIDE       => new \DateTime('2018-05-29 15:20:03'),
        ]);
        $this->validEntryDetailData = [
            EntryDetailRecord::TRANSACTION_CODE   => EntryDetailRecord::CHECKING_CREDIT_DEPOSIT,
            EntryDetailRecord::TRANSIT_ABA_NUMBER => '123456789',
            EntryDetailRecord::DFI_ACCOUNT_NUMBER => '01234567891011',
            EntryDetailRecord::AMOUNT             => '11.00',
            EntryDetailRecord::INDIVIDUAL_NAME    => 'A Valid Company Name',
            EntryDetailRecord::TRACE_NUMBER       => '12345678',
        ];
    }

    /**
     * @return array
     * @throws \RW\ACH\ValidationException
     */
    public function validInputsProvider()
    {
        $this->setUp();
        $noBatchOutput = <<<OUTPUT
101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
9000000000002000000000000000000000000000000000000000000                                       

OUTPUT;

        $emptyBatch = new Batch($this->validBatchHeaderRecord);
        $emptyBatch->close();
        $emptyBatchOutput = <<<OUTPUT
101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL   180529180529   1876543210000001
820000000000000000000000000000000000000000000123456789                         876543210000001
9000001000004000000000000000000000000000000000000000000                                       

OUTPUT;

        $singleEntryBatch = new Batch($this->validBatchHeaderRecord);
        $singleEntryBatch->addComponent(new EntryDetailRecord($this->validEntryDetailData, 1));
        $singleEntryBatch->close();
        $singleEntryBatchOutput = <<<OUTPUT
101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL   180529180529   1876543210000001
62212345678901234567891011   0000001100               A VALID COMPANY NAME    0123456780000001
820000000100123456780000000000000000000011000123456789                         876543210000001
9000001000005000000010012345678000000000000000000001100                                       

OUTPUT;

        $multiEntryBatch = new Batch($this->validBatchHeaderRecord);
        $multiEntryBatch->addComponent(new EntryDetailRecord($this->validEntryDetailData, 1));
        $multiEntryBatch->addComponent(new EntryDetailRecord($this->validEntryDetailData, 2));
        $this->validEntryDetailData[EntryDetailRecord::TRANSACTION_CODE] = EntryDetailRecord::CHECKING_DEBIT_PAYMENT;
        $this->validEntryDetailData[EntryDetailRecord::AMOUNT] = '15.00';
        $multiEntryBatch->addComponent(new EntryDetailRecord($this->validEntryDetailData, 3));
        $multiEntryBatch->close();
        $multiEntryBatchOutput = <<<OUTPUT
101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL   180529180529   1876543210000001
62212345678901234567891011   0000001100               A VALID COMPANY NAME    0123456780000001
62212345678901234567891011   0000001100               A VALID COMPANY NAME    0123456780000002
62712345678901234567891011   0000001500               A VALID COMPANY NAME    0123456780000003
820000000300370370340000000015000000000022000123456789                         876543210000001
9000001000007000000030037037034000000001500000000002200                                       

OUTPUT;

        $multiBatchOutput = <<<OUTPUT
101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL   180529180529   1876543210000001
62212345678901234567891011   0000001100               A VALID COMPANY NAME    0123456780000001
820000000100123456780000000000000000000011000123456789                         876543210000001
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL   180529180529   1876543210000001
62212345678901234567891011   0000001100               A VALID COMPANY NAME    0123456780000001
62212345678901234567891011   0000001100               A VALID COMPANY NAME    0123456780000002
62712345678901234567891011   0000001500               A VALID COMPANY NAME    0123456780000003
820000000300370370340000000015000000000022000123456789                         876543210000001
9000002000010000000040049382712000000001500000000003300                                       

OUTPUT;

        return [
            [
                [],
                $noBatchOutput,
            ],
            [
                [$emptyBatch],
                $emptyBatchOutput,
            ],
            [
                [$singleEntryBatch],
                $singleEntryBatchOutput,
            ],
            [
                [$multiEntryBatch],
                $multiEntryBatchOutput,
            ],
            [
                [
                    $singleEntryBatch,
                    $multiEntryBatch],
                $multiBatchOutput,
            ],
        ];
    }

    public function testUnableToGetContentFromOpenFile()
    {
        $e = null;
        try {
            $paymentFile = new PaymentFile($this->validFileHeaderRecord);
            $paymentFile->toString();
        } catch (\BadMethodCallException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(\BadMethodCallException::class, get_class($e));
    }

    public function testUnableToAddBatchToClosedFile()
    {
        $e = null;
        try {
            $paymentFile = new PaymentFile($this->validFileHeaderRecord);
            $paymentFile->close();
            $paymentFile->addComponent(new Batch($this->validBatchHeaderRecord));
        } catch (\BadMethodCallException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(\BadMethodCallException::class, get_class($e));
    }

    /**
     * @param $input
     * @param $output
     * @dataProvider validInputsProvider
     */
    public function testEntryCountIsAccurate($input, $output)
    {
        $paymentFile = new PaymentFile($this->validFileHeaderRecord);
        $output = 0;
        /** @var Batch $batch */
        foreach ($input as $batch) {
            $output += $batch->getEntryAndAddendaCount();
            $paymentFile->addComponent($batch);
        }
        $paymentFile->close();

        $this->assertEquals($output, $paymentFile->getEntryAndAddendaCount());
    }

    /**
     * @param $input
     * @param $output
     * @dataProvider validInputsProvider
     */
    public function testEntryDollarSumIsAccurate($input, $output)
    {
        $paymentFile = new PaymentFile($this->validFileHeaderRecord);
        $debitSum  = '0';
        $creditSum = '0';
        /** @var Batch $batch */
        foreach ($input as $batch) {
            $paymentFile->addComponent($batch);
            $debitSum = bcadd($batch->getEntryDollarSum(EntryDetailRecord::DEBIT_TRANSACTION_CODES), $debitSum, 0);
            $creditSum = bcadd($batch->getEntryDollarSum(EntryDetailRecord::CREDIT_TRANSACTION_CODES), $creditSum, 0);
        }
        $paymentFile->close();

        $this->assertEquals($debitSum, $paymentFile->getEntryDollarSum(EntryDetailRecord::DEBIT_TRANSACTION_CODES));
        $this->assertEquals($creditSum, $paymentFile->getEntryDollarSum(EntryDetailRecord::CREDIT_TRANSACTION_CODES));
    }

    /**
     * @param $input
     * @param $output
     * @throws \RW\ACH\ValidationException
     * @dataProvider validInputsProvider
     */
    public function testValidInputGeneratesCorrectBatch($input, $output)
    {
        $paymentFile = new PaymentFile($this->validFileHeaderRecord);
        foreach ($input as $batch) {
            $paymentFile->addComponent($batch);
        }
        $paymentFile->close();

        $this->assertEquals($output, $paymentFile->toString());
    }
}
