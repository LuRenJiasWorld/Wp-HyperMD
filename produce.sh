#!/bin/bash
# 删除文件(夹) - 删除其他库开发无关文件

# assets/Config/src/
rm -rf assets/Config/src/

# languages/
rm -rf languages/*.po

# vendor/
rm -rf vendor/bin/

# vendor/composer
rm -rf vendor/composer/installed.json
rm -rf vendor/composer/LICENSE

# vendor/michelf
rm -rf vendor/michelf/php-markdown/.gitignore
rm -rf vendor/michelf/php-markdown/composer.json
rm -rf vendor/michelf/php-markdown/.gitignore
rm -rf vendor/michelf/php-markdown/License.md
rm -rf vendor/michelf/php-markdown/Readme.md
rm -rf vendor/michelf/php-markdown/Readme.php
rm -rf vendor/michelf/php-markdown/Michelf/Markdown.inc.php
rm -rf vendor/michelf/php-markdown/Michelf/MarkdownExtra.inc.php
rm -rf vendor/michelf/php-markdown/Michelf/MarkdownInterface.inc.php

# vendor/jaxsonwang
rm -rf vendor/jaxsonwang/wp-settings-api-class/.git/
rm -rf vendor/jaxsonwang/wp-settings-api-class/.gitignore
rm -rf vendor/jaxsonwang/wp-settings-api-class/composer.json
rm -rf vendor/jaxsonwang/wp-settings-api-class/README.md

# /
rm -rf .git/
rm -rf .github/
rm -rf .idea/
rm -rf .gitignore
rm -rf composer.json
rm -rf composer.lock
rm -rf README.md
rm -rf CHANGELOG.md
rm -rf produce.sh
rm -rf node_modules/