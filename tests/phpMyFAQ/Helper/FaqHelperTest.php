<?php

namespace phpMyFAQ\Helper;

use phpMyFAQ\Configuration;
use phpMyFAQ\Database\Sqlite3;
use phpMyFAQ\Entity\FaqEntity;
use phpMyFAQ\System;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

class FaqHelperTest extends TestCase
{
    /** @var Configuration */
    private Configuration $configuration;

    /** @var FaqHelper*/
    private FaqHelper $faqHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $dbHandle = new Sqlite3();
        $dbHandle->connect(PMF_TEST_DIR . '/test.db', '', '');
        $this->configuration = new Configuration($dbHandle);
        $this->configuration->set('main.currentVersion', System::getVersion());
        $this->configuration->set('main.referenceURL', 'https://localhost:443/');

        $this->faqHelper = new FaqHelper($this->configuration);
    }

    public function testRewriteLanguageMarkupClass(): void
    {
        $this->assertEquals(
            '<div class="language-html">Foobar</div>',
            $this->faqHelper->rewriteLanguageMarkupClass('<div class="language-markup">Foobar</div>')
        );
    }

    public function testRewriteUrlFragments(): void
    {
        $content = '<a href="#Foobar">Hello, World</a>';
        $result = $this->faqHelper->rewriteUrlFragments($content, 'https://localhost:443/');

        $this->assertEquals(
            '<a href="https://localhost:443/#Foobar">Hello, World</a>',
            $result
        );
    }
    public function testCreateFaqUrl(): void
    {
        $faqEntity = new FaqEntity();
        $faqEntity
            ->setId(42)
            ->setLanguage('de');

        $this->assertEquals(
            'https://localhost:443/index.php?action=faq&cat=1&id=42&artlang=de',
            $this->faqHelper->createFaqUrl($faqEntity, 1)
        );
    }

    public function testCleanUpContent(): void
    {
        $content = '<p>Some text <script>alert("Hello, world!");</script><img src=foo onerror=alert(document.cookie)></p>';
        $expectedOutput = '<p>Some text <img src="foo" /></p>';

        $actualOutput = $this->faqHelper->cleanUpContent($content);

        $this->assertEquals($expectedOutput, $actualOutput);
    }


    public function testCleanUpContentWithUmlauts(): void
    {
        $content = '<p>Hellö, wörld!</p>';
        $expectedOutput = '<p>Hellö, wörld!</p>';

        $actualOutput = $this->faqHelper->cleanUpContent($content);

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testCleanUpContentWithYoutubeContent(): void
    {
        $content = <<<'HTML'
        <iframe 
          title="YouTube video player" 
          src="https://www.youtube.com/embed/WaFetxHpCbE" 
          width="560" 
          height="315" 
          frameborder="0" 
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
          allowfullscreen="allowfullscreen"></iframe>
        HTML;

        $actualOutput = $this->faqHelper->cleanUpContent($content);

        $this->assertStringContainsString('YouTube video player', $actualOutput);
        $this->assertStringNotContainsString('frameborder', $actualOutput);
    }

    public function testCleanUpEmptyIframes(): void
    {
        $content = '<iframe></iframe>';
        $expectedOutput = '';

        $actualOutput = $this->faqHelper->cleanUpContent($content);

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testCleanUpContentWithOverflow(): void
    {
        $content = '<p style="position: relative; overflow: auto;">Foobar!</p>';
        $expectedOutput = '<p style="position: relative; ">Foobar!</p>';

        $actualOutput = $this->faqHelper->cleanUpContent($content);

        $this->assertEquals($expectedOutput, $actualOutput);
    }
}
