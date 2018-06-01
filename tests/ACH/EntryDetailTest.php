<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-01
 * Time: 13:14
 */

namespace RW\Tests\ACH;


use PHPUnit\Framework\TestCase;
use RW\ACH\EntryDetail;
use RW\ACH\ValidationException;

class EntryDetailTest extends TestCase
{
    private const VALID_TRANSACTION_CODE   = '22';
    private const VALID_TRANSIT_ABA_NUMBER = '123456789';
    private const VALID_DFI_ACCOUNT_NUMBER = '01234-567-891011';
    private const VALID_AMOUNT             = '11.00';
    private const VALID_INDIVIDUAL_NAME    = 'A Valid Company Name';
    private const VALID_ADDENDA_INDICATOR  = '1';
    private const VALID_TRACE_NUMBER       = '12345678';
    private const VALID_ID_NUMBER          = 'AF34B52';
    private const VALID_DRAFT_INDICATOR    = '1*';

    // region Data Providers
    public function missingRequiredFieldInputsProvider()
    {
        return [
            [
                [
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                ],
                \InvalidArgumentException::class,
            ],
        ];
    }

    public function invalidInputsProvider()
    {
        return [
            [
                // Invalid Transaction Code
                [
                    EntryDetail::TRANSACTION_CODE   => '21',
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Transaction Code
                [
                    EntryDetail::TRANSACTION_CODE   => '',
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Transaction Code
                [
                    EntryDetail::TRANSACTION_CODE   => null,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Transit ABA Number
                [
                    EntryDetail::TRANSACTION_CODE   => 'A2345678',
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Transit ABA Number
                [
                    EntryDetail::TRANSACTION_CODE   => '123456789',
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Short Transit ABA Number
                [
                    EntryDetail::TRANSACTION_CODE   => '1234567',
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Transit ABA Number
                [
                    EntryDetail::TRANSACTION_CODE   => '',
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Transit ABA Number
                [
                    EntryDetail::TRANSACTION_CODE   => null,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid DFI Account Number
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => '1234.56789.1234',
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long DFI Account Number
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => '012345678901234567',
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty DFI Account Number
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => '',
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null DFI Account Number
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => null,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Amount
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => 'A2345.67',
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Amount
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => '123456789',
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Another Long Amount
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => '123456789.12',
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Amount
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => '',
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Amount
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => null,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Id Number
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::ID_NUMBER          => 'ABC123*&^',
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Id Number
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::ID_NUMBER          => '1234567890ABCDEF',
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Individual Name
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => 'A b*d name',
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Individual Name
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => 'This name is toooo long',
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Individual Name
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => '',
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Individual Name
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => null,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Bank Draft Indicator
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::DRAFT_INDICATOR    => '11',
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Bank Draft Indicator
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::DRAFT_INDICATOR    => '   ',
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Short Bank Draft Indicator
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::DRAFT_INDICATOR    => '1',
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Addenda Indicator
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::ADDENDA_INDICATOR  => '2',
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Addenda Indicator
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::ADDENDA_INDICATOR  => '01',
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Trace Number
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::ADDENDA_INDICATOR  => self::VALID_ADDENDA_INDICATOR,
                    EntryDetail::TRACE_NUMBER       => 'A2345678',
                ],
                ValidationException::class,
            ],
            [
                // Long Trace Number
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::ADDENDA_INDICATOR  => self::VALID_ADDENDA_INDICATOR,
                    EntryDetail::TRACE_NUMBER       => '123456789',
                ],
                ValidationException::class,
            ],
            [
                // Short Trace Number
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::ADDENDA_INDICATOR  => self::VALID_ADDENDA_INDICATOR,
                    EntryDetail::TRACE_NUMBER       => '1234567',
                ],
                ValidationException::class,
            ],
            [
                // Empty Trace Number
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::ADDENDA_INDICATOR  => self::VALID_ADDENDA_INDICATOR,
                    EntryDetail::TRACE_NUMBER       => '',
                ],
                ValidationException::class,
            ],
            [
                // Null Trace Number
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::ADDENDA_INDICATOR  => self::VALID_ADDENDA_INDICATOR,
                    EntryDetail::TRACE_NUMBER       => null,
                ],
                ValidationException::class,
            ],
        ];
    }

    public function validInputsProvider()
    {
        return [
            [
                // Standard Entries
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                '62212345678901234-567-891011 0000001100               A VALID COMPANY NAME    0123456780000001',
            ],
            [
                // Custom Id Number
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::ID_NUMBER          => self::VALID_ID_NUMBER,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                '62212345678901234-567-891011 0000001100AF34B52        A VALID COMPANY NAME    0123456780000001',
            ],
            [
                // Custom Draft Indicator
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::DRAFT_INDICATOR    => self::VALID_DRAFT_INDICATOR,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                '62212345678901234-567-891011 0000001100               A VALID COMPANY NAME  1*0123456780000001',
            ],
            [
                // Custom Addenda Indicator
                [
                    EntryDetail::TRANSACTION_CODE   => self::VALID_TRANSACTION_CODE,
                    EntryDetail::TRANSIT_ABA_NUMBER => self::VALID_TRANSIT_ABA_NUMBER,
                    EntryDetail::DFI_ACCOUNT_NUMBER => self::VALID_DFI_ACCOUNT_NUMBER,
                    EntryDetail::AMOUNT             => self::VALID_AMOUNT,
                    EntryDetail::INDIVIDUAL_NAME    => self::VALID_INDIVIDUAL_NAME,
                    EntryDetail::ADDENDA_INDICATOR  => self::VALID_ADDENDA_INDICATOR,
                    EntryDetail::TRACE_NUMBER       => self::VALID_TRACE_NUMBER,
                ],
                '62212345678901234-567-891011 0000001100               A VALID COMPANY NAME    1123456780000001',
            ],
        ];
    }
    // endregion

    /**
     * @param $input
     * @param $output
     * @dataProvider  missingRequiredFieldInputsProvider
     */
    public function testMissingRequiredFieldThrowsInvalidArgumentException($input, $output)
    {
        $e = null;
        try {
            new EntryDetail($input, 1);
        } catch (\Exception $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals($output, get_class($e));
    }

    /**
     * @param $input
     * @param $output
     * @dataProvider invalidInputsProvider
     */
    public function testInvalidInputThrowsValidationException($input, $output)
    {
        $e = null;
        try {
            new EntryDetail($input, 1);
        } catch (\Exception $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals($output, get_class($e));
    }

    /**
     * @param $input
     * @param $output
     * @throws \RW\ACH\ValidationException
     * @dataProvider validInputsProvider
     */
    public function testValidInputGeneratesCorrectEntryDetail($input, $output)
    {
        $this->assertEquals($output, (new EntryDetail($input, 1))->toString());
    }
}