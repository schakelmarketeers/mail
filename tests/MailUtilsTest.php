<?php
declare(strict_types=1);

namespace Schakel\Mail\Test;

use Schakel\Mail\MailUtils;

class MailUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function getMailTemplates(): array
    {
        $templates = [
            'base' => __DIR__ . '/Lib/mail-complex.html',
            'html' => __DIR__ . '/Lib/mail-complex-emo.html',
            'text' => __DIR__ . '/Lib/mail-complex-plain.txt'
        ];

        foreach ($templates as $key => $value) {
            if (file_exists($value) && is_readable($value) && is_file($value)) {
                $templates[$key] = file_get_contents($value);
            } else {
                $templates[$key] = '';
            }
        }
        return $templates;
    }
    /**
     * @covers Schakel\Mail\Mail::__construct
     * @expectedException PHPUnit_Framework_Error
     */
    public function testCreateEmailFromEmptyString()
    {
        $this->assertSame(MailUtils::createEmailHtml(''));
    }

    /**
     * @covers Schakel\Mail\MailUtils::createEmailHtml
     */
    public function testMailBodyHtml()
    {
        $templates = $this->getMailTemplates();

        if ($templates['base'] === '') {
            $this->markTestSkipped('Cannot read template file!');
            return;
        }

        $this->assertEquals(
            $templates['html'],
            MailUtils::createEmailHtml($templates['base'])
        );
    }

    /**
     * @covers Schakel\Mail\MailUtils::createEmailPlain
     */
    public function testMailBodyPlain()
    {
        $templates = $this->getMailTemplates();

        if ($templates['base'] === '') {
            $this->markTestSkipped('Cannot read template file!');
            return;
        }

        $this->assertEquals(
            $templates['text'],
            MailUtils::createEmailPlain($templates['base'])
        );
    }
}
