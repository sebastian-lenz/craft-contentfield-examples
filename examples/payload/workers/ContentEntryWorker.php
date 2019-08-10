<?php

namespace examples\payload\workers;

use Craft;
use craft\base\Element;
use craft\base\Field;
use craft\elements\Entry;
use Exception;
use lenz\contentfield\fields\ContentField;
use lenz\contentfield\models\Content;
use lenz\contentfield\models\fields\ReferenceField;
use lenz\contentfield\models\migration\Instance;
use lenz\contentfield\models\schemas\AbstractSchema;
use lenz\contentfield\models\schemas\AbstractSchemaContainer;
use lenz\contentfield\models\values\InstanceValue;
use lenz\contentfield\Plugin;
use Throwable;

/**
 * Class ContentEntryWorker
 */
class ContentEntryWorker extends EntryWorker
{
  /**
   * @var string[]
   */
  private $_fields;

  /**
   * @var array
   */
  private $_references = [];


  /**
   * ContentEntryWorker constructor.
   *
   * @param array $config
   * @throws Exception
   */
  public function __construct($config = []) {
    parent::__construct($config);

    $this->_fields = $this->getContentFields();
    $this->findReferences();
  }


  // Protected methods
  // -----------------

  /**
   * @param Instance $root
   * @param callable $callback
   * @return array
   */
  protected function convertReferences(Instance $root, callable $callback) {
    foreach ($this->_references as $qualifier => $fields) {
      $affected = $root->find($qualifier);
      $affected->each(function(Instance $target) use ($callback, $fields) {
        foreach ($fields as $field) {
          $name        = $field['name'];
          $elementType = $field['elementType'];
          $references  = $target->getAttribute($name);
          if (!is_array($references)) {
            return;
          }

          $references = $callback($elementType, $references);
          $target->setAttribute($name, $references);
        }
      });
    }

    return $root->getSerializedValue();
  }

  /**
   * @param InstanceValue $content
   * @return array
   */
  protected function exportContent(InstanceValue $content) {
    $root = new Instance($content->getSerializedValue());

    return $this->convertReferences($root, function($elementType, $references) {
      return array_filter(array_map(function($id) use ($elementType) {
        $element = $elementType::findOne(['id' => $id]);
        return is_null($element)
          ? null
          : $element->uid;
      }, $references));
    });
  }

  /**
   * @inheritDoc
   */
  protected function exportElement(Element $element) {
    $result = parent::exportElement($element);
    if (!($element instanceof Entry)) {
      throw new Exception('Invalid element type');
    }

    foreach ($this->_fields as $handle) {
      try {
        $field = $element->getFieldValue($handle);
      } catch (Throwable $error) {
        continue;
      }

      if (!($field instanceof Content)) {
        throw new Exception(sprintf(
          'Invalid content field %s on entry %s.',
          $handle, $element->id
        ));
      }

      $model = $field->getModel();
      if (!is_null($model)) {
        $result[$handle] = $this->exportContent($model);
      }
    }

    return $result;
  }

  /**
   * @throws Exception
   */
  protected function findReferences() {
    list($templates) = Plugin::getInstance()
      ->schemas
      ->getTemplateLoader()
      ->getAllSchemas();

    foreach ($templates as $template) {
      $this->findReferencesOnSchema($template);
    }
  }

  /**
   * @param AbstractSchema $schema
   */
  protected function findReferencesOnSchema(AbstractSchema $schema) {
    foreach ($schema->fields as $field) {
      if ($field instanceof ReferenceField) {
        $this->_references[$schema->qualifier][] = [
          'name'        => $field->name,
          'elementType' => $field->elementType,
        ];
      }
    }

    if ($schema instanceof AbstractSchemaContainer) {
      foreach ($schema->getLocalStructures() as $structure) {
        $this->findReferencesOnSchema($structure);
      }
    }
  }

  /**
   * @return string[]
   */
  protected function getContentFields() {
    $fields = array_filter(
      Craft::$app->getFields()->getAllFields(),
      function(Field $field) {
        return $field instanceof ContentField;
      }
    );

    return array_map(function(Field $field) {
      return $field->handle;
    }, $fields);
  }

  /**
   * @param array $data
   * @return array
   */
  protected function importContent(array $data) {
    $root = new Instance($data);

    return $this->convertReferences($root, function($elementType, $references) {
      return array_filter(array_map(function($uid) use ($elementType) {
        $element = $elementType::findOne(['uid' => $uid]);
        return is_null($element)
          ? null
          : $element->id;
      }, $references));
    });
  }

  /**
   * @inheritDoc
   */
  protected function importElement(Element $element, array $data) {
    parent::importElement($element, $data);
    if (!($element instanceof Entry)) {
      throw new Exception('Invalid element type');
    }

    foreach ($this->_fields as $handle) {
      if (!array_key_exists($handle, $data)) {
        continue;
      }

      $field = $element->$handle;
      if (!($field instanceof Content)) {
        throw new Exception(sprintf(
          'Invalid content field %s on entry %s',
          $handle, $element->id
        ));
      }

      $model = $this->importContent($data[$handle]);
      $field->setModel($model);
    }
  }
}
