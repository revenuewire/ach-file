<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-05-30
 * Time: 16:13
 */

namespace RW\ACH;


use DateTime;

class BatchHeaderRecord extends FileComponent
{
    /* FIXED VALUES */
    private const FIXED_RECORD_TYPE_CODE       = '5';
    private const FIXED_SETTLEMENT_DATE        = '   ';
    private const FIXED_ORIGINATOR_STATUS_CODE = '1';
    /* DEFAULT VALUES */
    private const DEFAULT_DISCRETIONARY_DATA = '';

    /* FIXED VALUE FIELD NAMES */
    private const SETTLEMENT_DATE        = 'SETTLEMENT_DATE';
    private const ORIGINATOR_STATUS_CODE = 'ORIGINATOR_STATUS_CODE';
    private const EFFECTIVE_ENTRY_DATE   = 'EFFECTIVE_ENTRY_DATE';   // variable, but managed through ENTRY_DATE_OVERRIDE
    /* VARIABLE VALUE FIELD NAMES */
    public const COMPANY_NAME              = 'COMPANY_NAME';
    public const DISCRETIONARY_DATA        = 'DISCRETIONARY_DATA';
    public const COMPANY_ID                = 'COMPANY_ID';
    public const STANDARD_ENTRY_CLASS_CODE = 'STANDARD_ENTRY_CLASS_CODE';
    public const COMPANY_ENTRY_DESCRIPTION = 'COMPANY_ENTRY_DESCRIPTION';
    public const COMPANY_DESCRIPTIVE_DATE  = 'COMPANY_DESCRIPTIVE_DATE'; // ENTRY_DATE_OVERRIDE is used if not provided
    public const ORIGINATING_DFI_ID        = 'ORIGINATING_DFI_ID';
    public const BATCH_NUMBER              = 'BATCH_NUMBER';

    /* Set the date and time in one using a DateTime object */
    public const ENTRY_DATE_OVERRIDE = 'ENTRY_DATE_OVERRIDE';

    protected const REQUIRED_FIELDS = [
        self::SERVICE_CLASS_CODE,
        self::COMPANY_NAME,
        self::COMPANY_ID,
        self::STANDARD_ENTRY_CLASS_CODE,
        self::COMPANY_ENTRY_DESCRIPTION,
        self::ORIGINATING_DFI_ID,
        self::BATCH_NUMBER,
    ];
    private const OPTIONAL_FIELDS = [
        self::ENTRY_DATE_OVERRIDE      => null,
        self::DISCRETIONARY_DATA       => null,
        self::COMPANY_DESCRIPTIVE_DATE => null,
    ];

    /**
     * BatchHeader constructor.
     *
     * @param array $fields is an array of field key => value pairs as follows:
     *                      [
     *                          // Required
     *                          SERVICE_CLASS_CODE        => One of MIXED_SERVICE_CLASS, CREDIT_SERVICE_CLASS, DEBIT_SERVICE_CLASS
     *                          COMPANY_NAME              => Alphanumeric string of length > 0 and <= 16
     *                          COMPANY_ID    => 10 digits
     *                          STANDARD_ENTRY_CLASS_CODE => 3 characters representing the entry format
     *                          COMPANY_ENTRY_DESCRIPTION => Alphanumeric string of length > 1 and <= 10 describing the purpose of the entry to the receiver
     *                          ORIGINATING_DFI_ID        => 8 digits representing where the file will be delivered for processing
     *                          BATCH_NUMBER              => 7 digits identifying the sequential order of this batch
     *                          // Optional
     *                          ENTRY_DATE_OVERRIDE       => DateTime object (default: current date)
     *                          DISCRETIONARY_DATA        => Alphanumeric string of length >= 0 and <= 20 (internal use)
     *                          COMPANY_DESCRIPTIVE_DATE  => DateTime object (default: Entry Date Override,
     *                      ]
     * @throws ValidationException
     */
    public function __construct($fields)
    {
        if (!is_array($fields)) {
            throw new \InvalidArgumentException('fields argument must be of type array.');
        }

        // Add any missing optional fields, but preserve user-provided values for those that exist
        $fields = array_merge(self::OPTIONAL_FIELDS, $fields);

        // Apply basic modifications where required, and provide defaults for missing values where possible
        foreach ($fields as $k => $v) {
            switch ($k) {
                case self::ENTRY_DATE_OVERRIDE:
                    // If an entry date was not provided, the override is null and the current date/time will be used
                    $entryDate                          = $v ?: new DateTime();
                    $fields[self::EFFECTIVE_ENTRY_DATE] = $entryDate->format('ymd');
                    break;
                case self::COMPANY_DESCRIPTIVE_DATE:
                    // If a descriptive date was not provided, we will try and use the entry date.
                    // Otherwise, the current date/time will be used
                    if ($v === null) {
                        $v = $fields[self::ENTRY_DATE_OVERRIDE] ?: new DateTime();
                    }
                    $fields[self::COMPANY_DESCRIPTIVE_DATE] = $v->format('ymd');
                    break;
                case self::DISCRETIONARY_DATA:
                    $fields[self::DISCRETIONARY_DATA] = $v ?: self::DEFAULT_DISCRETIONARY_DATA;
            }
        }

        // Remove the custom override key if it exists
        $fields = array_diff_key($fields, [self::ENTRY_DATE_OVERRIDE => null]);

        parent::__construct($fields);
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
     *
     * @return array
     */
    protected function getDefaultFieldSpecifications(): array
    {
        return [
            self::RECORD_TYPE_CODE          => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'N',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1}$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 1,
                self::POSITION_END    => 1,
                self::CONTENT         => self::FIXED_RECORD_TYPE_CODE,
            ],
            self::SERVICE_CLASS_CODE        => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'NNN',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^(200|220|225)$/'],
                self::LENGTH          => 3,
                self::POSITION_START  => 2,
                self::POSITION_END    => 4,
                self::CONTENT         => self::MIXED_SERVICE_CLASS,
            ],
            self::COMPANY_NAME              => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'Alphanumeric',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{1,16}$/'],
                self::LENGTH          => 16,
                self::POSITION_START  => 5,
                self::POSITION_END    => 20,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::DISCRETIONARY_DATA        => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::FORMAT          => 'Alphanumeric',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{0,20}$/'],
                self::LENGTH          => 20,
                self::POSITION_START  => 21,
                self::POSITION_END    => 40,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::COMPANY_ID                => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'NNNNNNNNNN',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{10}$/'],
                self::LENGTH          => 10,
                self::POSITION_START  => 41,
                self::POSITION_END    => 50,
                self::CONTENT         => null,
            ],
            self::STANDARD_ENTRY_CLASS_CODE => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'AAA',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z]{3}$/'],
                self::LENGTH          => 3,
                self::POSITION_START  => 51,
                self::POSITION_END    => 53,
                self::CONTENT         => null,
            ],
            self::COMPANY_ENTRY_DESCRIPTION => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'Alphanumeric',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{1,10}$/'],
                self::LENGTH          => 10,
                self::POSITION_START  => 54,
                self::POSITION_END    => 63,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::COMPANY_DESCRIPTIVE_DATE  => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::FORMAT          => 'YYMMDD',
                self::VALIDATOR       => [self::VALIDATOR_DATETIME, 'ymd'],
                self::LENGTH          => 6,
                self::POSITION_START  => 64,
                self::POSITION_END    => 69,
                self::CONTENT         => null,
            ],
            self::EFFECTIVE_ENTRY_DATE      => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_REQUIRED,
                self::FORMAT          => 'YYMMDD',
                self::VALIDATOR       => [self::VALIDATOR_DATETIME, 'ymd'],
                self::LENGTH          => 6,
                self::POSITION_START  => 70,
                self::POSITION_END    => 75,
                self::CONTENT         => null,
            ],
            self::SETTLEMENT_DATE           => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'bbb',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^ {3}$/'],
                self::LENGTH          => 3,
                self::POSITION_START  => 76,
                self::POSITION_END    => 78,
                self::CONTENT         => self::FIXED_SETTLEMENT_DATE,
            ],
            self::ORIGINATOR_STATUS_CODE    => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'N',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1}$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 79,
                self::POSITION_END    => 79,
                self::CONTENT         => self::FIXED_ORIGINATOR_STATUS_CODE,
            ],
            self::ORIGINATING_DFI_ID        => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'NNNNNNNN',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{8}$/'],
                self::LENGTH          => 8,
                self::POSITION_START  => 80,
                self::POSITION_END    => 87,
                self::CONTENT         => null,
            ],
            self::BATCH_NUMBER              => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'NNNNNNN',
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