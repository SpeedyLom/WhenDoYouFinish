<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\Tests\WebEngine;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use SpeedyLom\WhenDoYouFinish\WebEngine\HtmlTemplate;

class HtmlTemplateTest extends TestCase
{
    private vfsStreamDirectory $root;
    private HtmlTemplate $htmlTemplate;

    public function testNewInstance(): void
    {
        $this->assertEquals(
            HtmlTemplate::class,
            $this->htmlTemplate::class
        );
    }

    public function testViewTemplateWithNoValueReplacement(): void
    {
        vfsStream::newFile('views/test_no_value_replacement.html')
            ->at($this->root)
            ->setContent('<h1>No value replacement</h1>');

        $this->expectOutputString('<h1>No value replacement</h1>');
        $this->htmlTemplate->display('test_no_value_replacement');
    }

    public function testViewWithValueReplacement(): void
    {
        vfsStream::newFile('views/test_value_replacement.html')
            ->at($this->root)
            ->setContent('{{ content }}');

        $this->expectOutputString('&lt;h1&gt;Value&lt;/h1&gt;&lt;p&gt;Replacement&lt;/p&gt;');

        $values = [
            'content' => '<h1>Value</h1><p>Replacement</p>',
        ];
        $this->htmlTemplate->display('test_value_replacement', $values);
    }

    public function testViewWithValueReplacementWrongTag(): void
    {
        vfsStream::newFile('views/test_wrong_tag.html')
            ->at($this->root)
            ->setContent('{{ content }}');

        $values = [
            'wrong_tag' => '<h1>Value</h1><p>Replacement</p>',
        ];

        $this->expectOutputString(
            '{{ content }}',
        );

        $this->htmlTemplate->display('test_wrong_tag', $values);
    }

    public function testTwoTemplates(): void
    {
        vfsStream::newFile('views/test_file_one.html')
            ->at($this->root)
            ->setContent('<p>Template One</p>');

        $this->expectOutputString('<p>Template One</p>');
        $this->htmlTemplate->display('test_file_one');

        vfsStream::newFile('views/test_file_two.html')
            ->at($this->root)
            ->setContent('<p>Template Two</p>');

        $this->expectOutputString('<p>Template One</p><p>Template Two</p>');
        $this->htmlTemplate->display('test_file_two');
    }

    public function testNoOutputWhenTemplateNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->htmlTemplate->display('test_missing_file');
    }

    public function testExtendingALayoutWithStaticContent()
    {
        vfsStream::newFile('views/test_layout.html')
            ->at($this->root)
            ->setContent(
                '<title>{% tag title %}</title>
                        <body>{% tag body %}</body>'
            );

        vfsStream::newFile('views/test_static_content_with_layout.html')
            ->at($this->root)
            ->setContent(
                '{% extend test_layout %}
                {% insert title %}Burger{% end %}
                {% insert body %}With a side of chips.{% end %}'
            );

        $this->expectOutputString(
            '<title>Burger</title>
                        <body>With a side of chips.</body>'
        );
        $this->htmlTemplate->display('test_static_content_with_layout');
    }

    public function testExtendingALayoutWithDynamicContent()
    {
        vfsStream::newFile('views/test_layout.html')
            ->at($this->root)
            ->setContent(
                '<title>{% tag title %}</title>
                        <body>{% tag body %}</body>'
            );

        vfsStream::newFile('views/test_dynamic_content_with_layout.html')
            ->at($this->root)
            ->setContent(
                '{% extend test_layout %}
                {% insert title %}Burger{% end %}
                {% insert body %}With a side of {{ content }}.{% end %}'
            );

        $this->expectOutputString(
            '<title>Burger</title>
                        <body>With a side of Hello World!.</body>'
        );
        $this->htmlTemplate->display(
            'test_dynamic_content_with_layout',
            ['content' => 'Hello World!']
        );
    }

    public function testExtendingALayoutWithMultiLineContent()
    {
        vfsStream::newFile('views/test_layout.html')
            ->at($this->root)
            ->setContent(
                '<title>{% tag title %}</title>
                        <body>{% tag body %}</body>'
            );

        vfsStream::newFile('views/test_dynamic_content_with_layout.html')
            ->at($this->root)
            ->setContent(
                '{% extend test_layout %}
                {% insert title %}Burger{% end %}
                {% insert body %}
                With a side of {{ content }}.
                {% end %}'
            );

        $this->expectOutputString(
            '<title>Burger</title>
                        <body>
                With a side of Hello World!.
                </body>'
        );
        $this->htmlTemplate->display(
            'test_dynamic_content_with_layout',
            ['content' => 'Hello World!']
        );
    }

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('root', null, ['views' => [],]);
        $this->htmlTemplate = new HtmlTemplate($this->root->url() . '/views');
    }
}
