<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-05-28
 * Time: 14:48
 */

namespace RW\ACH;


use DateTime;

class FileHeader extends FileComponent
{
    /* FIXED VALUES */
    private const FIXED_RECORD_TYPE_CODE = '1';
    private const FIXED_PRIORITY_CODE    = '01';
    private const FIXED_RECORD_SIZE      = '094';
    private const FIXED_BLOCKING_FACTOR  = '10';
    private const FIXED_FORMAT_CODE      = '1';
    /* DEFAULT VALUES */
    private const DEFAULT_FILE_ID_MODIFIER = 'A';

    /* FIXED VALUE FIELD NAMES */
    private const PRIORITY_CODE      = 'PRIORITY_CODE';
    private const FILE_CREATION_DATE = 'FILE_CREATION_DATE'; // variable, but managed through FILE_DATE_OVERRIDE
    private const FILE_CREATION_TIME = 'FILE_CREATION_TIME'; // variable, but managed through FILE_DATE_OVERRIDE
    private const RECORD_SIZE        = 'RECORD_SIZE';
    private const BLOCKING_FACTOR    = 'BLOCKING_FACTOR';
    private const FORMAT_CODE        = 'FORMAT_CODE';
    /* VARIABLE VALUE FIELD NAMES */
    public const IMMEDIATE_DESTINATION = 'IMMEDIATE_DESTINATION';
    public const IMMEDIATE_ORIGIN      = 'IMMEDIATE_ORIGIN';
    public const FILE_ID_MODIFIER      = 'FILE_ID_MODIFIER';
    public const DESTINATION           = 'IMMEDIATE_DESTINATION_NAME';
    public const ORIGIN_NAME           = 'IMMEDIATE_ORIGIN_NAME';
    public const REFERENCE_CODE        = 'REFERENCE_CODE';

    /* Set the date and time in one using a DateTime object */
    public const FILE_DATE_OVERRIDE = 'FILE_DATE_OVERRIDE';

    protected const REQUIRED_FIELDS = [
        self::IMMEDIATE_DESTINATION,
        self::IMMEDIATE_ORIGIN,
        self::DESTINATION,
        self::ORIGIN_NAME,
    ];

    private const OPTIONAL_FIELDS = [
        self::FILE_DATE_OVERRIDE => null,
        self::FILE_ID_MODIFIER   => null,
        self::REFERENCE_CODE     => null,
    ];

    /**
     * PaymentFileHeader constructor.
     *
     * @param array $fields is an array of field key => value pairs as follows:
     *                      [
     *                          // Required
     *                          FileHeader::IMMEDIATE_DESTINATION => 9 digits prefixed with a space
     *                          FileHeader::IMMEDIATE_ORIGIN      => 10 digits
     *                          FileHeader::DESTINATION           => Alphanumeric string of length > 0 and <= 23
     *                          FileHeader::ORIGIN_NAME           => Alphanumeric string of length > 0 and <= 23
     *                          // Optional
     *                          FileHeader::FILE_DATE_OVERRIDE    => DateTime object (default: current date)
     *                          FileHeader::FILE_ID_MODIFIER      => Single character A-Z or 0-9 (default: 'A')
     *                          FileHeader::REFERENCE_CODE        => Alphanumeric string of length > 0 and <= 8
     *                      ]
     * @throws ValidationException if any values in $fields are not successfully validated
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
                case self::IMMEDIATE_DESTINATION:
                    // Add the space to the beginning of the destination number as needed to simplify validation
                    if (strlen($v) > 0 && $v[0] !== ' ') {
                        $fields[self::IMMEDIATE_DESTINATION] = ' ' . $v;
                    }
                    break;
                case self::FILE_DATE_OVERRIDE:
                    // If a file date was not provided, the override is null and the current date/time will be used
                    $fileDate                         = $v ?: new DateTime();
                    $fields[self::FILE_CREATION_DATE] = $fileDate->format('ymd');
                    $fields[self::FILE_CREATION_TIME] = $fileDate->format('Hi');

                    break;
                case self::FILE_ID_MODIFIER:
                    $fields[self::FILE_ID_MODIFIER] = $v ?: self::DEFAULT_FILE_ID_MODIFIER;
                    break;
            }
        }

        // Remove the custom override key if it exists
        $fields = array_diff_key($fields, [self::FILE_DATE_OVERRIDE => null]);

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
     */
    protected function getFieldSpecifications()
    {
        return [
            self::RECORD_TYPE_CODE      => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'N',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1}$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 1,
                self::POSITION_END    => 1,
                self::CONTENT         => self::FIXED_RECORD_TYPE_CODE,
            ],
            self::PRIORITY_CODE         => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_REQUIRED,
                self::FORMAT          => 'NN',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{2}$/'],
                self::LENGTH          => 2,
                self::POSITION_START  => 2,
                self::POSITION_END    => 3,
                self::CONTENT         => self::FIXED_PRIORITY_CODE,
            ],
            self::IMMEDIATE_DESTINATION => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'bNNNNNNNNN',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^ \d{9}$/'],
                self::LENGTH          => 10,
                self::POSITION_START  => 4,
                self::POSITION_END    => 13,
                self::CONTENT         => null,
            ],
            self::IMMEDIATE_ORIGIN      => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'NNNNNNNNNN',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{10}$/'],
                self::LENGTH          => 10,
                self::POSITION_START  => 14,
                self::POSITION_END    => 23,
                self::CONTENT         => null,
            ],
            self::FILE_CREATION_DATE    => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'YYMMDD',
                self::VALIDATOR       => [self::VALIDATOR_DATETIME, 'ymd'],
                self::LENGTH          => 6,
                self::POSITION_START  => 24,
                self::POSITION_END    => 29,
                self::CONTENT         => null,
            ],
            self::FILE_CREATION_TIME    => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::FORMAT          => 'HHMM',
                self::VALIDATOR       => [self::VALIDATOR_DATETIME, 'Hi'],
                self::LENGTH          => 4,
                self::POSITION_START  => 30,
                self::POSITION_END    => 33,
                self::CONTENT         => null,
            ],
            self::FILE_ID_MODIFIER      => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'A-Z, 0-9',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^([A-Z]|[0-9])$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 34,
                self::POSITION_END    => 34,
                self::CONTENT         => null,
            ],
            self::RECORD_SIZE           => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'NNN',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^094$/'],
                self::LENGTH          => 3,
                self::POSITION_START  => 35,
                self::POSITION_END    => 37,
                self::CONTENT         => self::FIXED_RECORD_SIZE,
            ],
            self::BLOCKING_FACTOR       => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'NN',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^10$/'],
                self::LENGTH          => 2,
                self::POSITION_START  => 38,
                self::POSITION_END    => 39,
                self::CONTENT         => self::FIXED_BLOCKING_FACTOR,
            ],
            self::FORMAT_CODE           => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::FORMAT          => 'N',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^1$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 40,
                self::POSITION_END    => 40,
                self::CONTENT         => self::FIXED_FORMAT_CODE,
            ],
            self::DESTINATION           => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::FORMAT          => 'Alphanumeric',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{1,23}$/'],
                self::LENGTH          => 23,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::POSITION_START  => 41,
                self::POSITION_END    => 63,
                self::CONTENT         => null,
            ],
            self::ORIGIN_NAME           => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::FORMAT          => 'Alphanumeric',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{1,23}$/'],
                self::LENGTH          => 23,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::POSITION_START  => 64,
                self::POSITION_END    => 86,
                self::CONTENT         => null,
            ],
            self::REFERENCE_CODE        => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::FORMAT          => 'Alphanumeric',
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{0,8}$/'],
                self::LENGTH          => 8,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::POSITION_START  => 87,
                self::POSITION_END    => 94,
                self::CONTENT         => null,
            ],
        ];
    }
}
