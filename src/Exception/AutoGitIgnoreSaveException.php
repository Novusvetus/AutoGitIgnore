<?php declare(strict_types=1);

/*
 * This file is part of AutoGitIgnore.
 *
 * (c) Novusvetus / Marcel Rudolf, Germany <development@novusvetus.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Novusvetus\AutoGitIgnore\Exceptions;

use Exception;

/**
 * This exception is used for failures while saving the .gitignore file
 */
class AutoGitIgnoreSaveException extends Exception
{
}
