<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\WebEngine;

use Exception;

final class HtmlTemplate
{
    private const EXTEND_REGEX = '/{% ?extend ?(?<layout_name>\w+) ?%}/';
    private const INSERT_REGEX =
        '/{% ?insert ?(?<tags>.*?) %}(?<content>.*?){% ?end ?%}/is';

    public function __construct(
        private string $directory
    ) {
    }

    /**
     * @throws Exception
     */
    public function display(
        string $templateName,
        mixed $placeholderContent = []
    ): void {
        if (!$this->templateExists($templateName)) {
            throw new Exception('template not found');
        }

        $template = $this->fetchTemplateByName($templateName);
        $template = $this->replacePlaceholdersWithContent(
            $placeholderContent,
            $template
        );

        $layoutName = $this->getLayoutNameFromTemplate($template) ?? null;
        if ($this->templateExists($layoutName)) {
            $template = $this->extendTemplateWithLayout($template, $layoutName);
        }

        echo $template;
    }

    private function getLayoutNameFromTemplate(
        string $template
    ): ?string {
        preg_match(self::EXTEND_REGEX, $template, $matches);

        return $matches['layout_name'] ?? null;
    }

    private function replacePlaceholdersWithContent(
        mixed $contentToInsert,
        string $template
    ): string {
        foreach ($contentToInsert as $placeholder => $content) {
            $template = str_replace(
                '{{ ' . $placeholder . ' }}',
                htmlspecialchars(strval($content)),
                $template
            );
        }
        return $template;
    }

    private function getPathFromTemplateName(
        ?string $templateName
    ): string {
        return $this->directory . DIRECTORY_SEPARATOR . $templateName . '.html';
    }

    private function fetchTemplateByName(
        string $templateName
    ): string|false {
        return file_get_contents($this->getPathFromTemplateName($templateName));
    }

    private function templateExists(
        ?string $templateName
    ): bool {
        return file_exists($this->getPathFromTemplateName($templateName));
    }

    private function extendTemplateWithLayout(
        string $template,
        string $layoutName
    ): string {
        $layout = $this->fetchTemplateByName($layoutName);
        return $this->replaceTagsWithContent($template, $layout);
    }

    private function replaceTagsWithContent(
        string $template,
        string $layout
    ): string {
        preg_match_all(self::INSERT_REGEX, $template, $matches);
        foreach ($matches['tags'] as $index => $tag) {
            $layout = str_replace(
                '{% tag ' . $tag . ' %}',
                strval($matches['content'][$index]),
                $layout
            );
        }
        return $layout;
    }
}
