<?php
namespace Satrio\Scrapdata;

use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase {
    
    public function test_gettarif()
    {
        $this->assertEquals('{"code":200,"data":[{"serviceType":"REG","serviceFees":"Rp 45000","courier":"TIKI"}]}',Util::GetongkirRajaOngkir("Depok","Jakarta+Pusat",1,1,2,1));
    }
}