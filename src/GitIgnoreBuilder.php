<?php declare(strict_types=1);

/*
 * This file is part of AutoGitIgnore.
 *
 * (c) Novusvetus / Marcel Rudolf, Germany <development@novusvetus.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Novusvetus\AutoGitIgnore;

use Composer\Script\Event;
use Novusvetus\ClassHelper\ClassHelper;

/**
 * This class implements the runner for the .gitignore builder
 */
class GitIgnoreBuilder extends ClassHelper
{
    /**
     * This runs the builder
     *
     * @param Composer\Script\Event $event The event which is fired by Composer
     *
     * @return bool Returns false, when there was an error
     */
    public static function Go(Event $event): bool
    {
        $event->getIO()->writeError('<info>Updating .gitignore: </info>', false);

        $composer = $event->getComposer();
        $repositoryManager = $composer->getRepositoryManager();
        $installManager = $composer->getInstallationManager();
        $extraConfiguration = $composer->getPackage()->getExtra();

        // Check in composer extra configuration if devOnly option is set
        $devRequires = (array_key_exists('autogitignore', $extraConfiguration)
            && $extraConfiguration['autogitignore'] == 'devOnly');

        // If devOnly option is set
        if ($devRequires) {
            // Grab original list of all require-dev packages
            $devRequires = array_keys($composer->getPackage()->getDevRequires());
            // Grab original list of all require packages
            $requires = array_keys($composer->getPackage()->getRequires());

            // Grab recursively require and require-dev packages
            foreach ($repositoryManager->getLocalRepository()->getPackages() as $package) {
                $devRequires = array_merge($devRequires, array_keys($package->getDevRequires()));
                $requires = array_merge($requires, array_keys($package->getRequires()));
            }

            // Remove duplicates
            $devRequires = array_unique($devRequires);
            $requires = array_unique($requires);

            // Sort packages
            sort($devRequires);
            sort($requires);
        }

        $packages = array();
        foreach ($repositoryManager->getLocalRepository()->getPackages() as $package) {
            // Test if we need to ignore the package
            // If the option devOnly is set, we check that
            // the package is not in the list of require-dev
            // OR is set in the list of require packages
            // before skipping.
            if ($devRequires && (!in_array($package->getName(), $devRequires) || in_array($package->getName(), $requires))) {
                continue;
            }
            $path = $installManager->getInstallPath($package);
            $packages[] = '/' . preg_replace('~^' . preg_quote(str_replace('\\', '/', getcwd()) . '/') . '~', '', str_replace('\\', '/', realpath($path))) . '/';
        }

        $packages = array_unique($packages);
        sort($packages);

        try {
            $gitIgnoreFile = GitIgnoreFile::create(getcwd() . DIRECTORY_SEPARATOR . '.gitignore');
            $gitIgnoreFile->setLines($packages);
            $gitIgnoreFile->save();
        } catch (Exception $exception) {
            $event->getIO()->writeError('<info>Failed - ' . $exception->getMessage() . '</info>');

            return false;
        }

        $event->getIO()->writeError('<info>Done - ' . count($packages) . ' packages ignored.</info>');

        return true;
    }
}
