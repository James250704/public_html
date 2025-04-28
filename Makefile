# Makefile for PHP project deployment
.PHONY: setup deploy clean

setup:
	@if [ ! -f config.php ]; then \
		cp config.template.php config.php; \
		echo "Please edit config.php with your database settings"; \
	else \
		echo "config.php already exists, skipping..."; \
	fi

clean:
	echo "Cleaning up..."
	rm -f config.php
	
update:
	git pull origin master