<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\Tests\InsertTag;

use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\CoreBundle\InsertTag\ResolvedParameters;
use Neo\AccessibilityBundle\InsertTag\AccessibilityInsertTag;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccessibilityInsertTagTest extends TestCase
{
    public function testLinkWithTheLabelOfThePageLanguage(): void
    {
        $result = $this->resolve(['link']);

        $this->assertSame('<a href="#" role="button" class="nax-link" data-nax-open>Barrierefreiheit</a>', $result->getValue());
        $this->assertSame(OutputType::html, $result->getOutputType());
    }

    public function testAnOwnLabelIsEscaped(): void
    {
        $result = $this->resolve(['link', ' Display <settings> & "more" ']);

        $this->assertSame('<a href="#" role="button" class="nax-link" data-nax-open>Display &lt;settings&gt; &amp; &quot;more&quot;</a>', $result->getValue());
    }

    public function testALabelWithDoubleColonsIsJoinedAgain(): void
    {
        $this->assertStringContainsString('>Help::Display</a>', $this->resolve(['link', 'Help', 'Display'])->getValue());
    }

    public function testExistingEntitiesAreNotEncodedTwice(): void
    {
        $this->assertStringContainsString('>Fish &amp; chips</a>', $this->resolve(['link', 'Fish &amp; chips'])->getValue());
    }

    public function testAnyOtherParameterOutputsNothing(): void
    {
        foreach ([[], ['button'], ['LINK']] as $parameters) {
            $result = $this->resolve($parameters);

            $this->assertSame('', $result->getValue());
            $this->assertSame(OutputType::text, $result->getOutputType());
        }
    }

    /**
     * @param list<string> $parameters
     */
    private function resolve(array $parameters): InsertTagResult
    {
        $translator = $this->createStub(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static fn (string $id, array $p = [], string|null $domain = null): string => 'link.label' === $id && 'neo_accessibility' === $domain ? 'Barrierefreiheit' : $id);

        return (new AccessibilityInsertTag($translator))(new ResolvedInsertTag('accessibility', new ResolvedParameters($parameters), []));
    }
}
