<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\CoreBundle\Routing\ResponseContext\Csp\CspHandler;
use Contao\CoreBundle\Routing\ResponseContext\ResponseContextAccessor;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Contao\PageModel;
use Neo\AccessibilityBundle\Panel\Features;
use Neo\AccessibilityBundle\Panel\HeadScript;
use Neo\AccessibilityBundle\Panel\Placement;
use Neo\BasicBundle\Dock\Dock;
use Neo\BasicBundle\Page\LanguageVersion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingException;

/**
 * Accessibility panel: a floating button (or the insert tag
 * {{accessibility::link}}) opens a dialog with display aids for visitors:
 * quick options (profiles) at the top, then the settings in three groups. The
 * dialog opens next to the button, as a drawer at a side or in the centre;
 * with the overlay option it is modal. Usually placed once in the page layout
 * (footer section), so it is on every page. The floating button takes part
 * in the buttons at the edge of the screen of NEO Basic (dock): with buttons
 * of other extensions in its corner it becomes an entry of one settings
 * button with a menu, or is stacked with them, as set in the page layout.
 *
 * The controller only decides what is offered and registers the assets:
 *  - an inline <head> script (HeadScript) that applies the stored choices
 *    before the first paint;
 *  - public/accessibility.css (panel look and the effects on html[data-a11y-*]);
 *  - public/accessibility.js (deferred; wires the panel, stores the choices).
 * Visitor texts come from translations/neo_accessibility.<lang>.yaml in the
 * language of the page.
 */
#[AsFrontendModule('neo_accessibility', category: 'neo', template: 'frontend_module/neo_accessibility')]
final class AccessibilityPanelController extends AbstractFrontendModuleController
{
    private const ASSET_PATH = 'bundles/neoaccessibility/';

    public function __construct(
        private readonly ContentUrlGenerator $urlGenerator,
        private readonly ResponseContextAccessor $responseContextAccessor,
        private readonly LanguageVersion $languageVersion,
        // Buttons at the edge of the screen of NEO Basic; null with a Basic that does not have them yet.
        private readonly Dock|null $dock = null,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $offered = Features::fromModuleValue($model->nax_features);
        $page = $this->getPageModel();
        // The statement in the language of this website (linked language versions).
        $statement = $this->findStatementPage($this->languageVersion->in((int) $model->nax_statement, (int) $page?->rootId));

        $template->set('uid', 'nax-'.$model->id);
        $template->set('position', Placement::position($model->nax_position));
        $template->set('layout', Placement::layout($model->nax_layout));
        $template->set('modal', (bool) $model->nax_overlay);
        $template->set('font', \in_array(Features::FONT, $offered, true));
        $template->set('switches', Features::switches($offered));
        $template->set('groups', Features::groups($offered));
        $template->set('structure', \in_array(Features::STRUCTURE, $offered, true));
        // Read aloud: only voices of the visitor's device, unless online voices are allowed.
        $template->set('online_voices', (bool) $model->nax_online_voices && \in_array(Features::SPEAK, $offered, true));
        // Text size enlarges the whole page (designs with font sizes in px).
        $template->set('font_zoom', (bool) $model->nax_font_zoom && \in_array(Features::FONT, $offered, true));
        $template->set('profiles', Features::profiles($offered, Features::profilesFromModuleValue($model->nax_profiles)));
        $template->set('statement_url', null === $statement ? null : $this->urlFor($statement));
        $template->set('statement_current', null !== $statement && null !== $page && (int) $page->id === (int) $statement->id);
        $template->set('locale', $this->locale($page, $request));

        // The panel texts are the same on every page; keep them out of the search index.
        $template->set('searchable', false);

        $this->registerAssets($offered, (bool) $model->nax_font_zoom);

        if ('none' !== Placement::position($model->nax_position)) {
            $this->dock?->register($page);
        }

        return $template->getResponse();
    }

    /**
     * @param list<string> $offered
     */
    private function registerAssets(array $offered, bool $zoom): void
    {
        $script = HeadScript::build($offered, $zoom);

        // Keyed: a second panel module on the same page does not add the script twice.
        $GLOBALS['TL_HEAD']['neo_accessibility'] = '<script>'.$script.'</script>';
        $this->allowInlineScript($script);

        $GLOBALS['TL_CSS']['neo_accessibility'] = self::ASSET_PATH.'accessibility.css|all|static'.$this->version('accessibility.css');
        $GLOBALS['TL_JAVASCRIPT']['neo_accessibility'] = self::ASSET_PATH.'accessibility.js|defer|static'.$this->version('accessibility.js');
    }

    /**
     * When the website uses Contao's Content Security Policy, the inline <head>
     * script is allowed by its hash; without a CSP nothing happens.
     */
    private function allowInlineScript(string $script): void
    {
        $context = $this->responseContextAccessor->getResponseContext();

        if (null !== $context && $context->has(CspHandler::class)) {
            $context->get(CspHandler::class)->addHash('script-src', $script);
        }
    }

    /**
     * File modification time as an asset flag, so browsers fetch the file again
     * after an update of the extension.
     */
    private function version(string $file): string
    {
        $path = \dirname(__DIR__, 3).'/public/'.$file;

        return is_file($path) ? '|'.filemtime($path) : '';
    }

    private function findStatementPage(int $id): PageModel|null
    {
        if ($id < 1) {
            return null;
        }

        $page = PageModel::findPublishedById($id);

        return $page instanceof PageModel ? $page : null;
    }

    private function urlFor(PageModel $page): string|null
    {
        try {
            return $this->urlGenerator->generate($page);
        } catch (RoutingException) {
            return null;
        }
    }

    private function locale(PageModel|null $page, Request $request): string
    {
        $language = null !== $page && '' !== (string) $page->language ? (string) $page->language : $request->getLocale();

        return str_replace('-', '_', $language);
    }
}
