<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-14
 * Time: 10:31
 */

namespace RW\ACH;


/**
 * Class NoticeOfChangeAddenda
 *
 * @package RW\ACH
 */
class NoticeOfChangeAddenda extends AddendaRecord
{
    public const CHANGE_CODE    = 'CHANGE_CODE';
    public const RESERVED_A     = 'RESERVED_A';
    public const CORRECTED_DATA = 'CORRECTED_DATA';
    public const RESERVED_B     = 'RESERVED_B';

    /* NOTIFICATION OF CHANGE CODES */
    // Generic code to use as a catch all if the provided code is not found in this list
    public const CXX = [self::CODE => 'CXX', self::NOTE => 'Unrecognized return code'];
    // Bank account number incorrect or formatted incorrectly
    public const C01 = [self::CODE => 'C01', self::NOTE => 'Incorrect bank account number'];
    // Once valid transit/routing number must be changed
    public const C02 = [self::CODE => 'C02', self::NOTE => 'Incorrect transit/routing number'];
    // Once valid transit/routing number must be changed and causes a change to bank account number structure
    public const C03 = [self::CODE => 'C03', self::NOTE => 'Incorrect transit/routing number and bank account number'];
    // Customer has changed name or ODFI submitted name incorrectly
    public const C04 = [self::CODE => 'C04', self::NOTE => 'Incorrect bank account name'];
    // Entry posted to demand account should contain savings payment codes or vice versa
    public const C05 = [self::CODE => 'C05', self::NOTE => 'Incorrect transaction code'];
    // Bank account number must be changed and payment code should indicate posting to another account type (demand/savings)
    public const C06 = [self::CODE => 'C06', self::NOTE => 'Incorrect bank account number and transit code'];
    // Changes required in three fields indicated
    public const C07 = [self::CODE => 'C07', self::NOTE => 'Incorrect transit/routing number, bank account number and payment code'];
    // International transfer has incorrect bank receiving DFI identification
    public const C08 = [self::CODE => 'C08', self::NOTE => 'Incorrect receiving DFI identification (IAT only)'];
    // Individual's ID number is incorrect
    public const C09 = [self::CODE => 'C09', self::NOTE => 'Incorrect individual ID number'];
    // Company name is no longer valid and should be changed.
    public const C10 = [self::CODE => 'C10', self::NOTE => 'Incorrect company name'];
    // Company ID is no longer valid and should be changed
    public const C11 = [self::CODE => 'C11', self::NOTE => 'Incorrect company identification'];
    // Both the company name and company id are no longer valid and must be changed
    public const C12 = [self::CODE => 'C12', self::NOTE => 'Incorrect company name and company ID'];
    // The addenda submitted was formatted incorrectly for the given SEC
    public const C13 = [self::CODE => 'C13', self::NOTE => 'Addenda format error'];
    // The SEC code provided was not valid for outbound international payments
    public const C14 = [self::CODE => 'C14', self::NOTE => 'Incorrect SEC code for outbound international payment'];

    public const C61 = [self::CODE => 'C61', self::NOTE => 'Mis-routed notification of change'];
    public const C62 = [self::CODE => 'C62', self::NOTE => 'Incorrect trace number'];
    public const C63 = [self::CODE => 'C63', self::NOTE => 'Incorrect company ID'];
    public const C64 = [self::CODE => 'C64', self::NOTE => 'Incorrect individual ID number'];
    public const C65 = [self::CODE => 'C65', self::NOTE => 'Incorrectly formatted corrected data'];
    public const C66 = [self::CODE => 'C66', self::NOTE => 'Incorrect discretionary data'];
    public const C67 = [self::CODE => 'C67', self::NOTE => 'Routing number not from original entry detail record'];
    public const C68 = [self::CODE => 'C68', self::NOTE => 'Account number not from original entry detail record'];
    public const C69 = [self::CODE => 'C69', self::NOTE => 'Incorrect transaction code'];

    /**
     * Build a Notice of Change addenda record from an existing string.
     *
     * @param string $input
     * @return NoticeOfChangeAddenda
     * @throws ValidationException
     */
    public static function buildFromString($input)
    {
        return new NoticeOfChangeAddenda(self::getBuildDataFromInputString($input), false);
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
            self::RECORD_TYPE_CODE => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1}$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 1,
                self::POSITION_END    => 1,
                self::CONTENT         => self::FIXED_RECORD_TYPE_CODE,
            ],
            self::ADDENDA_TYPE_CODE => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^(98|99)$/'],
                self::LENGTH          => 2,
                self::POSITION_START  => 2,
                self::POSITION_END    => 3,
                self::CONTENT         => null,
            ],
            self::CHANGE_CODE => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^C\d{2}$/'],
                self::LENGTH          => 3,
                self::POSITION_START  => 4,
                self::POSITION_END    => 6,
                self::CONTENT         => null,
            ],
            self::ORIGINAL_ENTRY_TRACE_NUMBER => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{15}$/'],
                self::LENGTH          => 15,
                self::POSITION_START  => 7,
                self::POSITION_END    => 21,
                self::CONTENT         => null,
            ],
            self::RESERVED_A => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^ {0,6}$/'],
                self::LENGTH          => 6,
                self::POSITION_START  => 22,
                self::POSITION_END    => 27,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::ORIGINAL_RECEIVING_DFI_ID => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_REQUIRED,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{8}$/'],
                self::LENGTH          => 8,
                self::POSITION_START  => 28,
                self::POSITION_END    => 35,
                self::CONTENT         => '',
            ],
            self::CORRECTED_DATA => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[-a-zA-Z0-9 ]{1,29}$/'],
                self::LENGTH          => 29,
                self::POSITION_START  => 36,
                self::POSITION_END    => 64,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::RESERVED_B => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^ {0,15}$/'],
                self::LENGTH          => 15,
                self::POSITION_START  => 65,
                self::POSITION_END    => 79,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::TRACE_NUMBER => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{15}$/'],
                self::LENGTH          => 15,
                self::POSITION_START  => 80,
                self::POSITION_END    => 94,
                self::CONTENT         => null,
            ],
        ];
    }
}
