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
    public const  FIXED_RECORD_TYPE_CODE       = '5';
    private const FIXED_SETTLEMENT_DATE        = '   ';
    private const FIXED_ORIGINATOR_STATUS_CODE = '1';
    /* DEFAULT VALUES */
    private const DEFAULT_DISCRETIONARY_DATA = '';

    /* FIXED VALUE FIELD NAMES */
    private const SETTLEMENT_DATE        = 'SETTLEMENT_DATE';
    private const ORIGINATOR_STATUS_CODE = 'ORIGINATOR_STATUS_CODE';
    /* VARIABLE VALUE FIELD NAMES */
    public const COMPANY_NAME              = 'COMPANY_NAME';
    public const DISCRETIONARY_DATA        = 'DISCRETIONARY_DATA';
    public const COMPANY_ID                = 'COMPANY_ID';
    public const STANDARD_ENTRY_CLASS_CODE = 'STANDARD_ENTRY_CLASS_CODE';
    public const COMPANY_ENTRY_DESCRIPTION = 'COMPANY_ENTRY_DESCRIPTION';
    public const COMPANY_DESCRIPTIVE_DATE  = 'COMPANY_DESCRIPTIVE_DATE';
    public const EFFECTIVE_ENTRY_DATE      = 'EFFECTIVE_ENTRY_DATE';
    public const ORIGINATING_DFI_ID        = 'ORIGINATING_DFI_ID';
    public const BATCH_NUMBER              = 'BATCH_NUMBER';

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
        self::DISCRETIONARY_DATA       => null,
        self::COMPANY_DESCRIPTIVE_DATE => null,
        self::EFFECTIVE_ENTRY_DATE     => null,
    ];

    // region VALID STANDARD ENTRY CLASS (SEC) CODES
    // Accounts Receivable Entry - This Standard Entry Class Code enables originators to convert to a Single Entry
    // ACH debit a consumer check received via the U.S. mail or at a drop-box location for the payment of goods or
    // services. The consumer’s source document (i.e., the check) is used to collect the consumer’s routing number,
    // account number, check serial number and dollar amount for the transaction.
    public const SEC_ARC = 'ARC';

    // Customer Initiated Entry - Customer Initiated Entries are limited to credit applications where the consumer
    // initiates the transfer of funds to a company for payment of funds owed to that company, typically through some
    // type of home banking product or bill payment service provider.
    public const SEC_CIE = 'CIE';

    // Machine Transfer Entry - The ACH Network supports the clearing of transactions from automated teller machines,
    // i.e., Machine Transfer Entries (MTE).
    public const SEC_MTE = 'MTE';

    // Consumer Cross-Border Payment - This Standard Entry Class Code is used for the transmission of consumer
    // cross-border ACH credit and debit entries. This SEC Code allows cross-border payments to be readily identified
    // so that financial institutions may apply special handling requirements for cross-border payments, as desired.
    // The PBR format accommodates detailed information unique to cross-border payments (e.g., foreign exchange
    // conversion, origination and destination currency, country codes, etc.).
    public const SEC_PBR = 'PBR';

    // Point-of-Purchase Entry - This ACH debit application is used by originators as a method of payment for the
    // in-person purchase of goods or services by consumers. These Single Entry debit entries are initiated by the
    // originator based on a written authorization and account information drawn from the source document (a check)
    // obtained from the consumer at the point-of-purchase. The source document, which is voided by the merchant and
    // returned to the consumer at the point-of-purchase, is used to collect the consumer’s routing number, account
    // number and check serial number that will be used to generate the debit entry to the consumer’s account.
    public const SEC_POP = 'POP';

    // Prearranged Payment & Deposit Entry -
    //  Direct Deposit (credit) - Direct deposit is a credit application that
    //      transfers funds into a consumer’s account at the Receiving Depository Financial Institution. The funds
    //      being deposited can represent a variety of products, such as payroll, interest, pension, dividends, etc.
    //  Pre-authorized Bill Payment (debit) - Pre-authorized payment is a debit application. Companies with billing
    //      operations may participate in the ACH through the electronic transfer (direct debit) of bill payment
    //      entries. Through standing authorizations, the consumer grants the company authority to initiate periodic
    //      charges to his or her account as bills become due. This concept has met with appreciable success in
    //      situations where the recurring bills are regular and do not vary in amount — insurance premiums, mortgage
    //      payments, and installment loan payments being the most prominent examples. Standing authorizations have
    //      also been successful for bills where the amount does vary, such as utility payments.
    public const SEC_PPD = 'PPD';

    // These two Standard Entry Class Codes represent point of sale debit applications in either a shared (SHR) or
    // non-shared (POS) environment. These transactions are most often initiated by the consumer via a plastic
    // access card.
    public const SEC_POS = 'POS';
    public const SEC_SHR = 'SHR';

    // Re-presented Check Entry - A Re-presented Check Entry is a Single Entry ACH debit application used by
    // originators to re-present a check that has been processed through the check collection system and returned
    // because of insufficient or uncollected funds. This method of collection via the ACH Network, compared to the
    // check collection process, provides originators with the potential for improvements to processing efficiency
    // (such as control over timing of the initiation of the debit entry) and decreased costs.
    public const SEC_RCK = 'RCK';

    // Telephone-Initiated Entry - This Standard Entry Class Code is used for the origination of a Single Entry debit
    // transaction to a consumer’s account pursuant to an oral authorization obtained from the consumer via the
    // telephone. This type of transaction may only be originated when there is either (1) an existing relationship
    // between the originator and the receiver, or (2) no existing relationship between the originator and the
    // receiver, but the receiver has initiated the telephone call. This SEC Code facilitates access to the ACH
    // Network by providing an alternative authorization method, oral authorization via the telephone, for certain
    // types of consumer debit entries.
    public const SEC_TEL = 'TEL';

    // Internet-Initiated Entry - This Standard Entry Class Code is used for the origination of debit entries (either
    // Single or Recurring Entry) to a consumer’s account pursuant to an authorization that is obtained from the
    // Receiver via the Internet. This SEC Code helps to address unique risk issues inherent to the Internet payment
    // environment through requirements for added security procedures and obligations.
    public const SEC_WEB = 'WEB';

    // Corporate Cross-Border Payment - This Standard Entry Class Code is used for the transmission of corporate
    // cross-border ACH credit and debit entries. This SEC Code allows cross-border payments to be readily identified
    // so that financial institutions may apply special handling requirements for cross-border payments, as desired.
    // The CBR format accommodates detailed information unique to cross-border payments (e.g., foreign exchange
    // conversion, origination and destination currency, country codes, etc.).
    public const SEC_CBR = 'CBR';

    // Cash Concentration or Disbursement - This application, Cash Concentration or Disbursement, can be either a
    // credit or debit application where funds are either distributed or consolidated between corporate entities.
    // This application can serve as a stand-alone funds transfer, or it can support a limited amount of payment
    // related data with the funds transfer.
    public const SEC_CCD = 'CCD';

    // Corporate Trade Exchange - The Corporate Trade Exchange application supports the transfer of funds (debit or
    // credit) within a trading partner relationship in which a full ANSI ASC X12 message or payment related UN/EDIFACT
    // information is sent with the funds transfer. The ANSI ASC X12 message or payment related UN/EDIFACT information
    // is placed in multiple addenda records.
    public const SEC_CTX = 'CTX';

    // Acknowledgment Entry - These optional Standard Entry Class Codes are available for use by the RDFI to
    // acknowledge the receipt of ACH credit payments originated using the CCD or CTX formats. These acknowledgments
    // indicate to the originator that the payment was received and that the RDFI will attempt to post the payment to
    // the Receiver’s account. Acknowledgment entries initiated in response to a CCD credit entry utilize the ACK
    // format. Acknowledgments initiated in response to a CTX credit entry utilize the ATX format.
    public const SEC_ACK = 'ACK';
    public const SEC_ATX = 'ATX';

    // Automated Accounting Advice - This Standard Entry Class Code represents an optional service to be provided by
    // ACH operators that identifies automated accounting advices of ACH accounting information in machine readable
    // format to facilitate the automation of accounting information for participating DFIs.
    public const SEC_ADV = 'ADV';

    // Automated Notification of Change or Refused Notification of Change - This Standard Entry Class Code is used by
    // an RDFI or ODFI when originating a Notification of Change or Refused Notification of Change in automated format.
    // It is also used by the ACH operator that converts paper Notifications of Change to automated format.
    public const SEC_COR = 'COR';

    // Death Notification Entry - This application is utilized by a federal government agency (e.g., Social Security
    // Administration) to notify a depository financial institution that the recipient of a government benefit
    // payment has died.
    public const SEC_DNE = 'DNE';

    // Automated Enrollment Entry - This optional SEC Code allows a depository financial institution to transmit ACH
    // enrollment information to federal government agencies via the ACH Network for future credit and debit
    // applications on behalf of both consumers and companies.
    public const SEC_ENR = 'ENR';

    // Truncated Entries - This Standard Entry Class Code is used to identify batches of truncated checks. For more
    // information on check truncation, please see the National Association for Check Safekeeping Guidelines available
    // from NACHA.
    public const SEC_TRC = 'TRC';

    // Truncated Entries - This Standard Entry Class Code is used to identify batches of truncated checks. For more
    // information on check truncation, please see the National Association for Check Safekeeping Guidelines available
    // from NACHA.
    public const SEC_TRX = 'TRX';

    // Destroyed Check Entry - This application can be utilized by a collecting institution for the collection of
    // certain checks when those checks have been destroyed.
    public const SEC_XCK = 'XCK';
    // endregion

    /**
     * @param string $input
     * @return BatchHeaderRecord
     * @throws ValidationException
     */
    public static function buildFromString($input): BatchHeaderRecord
    {
        $buildData                                 = self::getBuildDataFromInputString($input);
        $buildData[self::COMPANY_DESCRIPTIVE_DATE] = DateTime::createFromFormat('ymd', $buildData[self::COMPANY_DESCRIPTIVE_DATE]);
        $buildData[self::EFFECTIVE_ENTRY_DATE]     = DateTime::createFromFormat('ymd', $buildData[self::EFFECTIVE_ENTRY_DATE]);

        return new BatchHeaderRecord($buildData, false);
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
            self::RECORD_TYPE_CODE          => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1}$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 1,
                self::POSITION_END    => 1,
                self::CONTENT         => self::FIXED_RECORD_TYPE_CODE,
            ],
            self::SERVICE_CLASS_CODE        => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^(200|220|225)$/'],
                self::LENGTH          => 3,
                self::POSITION_START  => 2,
                self::POSITION_END    => 4,
                self::CONTENT         => self::MIXED_SERVICE_CLASS,
            ],
            self::COMPANY_NAME              => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{1,16}$/'],
                self::LENGTH          => 16,
                self::POSITION_START  => 5,
                self::POSITION_END    => 20,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::DISCRETIONARY_DATA        => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{0,20}$/'],
                self::LENGTH          => 20,
                self::POSITION_START  => 21,
                self::POSITION_END    => 40,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::COMPANY_ID                => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{10}$/'],
                self::LENGTH          => 10,
                self::POSITION_START  => 41,
                self::POSITION_END    => 50,
                self::CONTENT         => null,
            ],
            self::STANDARD_ENTRY_CLASS_CODE => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z]{3}$/'],
                self::LENGTH          => 3,
                self::POSITION_START  => 51,
                self::POSITION_END    => 53,
                self::CONTENT         => null,
            ],
            self::COMPANY_ENTRY_DESCRIPTION => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[a-zA-Z0-9 ]{1,10}$/'],
                self::LENGTH          => 10,
                self::POSITION_START  => 54,
                self::POSITION_END    => 63,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::COMPANY_DESCRIPTIVE_DATE  => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_DATETIME, 'ymd'],
                self::LENGTH          => 6,
                self::POSITION_START  => 64,
                self::POSITION_END    => 69,
                self::PADDING         => self::ALPHANUMERIC_PADDING,
                self::CONTENT         => null,
            ],
            self::EFFECTIVE_ENTRY_DATE      => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_REQUIRED,
                self::VALIDATOR       => [self::VALIDATOR_DATETIME, 'ymd'],
                self::LENGTH          => 6,
                self::POSITION_START  => 70,
                self::POSITION_END    => 75,
                self::CONTENT         => null,
            ],
            self::SETTLEMENT_DATE           => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^ {3}$/'],
                self::LENGTH          => 3,
                self::POSITION_START  => 76,
                self::POSITION_END    => 78,
                self::CONTENT         => self::FIXED_SETTLEMENT_DATE,
            ],
            self::ORIGINATOR_STATUS_CODE    => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{1}$/'],
                self::LENGTH          => 1,
                self::POSITION_START  => 79,
                self::POSITION_END    => 79,
                self::CONTENT         => self::FIXED_ORIGINATOR_STATUS_CODE,
            ],
            self::ORIGINATING_DFI_ID        => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^\d{8}$/'],
                self::LENGTH          => 8,
                self::POSITION_START  => 80,
                self::POSITION_END    => 87,
                self::CONTENT         => null,
            ],
            self::BATCH_NUMBER              => [
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

    /**
     * BatchHeader constructor.
     *
     * @param array $fields     is an array of field key => value pairs as follows:
     *                          [
     *                              // Required
     *                              SERVICE_CLASS_CODE        => One of MIXED_SERVICE_CLASS, CREDIT_SERVICE_CLASS, DEBIT_SERVICE_CLASS
     *                              COMPANY_NAME              => Alphanumeric string of length > 0 and <= 16
     *                              COMPANY_ID                => 10 digits
     *                              STANDARD_ENTRY_CLASS_CODE => 3 characters representing the entry format
     *                              COMPANY_ENTRY_DESCRIPTION => Alphanumeric string of length > 1 and <= 10 describing the purpose of the entry to the receiver
     *                              ORIGINATING_DFI_ID        => 8 digits representing where the file will be delivered for processing
     *                              BATCH_NUMBER              => 7 digits identifying the sequential order of this batch
     *                              // Optional
     *                              ENTRY_DATE_OVERRIDE       => DateTime object (default: current date)
     *                              DISCRETIONARY_DATA        => Alphanumeric string of length >= 0 and <= 20 (internal use)
     *                              COMPANY_DESCRIPTIVE_DATE  => DateTime object (default: Entry Date Override,
     *                          ]
     * @param bool  $validate
     * @throws \RW\ACH\ValidationException
     */
    public function __construct($fields, $validate = true)
    {
        if (!is_array($fields)) {
            throw new \InvalidArgumentException('fields argument must be of type array.');
        }

        // Add any missing optional fields, but preserve user-provided values for those that exist
        $fields = array_merge(self::OPTIONAL_FIELDS, $fields);

        // Apply basic modifications where required, and provide defaults for missing values where possible
        foreach ($fields as $k => $v) {
            switch ($k) {
                case self::EFFECTIVE_ENTRY_DATE:
                    // If an entry date was not provided, the override is null and the current date/time will be used
                    $entryDate                          = $v ?: new DateTime();
                    $fields[self::EFFECTIVE_ENTRY_DATE] = $entryDate->format('ymd');
                    break;
                case self::COMPANY_DESCRIPTIVE_DATE:
                    // This is optional, so it should be passed in explicitly - the default null value will otherwise
                    // be padded with spaces.

                    if (is_object($v) && get_class($v) === DateTime::class) {
                        /** @var DateTime $v */
                        $fields[self::COMPANY_DESCRIPTIVE_DATE] = $v->format('ymd');
                    }
                    break;
                case self::DISCRETIONARY_DATA:
                    $fields[self::DISCRETIONARY_DATA] = $v ?: self::DEFAULT_DISCRETIONARY_DATA;
            }
        }

        parent::__construct($fields, $validate);
    }
}
