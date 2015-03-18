<?php

namespace Configure;

Class Configure
{
    public static function loadConfiguration() {
        $config = array(
            'default_host' => 'http://apiv1.indico.io',
            'cloud' => false,
            'auth' => false
        );
        if (array_key_exists('HOME', $_ENV)) {
            $globalPath = $_ENV['HOME'] . '/.indicorc';
            $config = Configure::loadConfigFile($globalPath, $config);
        }
        $localPath = getcwd() . '/.indicorc';
        $config = Configure::loadConfigFile($localPath, $config);
        $config = Configure::loadEnvironmentVars($config);
        return $config;
    }

    public static function loadEnvironmentVars($indico_config) {
        $authDefined = (
            getenv('INDICO_USERNAME') &&
            getenv('INDICO_PASSWORD')
        );
        if ($authDefined) {
            $indico_config['auth'] = array(
                getenv('INDICO_USERNAME'),
                getenv('INDICO_PASSWORD')
            );
        }
        if (getenv('INDICO_CLOUD')) {
            $indico_config['cloud'] = getenv('INDICO_CLOUD');
        }
        return $indico_config;
    }

    public static function loadConfigFile($configPath, $config) {
        if (file_exists($configPath)) {
            $parsed_config = parse_ini_file($configPath, true);
            if (!$parsed_config) {
                return $config;
            }

            $authDefined = (
                array_key_exists('auth', $parsed_config) &&    
                array_key_exists('username', $parsed_config['auth']) &&
                array_key_exists('password', $parsed_config['auth'])
            );
            if ($authDefined) {
                $config['auth'] = array(
                    $parsed_config['auth']['username'],
                    $parsed_config['auth']['password']
                );
            }

            $cloudDefined = (
                array_key_exists('private_cloud', $parsed_config) &&
                array_key_exists('cloud', $parsed_config['private_cloud'])
            );
            if ($cloudDefined) {
                $config['cloud'] = $parsed_config['private_cloud']['cloud'];
            }
        }
        return $config;
    }
}