<?php

namespace Roots\Sage;

use Composer\Script\Event;
use Composer\Package\RootPackage;
use Composer\Util\ProcessExecutor;

class PostCreateProject
{
    public static function addMurmurTools(Event $event) {
        // @codingStandardsIgnoreStart
        $io = $event->getIO();

        if ($io->isInteractive()) :
            $exec = new ProcessExecutor($io);

            $io->write('<info>Add optional packages.</info>');

            $packages = [
                'better-excerpt' => $io->ask('<info><options=bold>Better Excerpt</>  Helps make more flexible excerpts. (<comment>y/N</comment>)</info>'),
                'cabinet' => $io->ask('<info><options=bold>Cabinet</>  A handy file wrapper post type, with pointers! (<comment>y/N</comment>)</info>'),
                'image-tools' => $io->ask('<info><options=bold>Image Tools</>  A collection of simple tools for interfacing with WP\'s media library. (<comment>y/N</comment>)</info>'),
                'use-fields' => $io->ask('<info><options=bold>Use Fields</>  Simplify your interactions with ACF. (<comment>y/N</comment>)</info>'),
                'mnemosyne' => $io->ask('<info><options=bold>Mnemosyne</>  Easy defaults with dynamic overrides. (<comment>y/N</comment>)</info>')
            ];

            $repositories = json_decode(file_get_contents('composer.json'))->repositories;

            foreach ($packages as $package => $install) :
                if (strtolower($install) === 'y') :
                    if($repositories->{sprintf('murmurcreative/%s', $package)}) :
                        $to_install[] = sprintf('murmurcreative/%s', $package);
                    endif;
                endif;
            endforeach;

            if(isset($to_install) && count($to_install) > 0) :
                $io->write('<comment> 💭 Thinking ... 💭 </comment>');
                $exec->execute(sprintf('composer require %s --no-suggest --ansi', join(' ', $to_install)));
                $io->write('<info> ✨ Well Done!✨ </info>');
            else :
                $io->write('<comment> Nothing to install! </comment>');
                $io->write('<info> Moving on ... </info>');
            endif;


        endif;
    }

    public static function updateHeaders(Event $event)
    {
        $io = $event->getIO();

        if ($io->isInteractive()) {
            $io->write('<info>Define theme headers. Press enter key for default.</info>');

            $theme_headers_default = [
                'name'        => 'Murmur Creative Project',
                'uri'         => 'https://murmurcreative.com/services/web-development/',
                'description' => 'This theme has been custom-built by Murmur Creative. It is based on roots/sage.',
                'version'     => '0.0',
                'author'      => 'Murmur Creative',
                'author_uri'  => 'https://murmurcreative.com/'
            ];
            $theme_headers = [
              'name'        => $io->ask('<info>Theme Name [<comment>'.$theme_headers_default['name'].'</comment>]:</info> ', $theme_headers_default['name']),
              'uri'         => $io->ask('<info>Theme URI [<comment>'.$theme_headers_default['uri'].'</comment>]:</info> ', $theme_headers_default['uri']),
              'description' => $io->ask('<info>Theme Description [<comment>'.$theme_headers_default['description'].'</comment>]:</info> ', $theme_headers_default['description']),
              'version'     => $io->ask('<info>Theme Version [<comment>'.$theme_headers_default['version'].'</comment>]:</info> ', $theme_headers_default['version']),
              'author'      => $io->ask('<info>Theme Author [<comment>'.$theme_headers_default['author'].'</comment>]:</info> ', $theme_headers_default['author']),
              'author_uri'  => $io->ask('<info>Theme Author URI [<comment>'.$theme_headers_default['author_uri'].'</comment>]:</info> ', $theme_headers_default['author_uri'])
            ];

            file_put_contents('resources/style.css', str_replace($theme_headers_default, $theme_headers, file_get_contents('resources/style.css')));
        }
    }

    public static function buildOptions(Event $event)
    {
        $io = $event->getIO();

        if ($io->isInteractive()) {
            $io->write('<info>Configure build settings. Press enter key for default.</info>');

            $current_settings = json_decode(file_get_contents('resources/assets/config.json'));

            $browsersync_settings_default = [
                'publicPath'  => $current_settings->publicPath,
                'devUrl'      => $current_settings->devUrl
            ];

            $browsersync_settings = [
                'publicPath'  => $io->ask('<info>Path to theme directory (eg. /wp-content/themes/sage) [<comment>'.$browsersync_settings_default['publicPath'].'</comment>]:</info> ', $browsersync_settings_default['publicPath']),
                'devUrl'      => $io->ask('<info>Local development URL of WP site [<comment>'.$browsersync_settings_default['devUrl'].'</comment>]:</info> ', $browsersync_settings_default['devUrl'])
            ];

            file_put_contents('resources/assets/config.json', str_replace('/app/themes/sage', $browsersync_settings['publicPath'], file_get_contents('resources/assets/config.json')));
            file_put_contents('resources/assets/config.json', str_replace($browsersync_settings_default['devUrl'], $browsersync_settings['devUrl'], file_get_contents('resources/assets/config.json')));
        }
    }
    // @codingStandardsIgnoreEnd
}
