<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\InsertTag;

use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\CoreBundle\InsertTag\Resolver\InsertTagResolverNestedResolvedInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Insert tag that opens the accessibility panel from anywhere in the content:
 *
 *   {{accessibility::link}}             link with the default label of the page language
 *   {{accessibility::link::Own label}}  link with an own label
 *
 * Output: <a href="#" role="button" class="nax-link" data-nax-open>Label</a>.
 * The panel's script opens the dialog on a click of any [data-nax-open]
 * element. The module must be on the page (page layout); without it the link
 * only jumps to the top of the page. Any other parameter outputs nothing.
 */
#[AsInsertTag('accessibility')]
final class AccessibilityInsertTag implements InsertTagResolverNestedResolvedInterface
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        $parameters = $insertTag->getParameters()->all();

        if ('link' !== ($parameters[0] ?? null)) {
            return new InsertTagResult('', OutputType::text);
        }

        // A label containing "::" arrives split into several parameters.
        $label = trim(implode('::', \array_slice($parameters, 1)));

        if ('' === $label) {
            // The translator's locale is the language of the current page.
            $label = $this->translator->trans('link.label', [], 'neo_accessibility');
        }

        return new InsertTagResult(
            '<a href="#" role="button" class="nax-link" data-nax-open>'.htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8', false).'</a>',
            OutputType::html,
        );
    }
}
