<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 14/12/16
 * Time: 03:20
 */

namespace Efrogg\CodeCoverage\Detector;


use Efrogg\CodeCoverage\CodeCoverageClient;

interface DetectorInterface
{
    public function setCoverageClient(CodeCoverageClient $client);
    public function detect();

    /**
     * @return bool
     */
    public function detectActivation();

    /**
     * @return bool
     */
    public function detectDeactivation();

}