# Terminus Developer Plugin

Developer - A Terminus plugin to assist with plugin development.

## Usage:
```
$ terminus developer:help <keyword> [--output=browse|print]
```
If the `--output` option is not provided, the default is `browse`.

## Examples:
```
$ terminus developer:help debug
```
Search for the keyword `debug` in the documentation and return results in the default browser.
```
$ terminus developer:help contribute --output=print
```
Search for the keyword `contribute` in the documentation and return results in the terminal.

## Installation:

For installation help, see [Manage Plugins](https://pantheon.io/docs/terminus/plugins/).

```
mkdir -p ~/.terminus/plugins
composer create-project -d ~/.terminus/plugins terminus-plugin-project/terminus-developer-plugin:~1
```

## Configuration:

This plugin requires no configuration to use.

## Help:
Run `terminus help developer:help` for help.
