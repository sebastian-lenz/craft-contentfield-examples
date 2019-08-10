<?php

namespace craft\contentmigrations;

use craft\db\Migration;
use examples\payload\Payload;
use Exception;

/**
 * m190810_104927_import_payload migration.
 */
class m190810_104927_import_payload extends Migration
{
  /**
   * @inheritdoc
   * @throws Exception
   */
  public function safeUp() {
    Payload::create()->import();
  }

  /**
   * @inheritdoc
   */
  public function safeDown() {
    echo "m190810_104927_import_payload cannot be reverted.\n";
    return false;
  }
}
