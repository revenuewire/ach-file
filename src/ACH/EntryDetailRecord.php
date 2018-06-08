<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-05-31
 * Time: 16:20
 */

namespace RW\ACH;


class EntryDetailRecord extends FileComponent
{
    /* FIXED VALUES */
    private const FIXED_RECORD_TYPE_CODE = '6';
    /* DEFAULT VALUES */
    private const DEFAULT_ADDENDA_INDICATOR = '0';
    /* VARIABLE VALUE FIELD NAMES */
    public const TRANSACTION_CODE   = 'TRANSACTION_CODE';
    public const TRANSIT_ABA_NUMBER = 'TRANSIT_ABA_NUMBER';
    public const CHECK_DIGIT        = 'CHECK_DIGIT';
    public const DFI_ACCOUNT_NUMBER = 'DFI_ACCOUNT_NUMBER';
    public const AMOUNT             = 'AMOUNT';
    public const ID_NUMBER          = 'ID_NUMBER';
    public const INDIVIDUAL_NAME    = 'INDIVIDUAL_NAME';
    public const DRAFT_INDICATOR    = 'DRAFT_INDICATOR';
    public const ADDENDA_INDICATOR  = 'ADDENDA_INDICATOR';
    public const TRACE_NUMBER       = 'TRACE_NUMBER';

    protected const REQUIRED_FIELDS = [
        self::TRANSACTION_CODE,
        self::TRANSIT_ABA_NUMBER,
        self::DFI_ACCOUNT_NUMBER,
        self::AMOUNT,
        self::INDIVIDUAL_NAME,
        self::TRACE_NUMBER,
    ];

    private const OPTIONAL_FIELDS = [
        self::ID_NUMBER         => null,
        self::DRAFT_INDICATOR   => null,
        self::ADDENDA_INDICATOR => null,
    ];

    /* TRANSACTION CODES */
    public const CHECKING_CREDIT_DEPOSIT     = '22';
    public const CHECKING_CREDIT_PRE_NOTE    = '23';
    public const CHECKING_CREDIT_ZERO_DOLLAR = '24';
    public const SAVINGS_CREDIT_DEPOSIT      = '32';
    public const SAVINGS_CREDIT_PRE_NOTE     = '33';
    public const SAVINGS_CREDIT_ZERO_DOLLAR  = '34';

    public const CHECKING_DEBIT_PAYMENT     = '27';
    public const CHECKING_DEBIT_PRE_NOTE    = '28';
    public const CHECKING_DEBIT_ZERO_DOLLAR = '29';
    public const SAVINGS_DEBIT_PAYMENT      = '37';
    public const SAVINGS_DEBIT_PRE_NOTE     = '38';
    public const SAVINGS_DEBIT_ZERO_DOLLAR  = '39';

    public const CREDIT_TRANSACTION_CODES = [
        self::CHECKING_CREDIT_DEPOSIT,
        self::CHECKING_CREDIT_PRE_NOTE,
        self::CHECKING_CREDIT_ZERO_DOLLAR,
        self::SAVINGS_CREDIT_DEPOSIT,
        self::SAVINGS_CREDIT_PRE_NOTE,
        self::SAVINGS_CREDIT_ZERO_DOLLAR,
    ];
    public const DEBIT_TRANSACTION_CODES  = [
        self::CHECKING_DEBIT_PAYMENT,
        self::CHECKING_DEBIT_PRE_NOTE,
        self::CHECKING_DEBIT_ZERO_DOLLAR,
        self::SAVINGS_DEBIT_PAYMENT,
        self::SAVINGS_DEBIT_PRE_NOTE,
        self::SAVINGS_DEBIT_ZERO_DOLLAR,
    ];

    private $entryDetailSequenceNumber;

    public function __construct(array $fields, int $sequence)
    {
        if (!is_array($fields)) {
            throw new \InvalidArgumentException('fields argument must be of type array.');
        }
        $this->entryDetailSequenceNumber = $sequence;

        // Add any missing optional fields, but preserve user-provided values for those that exist
        $fields = array_merge(self::OPTIONAL_FIELDS, $fields);

        // Apply basic modifications where required, and provide defaults for missing values where possible
        foreach ($fields as $k => $v) {
            switch ($k) {
                case self::TRANSIT_ABA_NUMBER:
                    // Work with an integer for easy processing
                    $v = (int) $v;

                    $fields[self::TRANSIT_ABA_NUMBER] = (int) ($v / 10); // Dump the last digit
                    $fields[self::CHECK_DIGIT]        = $v % 10;         // Extract the last digit
                    break;
                case self::AMOUNT:
                    // We can't work with amounts that aren't numeric, so add special validation here to prevent
                    // bcmul from silently converting bad inputs to zero
                    if (!is_numeric($v)) {
                        throw new ValidationException('Value: "' . ($v ?? 'null') . '" for "' . $k . '" must be numeric');
                    }
                    // Move decimal over and dump extra digits
                    $fields[self::AMOUNT] = bcmul($v, '100', 0);
                    break;
                case self::TRACE_NUMBER:
                    // Concatenate the provided immediate destination, and the left-padded sequence number
                    $fields[self::TRACE_NUMBER] = bcadd(bcmul($v, '10000000', 0), $sequence, 0);
                    break;
                case self::ADDENDA_INDICATOR:
                    $fields[self::ADDENDA_INDICATOR] = $v ?: self::DEFAULT_ADDENDA_INDICATOR;
            }
        }

        parent::__construct($fields);
    }

    public function getTransitAbaNumber()
    {
        return $this->fieldSpecifications[self::TRANSIT_ABA_NUMBER][self::CONTENT];
    }

    public function getTransactionCode()
    {
        return $this->fieldSpecifications[self::TRANSACTION_CODE][self::CONTENT];
    }

    public function getAmount()
    {
        return $this->fieldSpecifications[self::AMOUNT][self::CONTENT];
    }

    /**
     * Generate the field specifications for each field in the file component.
     * Format is an array of arrays as follows:
     *  $this->fieldSpecifications = [
     *      FIELD_NAME => [
     *          self::FIELD_INCLUSION => Mandatory, Required, or Optional (reserved for future use)
     *          self::FORMAT          => Description of the expected format (informational)
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
     */
    protected function getDefaultFieldSpecifications()
    {
        $validTransactionCodes = array_merge(self::DEBIT_TRANSACTION_CODES, self::CREDIT_TRANSACTION_CODES);

        return [
            self::RECORD_TYPE_CODE   => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'N',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1}$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 1,
                self::POSITION_END    => 1,
                self::CONTENT         => self::FIXED_RECORD_TYPE_CODE,
            ],
            self::TRANSACTION_CODE   => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'NNNNNN',
                self::VALIDATOR       => [self::VALIDATOR_ARRAY, $validTransactionCodes],
                self::LENGTH          => 2,
                self::POSITION_START  => 2,
                self::POSITION_END    => 3,
                self::CONTENT         => null,
            ],
            self::TRANSIT_ABA_NUMBER => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'NNNNNNNN',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{8}$/'],
                self::LENGTH          => 8,
                self::POSITION_START  => 4,
                self::POSITION_END    => 11,
                self::CONTENT         => '',
            ],
            self::CHECK_DIGIT        => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'N',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1}$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 12,
                self::POSITION_END    => 12,
                self::CONTENT         => null,
            ],
            self::DFI_ACCOUNT_NUMBER => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_REQUIRED,
                self::FORMAT          => 'Alphanumeric',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[-a-zA-Z0-9 ]{1,17}$/'],
                self::LENGTH          => 17,
                self::POSITION_START  => 13,
                self::POSITION_END    => 29,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::AMOUNT             => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => '$$$$$$$$$cc',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1,10}$/'],
                self::LENGTH          => 10,
                self::POSITION_START  => 30,
                self::POSITION_END    => 39,
                self::PADDING         => self::NUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::ID_NUMBER          => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::FORMAT          => 'Alphanumeric',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[-a-zA-Z0-9 ]{0,15}$/'],
                self::LENGTH          => 15,
                self::POSITION_START  => 40,
                self::POSITION_END    => 54,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::INDIVIDUAL_NAME    => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_REQUIRED,
                self::FORMAT          => 'Alphanumeric',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{1,22}$/'],
                self::LENGTH          => 22,
                self::POSITION_START  => 55,
                self::POSITION_END    => 76,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::DRAFT_INDICATOR    => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::FORMAT          => 'Alphanumeric',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^((1[\?\*])|  )?$/'],
                self::LENGTH          => 2,
                self::POSITION_START  => 77,
                self::POSITION_END    => 78,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::ADDENDA_INDICATOR  => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'N',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[01]$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 79,
                self::POSITION_END    => 79,
                self::CONTENT         => null,
            ],
            self::TRACE_NUMBER       => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'NNNNNNNNNNNNNNN',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{15}$/'],
                self::LENGTH          => 15,
                self::POSITION_START  => 80,
                self::POSITION_END    => 94,
                self::CONTENT         => null,
            ],
        ];
    }
}