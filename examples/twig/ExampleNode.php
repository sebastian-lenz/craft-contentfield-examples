<?php

namespace examples\twig;

use Twig\Compiler;
use Twig\Node\Node;

/**
 * Class ExampleTokenParser
 */
class ExampleNode extends Node
{
  /**
   * ExampleNode constructor.
   *
   * @param Node $content
   * @param string $example
   * @param int $lineno
   * @param string $tag
   */
  public function __construct(
    Node $content, string $example, int $lineno = 0, string $tag = null
  ) {
    parent::__construct(
      ['content' => $content],
      ['example' => $example],
      $lineno,
      $tag
    );
  }

  /**
   * @inheritDoc
   */
  public function compile(Compiler $compiler) {
    $id = uniqid();
    $example = $this->getAttribute('example');
    $compiler
      ->write("echo '<nav><div class=\"nav nav-tabs\" id=\"example-{$id}-tab\" role=\"tablist\">';")
      ->write("echo '<a class=\"nav-item nav-link active\" id=\"example-{$id}-output-tab\" data-toggle=\"tab\" href=\"#example-{$id}-output\" role=\"tab\" aria-controls=\"example-{$id}-output\" aria-selected=\"true\">Output</a>';")
      ->write("echo '<a class=\"nav-item nav-link\" id=\"example-{$id}-source-tab\" data-toggle=\"tab\" href=\"#example-{$id}-source\" role=\"tab\" aria-controls=\"example-{$id}-source\" aria-selected=\"false\">Source</a>';")
      ->write("echo '</div></nav>';")
      ->write("echo '<div class=\"tab-content pt-3 pr-3 pl-3 border-right border-bottom border-left\" id=\"example-{$id}-content\">';")
      ->write("echo '<div class=\"tab-pane fade show active\" id=\"example-{$id}-output\" role=\"tabpanel\" aria-labelledby=\"example-{$id}-output-tab\">';");

    $compiler->subcompile($this->getNode('content'));

    $compiler
      ->write("echo '</div><div class=\"tab-pane fade\" id=\"example-{$id}-source\" role=\"tabpanel\" aria-labelledby=\"example-{$id}-source-tab\">';")
      ->write("echo '<pre><code>';")
      ->write('echo ')
      ->repr(htmlentities($example))
      ->raw(';')
      ->write("echo '</code></pre>';")
      ->write("echo '</div></div>';");
  }
}
