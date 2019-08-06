<?php

namespace examples\twig;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Class ExampleTokenParser
 */
class ExampleTokenParser extends AbstractTokenParser
{
  /**
   * @param Token $token
   * @return bool
   */
  public function decideBlockEnd(Token $token) {
    return $token->test('endexample');
  }

  /**
   * @inheritDoc
   */
  public function getTag() {
    return 'example';
  }

  /**
   * @inheritDoc
   */
  public function parse(Token $token) {
    $lineno = $token->getLine();
    $stream = $this->parser->getStream();

    $stream->expect(Token::BLOCK_END_TYPE);
    $values = $this->parser->subparse([$this, 'decideBlockEnd'], true);

    $source  = explode("\n", $stream->getSourceContext()->getCode());
    $lines   = $stream->getCurrent()->getLine() - $lineno - 1;
    $example = array_splice($source, $lineno, $lines);

    $example = implode("\n", array_map(function($line) {
      return substr($line, 4);
    }, $example));

    $stream->expect(Token::BLOCK_END_TYPE);
    return new ExampleNode($values, $example, $lineno, $this->getTag());
  }
}
