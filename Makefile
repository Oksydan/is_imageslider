build-module-zip: build-composer build-zip

build-zip:
	rm -rf is_imageslider.zip
	cp -Ra $(PWD) /tmp/is_imageslider
	rm -rf /tmp/is_imageslider/config_*.xml
	rm -rf /tmp/is_imageslider/.github
	rm -rf /tmp/is_imageslider/.gitignore
	rm -rf /tmp/is_imageslider/.php-cs-fixer.cache
	rm -rf /tmp/is_imageslider/.git
	rm -rf /tmp/is_imageslider/img
	mkdir /tmp/is_imageslider/img
	mv -v /tmp/is_imageslider $(PWD)/is_imageslider
	zip -r is_imageslider.zip is_imageslider
	rm -rf $(PWD)/is_imageslider

build-composer:
	composer install --no-dev -o

