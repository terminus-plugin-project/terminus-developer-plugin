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
        ];

        $terms = [
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

        foreach ($docs as $doc) {
            if (!$this->isValidUrl($doc)) {
                $message = "The url {$doc} is not valid.";
                throw new TerminusException($message);
            }
            if ($options['output'] == 'browse') {
                $anchor = '';
                foreach ($terms as $term) {
                    if (stripos($term, $keyword) !== false) {
                        $anchor = $term;
                        break;
                    }
                }
                if ($anchor) {
                    $command = sprintf('%s %s', $cmd, $doc . '#' . $anchor);
                    exec($command);
                }
            } else {
                if ($content = @file_get_contents($doc)) {
                    $newlines = [];
                    $content = str_replace("\n", '<br />', $content);
                    $content = str_replace('`', '', $content);
                    preg_match('`<div class="col-xs-12 col-md-7 manual-doc">(.*)</div>`', $content, $matches);
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
                        print_r(implode("\n", $newlines));
                    }
                }
            }
        }
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
