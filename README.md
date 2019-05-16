# Website Installator


This is a PHP library to automation installation Wordpress on the server. In the future will be supported other CMS.

## Dependencies

The library uses [touki/ttp](https://github.com/touki653/php-ftp-wrapper) and [phpseclib/phpseclib](https://github.com/phpseclib/phpseclib). PHP versions supported >= 5.6.

## Basic usage



```php
require_once '../vendor/autoload.php';

use Vertiso\WebsiteInstallator\CMS\WordpressConfiguration;
use Vertiso\WebsiteInstallator\CMS\WordpressInstallation;
use Vertiso\WebsiteInstallator\Factory\InstallationManagerFactory;

$config = [
    'tmp'   =>  __DIR__ . '/tmp',
    'ssh'   =>  [
        'host'      =>  'yourSSHHost',
        'port'      =>  'yourSSHPort',
        'username'  =>  'yourSSHUsername',
        'password'  =>  'yourSSHPassword'
    ],
    'mysql' =>  [
        'host'      =>  'yourDBHost',
        'username'  =>  'yourDBUser',
        'password'  =>  'yourDBPass',
        'name'      =>  'yourDBHostName'
    ],
    'ftp'   =>  [
        'host'  =>  'yourFTPHost',
        'username'  =>  'yourFTPUser',
        'password'  =>  'yourFTPHostPass'
    ],
    'path'  =>  [
        'ssh'   =>  'absolute path to website catalog', // for example: /home/vertiso/domains/example.com/public_html
        'ftp'   =>  'absolute path to website catalog' // for example: /domains/example.com/public_html
    ],
    'website' =>  [
        'url'           =>  'http://example.com',
        'title'         =>  'Example Wordpress site by Vertiso WebsiteInstallator',
        'userNiceName'  =>  'Vertiso',
        'auth'  =>  [
            'username'  =>  'yourAuthUsername',
            'password'  =>  'yourAuthPassword',
            'email'     =>  'yourAuthEmail'
        ],
        'package'       =>  [
            'urlZipFile'    =>  'http://example.com/download',
            'fileName'      =>  'package.zip',
            'sqlFileName'   =>  'database.sql' // database file in package
        ]
    ]
];


try {
    $installator = InstallationManagerFactory::create($config);

    $wpConfiguration = new WordpressConfiguration(
        $config['mysql']['host'],
        $config['mysql']['username'],
        $config['mysql']['password'],
        $config['mysql']['name']
    );

    $wpInstallation = new WordpressInstallation($installator);
    $wpInstallation->setConfigurationFiles($wpConfiguration);
    $wpInstallation->setWebsiteTitle($config['website']['title']);
    $wpInstallation->setHomeUrl($config['website']['url']);
    $wpInstallation->setUser(
        $config['website']['userNiceName'],
        $config['website']['userNiceName'],
        $config['website']['auth']['username'],
        $config['website']['auth']['password'],
        $config['website']['auth']['email']
    );

    $wpInstallation->install(
        $config['website']['package']['urlZipFile'],
        $config['website']['package']['fileName'],
        $config['website']['package']['sqlFileName']
    );

    echo 'Website Installed!';

}catch (\Exception $exception) {
    echo $exception->getMessage();
}
```
