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
use RW\ACH\FileHeader;
use RW\ACH\ValidationException;

class FileHeaderTest extends TestCase
{
    // region Valid Inputs
    private const VALID_IMMEDIATE_DESTINATION = ' 123456789';
    private const VALID_IMMEDIATE_ORIGIN      = '0123456789';
    private const VALID_DESTINATION           = 'abcdefg0123456789';
    private const VALID_ORIGIN_NAME           = 'abcdefg9876543210';
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
                // Missing Immediate Destination
                [
                    FileHeader::IMMEDIATE_ORIGIN => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION      => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME      => self::VALID_ORIGIN_NAME,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Immediate Origin
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Destination Name
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                \InvalidArgumentException::class,
            ],
            [
                // Missing Origin Name
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
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
                    FileHeader::IMMEDIATE_DESTINATION => ' A23456789',
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Short Immediate Destination With Blank
                [
                    FileHeader::IMMEDIATE_DESTINATION => ' 12345678',
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Short Immediate Destination Without Blank
                [
                    FileHeader::IMMEDIATE_DESTINATION => '12345678',
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Long Immediate Destination With Blank
                [
                    FileHeader::IMMEDIATE_DESTINATION => ' 0123456789',
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Long Immediate Destination Without Blank
                [
                    FileHeader::IMMEDIATE_DESTINATION => '0123456789',
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Empty Immediate Destination
                [
                    FileHeader::IMMEDIATE_DESTINATION => '',
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // NULL Immediate Destination
                [
                    FileHeader::IMMEDIATE_DESTINATION => null,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Invalid Immediate Origin
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => 'A123456789',
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Short Immediate Origin
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => '123456789',
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Long Immediate Origin
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => '01234567890',
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Empty Immediate Origin
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => '',
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // NULL Immediate Origin
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => null,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Empty Destination Name
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => '',
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // NULL Destination Name
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => null,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                ],
                ValidationException::class,
            ],
            [
                // Empty Origin Name
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => '',
                ],
                ValidationException::class,
            ],
            [
                // NULL Origin Name
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => null,
                ],
                ValidationException::class,
            ],
            [
                // Invalid File Id
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                    FileHeader::FILE_ID_MODIFIER      => 'a',
                ],
                ValidationException::class,
            ],
            [
                // Invalid Reference Code
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                    FileHeader::REFERENCE_CODE        => 'ZZYY990$',
                ],
                ValidationException::class,
            ],
            [
                // Long Reference Code
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                    FileHeader::REFERENCE_CODE        => 'ZZYY99001',
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
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                    FileHeader::FILE_DATE_OVERRIDE    => new DateTime('2018-05-29 15:19:23'),
                ],
                '101 12345678901234567891805291519A094101ABCDEFG0123456789      ABCDEFG9876543210              ',
            ],
            [
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                    FileHeader::FILE_DATE_OVERRIDE    => new DateTime('2018-05-29 15:19:23'),
                    FileHeader::FILE_ID_MODIFIER      => 'B',
                ],
                '101 12345678901234567891805291519B094101ABCDEFG0123456789      ABCDEFG9876543210              ',
            ],
            [
                [
                    FileHeader::IMMEDIATE_DESTINATION => self::VALID_IMMEDIATE_DESTINATION,
                    FileHeader::IMMEDIATE_ORIGIN      => self::VALID_IMMEDIATE_ORIGIN,
                    FileHeader::DESTINATION           => self::VALID_DESTINATION,
                    FileHeader::ORIGIN_NAME           => self::VALID_ORIGIN_NAME,
                    FileHeader::FILE_DATE_OVERRIDE    => new DateTime('2018-05-29 15:19:23'),
                    FileHeader::REFERENCE_CODE        => 'ZZYY9900',
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
            new FileHeader($input);
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
            new FileHeader($input);
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
    public function testValidInputGeneratesCorrectFileHeader($input, $output)
    {
        $this->assertEquals($output, (new FileHeader($input))->toString());
    }
}