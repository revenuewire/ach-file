<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-05-28
 * Time: 13:32
 */

namespace RW\Tests\NACHA;


use PHPUnit\Framework\TestCase;
use RW\NACHA\ACHPaymentFile;

class ACHPaymentFileTest extends TestCase
{
    public function testFileHasHeader() {
        $ach_payment_file = new ACHPaymentFile();
        $this->assertNotNull($ach_payment_file->getHeader());
    }
}