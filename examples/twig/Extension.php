<?php

namespace examples\twig;

use examples\models\Menu;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class Extension
 */
class Extension extends AbstractExtension
{
  /**
   * @inheritDoc
   */
  public function getTokenParsers() {
    return [
      new ExampleTokenParser()
    ];
  }

  /**
   * @inheritDoc
   */
  public function getFunctions() {
    return [
      new TwigFunction('menu', [Menu::class, 'getInstance']),
    ];
  }
}
