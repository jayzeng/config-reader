# Config Reader to parse ini files
[![Build Status](https://travis-ci.org/jayzeng/config-reader.png)](https://travis-ci.org/jayzeng/config-reader)

Project website: (http://jayzeng.github.com/config-reader/)

##Usage:
config.ini
```ini
[production]
username = 'jayzeng'
password = 'password'

[whitelistIps]
ip[] = 127.0.0.1
ip[] = 192.168.0.1/24
```

```php
use ConfigReader\Ini as IniReader;

// read a specific rule
$username = IniReader::factory( __DIR__ . DIRECTORY_SEPARATOR . 'config.ini' )
                    ->setLabel('production')
                    ->getLabel('username');
                    // returns jayzeng
```

Read all rules within a label section
```php
use ConfigReader\Ini as IniReader;

// read all rules within production section
$prodConfig = IniReader::factory( __DIR__ . DIRECTORY_SEPARATOR . 'config.ini' )
                    ->setLabel('production')
                    ->toArray();

// returns
// array
// 'username' => 'jayzeng'
// 'password' => 'password'

// You can also populate an array
$ips = IniReader::factory( __DIR__ . DIRECTORY_SEPARATOR . 'config.ini' )
                    ->setLabel('whitelistIps')
                    ->toArray();
// returns
// array
// 'ip' => array ( '127.0.0.1', '192.168.0.1/24' );
```

##Issues & Development
- Source hosted [GitHub](https://github.com/jayzeng/config-reader)
- Report issues, questions, feature requests on [GitHub Issues](https://github.com/jayzeng/config-reader/issues)

##How to release new version?
- RELEASE_VERSION - version number
- RELEASE_MESSAGE - release message

```bash
make release RELEASE_VERSION="0.1" RELEASE_MESSAGE="v0.1 is released"
```

##Author:
[Jay Zeng](https://github.com/jayzeng/), e-mail: [jayzeng@jay-zeng.com](mailto:jayzeng@jay-zeng.com)
