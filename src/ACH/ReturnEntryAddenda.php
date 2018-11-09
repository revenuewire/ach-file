<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-14
 * Time: 10:32
 */

namespace RW\ACH;


use DateTime;

/**
 * Class ReturnEntryAddenda
 *
 * @package RW\ACH
 */
class ReturnEntryAddenda extends AddendaRecord
{
    public const RETURN_REASON_CODE  = 'RETURN_REASON_CODE';
    public const DATE_OF_DEATH       = 'DATE_OF_DEATH';
    public const ADDENDA_INFORMATION = 'ADDENDA_INFORMATION';

    /* RETURN REASON CODES */
    // Generic code to use as a catch all if the provided code is not found in this list
    public const RXX = [self::CODE => 'RXX', self::NOTE => 'Unrecognized return code'];
    // Available balance is not sufficient to cover the amount of the debit entry
    public const R01 = [self::CODE => 'R01', self::NOTE => 'Insufficient funds'];
    // Previously active amount has been closed by the customer of RDFI
    public const R02 = [self::CODE => 'R02', self::NOTE => 'Bank account closed'];
    // Account number does not correspond to the individual identified in the entry, or the account number designated
    // is not an open account
    public const R03 = [self::CODE => 'R03', self::NOTE => 'No bank account/unable to locate account'];
    // Account number structure is not valid
    public const R04 = [self::CODE => 'R04', self::NOTE => 'Invalid bank account number'];
    // CCD or CTX debit entry was transmitted to a consumer account of the receiver and was not authorized by the
    // receiver
    public const R05 = [self::CODE => 'R05', self::NOTE => 'Unauthorized debit to consumer account using corporate SEC code'];
    // ODFI requested the RDFI to return the entry
    public const R06 = [self::CODE => 'R06', self::NOTE => 'Returned per ODFI request'];
    // Receiver has revoked authorization
    public const R07 = [self::CODE => 'R07', self::NOTE => 'Authorization revoked by customer'];
    // Receiver of a recurring debit has stopped payment of an entry
    public const R08 = [self::CODE => 'R08', self::NOTE => 'Payment stopped'];
    // Collected funds are not sufficient for payment of the debit entry
    public const R09 = [self::CODE => 'R09', self::NOTE => 'Uncollected funds'];
    // Receiver has advised RDFI that originator is not authorized to debit his bank account
    public const R10 = [self::CODE => 'R10', self::NOTE => 'Customer advises not authorized'];
    // To be used when returning a check truncation entry
    public const R11 = [self::CODE => 'R11', self::NOTE => 'Check truncation entry return'];
    // RDFI unable to post entry destined for a bank account maintained at a branch sold to another financial
    // institution
    public const R12 = [self::CODE => 'R12', self::NOTE => 'Branch sold to another RDFI'];
    // Financial institution does not receive commercial ACH entries
    public const R13 = [self::CODE => 'R13', self::NOTE => 'RDFI not qualified to participate'];
    // The representative payee authorized to accept entries on behalf of a beneficiary is either deceased or unable
    // to continue in that capacity
    public const R14 = [self::CODE => 'R14', self::NOTE => 'Representative payee deceased or unable to continue in that capacity'];
    // (Other than representative payee) deceased* - (1) the beneficiary entitled to payments is deceased or (2) the
    // bank account holder other than a representative payee is deceased
    public const R15 = [self::CODE => 'R15', self::NOTE => 'Beneficiary or bank account holder'];
    // Funds in bank account are unavailable due to action by RDFI or legal order
    public const R16 = [self::CODE => 'R16', self::NOTE => 'Bank account frozen'];
    // Fields rejected by RDFI processing (identified in return addenda)
    public const R17 = [self::CODE => 'R17', self::NOTE => 'File record edit criteria'];
    // Entries have been presented prior to the first available processing window for the effective date.
    public const R18 = [self::CODE => 'R18', self::NOTE => 'Improper effective entry date'];
    // Improper formatting of the amount field
    public const R19 = [self::CODE => 'R19', self::NOTE => 'Amount field error'];
    // Entry destined for non-payment bank account defined by reg.
    public const R20 = [self::CODE => 'R20', self::NOTE => 'Non-payment bank account'];
    // The company ID information not valid (normally CIE entries)
    public const R21 = [self::CODE => 'R21', self::NOTE => 'Invalid company ID number'];
    // Individual id used by receiver is incorrect (CIE entries)
    public const R22 = [self::CODE => 'R22', self::NOTE => 'Invalid individual ID number'];
    // Receiver returned entry because minimum or exact amount not remitted, bank account is subject to litigation, or
    // payment represents an overpayment, originator is not known to receiver or receiver has not authorized this
    // credit entry to this bank account
    public const R23 = [self::CODE => 'R23', self::NOTE => 'Credit entry refused by receiver'];
    // RDFI has received a duplicate entry
    public const R24 = [self::CODE => 'R24', self::NOTE => 'Duplicate entry'];
    // Improper formatting of the addenda record information
    public const R25 = [self::CODE => 'R25', self::NOTE => 'Addenda error'];
    // Improper information in one of the mandatory fields
    public const R26 = [self::CODE => 'R26', self::NOTE => 'Mandatory field error'];
    // Original entry trace number is not valid for return entry; or addenda trace numbers do not correspond with entry
    // detail record
    public const R27 = [self::CODE => 'R27', self::NOTE => 'Trace number error'];
    // Check digit for the transit routing number is incorrect
    public const R28 = [self::CODE => 'R28', self::NOTE => 'Transit routing number check digit error'];
    // RDFI has bee notified by corporate receiver that debit entry of originator is not authorized
    public const R29 = [self::CODE => 'R29', self::NOTE => 'Corporate customer advises not authorized'];
    // Financial institution not participating in automated check safekeeping application
    public const R30 = [self::CODE => 'R30', self::NOTE => 'RDFI not participant in check truncation program'];
    // RDFI has been notified by the ODFI that it agrees to accept a CCD or CTX return entry
    public const R31 = [self::CODE => 'R31', self::NOTE => 'Permissible return entry (CCD and CTX only)'];
    // RDFI is not able to settle the entry
    public const R32 = [self::CODE => 'R32', self::NOTE => 'RDFI non-settlement'];
    // RDFI determines at its sole discretion to return an XCK entry; an XCK return entry may be initiated by midnight
    // of the sixtieth day following the settlement date if the XCK entry
    public const R33 = [self::CODE => 'R33', self::NOTE => 'Return of XCK entry'];
    // RDFI participation has been limited by a federal or state supervisor
    public const R34 = [self::CODE => 'R34', self::NOTE => 'Limited participation RDFI'];
    // ACH debit not permitted for use with the CIE standard entry class code (except for reversals)
    public const R35 = [self::CODE => 'R35', self::NOTE => 'Return of improper debit entry'];
    // ACH credit entries (except for reversing entries) are not permitted for use with ARC, BOC, POP< RCK, TEL, WEB
    // and XCK formats
    public const R36 = [self::CODE => 'R36', self::NOTE => 'Return of improper credit entry'];
    // The source document to which an ARC, BOC, or POP entry relates has been presented for payment
    public const R37 = [self::CODE => 'R37', self::NOTE => 'Source document presented for payment'];
    // A stop payment order has been placed on the source document to which the ARC or BOC entry relates
    public const R38 = [self::CODE => 'R38', self::NOTE => 'Stop payment on source document'];
    // The source document used for an ARC, BOC, or POP entry to it's receiver's account is improper, OR an ARC, BOC,
    // or POP entry and the source document to which the entry relates have both been presented for payment and posted
    // to the receiver's account
    public const R39 = [self::CODE => 'R39', self::NOTE => 'Improper source document OR source document presented for payment'];

    /* RETURN REASON CODES FOR USE BY FEDERAL GOVERNMENT AGENCIES RETURNING ENR ENTRIES */
    public const R40 = [self::CODE => 'R40', self::NOTE => 'Return of ENR entry by federal government agency'];
    public const R41 = [self::CODE => 'R41', self::NOTE => 'Invalid transaction code'];
    public const R42 = [self::CODE => 'R42', self::NOTE => 'Routing number or check digit error'];
    public const R43 = [self::CODE => 'R43', self::NOTE => 'Invalid DFI account number'];
    public const R44 = [self::CODE => 'R44', self::NOTE => 'Invalid individual ID number'];
    public const R45 = [self::CODE => 'R45', self::NOTE => 'Invalid individual name/company name'];
    public const R46 = [self::CODE => 'R46', self::NOTE => 'Invalid representative payee indicator'];
    public const R47 = [self::CODE => 'R47', self::NOTE => 'Duplicate enrollment'];

    /* RETURN REASON CODES TO BE USED FOR THE RETURN OF RCK ENTRIES */
    public const R50 = [self::CODE => 'R50', self::NOTE => 'State law affecting RCK acceptance'];
    public const R51 = [self::CODE => 'R51', self::NOTE => 'Item is ineligible, notice not provided, signature not genuine'];
    public const R52 = [self::CODE => 'R52', self::NOTE => 'Stop payment on item'];

    /* RETURN REASON CODES TO BE USED BY THE ODFI FOR DISHONORED RETURN ENTRIES */
    public const R61 = [self::CODE => 'R61', self::NOTE => 'Mis-routed return'];
    public const R62 = [self::CODE => 'R62', self::NOTE => 'Incorrect trace number'];
    public const R63 = [self::CODE => 'R63', self::NOTE => 'Incorrect dollar amount'];
    public const R64 = [self::CODE => 'R64', self::NOTE => 'Incorrect individual identification'];
    public const R65 = [self::CODE => 'R65', self::NOTE => 'Incorrect transaction code'];
    public const R66 = [self::CODE => 'R66', self::NOTE => 'Incorrect company identification'];
    public const R67 = [self::CODE => 'R67', self::NOTE => 'Duplicate return'];
    public const R68 = [self::CODE => 'R68', self::NOTE => 'Untimely return'];
    public const R69 = [self::CODE => 'R69', self::NOTE => 'Multiple errors'];
    public const R70 = [self::CODE => 'R70', self::NOTE => 'Permissible return entry not accepted'];

    /* RETURN REASON CODES TO BE USED BY GATEWAYS FOR THE RETURN OF INTERNATIONAL PAYMENTS */
    public const R71 = [self::CODE => 'R71', self::NOTE => 'Mis-routed dishonored return'];
    public const R72 = [self::CODE => 'R72', self::NOTE => 'Untimely dishonored return'];
    public const R73 = [self::CODE => 'R73', self::NOTE => 'Timely original return'];
    public const R74 = [self::CODE => 'R74', self::NOTE => 'Corrected return'];
    public const R80 = [self::CODE => 'R80', self::NOTE => 'Cross-border payment coding error'];
    public const R81 = [self::CODE => 'R81', self::NOTE => 'Nonparticipant in cross-border program'];
    public const R82 = [self::CODE => 'R82', self::NOTE => 'Invalid foreign receiving DFI identification'];
    public const R83 = [self::CODE => 'R83', self::NOTE => 'Foreign receiving DFI unable to settle'];
    public const R84 = [self::CODE => 'R84', self::NOTE => 'Entry not processed by gateway'];
    public const R85 = [self::CODE => 'R85', self::NOTE => 'Incorrectly coded outbound international payment'];

    /**
     * Build a Return addenda record from an existing string.
     *
     * @param string $input
     * @return ReturnEntryAddenda
     * @throws ValidationException
     */
    public static function buildFromString($input): self
    {
        $buildData                      = self::getBuildDataFromInputString($input);
        $buildData[self::DATE_OF_DEATH] = DateTime::createFromFormat('ymd', $buildData[self::DATE_OF_DEATH]);

        return new ReturnEntryAddenda($buildData, false);
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
            self::RETURN_REASON_CODE => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_MANDATORY,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^R\d{2}$/'],
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
            self::DATE_OF_DEATH => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_DATETIME, 'ymd'],
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
            self::ADDENDA_INFORMATION => [
                self::FIELD_INCLUSION => self::FIELD_INCLUSION_OPTIONAL,
                self::VALIDATOR       => [self::VALIDATOR_REGEX, '/^[-a-zA-Z0-9 ]{1,44}$/'],
                self::LENGTH          => 44,
                self::POSITION_START  => 36,
                self::POSITION_END    => 79,
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
