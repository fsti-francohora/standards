<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use NatePage\Standards\Exceptions\BinaryNotFoundException;
use NatePage\Standards\Interfaces\ToolInterface;
use Symfony\Component\Process\Process;

abstract class AbstractTool implements ToolInterface
{
    /**
     * Get tool description.
     *
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return null;
    }

    /**
     * Resolve given binary or return null.
     *
     * @param null|string $binary
     *
     * @return string
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException If binary not found
     */
    protected function resolveBinary(?string $binary = null): string
    {
        $binary = $binary ?? $this->getId();

        // Try inspected project vendor
        $vendor = \sprintf('vendor/bin/%s', $binary);

        if (\file_exists($vendor)) {
            return $vendor;
        }

        // Try command line tool
        $process = new Process(\sprintf('command -v %s', $binary));
        $process->run();
        $command = $process->getOutput();

        if (empty($command) === false && $process->isSuccessful()) {
            return \trim($command);
        }

        // Fallback to standards binary
        if (\defined('NP_STANDARDS_INTERNAL_VENDOR')
            && \file_exists(\sprintf('%sbin/%s', NP_STANDARDS_INTERNAL_VENDOR, $binary))) {
            return \sprintf('%sbin/%s', NP_STANDARDS_INTERNAL_VENDOR, $binary);
        }

        throw new BinaryNotFoundException(\sprintf('Binary for %s not found.', $binary));
    }

    /**
     * Return paths separated by spaces instead of commas.
     *
     * @param string $paths
     *
     * @return string
     */
    protected function spacePaths(string $paths): string
    {
        return \str_replace(',', ' ', $paths);
    }
}