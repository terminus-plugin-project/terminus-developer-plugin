<?php

namespace TerminusPluginProject\TerminusDeveloper\Commands;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Exceptions\TerminusException;

/**
 * Plugin development assistant.
 */
class DeveloperCommand extends TerminusCommand
{

    /**
     * Plugin development assistant.
     *
     * @command developer:help
     *
     * @option keyword Keyword to search in help
     *
     * @usage terminus developer:help <keyword> [--output=browse|print]
     *     Displays the results of a search based on the keyword provided.
     */
    public function help($keyword = '', $options = ['output' => 'browse']) {

        if (!$keyword) {
            $message = "Usage: terminus developer:help <keyword> [--output=browse|print]";
            throw new TerminusException($message);
        }

        switch (php_uname('s')) {
            case 'Linux':
                $cmd = 'xdg-open';
                break;
            case 'Darwin':
                $cmd = 'open';
                break;
            case 'Windows NT':
                $cmd = 'start';
                break;
            default:
                throw new TerminusException('Operating system not supported.');
        }

        $docs = [
            'https://pantheon.io/docs/terminus/plugins/create/',
            'https://github.com/pantheon-systems/terminus/blob/master/CONTRIBUTING.md',
        ];

        $terms = [];
        $terms[0] = [
            'create-the-example-plugin',
            '1.-create-plugin-directory',
            '2.-create-composer.json',
            '3.-add-commands',
            'debug',
            'distribute-plugin',
            'vendor-name',
            'psr-4-namespacing',
            'coding-standards',
            'plugin-versioning',
            'more-resources',
        ];
        $terms[1] = [
            'user-content-creating-issues',
            'user-content-setting-up',
            'user-content-submitting-patches',
            'user-content-running-and-writing-tests',
            'user-content-unit-tests',
            'user-content-functional-tests',
            'user-content-versioning',
            'user-content-versions',
            'user-content-what-qualifies-as-a-backward-incompatible-change',
            'user-content-release-stability',
            'user-content-feedback',
        ];

        foreach ($docs as $key => $doc) {
            if (!$this->isValidUrl($doc)) {
                $message = "The url {$doc} is not valid.";
                throw new TerminusException($message);
            }
            if ($options['output'] == 'browse') {
                foreach ($terms[$key] as $term) {
                    if (stripos($term, $keyword) !== false) {
                        $command = sprintf('%s %s', $cmd, $doc . '#' . $term);
                        $this->execute($command);
                    }
                }
            } else {
                if ($content = @file_get_contents($doc)) {
                    $newlines = [];
                    $content = str_replace("\n", '<br />', $content);
                    $content = str_replace('`', '', $content);
                    switch ($key) {
                        case 0:
                            $pattern = '`<div class="col-xs-12 col-md-7 manual-doc">(.*)</div>`';
                            break;
                        case 1:
                            $pattern = '`<div id="readme" class="readme blob instapaper_body">(.*)</div>`';
                            break;
                    }
                    preg_match($pattern, $content, $matches);
                    if (isset($matches[1])) {
                        $lines = explode('<br />', $matches[1]);
                        foreach ($lines as $l => $line) {
                            $newline = strip_tags($line);
                            if (stripos($newline, $keyword)) {
                                $newlines[] = html_entity_decode($newline);
                            }
                        }
                    }
                    if (!empty($newlines)) {
                        print("\nResults from {$doc}:\n");
                        print(implode("\n", $newlines));
                        print "\n";
                    }
                }
            }
        }
    }

    /**
     * Executes the command.
     */
    protected function execute($cmd) {
        $process = proc_open(
            $cmd,
            [
                0 => STDIN,
                1 => STDOUT,
                2 => STDERR,
            ],
            $pipes
        );
        proc_close($process);
    }

    /**
     * Check whether a URL is valid
     *
     * @param string $url The URL to check
     * @return bool True if the URL returns a 200 status
     */
    private function isValidUrl($url = '') {
        if (!$url) {
            return false;
        }
        $headers = @get_headers($url);
        if (!isset($headers[0])) {
            return false;
        }
        return (strpos($headers[0], '200') !== false);
    }

}
