<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-05-30
 * Time: 16:25
 */

namespace RW\Tests\ACH;


use PHPUnit\Framework\TestCase;
use RW\ACH\BatchHeader;
use RW\ACH\ValidationException;

class BatchHeaderTest extends TestCase
{
    // region Valid Inputs
    private const VALID_COMPANY_NAME              = 'A Real Company';
    private const VALID_DISCRETIONARY_DATA        = 'A Real Description';
    private const VALID_COMPANY_IDENTIFICATION    = '0123456789';
    private const VALID_STANDARD_ENTRY_CLASS_CODE = 'PPD';
    private const VALID_COMPANY_ENTRY_DESCRIPTION = 'Payroll';
    private const VALID_ORIGINATING_DFI_ID        = '87654321';
    private const VALID_BATCH_NUMBER              = '1';
    // endregion

    // region Data Providers
    public function missingRequiredFieldInputsProvider()
    {
        return [
            [
                // Null Input
                null,
                \InvalidArgumentException::class,
            ],
            [
                // Empty Input
                [],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Service Class Code
                [
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Company Name
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Company Identification
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Standard Entry Class Code
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Company Entry Description
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Originating DFI ID
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Batch Number
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                ],
                \InvalidArgumentException::class,
            ],
        ];
    }

    public function invalidInputsProvider()
    {
        return [
            [
                // Invalid Service Class Code
                [
                    BatchHeader::SERVICE_CLASS_CODE        => '201',
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Service Class Code
                [
                    BatchHeader::SERVICE_CLASS_CODE        => '',
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Service Class Code
                [
                    BatchHeader::SERVICE_CLASS_CODE        => null,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Company Name
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => 'B*d Name',
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Company Name
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => 'Long Company Name',
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Company Name
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => '',
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Company Name
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => null,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Company Identification
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => 'A234567890',
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Company Identification
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => '01234567890',
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Short Company Identification
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => '123456789',
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Company Identification
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => '',
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Company Identification
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => null,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Standard Entry Class Code
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => 'aB3',
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Short Standard Entry Class Code
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => 'AB',
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Standard Entry Class Code
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => 'ABCD',
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => '',
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Standard Entry Class Code
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => null,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Company Entry Description
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => 'Inv*lid',
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Company Entry Description
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => 'Long Descri',
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Company Entry Description
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => '',
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Company Entry Description
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => null,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Originating DFI ID
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => 'A2345678',
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Short Originating DFI ID
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => '1234567',
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Originating DFI ID
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => '123456789',
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Originating DFI ID
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => '',
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Originating DFI ID
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => null,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Batch Number
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => 'A234567',
                ],
                ValidationException::class,
            ],
            [
                // Long Batch Number
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => '10000000',
                ],
                ValidationException::class,
            ],
            [
                // Empty Batch Number
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => '',
                ],
                ValidationException::class,
            ],
            [
                // Null Batch Number
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => null,
                ],
                ValidationException::class,
            ],
        ];
    }

    public function validInputsProvider()
    {
        return [
            [
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::MIXED_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeader::DISCRETIONARY_DATA        => self::VALID_DISCRETIONARY_DATA,
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                    BatchHeader::ENTRY_DATE_OVERRIDE       => new \DateTime('2018-05-29 01:02:03'),
                ],
                '5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL   180529180529   1876543210000001',
            ],
            [
                [
                    BatchHeader::SERVICE_CLASS_CODE        => BatchHeader::CREDIT_SERVICE_CLASS,
                    BatchHeader::COMPANY_NAME              => self::VALID_COMPANY_NAME . '2',
                    BatchHeader::DISCRETIONARY_DATA        => self::VALID_DISCRETIONARY_DATA . '2',
                    BatchHeader::COMPANY_IDENTIFICATION    => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeader::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeader::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeader::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeader::BATCH_NUMBER              => '2',
                    BatchHeader::ENTRY_DATE_OVERRIDE       => new \DateTime('2018-05-29 01:02:03'),
                    BatchHeader::COMPANY_DESCRIPTIVE_DATE  => new \DateTime('2018-05-28 02:03:04'),
                ],
                '5220A REAL COMPANY2 A REAL DESCRIPTION2 0123456789PPDPAYROLL   180528180529   1876543210000002',
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
            new BatchHeader($input);
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
            new BatchHeader($input);
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
    public function testValidInputGeneratesCorrectBatchHeader($input, $output)
    {
        $this->assertEquals($output, (new BatchHeader($input))->toString());
    }
}