<?php
/**
 * Created by PhpStorm.
 * User: wim
 * Date: 14/03/15
 * Time: 0:20
 */

namespace Codelicious\Tests\BelgianBankStatement;


class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testCodaParse()
    {
        $parser = new \Codelicious\BelgianBankStatement\Parsers\Parser();

        $statements = $parser->parse($this->getSampleCoda(), 'coda');

        $this->assertEquals(1, count($statements));
        $statement = $statements[0];

        $this->assertNotEmpty($statement->account);
        $this->assertEquals(3, count($statement->transactions));
        $this->assertEquals("2015-01-18", $statement->date);
        $this->assertEquals(-4004.1, $statement->original_balance);
        $this->assertEquals(500012.1, $statement->new_balance);
        $this->assertEquals("CODELICIOUS", $statement->account->name);
        $this->assertEquals("001548226815", $statement->account->number);
        $tr1 = $statement->transactions[0];
        $this->assertNotEmpty($tr1->account);
        $this->assertEquals("2014-12-25", $tr1->transaction_date);
        $this->assertEquals(767.823, $tr1->amount);
        $this->assertEquals("112/4554/46812   813ANOTHER MESSAGEMESSAGE", $tr1->message);
    }

    public function testMt940Parse()
    {
        $parser = new \Codelicious\BelgianBankStatement\Parsers\Parser();

        $statements = $parser->parse($this->getSampleMt940(), 'mt940');

        $this->assertEquals(1, count($statements));
        $statement = $statements[0];

        $this->assertNotEmpty($statement->account);
        $this->assertEquals(3, count($statement->transactions));
        $this->assertEquals("2010-07-22", $statement->date);
        $this->assertEquals(44.89, $statement->original_balance);
        $this->assertEquals(9945.09, $statement->new_balance);
        $this->assertEquals("111111111", $statement->account->name);
        $this->assertEquals("100", $statement->account->number);
        $tr1 = $statement->transactions[0];
        $this->assertNotEmpty($tr1->account);
        $this->assertEquals("2010-07-22", $tr1->transaction_date);
        $this->assertEquals(0.56, $tr1->amount);
        $this->assertEquals("0111111111 V. DE JONG KERKSTRAAT 1154 1234 BWENSCHEDE BET.KENM. 1004510036716378 3305330802AFLOSSINGSTERMIJN 188616 / 1E TERMIJN", $tr1->message);
    }

    public function testCsvParse()
    {
        $parser = new \Codelicious\BelgianBankStatement\Parsers\Parser();

        $statements = $parser->parse($this->getSampleCsv(), 'csv');

        $this->assertEquals(1, count($statements));
        $statement = $statements[0];
        $this->assertNotEmpty($statement->account);
        $this->assertEquals(4, count($statement->transactions));
        $this->assertEquals("BE58 2135 3215 3215", $statement->account->number);
        $tr1 = $statement->transactions[0];
        $this->assertNotEmpty($tr1->account);
        $this->assertEquals("2015-01-13", $tr1->transaction_date);
        $this->assertEquals(-5.3, $tr1->amount);
        $this->assertEquals("MET KAART 2131 02XX XXXX X318 2 EEN WINKEL      9000 13-01-2015 VALUTADATUM : 13/01/2015", $tr1->message);
        $this->assertEquals("BETALING MET BANKKAART", $tr1->account->number);
    }

    private function getSampleMt940()
    {
        $content = array(
            "0000 01INGBNL2AXXXX00001",
            "0000 01INGBNL2AXXXX00001",
            "940 00",
            ":20:INGEB",
            ":25:0111111111",
            ":28C:100",
            ":60F:C100722EUR44,89",
            ":61:100722C0,56N078NONREF",
            ":86:0111111111 V. DE JONG KERKSTRAAT 1154 1234 BW",
            "ENSCHEDE BET.KENM. 1004510036716378 3305330802",
            "AFLOSSINGSTERMIJN 188616 / 1E TERMIJN",
            ":61:100722C10,45N077NONREF",
            ":86:0111111111 CUSTOMER NL SPOEDBETALING",
            "GE2009120212345",
            "RE091202­3737 /RFB/NL­FMI­021209 NL­FMI­021209",
            "VOORSCHOT",
            "COMMISSIE",
            ":61:100722D10000,99N029NONREF",
            ":86:VERZAMELBETALING BATCH­ID: 012345 TOTAAL 198",
            "POSTEN",
            ":62F:D100723EUR9945,09",
            ":64:D100723EUR9945,09",
            ":65:D100724EUR9945,09",
            ":65:D100726EUR9945,09",
            ":86 :D000001C000002D10000,99C11,01",
            "ING TESTREKENING",
            "XXX"
        );

        return $content;
    }

    private function getSampleCsv()
    {
        $content = array(
            '"JAAR + REFERTE";"UITVOERINGSDATUM";"VALUTADATUM";"BEDRAG";"MUNT V/D REKENING";"TEGENPARTIJ VAN DE VERRICHTING";"DETAILS";"REKENINGNUMMER"',
            '"2015-0124";"13/01/2015";"13/01/2015";"-5,30";"EUR";"BETALING MET BANKKAART";"MET KAART 2131 02XX XXXX X318 2 EEN WINKEL      9000 13-01-2015 VALUTADATUM : 13/01/2015";"BE58 2135 3215 3215 ";',
            '"2015-0118";"11/02/2015";"11/02/2015";"97,57";"EUR";"BE12 1566 1484 5045 ";"NAAM VAN DE KLANT BE15 3215 3215 2185  BIC A32DEDF5 MEDEDELING : dit is een custom message     VALUTADATUM : 11/03/2015";"BE58 2135 3215 3215 ";',
            '"2015-0076";"14/03/2015";"16/03/2015";"-78,48";"EUR";"BE12 5498 2135 2158 ";"SOME SHOP BE21354584321548 BIC VDSDEC21    VIA PC BANKING MEDEDELING : 215456321548 UITGEVOERD OP 13-03 VALUTADATUM : 16/03/2015";"BE58 2135 3215 3215 ";',
            '"2015-0075";"14/04/2015";"16/04/2015";"-80,46";"EUR";"BE15 2135 5148 2133 ";"TELENET NV BE23156489435123 BIC KREDBEBB    VIA PC BANKING MEDEDELING : 321231564845 UITGEVOERD OP 13-04 VALUTADATUM : 16/04/2015";"BE58 2135 3215 3215 ";',
        );

        return $content;
    }


    private function getSampleCoda()
    {
        $content = array(
            "0000018011520105        0938409934CODELICIOUS               GEBABEBB   09029308273 00001          984309          834080       2",
            "10155001548226815 EUR0BE                  0000000004004100241214CODELICIOUS               PROFESSIONAL ACCOUNT               255",
            "21000100000001200002835        1000000000767823251214001120000112/4554/46812   813                                 25121421401 0",
            "2200010000  ANOTHER MESSAGE                                           54875                       GEBCEEBB                   1 0",
            "2300010000BE54805480215856                  EURBVBA.BAKKER PIET                         MESSAGE                              0 1",
            "31000100010007500005482        004800001001BVBA.BAKKER PIET                                                                  1 0",
            "3200010001MAIN STREET 928                    5480 SOME CITY                                                                  0 0",
            "3300010001SOME INFORMATION ABOUT THIS TRANSACTION                                                                            0 0",
            "21000200000001200002835        0000000002767820251214001120001101112455446812  813                                 25121421401 0",
            "2200020000  ANOTHER MESSAGE                                           54875                       GEBCEEBB                   1 0",
            "2300020000BE54805480215856                  EURBVBA.BAKKER PIET                         MESSAGE                              0 1",
            "31000200010007500005482        004800001001BVBA.BAKKER PIET                                                                  1 0",
            "21000900000001200002835        0000000001767820251214001120000112/4554/46812   813                                 25121421401 0",
            "2200090000  ANOTHER MESSAGE                                           54875                       GEBCEEBB                   1 0",
            "8225001548226815 EUR0BE                  1000000500012100120515                                                                0",
            "4 00010005                      THIS IS A PUBLIC MESSAGE                                                                       0",
            "9               000015000000016837520000000003967220                                                                           1",
        );

        return $content;
    }
}