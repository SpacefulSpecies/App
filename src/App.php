<?php

namespace Species;

use Species\App\SlimAdapter;
use Species\App\StandardContainerBuilder;
use Species\App\StandardEnvironment;
use Species\App\StandardPaths;

/**
 * Species App.
 */
final class App extends SlimAdapter
{

    /**
     * @param string $rootPath
     * @return self
     */
    public static function withRootPath(string $rootPath): self
    {
        return new self(
            StandardContainerBuilder::BuildFrom(
                StandardEnvironment::FromPhpEnv(),
                StandardPaths::FromPhpEnvWithRootPath($rootPath)
            )
        );
    }

}
