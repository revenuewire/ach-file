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
use RW\ACH\File;
use RW\ACH\FileHeaderRecord;

class FileTest extends TestCase
{
    /** @var FileHeaderRecord */
    private $validFileHeaderRecord;
    /** @var BatchHeaderRecord */
    private $validPPDBatchHeaderRecord;
    /** @var BatchHeaderRecord */
    private $validCORBatchHeaderRecord;
    /** @var array */
    private $validEntryDetailData;

    /**
     * @throws \RW\ACH\ValidationException
     */
    public function setUp()
    {
        $this->validFileHeaderRecord     = new FileHeaderRecord([
            FileHeaderRecord::IMMEDIATE_DESTINATION => ' 123456789',
            FileHeaderRecord::IMMEDIATE_ORIGIN      => '0123456789',
            FileHeaderRecord::DESTINATION_NAME      => 'abcdefg0123456789',
            FileHeaderRecord::ORIGIN_NAME           => 'abcdefg9876543210',
            FileHeaderRecord::FILE_DATE             => new \DateTime('2018-05-29 15:19:45'),
        ]);
        $this->validPPDBatchHeaderRecord = new BatchHeaderRecord([
            BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
            BatchHeaderRecord::COMPANY_NAME              => 'A Real Company',
            BatchHeaderRecord::DISCRETIONARY_DATA        => 'A Real Description',
            BatchHeaderRecord::COMPANY_ID                => '0123456789',
            BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => BatchHeaderRecord::SEC_PPD,
            BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => 'Payroll',
            BatchHeaderRecord::ORIGINATING_DFI_ID        => '87654321',
            BatchHeaderRecord::BATCH_NUMBER              => '1',
            BatchHeaderRecord::EFFECTIVE_ENTRY_DATE      => new \DateTime('2018-05-29 15:20:03'),
        ]);
        $this->validCORBatchHeaderRecord = new BatchHeaderRecord([
            BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
            BatchHeaderRecord::COMPANY_NAME              => 'A Real Company',
            BatchHeaderRecord::DISCRETIONARY_DATA        => 'A Real Description',
            BatchHeaderRecord::COMPANY_ID                => '0123456789',
            BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => BatchHeaderRecord::SEC_COR,
            BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => 'Payroll',
            BatchHeaderRecord::ORIGINATING_DFI_ID        => '87654321',
            BatchHeaderRecord::BATCH_NUMBER              => '1',
            BatchHeaderRecord::EFFECTIVE_ENTRY_DATE      => new \DateTime('2018-05-29 15:20:03'),
        ]);
        $this->validEntryDetailData      = [
            EntryDetailRecord::TRANSACTION_CODE   => EntryDetailRecord::CHECKING_CREDIT_DEPOSIT,
            EntryDetailRecord::TRANSIT_ABA_NUMBER => '113000023',
            EntryDetailRecord::DFI_ACCOUNT_NUMBER => '01234567891011',
            EntryDetailRecord::AMOUNT             => '11.00',
            EntryDetailRecord::INDIVIDUAL_NAME    => 'A Valid Company Name',
            EntryDetailRecord::TRACE_NUMBER       => '87654321',
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
9000000000001000000000000000000000000000000000000000000                                       

OUTPUT;

        $emptyBatch = new Batch($this->validPPDBatchHeaderRecord);
        $emptyBatch->close();
        $emptyBatchOutput = <<<OUTPUT
101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL         180529   1876543210000001
820000000000000000000000000000000000000000000123456789                         876543210000001
9000001000001000000000000000000000000000000000000000000                                       

OUTPUT;

        $singleEntryBatch = new Batch($this->validPPDBatchHeaderRecord);
        $singleEntryBatch->addEntryDetailRecord(new EntryDetailRecord($this->validEntryDetailData, 1));
        $singleEntryBatch->close();
        $singleEntryBatchOutput = <<<OUTPUT
101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL         180529   1876543210000001
62211300002301234567891011   0000001100               A VALID COMPANY NAME    0876543210000001
820000000100113000020000000000000000000011000123456789                         876543210000001
9000001000001000000010011300002000000000000000000001100                                       

OUTPUT;

        $multiEntryBatch = new Batch($this->validPPDBatchHeaderRecord);
        $multiEntryBatch->addEntryDetailRecord(new EntryDetailRecord($this->validEntryDetailData, 1));
        $multiEntryBatch->addEntryDetailRecord(new EntryDetailRecord($this->validEntryDetailData, 2));
        $this->validEntryDetailData[EntryDetailRecord::TRANSACTION_CODE] = EntryDetailRecord::CHECKING_DEBIT_PAYMENT;
        $this->validEntryDetailData[EntryDetailRecord::AMOUNT] = '15.00';
        $multiEntryBatch->addEntryDetailRecord(new EntryDetailRecord($this->validEntryDetailData, 3));
        $this->validEntryDetailData[EntryDetailRecord::AMOUNT] = '10.00';
        $multiEntryBatch->addEntryDetailRecord(new EntryDetailRecord($this->validEntryDetailData, 4));
        $multiEntryBatch->close();
        $multiEntryBatchOutput = <<<OUTPUT
101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL         180529   1876543210000001
62211300002301234567891011   0000001100               A VALID COMPANY NAME    0876543210000001
62211300002301234567891011   0000001100               A VALID COMPANY NAME    0876543210000002
62711300002301234567891011   0000001500               A VALID COMPANY NAME    0876543210000003
62711300002301234567891011   0000001000               A VALID COMPANY NAME    0876543210000004
820000000400452000080000000025000000000022000123456789                         876543210000001
9000001000001000000040045200008000000002500000000002200                                       

OUTPUT;

        $multiBatchOutput = <<<OUTPUT
101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL         180529   1876543210000001
62211300002301234567891011   0000001100               A VALID COMPANY NAME    0876543210000001
820000000100113000020000000000000000000011000123456789                         876543210000001
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL         180529   1876543210000001
62211300002301234567891011   0000001100               A VALID COMPANY NAME    0876543210000001
62211300002301234567891011   0000001100               A VALID COMPANY NAME    0876543210000002
62711300002301234567891011   0000001500               A VALID COMPANY NAME    0876543210000003
62711300002301234567891011   0000001000               A VALID COMPANY NAME    0876543210000004
820000000400452000080000000025000000000022000123456789                         876543210000001
9000002000002000000050056500010000000002500000000003300                                       

OUTPUT;

        $this->tearDown();

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
                    $multiEntryBatch,
                ],
                $multiBatchOutput,
            ],
        ];
    }

    /**
     * @return array
     * @throws \RW\ACH\ValidationException
     */
    public function validResourceInputsProvider()
    {
        $singleEntryPaymentFileOutput = <<<OUTPUT
101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL   180529180529   1876543210000001
62212345678901234567891011   0000001100               A VALID COMPANY NAME    0876543210000001
820000000100123456780000000000000000000011000123456789                         876543210000001
9000001000001000000010012345678000000000000000000001100                                       

OUTPUT;

        $multiBatchPaymentFileOutput = <<<OUTPUT
101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL   180529180529   1876543210000001
62212345678901234567891011   0000001100               A VALID COMPANY NAME    0876543210000001
820000000100123456780000000000000000000011000123456789                         876543210000001
5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL   180529180529   1876543210000001
62212345678901234567891011   0000001100               A VALID COMPANY NAME    0876543210000001
62212345678901234567891011   0000001100               A VALID COMPANY NAME    0876543210000002
62712345678901234567891011   0000001500               A VALID COMPANY NAME    0876543210000003
820000000300370370340000000015000000000022000123456789                         876543210000001
9000002000001000000040049382712000000001500000000003300                                       

OUTPUT;

        $singleCorrectedEntryReturnFileOutput = <<<OUTPUT
1010123456789 1234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200FUTUREPAY INC   SCHEDULED PAYMENTS  0123456789CORPAYROLL   1805291805290001111000020000001
6260514051881010429692       00000000003604713        OSLER PORTILLO          1111000024637403
799C02111000020000020      05140518051403164                                   111000024637403
820000000200051405180000000000000000000000001454746175                         111000020000001
9000002000001000000040010460678000000010000000000000000                                       

OUTPUT;

        $singleReturnedEntryReturnFileOutput = <<<OUTPUT
1010123456789 1234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200FUTUREPAY INC   SCHEDULED PAYMENTS  0123456789PPDPAYROLL   1805291805290001111000020000001
6260514051881010429692       00000000003604713        OSLER PORTILLO          1111000024637403
799R01111000020000020      05140518051403164                                   111000024637403
820000000200051405180000000000000000000000001454746175                         111000020000001
9000002000001000000040010460678000000010000000000000000                                       

OUTPUT;

        $multiEntryTypeReturnFileOutput = <<<OUTPUT
1010123456789 1234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              
5200FUTUREPAY INC   SCHEDULED PAYMENTS  0123456789PPDPAYROLL   1805291805290001111000020000001
6260514051881010429692       00000000003604713        OSLER PORTILLO          1111000024637403
799R01111000020000020      05140518051403164                                   111000024637403
820000000200051405180000000000000000000000001454746175                         111000020000001
5200FUTUREPAY INC   SCHEDULED PAYMENTS  0123456789CORPAYROLL   1805291805290001111000020000001
6260514051881010429692       00000000003604713        OSLER PORTILLO          1111000024637403
799C02111000020000020      05140518051403164                                   111000024637403
820000000200051405180000000000000000000000001454746175                         111000020000001
9000002000001000000040010460678000000010000000000000000                                       

OUTPUT;

        return [
            [$singleEntryPaymentFileOutput, null],
            [$multiBatchPaymentFileOutput, null],
            [$singleCorrectedEntryReturnFileOutput, null],
            [$singleReturnedEntryReturnFileOutput, null],
            [$multiEntryTypeReturnFileOutput, null],
        ];
    }

    public function validStackedInputProvider()
    {
        $stackedFile = <<<PLAINTEXT
101 09100001918207977061812131144B094101WELLS FARGO            PAYMOTION              10979   
5200PAYMOTION       TRANSFER 10979      WFMSPAYMO1PPDPAYMENTS  181213181213   1091000010000001
622011302742459882897142539  00000089513549           COMPANY 5               0091000010000001
82000000010001130274000000000000000000008951WFMSPAYMO1                         091000010000001
9000001000001000000010001130274000000000000000000008951                                       
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
101 09100001918207977061812130244A094101WELLS FARGO            PAYMOTION              10959   
5200PAYMOTION       TRANSFER 10959      WFMSPAYMO1PPDPAYMENTS  181213181213   1091000010000001
6221210002484088258165       00004093823529           PAYMOTION               0REJ060300000001
82000000010012100024000000000000000000409382WFMSPAYMO1                         091000010000001
9000001000001000000010012100024000000000000000000409382                                       
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
101 09100001918207977061812131144C094101WELLS FARGO            PAYMOTION              10989   
5200PAYMOTION       TRANSFER 10989      2820797706PPDPAYMENTS  181213181213   1091000010000001
622011302742459882897142539  00000257313559           COMPANY 5               0091000010000001
820000000100011302740000000000000000000257312820797706                         091000010000001
9000001000001000000010001130274000000000000000000025731                                       
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999
9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999

PLAINTEXT;
        $fileOne = <<<PLAINTEXT
101 09100001918207977061812131144B094101WELLS FARGO            PAYMOTION              10979   
5200PAYMOTION       TRANSFER 10979      WFMSPAYMO1PPDPAYMENTS  181213181213   1091000010000001
622011302742459882897142539  00000089513549           COMPANY 5               0091000010000001
82000000010001130274000000000000000000008951WFMSPAYMO1                         091000010000001
9000001000001000000010001130274000000000000000000008951                                       

PLAINTEXT;
        $fileTwo = <<<PLAINTEXT
101 09100001918207977061812130244A094101WELLS FARGO            PAYMOTION              10959   
5200PAYMOTION       TRANSFER 10959      WFMSPAYMO1PPDPAYMENTS  181213181213   1091000010000001
6221210002484088258165       00004093823529           PAYMOTION               0REJ060300000001
82000000010012100024000000000000000000409382WFMSPAYMO1                         091000010000001
9000001000001000000010012100024000000000000000000409382                                       

PLAINTEXT;
        $fileThree = <<<PLAINTEXT
101 09100001918207977061812131144C094101WELLS FARGO            PAYMOTION              10989   
5200PAYMOTION       TRANSFER 10989      2820797706PPDPAYMENTS  181213181213   1091000010000001
622011302742459882897142539  00000257313559           COMPANY 5               0091000010000001
820000000100011302740000000000000000000257312820797706                         091000010000001
9000001000001000000010001130274000000000000000000025731                                       

PLAINTEXT;

        return [
            [$stackedFile, [$fileOne, $fileTwo, $fileThree]],
        ];
    }

    public function testUnableToGetContentFromOpenFile()
    {
        $e = null;
        try {
            $paymentFile = new File($this->validFileHeaderRecord);
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
            $paymentFile = new File($this->validFileHeaderRecord);
            $paymentFile->close();
            $paymentFile->addBatch(new Batch($this->validPPDBatchHeaderRecord));
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
        $paymentFile = new File($this->validFileHeaderRecord);
        $output      = '0';
        /** @var Batch $batch */
        foreach ($input as $batch) {
            $output += $batch->getEntryAndAddendaCount();
            $paymentFile->addBatch($batch);
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
        $paymentFile = new File($this->validFileHeaderRecord);
        $debitSum    = '0';
        $creditSum   = '0';
        /** @var Batch $batch */
        foreach ($input as $batch) {
            $paymentFile->addBatch($batch);
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
     * @dataProvider validInputsProvider
     */
    public function testValidInputGeneratesCorrectFile($input, $output)
    {
        $paymentFile = new File($this->validFileHeaderRecord);
        foreach ($input as $batch) {
            $paymentFile->addBatch($batch);
        }
        $paymentFile->close();

        $this->assertEquals($output, $paymentFile->toString());
    }

    /**
     * @param $input
     * @param $output
     * @throws \RW\ACH\ValidationException
     * @dataProvider validResourceInputsProvider
     */
    public function testValidFileResourceInputGeneratesValidOutput($input, $output)
    {
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $input);
        rewind($handle);

        $paymentFile = File::buildFromResource($handle);
        $this->assertEquals($input, $paymentFile->toString());
    }

    /**
     * @dataProvider validStackedInputProvider
     * @param $input
     * @param $output
     */
    public function testValidStackedFileResourceInputGeneratesValidOutput($input, $output)
    {
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $input);
        rewind($handle);

        $rejectFiles = File::buildFromStackedResource($handle);

        $this->assertTrue(is_array($rejectFiles), 'File::buildFromStackedResource() did not return an array.');
        $this->assertTrue(count($rejectFiles) === 3, 'File::buildFromStackedResource() did not return a populated array.');
        /**
         * @var int $key
         * @var File $rejectFile
         */
        foreach ($rejectFiles as $key => $rejectFile) {
            $this->assertInstanceOf(File::class, $rejectFile);
            $this->assertEquals($output[$key], $rejectFile->toString());
        }
    }
}
