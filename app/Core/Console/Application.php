<?php

namespace Safe4Work\Core\Console;

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ProcessUtils;
use Safe4Work\Core\Events\DispatchesEvents;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends \Illuminate\Console\Application
{
    use DispatchesEvents;

    protected $commandsLoaded = false;

    public function __construct(Container $laravel, Dispatcher $events, $version)
    {

        $parent = get_parent_class(\Illuminate\Console\Application::class);
        $parent::__construct('Leantime CLI (extends Laravel)', $version);

        $this->laravel = $laravel;
        $this->events = $events;
        $this->setAutoExit(false);
        $this->setCatchExceptions(false);

        $this->events->dispatch(new ArtisanStarting($this));

        $this->bootstrap();
    }

    /**
     * Runs the current application.
     *
     * @return int 0 if everything went fine, or an error code
     */
    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        // $this->setDomain($input);

        self::dispatchEvent('beforeRun', ['application' => $this, 'input' => $input, 'output' => $output]);

        /* wrapper for future use */
        return parent::doRun($input, $output);
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $definition = parent::getDefaultInputDefinition();

        $definition->addOption(new InputOption('--domain', null, InputOption::VALUE_OPTIONAL, 'Set domain for config'));

        return $definition;

    }

    protected function bootstrap()
    {

        foreach (static::$bootstrappers as $bootstrapper) {
            $bootstrapper($this);
        }
    }

    public static function artisanBinary()
    {
        return ProcessUtils::escapeArgument('bin/leantime');
    }
}
