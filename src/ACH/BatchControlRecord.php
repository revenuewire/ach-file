<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-06
 * Time: 16:17
 */

namespace RW\ACH;


/**
 * Class BatchControlRecord
 *
 * @package RW\ACH
 */
class BatchControlRecord extends FileComponent
{
    /* FIXED VALUES */
    public const FIXED_RECORD_TYPE_CODE = '8';

    /* FIXED VALUE FIELD NAMES */
    private const MESSAGE_AUTHENTICATION_CODE      = 'MESSAGE_AUTHENTICATION_CODE';

    /* CALCULATED/RETRIEVED VALUE FIELD NAMES */
    private const COMPANY_ID                       = 'COMPANY_ID';
    private const ORIGINATING_DFI_ID               = 'ORIGINATING_DFI_ID';
    private const BATCH_NUMBER                     = 'BATCH_NUMBER';

    /**
     * BatchControlRecord builder using the Batch Header record to ensure cross-referenced data is accurate. This is
     * the recommended method of creating a Batch Control record when using generated data.
     *
     * @param BatchHeaderRecord $batchHeaderRecord
     * @param string            $entryAndAddendaCount
     * @param int               $transitSum
     * @param string            $debitDollarSum
     * @param string            $creditDollarSum
     * @return BatchControlRecord
     * @throws ValidationException
     */
    public static function buildFromBatchData(
        $batchHeaderRecord,
        $entryAndAddendaCount,
        $transitSum,
        $debitDollarSum,
        $creditDollarSum
    ): BatchControlRecord {
        // Use the ten low-order (right most) digits from the sum of the transit numbers
        $entryHash                  = $transitSum % 10000000000;

        $serviceClassCodeField      = $batchHeaderRecord->fieldSpecifications[BatchHeaderRecord::SERVICE_CLASS_CODE];
        $companyIdentificationField = $batchHeaderRecord->fieldSpecifications[BatchHeaderRecord::COMPANY_ID];
        $originatingDfiIdField      = $batchHeaderRecord->fieldSpecifications[BatchHeaderRecord::ORIGINATING_DFI_ID];
        $batchNumberField           = $batchHeaderRecord->fieldSpecifications[BatchHeaderRecord::BATCH_NUMBER];

        return new BatchControlRecord([
            self::SERVICE_CLASS_CODE               => $serviceClassCodeField[self::CONTENT],
            self::ENTRY_AND_ADDENDA_COUNT          => $entryAndAddendaCount,
            self::ENTRY_HASH                       => "$entryHash",
            self::TOTAL_DEBIT_ENTRY_DOLLAR_AMOUNT  => $debitDollarSum,
            self::TOTAL_CREDIT_ENTRY_DOLLAR_AMOUNT => $creditDollarSum,
            self::COMPANY_ID                       => $companyIdentificationField[self::CONTENT],
            self::MESSAGE_AUTHENTICATION_CODE      => null,
            self::RESERVED                         => null,
            self::ORIGINATING_DFI_ID               => $originatingDfiIdField[self::CONTENT],
            self::BATCH_NUMBER                     => $batchNumberField[self::CONTENT],
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
            self::SERVICE_CLASS_CODE               => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^(200|220|225)$/'],
                self::LENGTH          => 3,
                self::POSITION_START  => 2,
                self::POSITION_END    => 4,
                self::CONTENT         => null,
            ],
            self::ENTRY_AND_ADDENDA_COUNT          => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1,6}$/'],
                self::LENGTH          => 6,
                self::POSITION_START  => 5,
                self::POSITION_END    => 10,
                self::PADDING         => self::NUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::ENTRY_HASH                       => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1,10}$/'],
                self::LENGTH          => 10,
                self::POSITION_START  => 11,
                self::POSITION_END    => 20,
                self::PADDING         => self::NUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::TOTAL_DEBIT_ENTRY_DOLLAR_AMOUNT  => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{0,12}$/'],
                self::LENGTH          => 12,
                self::POSITION_START  => 21,
                self::POSITION_END    => 32,
                self::PADDING         => self::NUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::TOTAL_CREDIT_ENTRY_DOLLAR_AMOUNT => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{0,12}$/'],
                self::LENGTH          => 12,
                self::POSITION_START  => 33,
                self::POSITION_END    => 44,
                self::PADDING         => self::NUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::COMPANY_ID                       => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_REQUIRED,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9]{10}$/'],
                self::LENGTH          => 10,
                self::POSITION_START  => 45,
                self::POSITION_END    => 54,
                self::CONTENT         => null,
            ],
            self::MESSAGE_AUTHENTICATION_CODE      => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^ {0,19}$/'],
                self::LENGTH          => 19,
                self::POSITION_START  => 55,
                self::POSITION_END    => 73,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::RESERVED                         => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^ {0,6}$/'],
                self::LENGTH          => 6,
                self::POSITION_START  => 74,
                self::POSITION_END    => 79,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::ORIGINATING_DFI_ID               => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{8}$/'],
                self::LENGTH          => 8,
                self::POSITION_START  => 80,
                self::POSITION_END    => 87,
                self::CONTENT         => null,
            ],
            self::BATCH_NUMBER                     => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1,7}$/'],
                self::LENGTH          => 7,
                self::POSITION_START  => 88,
                self::POSITION_END    => 94,
                self::PADDING         => self::NUMERIC_PADDING,
                self::CONTENT         => null,
            ],
        ];
    }
}
