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

class AddendaRecordTest extends TestCase
{
    /**
     * @throws \RW\ACH\ValidationException
     */
    public function testValidReturnStringInputGeneratesValidReturnAddendaRecord()
    {
        $input   = '799R02111000020000044      05320160                                            111000026188605';
        $addenda = AddendaRecord::buildFromString($input);
        $this->assertEquals($input, $addenda->toString());
    }

    /**
     * @throws \RW\ACH\ValidationException
     */
    public function testValidChangeStringInputGeneratesValidChangeAddendaRecord()
    {
        $input   = '798C02111000020000044      05320160                                            111000026188605';
        $addenda = AddendaRecord::buildFromString($input);
        $this->assertEquals($input, $addenda->toString());
    }

    public function testInvalidAddendaTypeThrowsInvalidArgumentException()
    {
        $e = null;
        $input = '797C02111000020000044      05320160                                            111000026188605';
        try {
            AddendaRecord::buildFromString($input);
        } catch (\Exception $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(\InvalidArgumentException::class, get_class($e));
    }
}
