<?php

namespace Configure;

Class Configure
{
    public static function loadConfiguration() {
        $config = array(
            'default_host' => 'https://apiv2.indico.io',
            'cloud' => false,
            'api_key' => false
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
        if (getenv('INDICO_API_KEY')) {
            $indico_config['api_key'] = getenv('INDICO_API_KEY');
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
                array_key_exists('api_key', $parsed_config['auth'])
            );
            if ($authDefined) {
                $config['api_key'] = $parsed_config['auth']['api_key'];
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
