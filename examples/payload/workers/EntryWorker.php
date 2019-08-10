<?php

namespace examples\payload\workers;

use Craft;
use craft\base\Element;
use craft\elements\Entry;
use craft\records\EntryType;
use Exception;

/**
 * Class EntryWorker
 */
class EntryWorker extends ElementWorker
{
  /**
   * EntryWorker constructor.
   *
   * @param array $config
   */
  public function __construct($config = []) {
    parent::__construct($config + [
      'elementType' => Entry::class,
    ]);
  }


  // Protected methods
  // -----------------

  /**
   * @inheritDoc
   */
  protected function exportElement(Element $element) {
    if (!($element instanceof Entry)) {
      throw new Exception('Invalid element type');
    }

    $parent    = $element->getParent();
    $parentUid = $parent instanceof Element
      ? $parent->uid
      : null;

    return parent::exportElement($element) + [
      'siteUid'    => $element->site->uid,
      'sectionUid' => $element->section->uid,
      'typeUid'    => $element->type->uid,
      'parentUid'  => $parentUid,
      'title'      => $element->title,
      'slug'       => $element->slug,
    ];
  }

  /**
   * @inheritDoc
   */
  protected function importElement(Element $element, array $data) {
    if (!($element instanceof Entry)) {
      throw new Exception('Invalid element type');
    }

    $app     = Craft::$app;
    $type    = EntryType::findOne(['uid' => $data['typeUid']]);
    $site    = $app->getSites()->getSiteByUid($data['siteUid']);
    $section = $app->getSections()->getSectionByUid($data['sectionUid']);

    $newParentId = null;
    if (!is_null($data['parentUid'])) {
      $parent = Entry::findOne(['uid' => $data['parentUid']]);
      if (!is_null($parent)) {
        $newParentId = $parent->id;
      }
    }

    $element->siteId      = $site->id;
    $element->sectionId   = $section->id;
    $element->typeId      = $type->id;
    $element->newParentId = $newParentId;
    $element->title       = $data['title'];
    $element->slug        = $data['slug'];
  }
}
