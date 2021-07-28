<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\WebEngine;

final class HtmlTemplate
{
    private const INCLUDE_BLOCK_REGEX = '/(?<include_block>{% ?(?<view_type>extends|include) ?\'?(?<view_name>[a-zA-Z]*?)\'? ?%})/i';
    private const EXTENDS_REGEX = '/{% ?(extends|include) ?\'?([a-zA-Z]*?)\'? ?%}/i';
    private const CODE_BLOCK_REGEX = '/{% ?block ?(.*?) ?%}(.*?){% ?endblock ?%}/is';
    private const YIELD_REGEX = '/{% ?yield ?(.*?) ?%}/i';
    private mixed $blocks = [];

    public function __construct(
        private bool $cacheEnabled = true,
        private string $viewPath = __DIR__ . '\..\..\views\\',
        private string $cachePath = __DIR__ . '\..\..\cache\\',
    ) {
    }

    public function view(string $viewName, mixed $data = []): void
    {
        if ($this->cacheEnabled && $this->canUseCachedView($viewName)) {
            $this->extractDataAndRequireView($viewName, $data);
            return;
        }

        $this->generateCacheFileForView($viewName, $this->getCachedViewPath($viewName));
        $this->extractDataAndRequireView($viewName, $data);
    }

    public function clearCache(): void
    {
        foreach (glob($this->cachePath . '*') as $file) {
            unlink($file);
        }
    }

    private function canUseCachedView(string $viewName): bool
    {
        $cachedViewPath = $this->getCachedViewPath($viewName);

        return ! file_exists($cachedViewPath) || filemtime($cachedViewPath) < filemtime($this->getViewFilePath($viewName));
    }

    private function getCachedViewPath(string $viewName): string
    {
        return $this->cachePath . $viewName . '.php';
    }

    private function getViewFilePath(string $viewName): string
    {
        return $this->viewPath . $viewName . '.html';
    }

    private function extractDataAndRequireView(string $viewName, mixed $data = []): void
    {
        extract($data, EXTR_SKIP);
        require $this->getCachedViewPath($viewName);
    }

    private function generateCacheFileForView(string $viewName, string $cachedViewPath): void
    {
        $this->createCacheDirectory();

        $viewSourceCode = $this->includeFiles($viewName);
        $viewSourceCode = $this->compileCode($viewSourceCode);

        $this->storeViewInCache($cachedViewPath, $viewSourceCode);
    }

    private function createCacheDirectory(): void
    {
        if (! file_exists($this->cachePath)) {
            mkdir($this->cachePath, 0744);
        }
    }

    private function includeFiles(string $viewName): string
    {
        $viewSourceCode = file_get_contents($this->getViewFilePath($viewName));

        preg_match_all(self::INCLUDE_BLOCK_REGEX, $viewSourceCode, $includeBlocks, PREG_SET_ORDER);

        foreach ($includeBlocks as $block) {
            $viewSourceCode = str_replace($block['include_block'], $this->includeFiles($block['view_name']), $viewSourceCode);
        }

        return preg_replace(self::EXTENDS_REGEX, '', $viewSourceCode);
    }

    private function compileCode(string $code): string
    {
        $code = $this->compileBlock($code);
        $code = $this->compileYield($code);
        $code = $this->compileEchos($code);
        return $this->compilePHP($code);
    }

    private function compileBlock(string $code): string
    {
        preg_match_all(self::CODE_BLOCK_REGEX, $code, $matches, PREG_SET_ORDER);

        foreach ($matches as $value) {
            if (! array_key_exists($value[1], $this->blocks)) {
                $this->blocks[$value[1]] = '';
            }

            if (! str_contains($value[2], '@parent')) {
                $this->blocks[$value[1]] = $value[2];
            } else {
                $this->blocks[$value[1]] = str_replace('@parent', $this->blocks[$value[1]], $value[2]);
            }
            $code = str_replace($value[0], '', $code);
        }
        return $code;
    }

    private function compileYield(string $code): string
    {
        foreach ($this->blocks as $block => $value) {
            $code = preg_replace('/{% ?yield ?' . $block . ' ?%}/', $value, $code);
        }

        return preg_replace(self::YIELD_REGEX, '', $code);
    }

    private function compileEchos(string $code): string
    {
        return preg_replace('~\{{\s*(.+?)\s*\}}~is', '<?=$1?>', $code);
    }

    private function compilePHP(string $code): string
    {
        return preg_replace('~\{%\s*(.+?)\s*\%}~is', '<?=$1?>', $code);
    }

    private function storeViewInCache(string $cachedViewPath, string $viewSourceCode): void
    {
        $cacheFileHeader = '<?php declare(strict_types=1); class_exists(\'' . self::class . '\') or exit; ?>' . PHP_EOL;

        file_put_contents($cachedViewPath, $cacheFileHeader . $viewSourceCode);
    }
}
