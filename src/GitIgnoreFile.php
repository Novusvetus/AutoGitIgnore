<?php

/*
 * This file is part of AutoGitIgnore.
 *
 * (c) Novusvetus / Marcel Rudolf, Germany <development@novusvetus.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Novusvetus\AutoGitIgnore;

use Novusvetus\AutoGitIgnore\Exceptions\AutoGitIgnoreParserException;
use Novusvetus\AutoGitIgnore\Exceptions\AutoGitIgnorePermissionException;
use Novusvetus\AutoGitIgnore\Exceptions\AutoGitIgnoreSaveException;
use Novusvetus\ClassHelper\ClassHelper;

/**
 * This class parses the existing .gitignore file and builds the new one
 */
class GitIgnoreFile extends ClassHelper
{
    /**
     * Full path to the .gitignore file
     *
     * @var string
     */
    private $filePath;

    /**
     * Array containing all lines of the current .gitgnore file
     *
     * @var array
     */
    private $fileContent = array();

    /**
     * This identifies the position of the auto generated .gitignore lines
     *
     * @var int
     */
    private $afterLine;

    /**
     * Git comment marking the start of the auto generated lines
     *
     * @var string
     */
    protected $startMarker = '# AUTOGITIGNORE START - Do not modify here';

    /**
     * Git comment marking the end of the auto generated lines
     *
     * @var string
     */
    protected $endMarker = '# AUTOGITIGNORE END';

    /**
     * The paths to set in the file
     *
     * @var array
     */
    protected $lines = array();

    /**
     * Constructor
     *
     * @param string $filePath The path to the .gitignore file
     */
    public function __construct($filePath = '.gitignore')
    {
        $this->setFile($filePath);
    }

    /**
     * Sets the file
     *
     * @param string $filePath The path to the .gitignore file
     *
     * @return self
     */
    public function setFile($filePath = '.gitignore')
    {
        $this->fileContent = array();
        $this->afterLine = null;
        $this->filePath = $filePath;
        $this->checkPermissions();
        $this->parse();
    }

    /**
     * Gets the file path
     *
     * @return string
     */
    public function getFile()
    {
        return $this->filePath;
    }

    /**
     * Permission check
     *
     * @throws AutoGitIgnorePermissionException
     *
     * @return self
     */
    protected function checkPermissions()
    {
        $file = $this->getFile();
        if (!file_exists($file)) {
            if (!is_writable(dirname($file))) {
                throw ClassHelper::create('AutoGitIgnorePermissionException', 'You don\'t have the permissions to create ' . $file . '.');
            }
            touch($file);
        } elseif (!is_writable($file)) {
            throw ClassHelper::create('AutoGitIgnorePermissionException', 'You don\'t have the permissions to edit ' . $file . '.');
        }

        return $this;
    }

    /**
     * Loads the .gitignore file and parses it.
     *
     * @throws AutoGitIgnoreParseException
     *
     * @return self
     */
    protected function parse()
    {
        $fileContent = file($this->getFile(), FILE_IGNORE_NEW_LINES);

        $found = false;
        $open = false;
        foreach ($fileContent as $line) {
            if ($line == $this->startMarker) {
                if ($open) {
                    throw ClassHelper::create('AutoGitIgnoreParserException', 'There are two openings in this file.');
                } else {
                    if ($found) {
                        throw ClassHelper::create('AutoGitIgnoreParserException', 'There are two blocks in this file.');
                    } else {
                        $open = true;
                    }
                }
            } elseif ($line == $this->endMarker) {
                if (!$open) {
                    throw ClassHelper::create('AutoGitIgnoreParserException', 'The line ending is before the start.');
                } else {
                    $found = true;
                    $open = false;
                    $this->afterLine = count($this->fileContent);
                }
            } else {
                if (!$open) {
                    $this->fileContent[] = $line;
                }
            }
        }

        if (!$found) {
            $this->afterLine = count($this->fileContent);
        }

        return $this;
    }

    /**
     * Set the paths to write in .gitignore file
     *
     * @param array $lines
     *
     * @return self
     */
    public function setLines($lines)
    {
        if (!is_array($lines)) {
            $this->lines = array(
                $lines
            );
        } else {
            $this->lines = $lines;
        }

        return $this;
    }

    /**
     * Save to the .gitinore file
     *
     * @throws AutoGitIgnoreSaveException
     *
     * @return self
     */
    public function save()
    {
        if ($this->afterLine === null) {
            throw ClassHelper::create('AutoGitIgnoreSaveException', 'No file loaded.');
        }

        $output = array();
        if ($this->afterLine === 0) {
            $output[] = $this->startMarker;
            foreach ($this->lines as $l) {
                $output[] = $l;
            }
            $output[] = $this->endMarker;
        }
        $i = 0;
        foreach ($this->fileContent as $line) {
            $i++;

            $output[] = $line;
            if ($i == $this->afterLine) {
                $output[] = $this->startMarker;
                foreach ($this->lines as $l) {
                    $output[] = $l;
                }
                $output[] = $this->endMarker;
            }
        }

        if (!file_put_contents($this->getFile(), implode(PHP_EOL, $output))) {
            throw ClassHelper::create('AutoGitIgnoreSaveException', 'Saving to ' . $this->getFile() . ' failed.');
        }

        return $this;
    }
}
