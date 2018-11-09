<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-13
 * Time: 11:38
 */

namespace RW\Tests\ACH;


use PHPUnit\Framework\TestCase;
use RW\ACH\AddendaRecord;
use RW\ACH\NoticeOfChangeAddenda;
use RW\ACH\ReturnEntryAddenda;

class AddendaRecordTest extends TestCase
{
    public function validInputsProvider()
    {
        $standardInput = 'C02111000020000044      05320160                                            111000026188605';

        return [
            'ReturnEntry'    => [
                $noticeOfChangeInput = '799' . $standardInput,
                ReturnEntryAddenda::class,
            ],
            'NoticeOfChange' => [
                $returnEntryInput = '798' . $standardInput,
                NoticeOfChangeAddenda::class,
            ],
        ];
    }

    public function invalidInputsProvider()
    {
        $standardInput = 'C02111000020000044      05320160                                            111000026188605';

        return [
            'Invalid' => [
                $invalidInput = '797' . $standardInput,
                \InvalidArgumentException::class,
            ],
        ];
    }

    /**
     * @param $input
     * @param $output
     * @throws \RW\ACH\ValidationException
     * @dataProvider validInputsProvider
     */
    public function testValidInputGeneratesCorrectAddendaRecord($input, $output)
    {
        $addenda = AddendaRecord::buildFromString($input);
        $this->assertEquals($input, $addenda->toString());
        $this->assertEquals($output, get_class($addenda));
    }

    /**
     * @param $input
     * @param $output
     * @dataProvider invalidInputsProvider
     */
    public function testInvalidAddendaTypeThrowsInvalidArgumentException($input, $output)
    {
        $e = null;
        try {
            AddendaRecord::buildFromString($input);
        } catch (\Exception $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals($output, get_class($e));
    }
}
