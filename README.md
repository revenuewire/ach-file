# ACH File
Generate an ACH File object from data for submission to an ODFI, or create an ACH File object from
a return file provided by the ODFI.

## Requirements
* PHP 7
* PHPUnit 7.1 for unit testing

## Usage
#### Creating an ACH File From Generated Data
```php
// The code below will generate an ACH File object that contains two batches
// (one credit entry batch and one debit entry batch), each of which contains
// two Entry Detail Records.

$companyID = '0123456789';

// Create a File Header record to use when creating a file
$achFileHeaderRecord = new \RW\ACH\FileHeaderRecord([
    // Required
    \RW\ACH\FileHeaderRecord::IMMEDIATE_DESTINATION => ' 123456789',
    \RW\ACH\FileHeaderRecord::IMMEDIATE_ORIGIN      => $companyID,
    \RW\ACH\FileHeaderRecord::DESTINATION_NAME      => 'A Bank Name',    // Max 23 characters
    \RW\ACH\FileHeaderRecord::ORIGIN_NAME           => 'A Company Name', // Max 23 characters
    // Optional
    \RW\ACH\FileHeaderRecord::FILE_DATE             => new DateTime('2018-06-20 19:18:17'),
    \RW\ACH\FileHeaderRecord::FILE_ID_MODIFIER      => 'A',              // Sequential, A-Z or 0-9
    \RW\ACH\FileHeaderRecord::REFERENCE_CODE        => 'ABCD1234',       // Max 8 characters, originator's internal use
]);

// Create Batch Header records to use when creating batches
$creditBatchHeader = new \RW\ACH\BatchHeaderRecord([
    // Required
    \RW\ACH\BatchHeaderRecord::SERVICE_CLASS_CODE        => \RW\ACH\BatchHeaderRecord::CREDIT_SERVICE_CLASS,
    \RW\ACH\BatchHeaderRecord::COMPANY_NAME              => 'Company Name', // Max 16 characters
    \RW\ACH\BatchHeaderRecord::COMPANY_ID                => $companyID,
    \RW\ACH\BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => \RW\ACH\BatchHeaderRecord::SEC_PPD,
    \RW\ACH\BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => 'Payroll',      // Transaction type description
    \RW\ACH\BatchHeaderRecord::ORIGINATING_DFI_ID        => '12345678',
    \RW\ACH\BatchHeaderRecord::BATCH_NUMBER              => '1',            // Sequential
    // Optional
    \RW\ACH\BatchHeaderRecord::EFFECTIVE_ENTRY_DATE      => new DateTime('2018-06-20 19:18:17'),
    \RW\ACH\BatchHeaderRecord::DISCRETIONARY_DATA        => '20 Char Internal Use',
    \RW\ACH\BatchHeaderRecord::COMPANY_DESCRIPTIVE_DATE  => new DateTime('2018-06-20 19:18:17'),
]);
$debitBatchHeader = new \RW\ACH\BatchHeaderRecord([
    // Required
    \RW\ACH\BatchHeaderRecord::SERVICE_CLASS_CODE        => \RW\ACH\BatchHeaderRecord::DEBIT_SERVICE_CLASS,
    \RW\ACH\BatchHeaderRecord::COMPANY_NAME              => 'Company Name', // Max 16 characters
    \RW\ACH\BatchHeaderRecord::COMPANY_ID                => $companyID,
    \RW\ACH\BatchHeaderRecord::STANDARD_ENTRY_CLASS_CODE => \RW\ACH\BatchHeaderRecord::SEC_PPD,
    \RW\ACH\BatchHeaderRecord::COMPANY_ENTRY_DESCRIPTION => 'Payroll',      // Transaction type description
    \RW\ACH\BatchHeaderRecord::ORIGINATING_DFI_ID        => '12345678',
    \RW\ACH\BatchHeaderRecord::BATCH_NUMBER              => '2',            // Sequential
    // Optional
    \RW\ACH\BatchHeaderRecord::EFFECTIVE_ENTRY_DATE      => new DateTime('2018-06-20 19:18:17'),
    \RW\ACH\BatchHeaderRecord::DISCRETIONARY_DATA        => '20 Char Internal Use',
    \RW\ACH\BatchHeaderRecord::COMPANY_DESCRIPTIVE_DATE  => new DateTime('2018-06-20 19:18:17'),
]);

// Create Entry Detail Records to add to batches
$creditEntryDetailOne = new \RW\ACH\EntryDetailRecord([
    // Required
    \RW\ACH\EntryDetailRecord::TRANSACTION_CODE   => \RW\ACH\EntryDetailRecord::CHECKING_CREDIT_DEPOSIT,
    \RW\ACH\EntryDetailRecord::TRANSIT_ABA_NUMBER => '987654321',       // Customer's bank transit number
    \RW\ACH\EntryDetailRecord::DFI_ACCOUNT_NUMBER => '0123456789',      // Customer's bank account number
    \RW\ACH\EntryDetailRecord::AMOUNT             => '25.55',           // Decimal format
    \RW\ACH\EntryDetailRecord::INDIVIDUAL_NAME    => 'Customer Name',   // Max 22 characters
    \RW\ACH\EntryDetailRecord::TRACE_NUMBER       => '12345678',        // ORIGINATING_DFI_ID from batch header
    // Optional
    \RW\ACH\EntryDetailRecord::ID_NUMBER          => '9876543210',      // Customer's internal account number
    \RW\ACH\EntryDetailRecord::DRAFT_INDICATOR    => '  ',
    \RW\ACH\EntryDetailRecord::ADDENDA_INDICATOR  => '0',
], 1);
$creditEntryDetailTwo = new \RW\ACH\EntryDetailRecord([
    // Required
    \RW\ACH\EntryDetailRecord::TRANSACTION_CODE   => \RW\ACH\EntryDetailRecord::SAVINGS_CREDIT_DEPOSIT,
    \RW\ACH\EntryDetailRecord::TRANSIT_ABA_NUMBER => '987654321',
    \RW\ACH\EntryDetailRecord::DFI_ACCOUNT_NUMBER => '0123456789',
    \RW\ACH\EntryDetailRecord::AMOUNT             => '32.45',
    \RW\ACH\EntryDetailRecord::INDIVIDUAL_NAME    => 'Another Customer',
    \RW\ACH\EntryDetailRecord::TRACE_NUMBER       => '12345678',
    // Optional
    \RW\ACH\EntryDetailRecord::ID_NUMBER          => '9876543210',
    \RW\ACH\EntryDetailRecord::DRAFT_INDICATOR    => '  ',
    \RW\ACH\EntryDetailRecord::ADDENDA_INDICATOR  => '0',
], 2);
$debitEntryDetailOne = new \RW\ACH\EntryDetailRecord([
    // Required
    \RW\ACH\EntryDetailRecord::TRANSACTION_CODE   => \RW\ACH\EntryDetailRecord::CHECKING_DEBIT_PAYMENT,
    \RW\ACH\EntryDetailRecord::TRANSIT_ABA_NUMBER => '987654321',
    \RW\ACH\EntryDetailRecord::DFI_ACCOUNT_NUMBER => '0123456789',
    \RW\ACH\EntryDetailRecord::AMOUNT             => '11.00',
    \RW\ACH\EntryDetailRecord::INDIVIDUAL_NAME    => 'Yet Another Customer',
    \RW\ACH\EntryDetailRecord::TRACE_NUMBER       => '12345678',
    // Optional
    \RW\ACH\EntryDetailRecord::ID_NUMBER          => '9876543210',
    \RW\ACH\EntryDetailRecord::DRAFT_INDICATOR    => '  ',
    \RW\ACH\EntryDetailRecord::ADDENDA_INDICATOR  => '0',
], 3);
$debitEntryDetailTwo = new \RW\ACH\EntryDetailRecord([
    // Required
    \RW\ACH\EntryDetailRecord::TRANSACTION_CODE   => \RW\ACH\EntryDetailRecord::SAVINGS_DEBIT_PAYMENT,
    \RW\ACH\EntryDetailRecord::TRANSIT_ABA_NUMBER => '987654321',
    \RW\ACH\EntryDetailRecord::DFI_ACCOUNT_NUMBER => '0123456789',
    \RW\ACH\EntryDetailRecord::AMOUNT             => '5.64',
    \RW\ACH\EntryDetailRecord::INDIVIDUAL_NAME    => 'One More Customer',
    \RW\ACH\EntryDetailRecord::TRACE_NUMBER       => '12345678',
    // Optional
    \RW\ACH\EntryDetailRecord::ID_NUMBER          => '9876543210',
    \RW\ACH\EntryDetailRecord::DRAFT_INDICATOR    => '  ',
    \RW\ACH\EntryDetailRecord::ADDENDA_INDICATOR  => '0',
], 4);

$batchOne = new \RW\ACH\Batch($creditBatchHeader);
$batchOne->addComponent($creditEntryDetailOne)
    ->addComponent($creditEntryDetailTwo)
    ->close();
$batchTwo = new \RW\ACH\Batch($debitBatchHeader);
$batchTwo->addComponent($debitEntryDetailOne)
    ->addComponent($debitEntryDetailTwo)
    ->close();

$achPaymentFile = new \RW\ACH\File($achFileHeaderRecord);
$achPaymentFile->addComponent($batchOne)
    ->addComponent($batchTwo)
    ->close();
```

#### Example of Uploading an ACH File Object to a Destination SFTP Server
```php
// This example uses the php ssh2 library to manage the sftp connection

$host = 'url.to.destination.com';
$port = 22;
$userName = 'userName';
$publicKey = '/path/to/public/key.pub';
$privateKey = '/path/to/private/key';
$inputDirectory = '/path/to/inbound/directory/'; // leading and trailing slashes
$date = new DateTime();
$fileName = 'ACHPaymentFile_' . $date->format('Ymd-His') . '.txt';

$connection = ssh2_connect($host, $port);
$auth = ssh2_auth_pubkey_file($connection, $userName, $publicKey, $privateKey);
$sftp = ssh2_sftp($connection);

$destination = fopen('ssh2.sftp://' . intval($sftp) . $inputDirectory . $fileName, 'w');
fwrite($destination, $achPaymentFile->toString());
fclose($destination);
```

#### Example of Creating an ACH File Object Using a Provided Return File
```php
$handle = fopen('/path/to/return/file.txt', 'r+');
$achReturnFile = \RW\ACH\File::buildFromResource($handle);

// Get data about the file from the header
$fileHeader = $achReturnFile->getHeaderRecord();
$exampleData = $fileHeader->getField(\RW\ACH\FileHeader::EXAMPLE_FIELD_NAME);

/** @var Batch $batch */
foreach ($achReturnFile->getCollection() as $batch) {
    // Get data about batches from the header
    $batchHeader = $batch->getHeaderRecord();
    $serviceClassCode = $batchHeader->getField(\RW\ACH\BatchHeader::SERVICE_CLASS_CODE);

    foreach ($batch->getCollection() as $entryDetailRecord) {
        // Process the entry detail record in some way
        switch ($serviceClassCode) {
            // The entry detail record also contains the addenda record, which
            // can be accessed with $entryDetailRecord->getAddendaRecord()
            case \RW\ACH\BatchHeader::SEC_COR:
                processCorrectionEntry($entryDetailRecord);
                break;
            case \RW\ACH\BatchHeader::SEC_PPD:
                processReturnEntry($entryDetailRecord);
                break;
            default:
                echo "$serviceClassCode is an unhandled service class";
        }
    }
}
```