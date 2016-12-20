<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 14/12/16
 * Time: 03:20
 */

namespace Efrogg\CodeCoverage\Detector;


use Efrogg\CodeCoverage\CodeCoverageClient;
use Efrogg\CodeCoverage\Persister\PersisterInterface;

abstract class PersistedDetector implements DetectorInterface
{
    /** @var  CodeCoverageClient */
    protected $coverage_client;

    /** @var  PersisterInterface */
    protected $persister;

    public function setCoverageClient(CodeCoverageClient $client) {
        $this->coverage_client = $client;
        return $this;
    }

    /**
     * @param PersisterInterface $persister
     * @return PersistedDetector
     */
    public function setPersister(PersisterInterface $persister)
    {
        $this->persister = $persister;
        return $this;
    }

}