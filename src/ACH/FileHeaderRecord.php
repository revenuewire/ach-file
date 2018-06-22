<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-14
 * Time: 14:17
 */

namespace RW\ACH;


use DateTime;
use InvalidArgumentException;

/**
 * Class FileHeaderRecord
 *
 * @package RW\ACH
 */
class FileHeaderRecord extends FileComponent
{
    /* FIELD NAMES */
    protected const PRIORITY_CODE         = 'PRIORITY_CODE';
    public const    IMMEDIATE_DESTINATION = 'IMMEDIATE_DESTINATION';
    public const    IMMEDIATE_ORIGIN      = 'IMMEDIATE_ORIGIN';
    protected const FILE_CREATION_DATE    = 'FILE_CREATION_DATE';
    protected const FILE_CREATION_TIME    = 'FILE_CREATION_TIME';
    public const    FILE_ID_MODIFIER      = 'FILE_ID_MODIFIER';
    protected const RECORD_SIZE           = 'RECORD_SIZE';
    protected const BLOCKING_FACTOR       = 'BLOCKING_FACTOR';
    protected const FORMAT_CODE           = 'FORMAT_CODE';
    public const    DESTINATION_NAME      = 'IMMEDIATE_DESTINATION_NAME';
    public const    ORIGIN_NAME           = 'IMMEDIATE_ORIGIN_NAME';
    public const    REFERENCE_CODE        = 'REFERENCE_CODE';

    /* FIXED VALUES */
    public const    FIXED_RECORD_TYPE_CODE = '1';
    protected const FIXED_PRIORITY_CODE    = '01';
    protected const FIXED_BLOCKING_FACTOR  = '10';
    protected const FIXED_RECORD_SIZE      = '094';
    protected const FIXED_FORMAT_CODE      = '1';

    protected const DEFAULT_FILE_ID_MODIFIER = 'A';

    /* Set the date and time in one using a DateTime object */
    public const FILE_DATE = 'FILE_DATE';

    protected const REQUIRED_FIELDS    = [
        self::IMMEDIATE_DESTINATION,
        self::IMMEDIATE_ORIGIN,
        self::DESTINATION_NAME,
        self::ORIGIN_NAME,
    ];
    protected const OPTIONAL_FIELDS    = [
        self::FILE_DATE        => null,
        self::FILE_ID_MODIFIER => null,
        self::REFERENCE_CODE   => null,
    ];

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
     *          self::POSITION_START  => Starting position within the component
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
            self::RECORD_TYPE_CODE      => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1}$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 1,
                self::POSITION_END    => 1,
                self::CONTENT         => static::FIXED_RECORD_TYPE_CODE,
            ],
            self::PRIORITY_CODE         => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_REQUIRED,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{2}$/'],
                self::LENGTH          => 2,
                self::POSITION_START  => 2,
                self::POSITION_END    => 3,
                self::CONTENT         => self::FIXED_PRIORITY_CODE,
            ],
            self::IMMEDIATE_DESTINATION => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^ \d{9}$/'],
                self::LENGTH          => 10,
                self::POSITION_START  => 4,
                self::POSITION_END    => 13,
                self::CONTENT         => null,
            ],
            self::IMMEDIATE_ORIGIN      => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{10}$/'],
                self::LENGTH          => 10,
                self::POSITION_START  => 14,
                self::POSITION_END    => 23,
                self::CONTENT         => null,
            ],
            self::FILE_CREATION_DATE    => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_DATETIME, 'ymd'],
                self::LENGTH          => 6,
                self::POSITION_START  => 24,
                self::POSITION_END    => 29,
                self::CONTENT         => null,
            ],
            self::FILE_CREATION_TIME    => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_DATETIME, 'Hi'],
                self::LENGTH          => 4,
                self::POSITION_START  => 30,
                self::POSITION_END    => 33,
                self::CONTENT         => null,
            ],
            self::FILE_ID_MODIFIER      => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[A-Z]$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 34,
                self::POSITION_END    => 34,
                self::CONTENT         => null,
            ],
            self::RECORD_SIZE           => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^094$/'],
                self::LENGTH          => 3,
                self::POSITION_START  => 35,
                self::POSITION_END    => 37,
                self::CONTENT         => self::FIXED_RECORD_SIZE,
            ],
            self::BLOCKING_FACTOR  => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^10$/'],
                self::LENGTH          => 2,
                self::POSITION_START  => 38,
                self::POSITION_END    => 39,
                self::CONTENT         => self::FIXED_BLOCKING_FACTOR,
            ],
            self::FORMAT_CODE      => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^1$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 40,
                self::POSITION_END    => 40,
                self::CONTENT         => self::FIXED_FORMAT_CODE,
            ],
            self::DESTINATION_NAME => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{1,23}$/'],
                self::LENGTH          => 23,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::POSITION_START  => 41,
                self::POSITION_END    => 63,
                self::CONTENT         => null,
            ],
            self::ORIGIN_NAME           => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{1,23}$/'],
                self::LENGTH          => 23,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::POSITION_START  => 64,
                self::POSITION_END    => 86,
                self::CONTENT         => null,
            ],
            self::REFERENCE_CODE        => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{0,8}$/'],
                self::LENGTH          => 8,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::POSITION_START  => 87,
                self::POSITION_END    => 94,
                self::CONTENT         => null,
            ],
        ];
    }

    /**
     * FileHeaderRecord constructor.
     *
     * @param array $fields   is an array of field key => value pairs as follows:
     *                        [
     *                            // Required
     *                            IMMEDIATE_DESTINATION => The ID number of the destination bank
     *                            IMMEDIATE_ORIGIN      => Ten digit ID number for your company
     *                            DESTINATION_NAME      => The name of the destination bank
     *                            ORIGIN_NAME           => Your company name
     *                            // Optional
     *                            FILE_DATE             => DateTime object, default: new DateTime('now')
     *                            FILE_ID_MODIFIER      => Code to distinguish multiple input files per day ('A', 'B', ...)
     *                            REFERENCE_CODE        => Identify the file for your internal use
     *                        ]
     * @param bool  $validate
     * @throws ValidationException
     */
    public function __construct($fields, $validate = true)
    {
        if (!is_array($fields)) {
            throw new \InvalidArgumentException('fields argument must be of type array.');
        }

        // Check for required fields
        $missing_fields = array_diff(self::REQUIRED_FIELDS, array_keys($fields));
        if ($missing_fields) {
            throw new InvalidArgumentException('Cannot create ' . self::class . ' without all required fields, missing: ' . implode(', ', $missing_fields));
        }

        // If validating, add any missing optional fields (but preserve user-provided values for those that exist)
        if ($validate) {
            $fields = array_merge(self::OPTIONAL_FIELDS, $fields);
            $fields = $this->getModifiedFields($fields);
        }

        parent::__construct($fields, $validate);
    }

    /**
     * @param $fields
     * @return array
     */
    protected function getModifiedFields($fields): array
    {
        // If the date override was provided, use it. Otherwise use today's date.
        $fileDate                         = $fields[self::FILE_DATE] ?: new DateTime();
        $fields[self::FILE_CREATION_DATE] = $fileDate->format('ymd');
        $fields[self::FILE_CREATION_TIME] = $fileDate->format('Hi');

        // If the File ID Modifier was not provided, use default
        if (null === $fields[self::FILE_ID_MODIFIER]) {
            $fields[self::FILE_ID_MODIFIER] = self::DEFAULT_FILE_ID_MODIFIER;
        }

        return $fields;
    }
}
