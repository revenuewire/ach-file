# ACH File
Generate an ACH File object from data for submission to an ODFI, or create an ACH File object from
a return/reject file provided by the ODFI.

## Requirements
* PHP 7
* PHPUnit 7.1 for unit testing

## Installation
* Install using composer:
```json
"require": {
    "revenuewire/ach-file": "1.1.*"
}
```

## Run Tests
* Test using phpunit - with xdebug enabled tests will provide code coverage data
```
php vendor/bin/phpunit
```

## Usage (Code Examples)
#### Creating an ACH File From Generated Data
```php
// The code below will generate an ACH File object that contains two batches
// (one credit entry batch and one debit entry batch), each of which contains
// two Entry Detail Records.

$companyID = '0123456789';
$originatingDFIID = '987654321';

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
    \RW\ACH\BatchHeaderRecord::ORIGINATING_DFI_ID        => $originatingDFIID,
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
    \RW\ACH\BatchHeaderRecord::ORIGINATING_DFI_ID        => $originatingDFIID,
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
    \RW\ACH\EntryDetailRecord::TRACE_NUMBER       => $originatingDFIID,
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
    \RW\ACH\EntryDetailRecord::TRACE_NUMBER       => $originatingDFIID,
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
    \RW\ACH\EntryDetailRecord::TRACE_NUMBER       => $originatingDFIID,
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
    \RW\ACH\EntryDetailRecord::TRACE_NUMBER       => $originatingDFIID,
    // Optional
    \RW\ACH\EntryDetailRecord::ID_NUMBER          => '9876543210',
    \RW\ACH\EntryDetailRecord::DRAFT_INDICATOR    => '  ',
    \RW\ACH\EntryDetailRecord::ADDENDA_INDICATOR  => '0',
], 4);

$batchOne = new \RW\ACH\Batch($creditBatchHeader);
$batchOne->addEntryDetailRecord($creditEntryDetailOne)
    ->addEntryDetailRecord($creditEntryDetailTwo)
    ->close();
$batchTwo = new \RW\ACH\Batch($debitBatchHeader);
$batchTwo->addEntryDetailRecord($debitEntryDetailOne)
    ->addEntryDetailRecord($debitEntryDetailTwo)
    ->close();

$achPaymentFile = new \RW\ACH\File($achFileHeaderRecord);
$achPaymentFile->addBatch($batchOne)
    ->addBatch($batchTwo)
    ->close();
```

#### Uploading an ACH File Object to a Destination SFTP Server
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

#### Downloading ACH Files From a Destination SFTP Server
```php
// This example uses the php ssh2 library to manage the connection
// Set up transmission details

$host = 'url.to.destination.com';
$port = 22;
$userName = 'userName';
$publicKey = '/path/to/public/key.pub';
$privateKey = '/path/to/private/key';
$outboundDirectory = '/path/to/outbound/directory/'; // leading and trailing slashes

// Establish connection
if (($connection = ssh2_connect($host, $port)) === false) {
    exit ('Failed to establish connection');
}
if (ssh2_auth_pubkey_file($connection, $userName, $publicKey, $privateKey) === false) {
    exit ('Failed to authorize secure connection');
}
$sftp = ssh2_sftp($connection);

// Get a list of available files for download
$files = array();
$dirHandle = opendir('ssh2.sftp://' . intval($sftp) . $outputDirectory);
while (($file = readdir($dirHandle))) {
    if ($file != '.' && $file != '..') {
        $files[] = $file;
    }
}

// Download and parse the files
foreach ($files as $k => $remoteFileName) {
    // Prep the local file name
    $fileStats = ssh2_sftp_stat($sftp, $outputDirectory . $remoteFileName);
    $fileDateString = (new DateTime())->setTimestamp($fileStats['mtime'])->format('Ymd-His');
    $localFileName = "ACH_RETURN_FILE_{$fileDateString}_{$remoteFileName}";

    // Open the file for processing
    if (!($handle = fopen('ssh2.sftp://' . intval($sftp) . $outputDirectory . $remoteFileName, 'r'))) {
        echo "Failed to open remote file {$remoteFileName} in {$outputDirectory}";
        continue;
    }

    /* Parse the file */

    // Clean up
    fclose($handle)
}
```

#### Creating an ACH File Object Using a Provided Return File
```php
$handle = fopen('/path/to/return/file.txt', 'r+');
$achReturnFile = \RW\ACH\File::buildFromResource($handle);

// Get data about the file from the header
$fileHeader = $achReturnFile->getHeaderRecord();
$exampleData = $fileHeader->getField(\RW\ACH\FileHeader::EXAMPLE_FIELD_NAME);

/** @var Batch $batch */
foreach ($achReturnFile->getBatches() as $batch) {
    // Get data about batches from the header
    $batchHeader = $batch->getHeaderRecord();
    $exampleData = $batchHeader->getField(\RW\ACH\BatchHeader::EXAMPLE_FIELD_NAME);

    foreach ($batch->getEntryDetailRecords() as $entryDetailRecord) {
        // Process the entry detail record in some way
        switch (get_class($entryDetailRecord->getAddendaRecord())) {
            case \RW\ACH\NoticeOfChangeAddenda::class:
                // handleNoticeOfChange($entryDetailRecord);
                break;
            case \RW\ACH\ReturnEntryAddenda::class:
                // handleReturnEntry($entryDetailRecord);
                break;
            default:
                // Error
        }
    }
}
```
