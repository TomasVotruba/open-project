<?php declare(strict_types=1);

namespace OpenTraining;

use Iterator;
use OpenProject\BetterEasyAdmin\DependencyInjection\CompilerPass\CorrectionCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\Autodiscovery\Discovery;
use Symplify\FlexLoader\Flex\FlexLoader;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoReturnFactoryCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\ConfigurableCollectorCompilerPass;

final class OpenTrainingKernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @var FlexLoader
     */
    private $flexLoader;

    /**
     * @var Discovery
     */
    private $discovery;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);
        $this->flexLoader = new FlexLoader($environment, $this->getProjectDir());
        $this->discovery = new Discovery($this->getProjectDir(), [__DIR__ . '/../../../packages/user/']);
    }

    public function registerBundles(): Iterator
    {
        return $this->flexLoader->loadBundles();
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $this->discovery->discoverEntityMappings($containerBuilder);
        $this->discovery->discoverTemplates($containerBuilder);
        $this->discovery->discoverTranslations($containerBuilder);

        $this->flexLoader->loadConfigs($containerBuilder, $loader, [
            __DIR__ . '/../../../packages/*/config/config', // root packages
            // project packages
            $this->getProjectDir() . '/packages/*/config/*',
            $this->getProjectDir() . '/packages/*/config/packages/*',
        ]);
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $this->discovery->discoverRoutes($routeCollectionBuilder);

        $this->flexLoader->loadRoutes($routeCollectionBuilder);
    }

    /**
     * Order matters!
     */
    protected function build(ContainerBuilder $containerBuilder): void
    {
        // needs to be first, since it's adding new service definitions
        $containerBuilder->addCompilerPass(new AutoReturnFactoryCompilerPass());

        // correction compiler pass - needs to run before collector
        $containerBuilder->addCompilerPass(new CorrectionCompilerPass());

        $containerBuilder->addCompilerPass(new ConfigurableCollectorCompilerPass());

        // autowiring
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireSinglyImplementedCompilerPass());
    }
}
