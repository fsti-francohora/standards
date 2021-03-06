<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use Symfony\Component\Process\Process;

class PhpStan extends AbstractTool
{
    /**
     * Get tool name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'PHPSTAN';
    }

    /**
     * Get tool options.
     *
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return [
            'neon-file' => [
                'default' => 'phpstan.neon',
                'description' => 'Config file for PhpStan, ignored if file doesn\'t exist'
            ],
            'reporting-level' => [
                'default' => 7,
                'description' => 'The reporting level, 1 = loose, 7 = strict'
            ]
        ];
    }

    /**
     * Get process to run.
     *
     * @return \Symfony\Component\Process\Process
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException
     */
    public function getProcess(): Process
    {
        $configFile = $this->getOptionValue('neon-file') ?? '';
        $neonFile = \file_exists($configFile) ? \sprintf('--configuration=%s', $configFile) : '';

        return new Process($this->buildCli([
            $this->resolveBinary(),
            'analyze',
            $this->explodePaths($this->config->getValue('paths')),
            $neonFile,
            '--ansi',
            \sprintf('--level=%d', $this->getOptionValue('reporting-level')),
            '--no-progress'
        ]));
    }
}
