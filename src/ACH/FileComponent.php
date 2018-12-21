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
    public const    DEBIT                  = 'DEBIT';
    public const    CREDIT                 = 'CREDIT';
    protected const VALID_OPTIONAL_VALUES  = ['', false, null];
    protected const FIXED_RECORD_TYPE_CODE = null;
    private const   REJECT_INDICATOR       = 'REJ0';

    /* REJECT CODES */
    // These codes are part of the abstract class because a file level reject code might appear on all records
    public const REJECT_CODES = [
        0001 => 'No endpoints for destination ID',
        0010 => 'Duplicate file or batch',
        0020 => 'Failed ACH edit',
        1020 => 'Rejected in pre-edit',
        1030 => 'File rejected for control errors in pre-edit',
        1040 => 'File rejected due to control errors',
        1050 => 'File rejected due to file identification errors',
        1060 => 'Rejected due to duplicate presence',
        1070 => 'Reject due to risk',
        1505 => 'Immediate destination not found',
        1507 => 'Immediate destination is not numeric',
        1510 => 'File ID was modified according to customer setup',
        1512 => 'Special rules expiration date has passed',
        1520 => 'File ID modifier contained a space',
        1525 => 'File ID modifier is not alphanumeric as required by Fed',
        1530 => 'Create date was non-numeric or zero',
        1540 => 'Create date contained incorrect format',
        1550 => 'Priority code was not numeric',
        1560 => 'Immediate destination name was spaces',
        1561 => 'Immediate origin name is spaces',
        1570 => 'Record length not 94 bytes (94 characters)',
        1580 => 'Record length was not numeric',
        1590 => 'Blocking factor not 10',
        1600 => 'Blocking factor was not numeric',
        1610 => 'Format code was not 1',
        5010 => 'Batch rejected due to edits in pre-edit',
        5020 => 'File rejected due to company identification errors',
        5030 => 'Batch rejected – descriptive date was spaces for a MTE, POS or SHR',
        5040 => 'Batch rejected – class code of COR sent from originator',
        5070 => 'Batch contained invalid entry class; forced to PPD',
        5071 => 'Batch rejected - invalid SEC code',
        5072 => 'Batch rejected - SEC code excluded specifically',
        5080 => 'Reject credits – foreign exchange indicator and foreign exchange reference indicator conflict',
        5082 => 'Reject credits – unknown foreign exchange indicator on CBR or PBR batch',
        5090 => 'Batch rejected – class code DNE sent from originator',
        5101 => 'IAT – batch rejected – invalid foreign exchange indicator',
        5102 => 'IAT – batch rejected – invalid ISO destination country code',
        5103 => 'IAT – customer with ACF IAT setup, contains invalid class code',
        5104 => 'IAT – batch rejected – customer not set up on ACF for IAT',
        5105 => 'IAT – batch rejected – invalid ISO origination currency code',
        5106 => 'IAT – batch rejected – invalid ISO destination currency code',
        5107 => 'IAT – batch rejected – IATCOR without class code set to COR',
        5108 => 'IAT – batch rejected – foreign exchange reference indicator and foreign exchange reference conflict',
        5510 => 'Batch contained invalid service class code',
        5512 => 'Service class code did not match customer file',
        5515 => 'Service class code was not numeric',
        5522 => 'ACF special rules expiration date expired',
        5530 => 'Company identification was modified in pre-edit',
        5540 => 'Originating DFI is not a Wells Fargo originator',
        5550 => 'Originating DFI was not found',
        5560 => 'Originating DFI was not numeric',
        5570 => 'Posting date was changed in pre-edit process',
        5580 => 'Posting date was in invalid format',
        5590 => 'Originating status code was not valid',
        5600 => 'Entry description area was all spaces',
        5605 => 'Entry description area was not alphanumeric as required by Fed',
        5606 => 'RCK entry class has invalid entry description',
        5607 => 'POP entry class has invalid entry description',
        5608 => 'PPD entry class cannot have REDEPCHECK in entry description',
        5610 => 'Originating company name was not all alphanumeric',
        5615 => 'Originating company name was all spaces',
        5620 => 'Descriptive date was spaces for a MTE, POS, or SHR',
        5630 => 'Batch file number was not numeric',
        5650 => 'Batch sequence number was not numeric',
        5660 => 'Foreign exchange indicator is fixed to fixed but related fields differ',
        5662 => 'Foreign exchange reference indicator is fixed to fixed but related fields differ',
        5664 => 'Unknown foreign exchange indicator; changed to fixed to fixed',
        5670 => 'Foreign exchange reference contains non-alphanumeric data',
        5675 => 'ISO destination country code contains non-alphanumeric data',
        5680 => 'ISO originating currency code contains non-alphanumeric data',
        5685 => 'ISO destination currency code contains non-alphanumeric data',
        5701 => 'IAT entry class has missing IAT indicator',
        5702 => 'IAT entry class has missing foreign exchange reference indicator',
        5703 => 'IAT entry class has missing foreign exchange reference',
        6001 => 'Receiving DFI ID not found',
        6002 => 'Addenda record indicator is non-numeric',
        6003 => 'Addenda record indicator not 1 or 0 on a received file',
        6004 => 'Addenda record indicator is 1, but addenda record does not follow',
        6005 => 'Transaction code is invalid – changed to a demand credit (22), same as previous detail record',
        6006 => 'Transaction code is invalid – changed to a demand debit (27), same as previous detail record',
        6007 => 'Amount is not zero for a prenote',
        6008 => 'Amount is zero for a non-prenote',
        6009 => 'Amount field is non-numeric',
        6010 => 'Invalid characters found in account number field',
        6011 => 'CCD – transaction code is a return type',
        6012 => 'Individual ID number is spaces or zeroes',
        6014 => 'CTX – number of special addenda not equal to accumulated addenda records',
        6015 => 'MTE – addenda record indicator is not 1',
        6016 => 'MTE – individual name is spaces',
        6017 => 'MTE – individual ID number is spaces or zeroes',
        6018 => 'MTE – record after 6 record is not a 7 record',
        6019 => 'Item rejected – card type transaction code is zeroes for POS class',
        6020 => 'Item rejected – card type transaction code was not alphameric for POS class',
        6021 => 'POS – record after 6 record is not a 7 record',
        6022 => 'SHR – item rejected, card type transaction code was not numeric',
        6023 => 'SHR – record after 6 record is not a 7 record',
        6024 => 'PPD – transaction code cannot be a return',
        6025 => 'COR – transaction code not a return',
        6026 => 'COR – dollar amount is not numeric',
        6027 => 'COR – dollar amount is greater than zero',
        6029 => 'MICR item amount is not greater than zero',
        6030 => 'Receiving DFI ID not within range',
        6031 => 'Invalid transaction code – changed to a savings credit (32), same as previous detail record',
        6032 => 'Invalid transaction code – changed to a savings debit (37), same as previous detail record',
        6033 => 'Invalid transaction code – forced to (27) because transaction code for previous record not known',
        6034 => 'Individual ID number cannot be spaces or zeros for CIE',
        6035 => 'Invalid character(s) found in individual ID field',
        6036 => 'Invalid character(s) found in individual name field',
        6037 => 'Invalid characters found in discretionary data field (CCD, PPD, CIE, MTE)',
        6038 => 'Invalid discretionary data value',
        6039 => 'Invalid characters found in individual name',
        6040 => 'Invalid characters found in sending company audit field (CTX)',
        6041 => 'Invalid characters found in receiving company name/ID (CTX)',
        6042 => 'Invalid characters found in discretionary data field (CTX)',
        6043 => 'Invalid characters found in card expiration date',
        6044 => 'Invalid characters found in document reference number',
        6045 => 'SHR – card type transaction code was not numeric',
        6046 => 'Original trace sequence number is not numeric or is zero',
        6047 => 'Original trace sequence number not in ascending order',
        6048 => 'Original trace RT not equal 09100001',
        6049 => 'Item rejected - blocked routing transit number',
        6050 => 'Created prenote was rejected because related item reject',
        6051 => 'DNE transaction is not in prenote format',
        6052 => 'DNE item does not contain an addenda',
        6053 => 'DNE items can have only one addenda',
        6054 => 'Receiving DFI not found',
        6055 => 'Invalid character in receiving DFI',
        6056 => 'Transaction code not numeric',
        6057 => 'Transaction code not valid',
        6058 => 'GL/Recon Plus transaction code not valid',
        6059 => 'Remittance transaction contains non-zero dollar amount',
        6060 => 'Remittance transaction cannot be used with current class code',
        6061 => 'Transaction code must be credit remittance transaction',
        6062 => 'Transaction requires an addenda',
        6063 => 'Transaction requires “REVERSAL” in the entry description on the 5 record',
        6064 => 'G/L transaction account missing source code preceded by an asterisk (*)',
        6065 => 'G/L transactions require at least 9 digits prior to the asterisk (*) and source',
        6066 => 'G/L transaction was classified as MICR; cannot process G/L transaction',
        6070 => 'CBR/PBR – record after 6 record is not a 7 record',
        6071 => 'CBR/PBR – transaction code cannot be a return',
        6072 => 'Serial number must be supplied',
        6073 => 'Credit rejected – batch 5 record has invalid data in international data fields',
        6075 => 'G/L transaction - RT must be GL RT if tran code is GL tran code',
        6076 => 'G/L transaction - tran code must be GL tran code if RT is GL RT',
        6077 => 'G/L transaction - AU portion of account must be numeric',
        6078 => 'G/L transaction - GL Acct portion of account must be numeric',
        6079 => 'G/L transaction - source portion of account can only contain A-Z or 0-9',
        6080 => 'POP – transaction code cannot be a return transaction code',
        6081 => 'POP – invalid serial number/city/state',
        6082 => 'SEC code is invalid for a credit item',
        6090 => 'Transaction code cannot be a return transaction code',
        6091 => 'Invalid or blank entry description',
        6092 => 'RCK – transaction exceeds maximum dollar amount',
        6093 => 'WEB/TEL – transaction code cannot be a return',
        6094 => 'WEB/TEL – invalid credit transaction',
        6100 => 'Check conversion not allowed for this routing/transit number',
        6101 => 'Check conversion – account number exceeds allowed length for this routing/transit number',
        6102 => 'Check conversion – check serial number field exceeds maximum of five digits',
        6103 => 'ARC/BOC/POP transactions over $25,000',
        6104 => 'IAT – invalid or missing number of addenda records',
        6105 => 'IAT – missing all mandatory addenda records for origination',
        6106 => 'IAT – missing all mandatory addenda records for a return',
        6107 => 'IAT – invalid account number',
        6108 => 'IAT – missing required remittance (717) addenda on ARC, BOC, RCK or POP',
        6109 => 'IAT – RTN must be 391001268 for international transactions',
        6110 => 'IAT – number of addenda records field is non-numeric',
        6111 => 'IAT – number of addenda records field does not match number of addenda records with item',
        6112 => 'IAT – transaction contains no addenda records',
        6113 => 'IAT – transaction contains more than two 717 type addenda records',
        6114 => 'IAT – transaction contains more than a total of five 717 and 718 addenda',
        6115 => 'Not authorized for international ACH transactions',
        6501 => 'Addenda indicator not 1 or 0',
        6502 => 'Addenda indicator not 1 – overridden on originated file',
        6503 => 'Trace number not numeric – override with bank control routing/transit number for origination file',
        6504 => 'Trace number does not equal routing/transit number on 5 record',
        6505 => 'Check digit is not numeric on originated file',
        6506 => 'Check digit invalid on originated file',
        6507 => 'File level item limit exceeded',
        6508 => 'Company level item limit exceeded',
        6509 => 'Transaction code is invalid for service class code of 200',
        6510 => 'Transaction code is invalid for service class code of 225',
        6511 => 'Receiving company name/ID number is not left-justified',
        6512 => 'CTX – receiving company name/ID number is not left-justified',
        6513 => 'POS – addenda record indicator not 1',
        6514 => 'SHR – addenda record indicator not 1',
        6515 => 'Addenda indicator not 0 – overridden on originated file',
        6516 => 'Addenda record indicator must be 1 for MTE class code',
        6517 => 'Invalid account number – contains only spaces, slashes, zeros, or dashes',
        6518 => 'Account number is not left-justified',
        6520 => 'Transaction doesn’t qualify as zero dollar transaction – prenote forced',
        6530 => 'Account number on both AA addenda and detail record',
        6531 => 'Detail account number existed and AA addenda existed without account number',
        6532 => 'Discretionary data of 01 and AA addenda existed but no account number found',
        6533 => 'Discretionary data of 01, account number missing from detail; no AA addenda',
        6534 => 'Discretionary data of 01, account number missing from detail; no addenda record',
        6535 => 'Discretionary data 01 forced because of AA addenda',
        6536 => 'Discretionary data 01 forced because of 02 addenda',
        6537 => 'WEB – Position 77 must be R or S',
        6538 => 'Found R or S in position 78, moved to position 77',
        6551 => 'IAT – invalid OFAC screening indicator',
        6552 => 'IAT – invalid secondary OFAC screening indicator',
        6553 => 'IAT – entry detail record – reserved area has data in position 17-29',
        6554 => 'CIE - debit transaction are not allowed',
        7001 => 'CCD – addenda type code 01 on a received file',
        7002 => 'CCD – addenda type code not 01 or 05',
        7003 => 'CCD – more than one addenda record follows a 6 record',
        7004 => 'CCD – entry detail sequence number not equal to last 7 digits of 6 record trace number',
        7005 => 'CIE – addenda type code not 01 or 05',
        7018 => 'CTX – addenda type code not 05',
        7019 => 'CTX – addenda sequence number is non-numeric, overridden',
        7020 => 'CTX – entry detail sequence number not equal to 6 record trace number (last 7 characters)',
        7021 => 'CTX – addenda sequence number not is ascending order, overridden',
        7022 => 'MTE – addenda type not 02',
        7023 => 'MTE – entry detail sequence number not equal to 6 record trace number (last 7 characters)',
        7024 => 'POS – entry detail sequence number not equal to 6 record trace number (last 7 characters)',
        7025 => 'SHR – entry detail sequence number not equal to 6 record trace number (last 7 characters)',
        7026 => 'PPD – addenda type code 01 (originated file)',
        7027 => 'PPD – addenda type code not 01 or 05',
        7028 => 'PPD – more than 1 addenda record follows a 6 record',
        7029 => 'COR – addenda type code is not 98',
        7030 => 'COR – change code is spaces or zeros',
        7031 => 'COR – invalid change code',
        7032 => 'COR – corrected data is zeros or spaces',
        7033 => 'COR – original entry trace number is zeros',
        7034 => 'COR – original entry trace number not numeric',
        7035 => 'COR – routing/transit number is not numeric',
        7036 => 'COR – routing/transit number is zeros',
        7037 => 'Addenda type code not valid for return/NOC',
        7038 => 'COR - IAT NOC cannot be C03, must be C01 or C02',
        7059 => 'Addenda type not 01, 02, 03, 04, 05, 98, 99, or AA',
        7060 => 'Total number of addenda per detail is greater than 9999',
        7061 => 'Addenda type code not found',
        7062 => 'COR – (refused NOC) change code is spaces or zeros',
        7063 => 'COR – (refused NOC) COR trace sequence number is not numeric',
        7064 => 'COR – (refused NOC) COR trace sequence number is zeros',
        7065 => 'C01 – incorrectly formatted DFI account number in corrected data field',
        7066 => 'C02 – incorrectly formatted routing/transit number in corrected data field',
        7067 => 'C03 – incorrect routing/transit and account number; invalid corrected data format',
        7069 => 'C05 – incorrectly formatted transaction code in corrected data field',
        7070 => 'C06 – incorrect account number and transaction code in corrected data field',
        7071 => 'C07 – incorrect routing/transit number, account number, and transaction code – invalid corrected data format',
        7072 => 'C09 – incorrect individual ID; SEC code must be CIE, MTE, POS, or COR',
        7073 => 'C09 – incorrect individual ID; invalid format in corrected data field',
        7080 => 'DNE – addenda does not have required 05 addenda type',
        7090 => 'CBR/PBR – addenda type code is not 01',
        7091 => 'CBR/PBR – addenda type code is not 01',
        7092 => 'CBR/PBR – more than 1 addenda record follows a 6 record',
        7100 => 'POP – addenda record is not allowed',
        7110 => 'RCK – addenda record not allowed',
        7119 => 'IAT – 710 addenda – transaction type code invalid',
        7120 => 'IAT – 710 addenda – invalid foreign payment amount',
        7121 => 'IAT – 710 addenda – invalid foreign trace number',
        7122 => 'IAT – 710 addenda – invalid receiving company name',
        7130 => 'IAT – 711 addenda – invalid originator name',
        7131 => 'IAT – 711 addenda - invalid originator address – blank',
        7132 => 'IAT – 711 addenda – invalid originator address – post office box',
        7140 => 'IAT – 712 addenda – invalid originator city and state',
        7141 => 'IAT – 712 addenda – invalid originator country and postal code',
        7150 => 'IAT – 713 addenda – invalid originating DFI name',
        7151 => 'IAT – 713 addenda – invalid originating DFI identification number qualifier',
        7152 => 'IAT – 713 addenda – invalid originating DFI identification',
        7153 => 'IAT – 713 addenda – invalid originating DFI branch country code',
        7160 => 'IAT – 714 addenda – invalid receiving DFI name',
        7161 => 'IAT – 714 addenda – invalid receiving DFI identification number qualifier',
        7162 => 'IAT – 714 addenda – invalid receiving DFI identification',
        7163 => 'IAT – 714 addenda – invalid receiving DFI branch country code',
        7170 => 'IAT – 715 addenda – invalid receiver identification number',
        7171 => 'IAT – 715 addenda – invalid receiver street address',
        7180 => 'IAT – 716 addenda – invalid receiver city and state',
        7181 => 'IAT – 716 addenda – invalid receiver country and postal code',
        7190 => 'IAT – 717 addenda – invalid payment related info',
        7191 => 'IAT – 717 addenda – invalid addenda sequence number',
        7192 => 'IAT – 717 addenda – invalid check serial number',
        7193 => 'IAT – 717 addenda – invalid check serial number and terminal city, state',
        7200 => 'IAT – 718 addenda – invalid correspondent bank name',
        7201 => 'IAT – 718 addenda – invalid correspondent bank ID qualifier',
        7202 => 'IAT – 718 addenda – invalid correspondent bank ID',
        7203 => 'IAT – 718 addenda – invalid correspondent bank branch country code',
        7204 => 'IAT – 718 addenda – invalid addenda sequence number',
        7210 => 'IAT – 799 addenda – invalid original forward payment amount',
        7211 => 'IAT – 799 addenda – invalid addenda information',
        7212 => 'IAT – 799 addenda – invalid original trace',
        7213 => 'IAT – 799 addenda – invalid original routing transit',
        7501 => 'CCD – addenda type code 01 on originated file, overridden to 05',
        7502 => 'CCD – addenda type code 01 on received file',
        7503 => 'CCD – addenda sequence number is non-numeric or all zeros, overridden',
        7504 => 'CIE – addenda type code 01, overridden',
        7505 => 'MTE – transaction description is blank',
        7506 => 'MTE – record terminal ID code is blank',
        7507 => 'MTE – transaction serial number is blank',
        7508 => 'MTE – transaction date is not numeric',
        7509 => 'MTE – transaction time is not numeric',
        7510 => 'MTE – record terminal location is blank',
        7511 => 'MTE – record terminal city is blank',
        7512 => 'MTE – record terminal state is blank',
        7513 => 'POS – record terminal ID code is blank',
        7514 => 'POS – transaction serial number is blank',
        7515 => 'POS – record terminal location is blank',
        7516 => 'POS – transaction date is not numeric',
        7517 => 'POS – record terminal city is blank',
        7518 => 'POS – record terminal state is blank',
        7519 => 'SHR – record terminal ID code is blank',
        7520 => 'SHR – transaction serial number is blank',
        7521 => 'SHR – record terminal location is blank',
        7522 => 'SHR – transaction date is not numeric',
        7523 => 'SHR – record terminal city is blank',
        7524 => 'SHR – record terminal state is blank',
        7525 => 'PPD – addenda type code 01 on an originated file',
        7526 => 'PPD – addenda sequence number is non-numeric or all zeros, overridden',
        7527 => 'PPD – entry detail sequence number not equal to last 7 digits of 6 record trace number',
        7528 => 'COR – trace number is not numeric',
        7529 => 'COR – trace number is zeros',
        7530 => 'CBR/PBR – addenda sequence number is non-numeric or all zeros, overridden',
        7531 => 'WEB - individual name cannot be blank or zeroes',
        7532 => 'TEL - individual name cannot be blank or zeroes',
        7540 => 'POP – addenda sequence number is non-numeric or all zeros, overridden',
        7541 => 'POP – addenda type code 01 on an originated file',
        7550 => 'RCK – addenda sequence number is non-numeric or all zeros, overridden',
        7551 => 'RCK – addenda type code is 01 on an originated file',
        7552 => 'TEL – addenda record is not allowed',
        7553 => 'WEB – addenda type code is not 05',
        7554 => 'WEB – addenda type code is not numeric',
        7555 => 'WEB – addenda sequence number is non-numeric or all zeros',
        7556 => 'WEB – more than one addenda record follows a 6 record',
        8500 => 'Service class code invalid',
        8501 => 'Service class code not equal to 5 record service class code',
        8502 => 'Service class code not numeric',
        8503 => 'Entry/addenda count not equal to actual accumulated',
        8504 => 'Entry/addenda count not numeric',
        8505 => 'Entry hash total not equal to accumulated 6 record totals of routing/transitnumbers',
        8506 => 'Entry hash totals not numeric',
        8507 => 'Total debit entry dollar amount not equal to 6 record accumulation',
        8508 => 'Total debit entry dollar amount not numeric',
        8509 => 'Total credit entry dollar amount not equal to 6 record accumulation',
        8510 => 'Total credit entry dollar amount not numeric',
        8511 => 'Company ID not equal to 5 record company ID',
        8512 => 'Batch number not equal to 5 record batch number',
        8513 => 'Batch number not numeric',
        8514 => 'Batch amount exceeds company level processing limit',
        8515 => 'Debit only class contained credits; overridden to 200',
        8516 => 'Credit only class contained debits; overridden to 200',
        8530 => 'Batch did not contain an 8 record',
        9500 => 'Batch count not equal to 8 record accumulated count',
        9501 => 'Batch count not numeric',
        9502 => 'Block count not numeric',
        9503 => 'Entry/addenda count not equal to 8 record count accumulated',
        9504 => 'Entry/addenda count not numeric',
        9505 => 'Entry hash total not equal to 8 record hash accumulation',
        9506 => 'Entry hash total not numeric',
        9507 => 'Total debit entry dollar amount not equal to 8 record totals',
        9508 => 'Total debit entry dollar amount not numeric',
        9509 => 'Total credit entry dollar amount not equal to 8 record totals',
        9510 => 'Total credit entry dollar amount not numeric',
        9511 => 'Total number of invalid account numbers is greater than 50%',
        9512 => 'File amount exceeds file reporting debit or credit limits',
        9530 => 'File did not contain 9 record',
    ];

    // Field values and validation data
    protected $fieldSpecifications;

    private $validate;

    /**
     * Generate the field specifications for each field in the file component. This should always be merging the
     * parent specifications with the child specifications to avoid duplication of re-used fields.
     *
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
    abstract protected static function getFieldSpecifications(): array;

    /**
     * Override this method if the constructor for your component is overloaded, or data needs to be manipulated
     * to meet the submission style chosen for the component.
     *
     * @param string $input 94 character fixed-width ACH Record string
     * @return static
     * @throws ValidationException
     */
    public static function buildFromString($input)
    {
        $buildData = self::getBuildDataFromInputString($input);

        return new static($buildData, false);
    }

    /**
     * @return bool
     */
    public function isRejected()
    {
        // Reject indicator is always at position 80-87 (eg REJ0xxxx, where xxxx is the first reject code triggered)
        return mb_substr($this->toString(), 79, 4) === self::REJECT_INDICATOR;
    }

    /**
     * @return string|null
     */
    public function getRejectCode()
    {
        return $this->isRejected() ? mb_substr($this->toString(), 83, 4) : null;
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

    public function getField($fieldName): string
    {
        if (!isset($this->fieldSpecifications[$fieldName])) {
            throw new InvalidArgumentException($fieldName . ' does not exist in ' . static::class);
        }

        return $this->fieldSpecifications[$fieldName][self::CONTENT];
    }

    /**
     * Get the string representation of the file component. There is no trailing newline applied,
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

    /**
     * FileComponent constructor.
     *
     * @param array $fields
     * @param       $validate
     * @throws ValidationException
     */
    protected function __construct($fields, $validate)
    {
        // Check for required fields, this should be done in the child class in most cases, but we will do it again
        // here to make sure it happens for cases when the child class doesn't override the constructor, or doesn't
        // check and modify any fields
        $missing_fields = array_diff(static::REQUIRED_FIELDS, array_keys($fields));
        if ($missing_fields) {
            throw new InvalidArgumentException('Cannot create ' . static::class . ' without all required fields, missing: ' . implode(', ', $missing_fields));
        }

        $this->fieldSpecifications = static::getFieldSpecifications();

        // Remove any extra fields that are not part of the specification (e.g. FILE_DATE)
        $fields = array_intersect_key($fields, $this->fieldSpecifications);

        foreach ($fields as $k => $v) {
            $this->setField($k, $v, $validate);
        }
    }

    /**
     * Validate the value and set the CONTENT of a specific field.
     *
     * @param $k
     * @param $v
     * @param bool $validate
     * @return FileComponent
     * @throws ValidationException
     */
    protected function setField($k, $v, $validate): FileComponent
    {
        // Always work with strings
        $v = (string) $v;

        // Make sure the key exists
        if (empty($this->fieldSpecifications[$k])) {
            throw new \InvalidArgumentException('Key "' . $k . '" does not match a valid field.');
        }

        if ($validate) {
            // Validate value unless validation is overridden (e.g. for when rebuilding object from string)
            switch ($this->fieldSpecifications[$k][self::VALIDATOR][0]) {
                case self::VALIDATOR_REGEX:
                    if (!preg_match($this->fieldSpecifications[$k][self::VALIDATOR][1], $v)) {
                        throw new ValidationException('Value: "' . ($v ?? 'null') . '" for "' . $k . '" does not match regular expression ' . $this->fieldSpecifications[$k][self::VALIDATOR][1]);
                    }
                    break;
                case self::VALIDATOR_DATETIME:
                    $optional           = $this->fieldSpecifications[$k][self::FIELD_INCLUSION] === self::FIELD_INCLUSION_OPTIONAL;
                    $validOptionalValue = $optional && in_array($v, self::VALID_OPTIONAL_VALUES, true);

                    $validValue = DateTime::createFromFormat($this->fieldSpecifications[$k][self::VALIDATOR][1], $v);
                    if (!$validValue && !$validOptionalValue) {
                        throw new ValidationException('"' . ($v ?? 'null') . '" for "' . $k . '" does not match date time format ' . $this->fieldSpecifications[$k][self::VALIDATOR][1]);
                    }
                    break;
                case self::VALIDATOR_ARRAY:
                    if (!in_array($v, $this->fieldSpecifications[$k][self::VALIDATOR][1], true)) {
                        throw new ValidationException('"' . ($v ?? 'null') . '" for "' . $k . '" must be one of: [ ' . implode(', ', $this->fieldSpecifications[$k][self::VALIDATOR][1]) . ' ]');
                    }
                    break;
                default:
                    throw new ValidationException('"' . $this->fieldSpecifications[$k][self::VALIDATOR][0] . '" is not a valid validation type');
            }
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

        $this->fieldSpecifications[$k][self::CONTENT] = mb_strtoupper($v);

        return $this;
    }

    /**
     * @param string $input 94 character fixed-width ACH Record string
     * @return array
     */
    protected static function getBuildDataFromInputString($input): array
    {
        if (!is_string($input) || mb_strlen($input) !== 94) {
            throw new \InvalidArgumentException('input data is not of the correct size or type');
        }

        // Extract the data required for submission
        $buildData           = [];
        $fieldSpecifications = static::getFieldSpecifications();
        foreach ($fieldSpecifications as $fieldName => $fieldData) {
            $buildData[$fieldName] = mb_substr($input, $fieldData[self::POSITION_START] - 1, $fieldData[self::LENGTH]);
        }

        return $buildData;
    }
}
