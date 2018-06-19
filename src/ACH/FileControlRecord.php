<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-08
 * Time: 09:53
 */

namespace RW\ACH;


class FileControlRecord extends FileComponent
{
    /* FIXED VALUES */
    public const FIXED_RECORD_TYPE_CODE = '9';
    /* CALCULATED/RETRIEVED VALUE FIELD NAMES */
    private const BATCH_COUNT = 'BATCH_COUNT';
    private const BLOCK_COUNT = 'BLOCK_COUNT';

    /**
     * @param string $input
     * @return FileControlRecord
     * @throws ValidationException
     */
    public static function buildFromString($input): FileControlRecord
    {
        $buildData = self::getBuildDataFromInputString($input);

        return new FileControlRecord($buildData, false);
    }

    /**
     * @param string $batchCount
     * @param string $blockCount
     * @param string $entryAndAddendaCount
     * @param int    $transitSum
     * @param string $debitDollarSum
     * @param string $creditDollarSum
     * @return FileControlRecord
     * @throws ValidationException
     */
    public static function buildFromBatchData(
        $batchCount,
        $blockCount,
        $entryAndAddendaCount,
        $transitSum,
        $debitDollarSum,
        $creditDollarSum
    ) {
        // Use the ten low-order (right most) digits from the sum of the transit numbers
        $entryHash = $transitSum % 10000000000;

        return new FileControlRecord([
            self::BATCH_COUNT                      => $batchCount,
            self::BLOCK_COUNT                      => $blockCount,
            self::ENTRY_AND_ADDENDA_COUNT          => $entryAndAddendaCount,
            self::ENTRY_HASH                       => $entryHash,
            self::TOTAL_DEBIT_ENTRY_DOLLAR_AMOUNT  => $debitDollarSum,
            self::TOTAL_CREDIT_ENTRY_DOLLAR_AMOUNT => $creditDollarSum,
            self::RESERVED                         => null,
        ], true);
    }

    /**
     * Generate the field specifications for each field in the file component.
     * Format is an array of arrays as follows:
     *  $this->fieldSpecifications = [
     *      FIELD_NAME => [
     *          self::FIELD_INCLUSION => Mandatory, Required, or Optional (reserved for future use)
     *          self::VALIDATOR       => array: [
     *              Validation type (self::VALIDATOR_REGEX or self::VALIDATOR_DATE_TIME)
     *              Validation string (regular expression or date-time format)
     *          ]
     *          self::LENGTH          => Required if 'PADDING' is provided: Fixed width of the field
     *          self::POSITION_START  => Starting position within the component (reserved for future use)
     *          self::POSITION_END    => Ending position within the component (reserved for future use)
     *          self::PADDING         => Optional: self::ALPHANUMERIC_PADDING or self::NUMERIC_PADDING
     *          self::CONTENT         => The content to be output for this field
     *      ],
     *      ...
     *  ]
     *
     * @return array
     */
    protected static function getFieldSpecifications(): array
    {
        return [
            self::RECORD_TYPE_CODE                 => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1}$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 1,
                self::POSITION_END    => 1,
                self::CONTENT         => self::FIXED_RECORD_TYPE_CODE,
            ],
            self::BATCH_COUNT                      => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1,6}$/'],
                self::LENGTH          => 6,
                self::POSITION_START  => 2,
                self::POSITION_END    => 7,
                self::PADDING         => self::NUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::BLOCK_COUNT                      => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1,6}$/'],
                self::LENGTH          => 6,
                self::POSITION_START  => 8,
                self::POSITION_END    => 13,
                self::PADDING         => self::NUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::ENTRY_AND_ADDENDA_COUNT          => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1,8}$/'],
                self::LENGTH          => 8,
                self::POSITION_START  => 14,
                self::POSITION_END    => 21,
                self::PADDING         => self::NUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::ENTRY_HASH                       => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1,10}$/'],
                self::LENGTH          => 10,
                self::POSITION_START  => 22,
                self::POSITION_END    => 31,
                self::PADDING         => self::NUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::TOTAL_DEBIT_ENTRY_DOLLAR_AMOUNT  => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{0,12}$/'],
                self::LENGTH          => 12,
                self::POSITION_START  => 32,
                self::POSITION_END    => 43,
                self::PADDING         => self::NUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::TOTAL_CREDIT_ENTRY_DOLLAR_AMOUNT => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{0,12}$/'],
                self::LENGTH          => 12,
                self::POSITION_START  => 44,
                self::POSITION_END    => 55,
                self::PADDING         => self::NUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::RESERVED                         => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^ {0,39}$/'],
                self::LENGTH          => 39,
                self::POSITION_START  => 56,
                self::POSITION_END    => 94,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
        ];
    }
}
