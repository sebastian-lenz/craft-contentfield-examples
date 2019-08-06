<?php

namespace examples\models;

use craft\base\ElementInterface;
use craft\elements\Entry;
use craft\helpers\Html;
use craft\helpers\Template;
use Twig\Markup;
use Yii;

/**
 * Class MenuItem
 */
class MenuItem
{
  /**
   * @var bool
   */
  public $active = false;

  /**
   * @var string
   */
  public $customLinkAttributes;

  /**
   * @var int
   */
  public $id;

  /**
   * @var int|false
   */
  public $parentId = false;

  /**
   * @var string
   */
  public $title;

  /**
   * @var int
   */
  public $sectionId;

  /**
   * @var int
   */
  public $typeId;

  /**
   * @var string
   */
  public $url;


  /**
   * MenuItem constructor.
   * @param ElementInterface|array $config
   */
  function __construct($config) {
    if ($config instanceof ElementInterface) {
      $this->setElement($config);
    } elseif (is_array($config)) {
      Yii::configure($this, $config);
    }
  }

  /**
   * @param bool $includeSelf
   * @return MenuItem[]
   */
  public function getAncestors($includeSelf = false) {
    return Menu::getInstance()->getAncestors($this, $includeSelf);
  }

  /**
   * @return MenuItem[]
   */
  public function getChildren() {
    return Menu::getInstance()->getChildren($this->id);
  }

  /**
   * @return Markup
   */
  public function getLinkAttributes() {
    if (isset($this->customLinkAttributes)) {
      return Template::raw($this->customLinkAttributes);
    }

    return Template::raw(Html::renderTagAttributes([
      'href' => $this->url
    ]));
  }

  /**
   * @return MenuItem|null
   */
  public function getParent() {
    return $this->parentId === false
      ? null
      : Menu::getInstance()->getById($this->parentId);
  }

  /**
   * @return bool
   */
  public function hasChildren() {
    return count($this->getChildren()) > 0;
  }


  // Protected methods
  // -----------------

  /**
   * @param ElementInterface $element
   */
  protected function setElement(ElementInterface $element) {
    $this->id = intval($element->getId());

    if ($element instanceof Entry) {
      $this->title     = $element->title;
      $this->sectionId = intval($element->sectionId);
      $this->typeId    = intval($element->typeId);
      $this->url       = $element->getUrl();
    }
  }


  // Static methods
  // --------------

  /**
   * @param ElementInterface[] $elements
   * @return array
   */
  static public function fromElements($elements) {
    $result = [];
    foreach ($elements as $element) {
      $result[] = new static($element);
    }

    return $result;
  }
}
