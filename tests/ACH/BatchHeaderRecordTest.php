<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-05-30
 * Time: 16:25
 */

namespace RW\Tests\ACH;


use PHPUnit\Framework\TestCase;
use RW\ACH\BatchHeaderRecord;
use RW\ACH\ValidationException;

class BatchHeaderRecordTest extends TestCase
{
    private const VALID_COMPANY_NAME              = 'A Real Company';
    private const VALID_DISCRETIONARY_DATA        = 'A Real Description';
    private const VALID_COMPANY_IDENTIFICATION    = '0123456789';
    private const VALID_STANDARD_ENTRY_CLASS_CODE = 'PPD';
    private const VALID_COMPANY_ENTRY_DESCRIPTION = 'Payroll';
    private const VALID_ORIGINATING_DFI_ID        = '87654321';
    private const VALID_BATCH_NUMBER              = '1';

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
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Company Name
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Company Identification
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Standard Entry Class Code
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Company Entry Description
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Originating DFI ID
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Batch Number
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
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
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => '201',
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Service Class Code
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => '',
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Service Class Code
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => null,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Company Name
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => 'B*d Name',
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Company Name
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => 'Long Company Name',
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Company Name
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => '',
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Company Name
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => null,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Company Identification
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => 'A234567890',
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Company Identification
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => '01234567890',
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Short Company Identification
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => '123456789',
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Company Identification
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => '',
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Company Identification
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => null,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Standard Entry Class Code
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => 'aB3',
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Short Standard Entry Class Code
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => 'AB',
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Standard Entry Class Code
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => 'ABCD',
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => '',
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Standard Entry Class Code
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => null,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Company Entry Description
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => 'Inv*lid',
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Company Entry Description
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => 'Long Descri',
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Company Entry Description
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => '',
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Company Entry Description
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => null,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Originating DFI ID
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => 'A2345678',
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Short Originating DFI ID
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => '1234567',
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Long Originating DFI ID
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => '123456789',
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Empty Originating DFI ID
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => '',
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Null Originating DFI ID
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => null,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Batch Number
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => 'A234567',
                ],
                ValidationException::class,
            ],
            [
                // Long Batch Number
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => '10000000',
                ],
                ValidationException::class,
            ],
            [
                // Empty Batch Number
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => '',
                ],
                ValidationException::class,
            ],
            [
                // Null Batch Number
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => null,
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
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::MIXED_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME,
                    BatchHeaderRecord::DISCRETIONARY_DATA        => self::VALID_DISCRETIONARY_DATA,
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => self::VALID_BATCH_NUMBER,
                    BatchHeaderRecord::EFFECTIVE_ENTRY_DATE      => new \DateTime('2018-05-29 01:02:03'),
                ],
                '5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL         180529   1876543210000001',
            ],
            [
                [
                    BatchHeaderRecord::SERVICE_CLASS_CODE        => BatchHeaderRecord::CREDIT_SERVICE_CLASS,
                    BatchHeaderRecord::COMPANY_NAME              => self::VALID_COMPANY_NAME . '2',
                    BatchHeaderRecord::DISCRETIONARY_DATA        => self::VALID_DISCRETIONARY_DATA . '2',
                    BatchHeaderRecord::COMPANY_ID                => self::VALID_COMPANY_IDENTIFICATION,
                    BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => self::VALID_STANDARD_ENTRY_CLASS_CODE,
                    BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => self::VALID_COMPANY_ENTRY_DESCRIPTION,
                    BatchHeaderRecord::ORIGINATING_DFI_ID        => self::VALID_ORIGINATING_DFI_ID,
                    BatchHeaderRecord::BATCH_NUMBER              => '2',
                    BatchHeaderRecord::EFFECTIVE_ENTRY_DATE      => new \DateTime('2018-05-29 01:02:03'),
                    BatchHeaderRecord::COMPANY_DESCRIPTIVE_DATE  => new \DateTime('2018-05-28 02:03:04'),
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
            new BatchHeaderRecord($input);
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
            new BatchHeaderRecord($input);
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
    public function testValidInputGeneratesCorrectBatchHeaderRecord($input, $output)
    {
        $this->assertEquals($output, (new BatchHeaderRecord($input))->toString());
    }

    /**
     * @throws ValidationException
     */
    public function testValidStringInputGeneratesValidBatchHeaderRecord()
    {
        $input = '5200A REAL COMPANY  A REAL DESCRIPTION  0123456789PPDPAYROLL   180529180529   1876543210000001';
        $fhr   = BatchHeaderRecord::buildFromString($input);
        $this->assertEquals($input, $fhr->toString());
    }
}
