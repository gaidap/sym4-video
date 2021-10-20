<?php

namespace App\Tests\Utils\Twig;

use App\Twig\AppExtension;
use Generator;
use PHPUnit\Framework\TestCase;

class SluggerTest extends TestCase
{
    /**
     * @dataProvider getSlugs
     */
    public function testSlugify(string $input, string $output): void
    {
        $slugger = new AppExtension();
        $this->assertSame($output, $slugger->slugify($input));
    }

    public function getSlugs(): Generator
    {
        yield ['Cell Phones', 'cell-phones'];
        yield ['  Lorem Ipsum', 'lorem-ipsum'];
        yield ['cElL phOnes  ', 'cell-phones'];
        yield [' Cell   Phones  ', 'cell-phones'];
        yield ['CeLL PhonES', 'cell-phones'];
        yield ['Cell PhoNes     ', 'cell-phones'];
        yield ['cell phoneS', 'cell-phones'];
    }
}
