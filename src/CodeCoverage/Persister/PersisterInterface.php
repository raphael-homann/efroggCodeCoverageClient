<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 29/11/16
 * Time: 10:02
 */

namespace efrogg\CodeCoverage\Persister;


interface PersisterInterface
{

    /**
     * @return bool
     */
    public function persist($value);

    /**
     * @return bool
     */
    public function exists();

    /**
     * @return string
     */
    public function read();

    /**
     * @return bool
     */
    public function delete();
}