<?php

namespace examples\payload\workers;

/**
 * Interface WorkerInterface
 */
interface WorkerInterface
{
  /**
   * @return array
   */
  public function export() : array;

  /**
   * @param array $data
   */
  public function import(array $data);
}
