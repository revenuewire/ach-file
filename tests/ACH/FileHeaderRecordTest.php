<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-05-28
 * Time: 16:42
 */

namespace RW\Tests\ACH;


use DateTime;
use PHPUnit\Framework\TestCase;
use RW\ACH\FileHeaderRecord;
use RW\ACH\ValidationException;

class FileHeaderRecordTest extends TestCase
{
    private const VALID_IMMEDIATE_DESTINATION = ' 123456789';
    private const VALID_IMMEDIATE_ORIGIN      = '0123456789';
    private const VALID_DESTINATION           = 'abcdefg0123456789';
    private const VALID_ORIGIN_NAME           = 'abcdefg9876543210';

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
                // Missing Immediate Destination
                [
                    FileHeaderRecord::IMMEDIATE_ORIGIN => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME      => self::VALID_ORIGIN_NAME,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Immediate Origin
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Destination Name
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Origin Name
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                ],
                \InvalidArgumentException::class,
            ],
        ];
    }

    public function invalidInputsProvider()
    {
        return [
            [
                // Invalid Immediate Destination
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => ' A23456789',
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Short Immediate Destination With Blank
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => ' 12345678',
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Short Immediate Destination Without Blank
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => '12345678',
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Long Immediate Destination With Blank
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => ' 0123456789',
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Long Immediate Destination Without Blank
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => '0123456789',
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Empty Immediate Destination
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => '',
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // NULL Immediate Destination
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => null,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Immediate Origin
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => 'A123456789',
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Short Immediate Origin
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => '123456789',
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Long Immediate Origin
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => '01234567890',
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Empty Immediate Origin
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => '',
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // NULL Immediate Origin
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => null,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Empty Destination Name
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => '',
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // NULL Destination Name
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => null,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Empty Origin Name
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => '',
                ],
                ValidationException::class,
            ],
            [
                // NULL Origin Name
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => null,
                ],
                ValidationException::class,
            ],
            [
                // Invalid File Id
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                    FileHeaderRecord::FILE_ID_MODIFIER      => 'a',
                ],
                ValidationException::class,
            ],
            [
                // Invalid Reference Code
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                    FileHeaderRecord::REFERENCE_CODE        => 'ZZYY990$',
                ],
                ValidationException::class,
            ],
            [
                // Long Reference Code
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                    FileHeaderRecord::REFERENCE_CODE        => 'ZZYY99001',
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
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                    FileHeaderRecord::FILE_DATE             => new DateTime('2018-05-29 15:19:23'),
                ],
                '101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              ',
            ],
            [
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                    FileHeaderRecord::FILE_DATE             => new DateTime('2018-05-29 15:19:23'),
                    FileHeaderRecord::FILE_ID_MODIFIER      => 'B',
                ],
                '101 12345678901234567891805291519B094101ABCDEFG0123456789      ABCDEFG9876543210              ',
            ],
            [
                [
                    FileHeaderRecord::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeaderRecord::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeaderRecord::DESTINATION_NAME      => self::VALID_DESTINATION,
                    FileHeaderRecord::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                    FileHeaderRecord::FILE_DATE             => new DateTime('2018-05-29 15:19:23'),
                    FileHeaderRecord::REFERENCE_CODE        => 'ZZYY9900',
                ],
                '101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210      ZZYY9900',
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
            new FileHeaderRecord($input);
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
            new FileHeaderRecord($input);
        } catch (\Exception $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals($output, get_class($e));
    }

    /**
     * @param $input
     * @param $output
     * @throws ValidationException
     * @dataProvider validInputsProvider
     */
    public function testValidInputGeneratesCorrectFileHeaderRecord($input, $output)
    {
        $this->assertEquals($output, (new FileHeaderRecord($input))->toString());
    }

    /**
     * @throws ValidationException
     */
    public function testValidStringInputGeneratesValidFileHeaderRecord()
    {
        $input = '101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              ';
        $fhr   = FileHeaderRecord::buildFromString($input);
        $this->assertEquals($input, $fhr->toString());
    }
}
