<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-13
 * Time: 11:41
 */

namespace RW\ACH;


/**
 * Class AddendaRecord represents any type of Addenda record attached to an Entry Detail record.
 *
 * @package RW\ACH
 */
abstract class AddendaRecord extends FileComponent
{
    public const CODE = 'CODE';
    public const NOTE = 'NOTE';

    public const FIXED_RECORD_TYPE_CODE = '7';

    public const ADDENDA_TYPE_CODE           = 'ADDENDA_TYPE_CODE';
    public const ORIGINAL_ENTRY_TRACE_NUMBER = 'ORIGINAL_ENTRY_TRACE_NUMBER';
    public const ORIGINAL_RECEIVING_DFI_ID   = 'ORIGINAL_RECEIVING_DFI_ID';
    public const TRACE_NUMBER                = 'TRACE_NUMBER';

    /**
     * Build an Addenda record from an existing string. Supported Addenda record types will be identified by the
     * Addenda Type Code in fixed position 02-03.
     *
     * @param string $input
     * @return ReturnEntryAddenda|NoticeOfChangeAddenda
     * @throws ValidationException
     */
    public static function buildFromString($input)
    {
        switch (self::getAddendaClassNameFromString($input)) {
            case ReturnEntryAddenda::class:
                return ReturnEntryAddenda::buildFromString($input);
            case NoticeOfChangeAddenda::class:
                return NoticeOfChangeAddenda::buildFromString($input);
            default:
                throw new \InvalidArgumentException('Unrecognized addenda type');
        }
    }

    /**
     * @param $input
     * @return string
     */
    public static function getAddendaClassNameFromString($input): string
    {
        switch (mb_substr($input, 1, 2)) {
            case '98':
                return NoticeOfChangeAddenda::class;
            case '99':
                return ReturnEntryAddenda::class;
            default:
                throw new \InvalidArgumentException('Unrecognized addenda type');
        }
    }
}
