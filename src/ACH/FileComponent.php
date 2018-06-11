<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-05-28
 * Time: 16:17
 */

namespace RW\ACH;


use DateTime;
use InvalidArgumentException;

/**
 * Class ACHFileComponent represents any component part of an ACH file.
 *
 * @package RW\ACH
 */
abstract class FileComponent
{
    /* RE-USED FIELD NAMES */
    protected const RECORD_TYPE_CODE                 = 'RECORD_TYPE_CODE';
    protected const ENTRY_AND_ADDENDA_COUNT          = 'ENTRY_AND_ADDENDA_COUNT';
    protected const ENTRY_HASH                       = 'ENTRY_HASH';
    protected const TOTAL_DEBIT_ENTRY_DOLLAR_AMOUNT  = 'TOTAL_DEBIT_ENTRY_DOLLAR_AMOUNT';
    protected const TOTAL_CREDIT_ENTRY_DOLLAR_AMOUNT = 'TOTAL_CREDIT_ENTRY_DOLLAR_AMOUNT';
    protected const RESERVED                         = 'RESERVED';
    public const    SERVICE_CLASS_CODE               = 'SERVICE_CLASS_CODE';

    /* FIELD SPECIFICATION KEYS */
    protected const FIELD_INCLUSION    = 'FIELD_INCLUSION';
    protected const FORMAT             = 'FORMAT';
    protected const VALIDATOR          = 'VALIDATOR';
    protected const LENGTH             = 'LENGTH';
    protected const POSITION_START     = 'POSITION_START';
    protected const POSITION_END       = 'POSITION_END';
    protected const PADDING            = 'PADDING';
    protected const CONTENT            = 'CONTENT';

    /* VALIDATION TYPES */
    protected const VALIDATOR_REGEX    = 1;
    protected const VALIDATOR_DATETIME = 2;
    protected const VALIDATOR_ARRAY    = 3;

    /* SERVICE CLASS CODES */
    public const    DEBIT_SERVICE_CLASS  = '225';
    public const    MIXED_SERVICE_CLASS  = '200';
    public const    CREDIT_SERVICE_CLASS = '220';

    /* REQUIREMENT TYPES */
    // Information necessary to ensure the proper routing and/or posting of an ACH entry
    protected const FIELD_INCLUSION_MANDATORY = 'M';
    // Omission may not cause rejection at the ACH operator, but may cause
    // rejection at the Receiving Depository Financial Institution (RDFI)
    protected const FIELD_INCLUSION_REQUIRED = 'R';
    // Inclusion is at the discretion of the originator, and would be included in any returns
    protected const FIELD_INCLUSION_OPTIONAL = 'O';

    protected const REQUIRED_FIELDS = [];

    /* PADDING TYPES */
    protected const ALPHANUMERIC_PADDING = ' ';
    protected const NUMERIC_PADDING      = '0';

    /* GENERAL CONSTANTS */
    public const DEBIT  = 'DEBIT';
    public const CREDIT = 'CREDIT';

    // Field values and validation data
    protected $fieldSpecifications;

    /**
     * FileComponent constructor.
     *
     * @param array $fields
     * @throws ValidationException
     */
    protected function __construct($fields)
    {
        // Check for required fields
        $missing_fields = array_diff(static::REQUIRED_FIELDS, array_keys($fields));
        if ($missing_fields) {
            throw new InvalidArgumentException('Cannot create ' . static::class . ' without all required fields, missing: ' . implode(', ', $missing_fields));
        }

        $this->fieldSpecifications = $this->getDefaultFieldSpecifications();

        // Remove any extra fields that are not part of the specification
        $fields = array_intersect_key($fields, $this->fieldSpecifications);

        foreach ($fields as $k => $v) {
            $this->setField($k, $v);
        }
    }

    /**
     * Standard file components consist of a single 'block' (line), but this can be overwritten
     * to account for special cases like addenda records within entry detail records.
     *
     * @return int
     */
    public function getBlockCount(): int
    {
        return 1;
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
    protected abstract function getDefaultFieldSpecifications(): array;

    /**
     * Validate the value and set the CONTENT of a specific field.
     *
     * @param $k
     * @param $v
     * @return FileComponent
     * @throws ValidationException
     */
    protected function setField($k, $v): FileComponent
    {
        // Always work with strings
        $v = (string) $v;

        // Make sure the key exists
        if (empty($this->fieldSpecifications[$k])) {
            throw new \InvalidArgumentException('Key "' . $k . '" does not match a valid field.');
        }

        // Validate value
        switch ($this->fieldSpecifications[$k][self::VALIDATOR][0]) {
            case self::VALIDATOR_REGEX:
                if (!preg_match($this->fieldSpecifications[$k][self::VALIDATOR][1], $v)) {
                    throw new ValidationException('Value: "' . ($v ?? 'null') . '" for "' . $k . '" does not match regular expression ' . $this->fieldSpecifications[$k][self::VALIDATOR][1]);
                }
                break;
            case self::VALIDATOR_DATETIME:
                if (!DateTime::createFromFormat($this->fieldSpecifications[$k][self::VALIDATOR][1], $v)) {
                    throw new ValidationException('"' . $v ?? 'null' . '" for "' . $k . '" does not match date time format ' . $this->fieldSpecifications[$k][self::VALIDATOR][1]);
                }
                break;
            case self::VALIDATOR_ARRAY:
                if (!in_array($v, $this->fieldSpecifications[$k][self::VALIDATOR][1], true)) {
                    throw new ValidationException('"' . $v ?? 'null' . '" for "' . $k . '" must be one of: [ ' . implode(', ', $this->fieldSpecifications[$k][self::VALIDATOR][1]) . ' ]');
                }
                break;
            default:
                throw new ValidationException('"' . $this->fieldSpecifications[$k][self::VALIDATOR][0] . '" is not a valid validation type');
        }

        // Pad as necessary
        $padding = $this->fieldSpecifications[$k][self::PADDING] ?? null;
        if (null !== $padding) {
            $v = str_pad(
                $v,
                $this->fieldSpecifications[$k][self::LENGTH],
                $padding,
                $padding === self::ALPHANUMERIC_PADDING ? STR_PAD_RIGHT : STR_PAD_LEFT
            );
        }

        $this->fieldSpecifications[$k][self::CONTENT] = strtoupper($v);

        return $this;
    }

    /**
     * Convert the file component to string form - note that no special line ending characters are applied,
     * if concatenating multiple components the user must supply the appropriate line ending.
     *
     * @return string
     */
    public function toString()
    {
        $record = '';
        foreach ($this->fieldSpecifications as $field) {
            $record .= $field[self::CONTENT];
        }

        return $record;
    }
}
